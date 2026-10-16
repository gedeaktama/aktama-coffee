<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\ConsignmentAudit;
use App\Models\ConsignmentAuditItem;
use App\Models\ConsignmentItem;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\StoreVisit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DriverPortalController extends Controller
{
    /**
     * Dashboard Utama Portal Mobile Driver
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Cari kunjungan yang masih aktif (in_progress) hari ini
        $activeVisit = StoreVisit::with('customer')
            ->where('user_id', $user->id)
            ->where('status', StoreVisit::STATUS_IN_PROGRESS)
            ->latest('check_in_at')
            ->first();

        // Statistik kinerja driver hari ini
        $todayStats = [
            'stores_visited' => StoreVisit::where('user_id', $user->id)
                ->where('visit_date', $today)
                ->where('status', StoreVisit::STATUS_COMPLETED)
                ->count(),
            'total_sold_qty' => (int) StoreVisit::where('user_id', $user->id)
                ->where('visit_date', $today)
                ->where('status', StoreVisit::STATUS_COMPLETED)
                ->sum('summary_sold_qty'),
            'total_sold_amount' => (float) StoreVisit::where('user_id', $user->id)
                ->where('visit_date', $today)
                ->where('status', StoreVisit::STATUS_COMPLETED)
                ->sum('summary_sold_amount'),
            'total_restock_qty' => (int) StoreVisit::where('user_id', $user->id)
                ->where('visit_date', $today)
                ->where('status', StoreVisit::STATUS_COMPLETED)
                ->sum('summary_restock_qty'),
        ];

        // Daftar kunjungan hari ini
        $todayVisits = StoreVisit::with('customer')
            ->where('user_id', $user->id)
            ->where('visit_date', $today)
            ->orderBy('check_in_at', 'desc')
            ->get();

        // Daftar target seluruh toko mitra untuk navigasi dan kunjungan
        $allStores = Customer::where('is_active', true)
            ->orderBy('business_name')
            ->get()
            ->map(function ($store) use ($today, $user) {
                $hasVisitedToday = StoreVisit::where('customer_id', $store->id)
                    ->where('visit_date', $today)
                    ->where('status', StoreVisit::STATUS_COMPLETED)
                    ->exists();

                $store->visited_today = $hasVisitedToday;
                $store->active_consignment_stock = $store->getActiveConsignmentStock();
                $store->is_low_stock = $store->active_consignment_stock <= $store->min_stock_alert;

                return $store;
            });

        return view('driver.dashboard', compact(
            'user',
            'activeVisit',
            'todayStats',
            'todayVisits',
            'allStores'
        ));
    }

    /**
     * Halaman Scanner Kamera QR Code
     */
    public function scan()
    {
        $stores = Customer::where('is_active', true)
            ->orderBy('business_name')
            ->get(['id', 'code', 'business_name', 'store_chain', 'address', 'qr_token', 'latitude', 'longitude']);

        return view('driver.scan', compact('stores'));
    }

    /**
     * Memproses Check-in Kunjungan dari Scan QR / Pemilihan Toko + GPS
     */
    public function processCheckIn(Request $request)
    {
        $request->validate([
            'qr_token' => 'nullable|string',
            'customer_id' => 'nullable|integer',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'visit_purpose' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Cari toko berdasarkan QR token atau customer_id
        $customer = null;
        if (!empty($request->qr_token)) {
            $customer = Customer::where('qr_token', trim($request->qr_token))->first();
            
            // Fallback: coba cocokkan dengan code jika token berupa CUST-XXXX
            if (!$customer) {
                $customer = Customer::where('code', trim($request->qr_token))->first();
            }
        }

        if (!$customer && !empty($request->customer_id)) {
            $customer = Customer::find($request->customer_id);
        }

        if (!$customer) {
            return redirect()->route('driver.scan')->with('error', 'Toko tidak ditemukan atau QR Code tidak valid.');
        }

        // Hitung jarak Geofencing jika koordinat driver dan toko tersedia
        $distanceMeters = null;
        $isGeofenceValid = true;

        if ($request->filled('latitude') && $request->filled('longitude') && $customer->latitude && $customer->longitude) {
            $distanceMeters = (int) $this->calculateHaversineDistance(
                (float) $request->latitude,
                (float) $request->longitude,
                (float) $customer->latitude,
                (float) $customer->longitude
            );

            // Validasi Geofence: toleransi radius 250 meter
            if ($distanceMeters > 250) {
                $isGeofenceValid = false;
            }
        }

        // Cek apakah ada kunjungan aktif yang belum selesai di toko ini
        $visit = StoreVisit::where('user_id', $user->id)
            ->where('customer_id', $customer->id)
            ->where('status', StoreVisit::STATUS_IN_PROGRESS)
            ->first();

        if (!$visit) {
            $visitNumber = 'SV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

            $visit = StoreVisit::create([
                'visit_number' => $visitNumber,
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'visit_date' => Carbon::today()->toDateString(),
                'check_in_at' => Carbon::now(),
                'check_in_lat' => $request->latitude,
                'check_in_lng' => $request->longitude,
                'distance_meters' => $distanceMeters,
                'is_geofence_valid' => $isGeofenceValid,
                'visit_purpose' => $request->input('visit_purpose', StoreVisit::PURPOSE_AUDIT_DELIVERY),
                'status' => StoreVisit::STATUS_IN_PROGRESS,
            ]);
        }

        $msg = "Berhasil check-in di {$customer->business_name}!";
        if (!$isGeofenceValid && $distanceMeters !== null) {
            $msg .= " Catatan: Posisi GPS Anda berjarak {$distanceMeters}m dari titik toko.";
        }

        return redirect()->route('driver.visit.action', $visit->id)->with('success', $msg);
    }

    /**
     * Halaman Formulir Aktivitas di Toko (Audit Stok, Serah Terima, POD)
     */
    public function visitAction(StoreVisit $visit)
    {
        $visit->load('customer');
        $customer = $visit->customer;

        // Ambil data konsinyasi aktif di toko ini
        $activeConsignment = Consignment::with(['items.variant.product'])
            ->where('customer_id', $customer->id)
            ->where('status', Consignment::STATUS_ACTIVE)
            ->first();

        // Jika toko belum punya konsinyasi, ambil semua varian produk untuk formulir titip baru
        $allVariants = ProductVariant::with('product')
            ->where('is_active', true)
            ->get();

        return view('driver.visit_detail', compact(
            'visit',
            'customer',
            'activeConsignment',
            'allVariants'
        ));
    }

    /**
     * Menyelesaikan Kunjungan Toko (Hitung Laku, Update Stok Toko, Simpan POD)
     */
    public function completeVisit(Request $request, StoreVisit $visit)
    {
        $request->validate([
            'recipient_name' => 'nullable|string|max:100',
            'recipient_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'photo_proof' => 'nullable|image|max:5120', // Maks 5MB
            'audit_items' => 'nullable|array',
        ]);

        $user = Auth::user();
        $customer = $visit->customer;

        DB::beginTransaction();
        try {
            // 1. Handle Upload Foto Bukti Serah Terima / Rak
            $photoPath = $visit->photo_proof;
            if ($request->hasFile('photo_proof')) {
                $file = $request->file('photo_proof');
                $filename = 'pod_' . $visit->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $photoPath = $file->storeAs('proof_of_delivery', $filename, 'public');
            }

            $totalSoldQty = 0;
            $totalSoldAmount = 0;
            $totalRestockQty = 0;

            // 2. Proses Audit Stok Sisa Fisik Rak Kopi
            if ($request->has('audit_items') && is_array($request->audit_items)) {
                $consignment = Consignment::where('customer_id', $customer->id)
                    ->where('status', Consignment::STATUS_ACTIVE)
                    ->first();

                if ($consignment) {
                    $auditNumber = 'SRV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
                    
                    $audit = ConsignmentAudit::create([
                        'audit_number' => $auditNumber,
                        'consignment_id' => $consignment->id,
                        'customer_id' => $customer->id,
                        'user_id' => $user->id,
                        'audit_date' => Carbon::today()->toDateString(),
                        'total_sold_qty' => 0,
                        'total_sold_amount' => 0,
                        'total_restock_qty' => 0,
                        'payment_status' => ConsignmentAudit::PAYMENT_STATUS_UNPAID,
                        'payment_method' => ConsignmentAudit::PAYMENT_METHOD_CASH,
                        'notes' => 'Audit kunjungan sopir: ' . ($request->notes ?? '-'),
                    ]);

                    foreach ($request->audit_items as $itemId => $data) {
                        $consignmentItem = ConsignmentItem::find($itemId);
                        if (!$consignmentItem) {
                            continue;
                        }

                        $stockBefore = (int) $consignmentItem->current_quantity;
                        $physicalRemaining = isset($data['physical_remaining']) ? max(0, (int)$data['physical_remaining']) : $stockBefore;
                        $restockQty = isset($data['restock_qty']) ? max(0, (int)$data['restock_qty']) : 0;
                        
                        // Hitung jumlah laku
                        $qtySold = max(0, $stockBefore - $physicalRemaining);
                        $pricePerUnit = (float) $consignmentItem->selling_price;
                        $subtotal = $qtySold * $pricePerUnit;

                        // Stok akhir di toko = sisa fisik saat ini + restock baru
                        $stockAfter = $physicalRemaining + $restockQty;

                        // Simpan audit item
                        ConsignmentAuditItem::create([
                            'consignment_audit_id' => $audit->id,
                            'consignment_item_id' => $consignmentItem->id,
                            'product_variant_id' => $consignmentItem->product_variant_id,
                            'stock_before' => $stockBefore,
                            'physical_remaining' => $physicalRemaining,
                            'quantity_sold' => $qtySold,
                            'quantity_returned' => 0,
                            'restock_quantity' => $restockQty,
                            'stock_after' => $stockAfter,
                            'price_per_unit' => $pricePerUnit,
                            'subtotal' => $subtotal,
                            'notes' => $data['item_notes'] ?? null,
                        ]);

                        // Update consignment_item di database
                        $consignmentItem->update([
                            'current_quantity' => $stockAfter,
                            'sold_quantity' => $consignmentItem->sold_quantity + $qtySold,
                        ]);

                        $totalSoldQty += $qtySold;
                        $totalSoldAmount += $subtotal;
                        $totalRestockQty += $restockQty;
                    }

                    // Update header audit konsinyasi
                    $audit->update([
                        'total_sold_qty' => $totalSoldQty,
                        'total_sold_amount' => $totalSoldAmount,
                        'total_restock_qty' => $totalRestockQty,
                    ]);

                    // Update total_items_count di consignment
                    $consignment->update([
                        'total_items_count' => ConsignmentItem::where('consignment_id', $consignment->id)->sum('current_quantity')
                    ]);

                    $visit->consignment_audit_id = $audit->id;
                }
            }

            // 3. Selesaikan Status Kunjungan
            $visit->update([
                'check_out_at' => Carbon::now(),
                'status' => StoreVisit::STATUS_COMPLETED,
                'photo_proof' => $photoPath,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'notes' => $request->notes,
                'summary_sold_qty' => $totalSoldQty,
                'summary_sold_amount' => $totalSoldAmount,
                'summary_restock_qty' => $totalRestockQty,
            ]);

            DB::commit();

            return redirect()->route('driver.dashboard')->with('success', "Kunjungan di {$customer->business_name} selesai! Tercatat {$totalSoldQty} pack laku (Rp " . number_format($totalSoldAmount, 0, ',', '.') . ") dan restock {$totalRestockQty} pack.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyelesaikan kunjungan: ' . $e->getMessage());
        }
    }

    /**
     * Riwayat Kunjungan Driver
     */
    public function visitsHistory(Request $request)
    {
        $user = Auth::user();
        $date = $request->input('date', Carbon::today()->toDateString());

        $visits = StoreVisit::with(['customer', 'consignmentAudit.items.variant'])
            ->where('user_id', $user->id)
            ->whereDate('visit_date', $date)
            ->orderBy('check_in_at', 'desc')
            ->paginate(15);

        return view('driver.visits_history', compact('user', 'visits', 'date'));
    }

    /**
     * Cetak Lembar Stiker QR Code Toko (Untuk ditempel di rak pajangan)
     */
    public function printStoreQr(Customer $customer)
    {
        $customer->ensureQrToken();
        return view('driver.print_qr', compact('customer'));
    }

    /**
     * Rumus Haversine untuk menghitung jarak antara 2 koordinat (dalam meter)
     */
    private function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
