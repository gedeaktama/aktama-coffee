<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Order;
use App\Services\DeliveryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    protected DeliveryService $deliveryService;

    public function __construct(DeliveryService $deliveryService)
    {
        $this->deliveryService = $deliveryService;
    }

    /**
     * Daftar Surat Jalan & Monitoring Logistik
     */
    public function index(Request $request)
    {
        $query = DeliveryOrder::with(['order', 'customer', 'user', 'items'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('courier_type')) {
            $query->where('courier_type', $request->courier_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('business_name', 'like', "%{$search}%");
                  });
            });
        }

        $deliveries = $query->paginate(15)->withQueryString();

        $metrics = [
            'total_deliveries' => DeliveryOrder::count(),
            'processing_count' => DeliveryOrder::where('status', DeliveryOrder::STATUS_PROCESSING)->count(),
            'shipped_count' => DeliveryOrder::where('status', DeliveryOrder::STATUS_SHIPPED)->count(),
            'delivered_count' => DeliveryOrder::where('status', DeliveryOrder::STATUS_DELIVERED)->count(),
        ];

        return view('logistics.deliveries.index', compact('deliveries', 'metrics'));
    }

    /**
     * Form Terbitkan Surat Jalan Baru dari Pesanan Penjualan
     */
    public function create(Order $order)
    {
        if ($order->isPending()) {
            return redirect()->route('orders.show', $order)->with('error', 'Pesanan harus dikonfirmasi terlebih dahulu sebelum menerbitkan Surat Jalan.');
        }

        if ($order->isCancelled()) {
            return redirect()->route('orders.show', $order)->with('error', 'Tidak dapat menerbitkan Surat Jalan untuk pesanan yang telah dibatalkan.');
        }

        $order->load(['customer', 'items.variant.product']);

        // Default Auto Number preview
        $prefix = 'SJ-' . date('Ym') . '-';
        $latest = DeliveryOrder::where('delivery_number', 'like', "{$prefix}%")->latest('id')->first();
        $nextNumber = 1;
        if ($latest) {
            $nextNumber = (int)substr($latest->delivery_number, -4) + 1;
        }
        $autoDeliveryNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('logistics.deliveries.create', compact('order', 'autoDeliveryNumber'));
    }

    /**
     * Simpan Surat Jalan Baru
     */
    public function store(Request $request, Order $order)
    {
        $validated = $request->validate([
            'delivery_date' => ['required', 'date'],
            'courier_type' => ['required', 'in:internal,expedition'],
            'courier_name' => ['nullable', 'string', 'max:100'],
            'driver_name' => ['nullable', 'string', 'max:100'],
            'driver_phone' => ['nullable', 'string', 'max:50'],
            'vehicle_plate_number' => ['nullable', 'string', 'max:30'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'shipping_address' => ['required', 'string'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'recipient_phone' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:processing,shipped'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $deliveryOrder = $this->deliveryService->createDeliveryOrder($order, $validated, Auth::id());

            return redirect()->route('delivery.show', $deliveryOrder)
                ->with('success', "Surat Jalan {$deliveryOrder->delivery_number} berhasil diterbitkan.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menerbitkan Surat Jalan: ' . $e->getMessage());
        }
    }

    /**
     * Detail Surat Jalan & Status Pelacakan
     */
    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['order.customer', 'customer', 'user', 'items.variant.product']);
        return view('logistics.deliveries.show', compact('deliveryOrder'));
    }

    /**
     * Ubah status menjadi Shipped (Barang dibawa pengemudi / diserahkan kurir)
     */
    public function markShipped(Request $request, DeliveryOrder $deliveryOrder)
    {
        $validated = $request->validate([
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'driver_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $this->deliveryService->markAsShipped(
                $deliveryOrder,
                $validated['tracking_number'] ?? null,
                $validated['driver_name'] ?? null,
                $validated['notes'] ?? null
            );

            return back()->with('success', "Status pengiriman {$deliveryOrder->delivery_number} diperbarui menjadi 'Dalam Pengiriman' (Shipped).");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Ubah status menjadi Delivered (Barang selesai diterima fisik di lokasi tujuan)
     */
    public function markDelivered(Request $request, DeliveryOrder $deliveryOrder)
    {
        $validated = $request->validate([
            'received_by' => ['required', 'string', 'max:100'],
            'received_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $this->deliveryService->markAsDelivered(
                $deliveryOrder,
                $validated['received_by'],
                $validated['received_at'] ?? now()->toDateTimeString(),
                $validated['notes'] ?? null
            );

            return back()->with('success', "Status pengiriman {$deliveryOrder->delivery_number} berhasil dikonfirmasi Selesai Diterima (Delivered).");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Fisik Dokumen Surat Jalan Resmi
     */
    public function print(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['order.customer', 'customer', 'user', 'items.variant.product']);
        return view('logistics.deliveries.print', compact('deliveryOrder'));
    }
}
