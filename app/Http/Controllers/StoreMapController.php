<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\StoreVisit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StoreMapController extends Controller
{
    /**
     * Peta Sebaran Toko Interaktif (Store Locator Map)
     */
    public function index(Request $request)
    {
        $today = Carbon::today();
        $selectedChain = $request->input('chain', 'all');
        $selectedStatus = $request->input('status', 'all');

        $query = Customer::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($selectedChain !== 'all') {
            $query->where('store_chain', $selectedChain);
        }

        $stores = $query->orderBy('business_name')->get()->map(function ($store) use ($today) {
            $stock = $store->getActiveConsignmentStock();
            $isOverdue = $store->is_overdue;
            $overdueAmount = $store->overdue_amount;
            $hasVisited = StoreVisit::where('customer_id', $store->id)
                ->where('visit_date', $today)
                ->where('status', StoreVisit::STATUS_COMPLETED)
                ->exists();

            $isLowStock = $stock <= $store->min_stock_alert;

            // Tentukan status marker & warna
            $pinColor = 'green'; // Default aman
            $statusLabel = 'Stok Aman';

            if ($isOverdue) {
                $pinColor = 'red';
                $statusLabel = 'Menunggak Tagihan';
            } elseif ($isLowStock) {
                $pinColor = 'orange';
                $statusLabel = 'Perlu Restock Segera';
            } elseif ($hasVisited) {
                $pinColor = 'blue';
                $statusLabel = 'Sudah Dikunjungi Hari Ini';
            }

            return [
                'id' => $store->id,
                'name' => $store->business_name,
                'code' => $store->code,
                'chain' => strtoupper($store->store_chain ?? 'Retail'),
                'address' => $store->shipping_address ?? $store->address,
                'pic' => $store->pic_name ?? '-',
                'phone' => $store->phone ?? '-',
                'lat' => (float) $store->latitude,
                'lng' => (float) $store->longitude,
                'stock' => $stock,
                'min_stock' => $store->min_stock_alert,
                'is_low_stock' => $isLowStock,
                'is_overdue' => $isOverdue,
                'overdue_amount' => $overdueAmount,
                'formatted_overdue' => 'Rp ' . number_format($overdueAmount, 0, ',', '.'),
                'visited_today' => $hasVisited,
                'pin_color' => $pinColor,
                'status_label' => $statusLabel,
                'google_maps_url' => $store->google_maps_url,
                'print_qr_url' => route('driver.print_qr', $store->id),
            ];
        });

        // Filter status di PHP collection jika filter status dipilih
        if ($selectedStatus === 'overdue') {
            $stores = $stores->where('is_overdue', true)->values();
        } elseif ($selectedStatus === 'low_stock') {
            $stores = $stores->where('is_low_stock', true)->values();
        } elseif ($selectedStatus === 'not_visited') {
            $stores = $stores->where('visited_today', false)->values();
        } elseif ($selectedStatus === 'visited') {
            $stores = $stores->where('visited_today', true)->values();
        }

        // Hitung ringkasan statistik toko
        $summary = [
            'total_mapped' => Customer::whereNotNull('latitude')->whereNotNull('longitude')->count(),
            'alfamart_count' => Customer::where('store_chain', Customer::STORE_CHAIN_ALFAMART)->count(),
            'indomaret_count' => Customer::where('store_chain', Customer::STORE_CHAIN_INDOMARET)->count(),
            'overdue_count' => Customer::all()->filter->is_overdue->count(),
            'visited_today_count' => StoreVisit::where('visit_date', $today)->where('status', StoreVisit::STATUS_COMPLETED)->distinct('customer_id')->count(),
        ];

        return view('maps.stores', compact('stores', 'selectedChain', 'selectedStatus', 'summary'));
    }

    /**
     * Peta Jejak Kunjungan Karyawan / Sopir Hari Ini (Driver Fleet Tracking)
     */
    public function driverTracking(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $driverId = $request->input('driver_id', 'all');

        $drivers = User::whereIn('role', [User::ROLE_DRIVER, User::ROLE_SALES])->get();

        $query = StoreVisit::with(['customer', 'user'])
            ->whereDate('visit_date', $date)
            ->whereNotNull('check_in_lat')
            ->whereNotNull('check_in_lng')
            ->orderBy('check_in_at', 'asc');

        if ($driverId !== 'all') {
            $query->where('user_id', $driverId);
        }

        $visits = $query->get()->map(function ($v, $index) {
            return [
                'index' => $index + 1,
                'visit_number' => $v->visit_number,
                'driver_name' => $v->user->name ?? 'Driver',
                'driver_email' => $v->user->email ?? '',
                'store_name' => $v->customer->business_name ?? 'Toko',
                'store_chain' => strtoupper($v->customer->store_chain ?? 'Retail'),
                'address' => $v->customer->shipping_address ?? $v->customer->address,
                'check_in_time' => $v->check_in_at->format('H:i'),
                'check_out_time' => $v->check_out_at ? $v->check_out_at->format('H:i') : 'Sedang di Lokasi',
                'duration' => $v->duration_minutes ? $v->duration_minutes . ' menit' : '-',
                'lat' => (float) $v->check_in_lat,
                'lng' => (float) $v->check_in_lng,
                'store_lat' => (float) ($v->customer->latitude ?? $v->check_in_lat),
                'store_lng' => (float) ($v->customer->longitude ?? $v->check_in_lng),
                'distance_meters' => $v->distance_meters,
                'is_geofence_valid' => $v->is_geofence_valid,
                'sold_qty' => $v->summary_sold_qty,
                'sold_amount' => $v->summary_sold_amount,
                'formatted_sold_amount' => 'Rp ' . number_format($v->summary_sold_amount, 0, ',', '.'),
                'restock_qty' => $v->summary_restock_qty,
                'notes' => $v->notes ?? '-',
                'status' => $v->status,
                'status_label' => $v->status_label,
            ];
        });

        // Total ringkasan kunjungan pada tanggal ini
        $stats = [
            'total_visits' => $visits->count(),
            'total_sold_qty' => $visits->sum('sold_qty'),
            'total_sold_amount' => $visits->sum('sold_amount'),
            'total_restock_qty' => $visits->sum('restock_qty'),
        ];

        return view('maps.driver_tracking', compact('visits', 'drivers', 'date', 'driverId', 'stats'));
    }
}
