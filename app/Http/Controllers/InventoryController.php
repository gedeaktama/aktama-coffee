<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = ProductVariant::with('product.category')->where('is_active', true)->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('variant_name', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('filter')) {
            if ($request->filter === 'low_stock') {
                $query->whereColumn('current_stock', '<=', 'minimum_stock');
            } elseif ($request->filter === 'out_of_stock') {
                $query->where('current_stock', '<=', 0);
            }
        }

        $variants = $query->paginate(15)->withQueryString();

        $metrics = [
            'total_items' => ProductVariant::where('is_active', true)->sum('current_stock'),
            'low_stock_count' => ProductVariant::where('is_active', true)->whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'out_of_stock_count' => ProductVariant::where('is_active', true)->where('current_stock', '<=', 0)->count(),
        ];

        return view('inventory.stocks.index', compact('variants', 'metrics'));
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with(['variant.product', 'user'])->latest();

        if ($request->filled('variant_id')) {
            $query->where('product_variant_id', $request->variant_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->paginate(20)->withQueryString();
        $variants = ProductVariant::with('product')->where('is_active', true)->get();

        return view('inventory.stocks.movements', compact('movements', 'variants'));
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'actual_stock' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'reason.required' => 'Alasan penyesuaian stok (stock opname) wajib diisi untuk keperluan audit.',
            'reason.min' => 'Alasan penyesuaian minimal 5 karakter.',
            'actual_stock.min' => 'Jumlah stok fisik tidak boleh negatif.',
        ]);

        try {
            $movement = $this->inventoryService->adjustStock(
                variant: $validated['product_variant_id'],
                newActualStock: (int)$validated['actual_stock'],
                reason: $validated['reason'],
                userId: Auth::id()
            );

            return redirect()->route('inventory.index')->with('success', "Penyesuaian stok SKU {$movement->variant->sku} berhasil disimpan (Stok baru: {$movement->stock_after}).");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menyesuaikan stok: ' . $e->getMessage());
        }
    }
}
