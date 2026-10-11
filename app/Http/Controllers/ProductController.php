<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhereHas('variants', function ($vq) use ($search) {
                      $vq->where('sku', 'like', "%{$search}%")
                         ->orWhere('variant_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('coffee_type')) {
            $query->where('coffee_type', $request->coffee_type);
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return view('master.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('master.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'coffee_type' => ['required', 'string'],
            'form' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            // First variant
            'variant_name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:product_variants,sku'],
            'package_weight_grams' => ['required', 'integer', 'min:1'],
            'grind_form' => ['required', 'string'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ], [
            'sku.unique' => 'Kode SKU ini sudah digunakan pada varian lain.',
            'category_id.required' => 'Kategori kopi wajib dipilih.',
            'name.required' => 'Nama produk wajib diisi.',
            'selling_price.required' => 'Harga jual standar per SKU wajib diisi.',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']) . '-' . Str::random(4),
                'brand' => $validated['brand'],
                'coffee_type' => $validated['coffee_type'],
                'form' => $validated['form'],
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            ProductVariant::create([
                'product_id' => $product->id,
                'sku' => strtoupper(trim($validated['sku'])),
                'variant_name' => $validated['variant_name'],
                'package_weight_grams' => $validated['package_weight_grams'],
                'grind_form' => $validated['grind_form'],
                'cost_price' => $validated['cost_price'],
                'selling_price' => $validated['selling_price'],
                'current_stock' => $validated['current_stock'],
                'minimum_stock' => $validated['minimum_stock'],
                'is_active' => true,
            ]);
        });

        return redirect()->route('products.index')->with('success', 'Produk dan varian awal berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'variants']);
        return view('master.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('variants');
        $categories = Category::where('is_active', true)->get();
        return view('master.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'coffee_type' => ['required', 'string'],
            'form' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $product->update($validated);

        return redirect()->route('products.show', $product)->with('success', 'Informasi produk berhasil diperbarui.');
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('products.index')->with('success', "Status produk {$product->name} berhasil {$status}.");
    }

    public function storeVariant(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:100', 'unique:product_variants,sku'],
            'variant_name' => ['required', 'string', 'max:255'],
            'package_weight_grams' => ['required', 'integer', 'min:1'],
            'grind_form' => ['required', 'string'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ]);

        $product->variants()->create([
            'sku' => strtoupper(trim($validated['sku'])),
            'variant_name' => $validated['variant_name'],
            'package_weight_grams' => $validated['package_weight_grams'],
            'grind_form' => $validated['grind_form'],
            'cost_price' => $validated['cost_price'],
            'selling_price' => $validated['selling_price'],
            'current_stock' => $validated['current_stock'],
            'minimum_stock' => $validated['minimum_stock'],
            'is_active' => true,
        ]);

        return redirect()->route('products.show', $product)->with('success', 'Varian baru berhasil ditambahkan pada produk ini.');
    }

    public function updateVariant(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'variant_name' => ['required', 'string', 'max:255'],
            'package_weight_grams' => ['required', 'integer', 'min:1'],
            'grind_form' => ['required', 'string'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $variant->update($validated);

        return redirect()->route('products.show', $variant->product_id)->with('success', "Varian SKU {$variant->sku} berhasil diperbarui.");
    }
}
