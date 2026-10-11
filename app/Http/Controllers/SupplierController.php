<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('pic_name', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->paginate(10)->withQueryString();

        return view('master.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:suppliers,code'],
            'name' => ['required', 'string', 'max:255'],
            'pic_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string'],
        ], [
            'code.unique' => 'Kode supplier ini sudah terdaftar.',
            'code.required' => 'Kode supplier wajib diisi.',
            'name.required' => 'Nama perusahaan supplier wajib diisi.',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = true;

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Data pemasok berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'pic_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', "Informasi supplier {$supplier->name} berhasil diperbarui.");
    }

    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update(['is_active' => !$supplier->is_active]);

        $status = $supplier->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('suppliers.index')->with('success', "Supplier {$supplier->name} berhasil {$status}.");
    }
}
