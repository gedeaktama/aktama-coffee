<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Customer::with('salesUser')->latest();

        // If logged in as Sales, only see assigned customers (or unassigned), unless owner/admin
        if ($user->isSales()) {
            $query->where(function ($q) use ($user) {
                $q->where('sales_user_id', $user->id)
                  ->orWhereNull('sales_user_id');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('pic_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('customer_type', $request->type);
        }

        $customers = $query->paginate(10)->withQueryString();
        $salesList = User::where('role', User::ROLE_SALES)->where('is_active', true)->get();

        return view('master.customers.index', compact('customers', 'salesList'));
    }

    public function create()
    {
        $salesList = User::where('role', User::ROLE_SALES)->where('is_active', true)->get();
        return view('master.customers.create', compact('salesList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:customers,code'],
            'customer_type' => ['required', 'in:kafe,toko,reseller,retail,konsinyasi'],
            'store_chain' => ['nullable', 'string', 'max:50'],
            'business_name' => ['required', 'string', 'max:255'],
            'pic_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'shipping_address' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'min_stock_alert' => ['nullable', 'integer', 'min:0'],
            'sales_user_id' => ['nullable', 'exists:users,id'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'payment_terms_days' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ], [
            'code.unique' => 'Kode pelanggan ini sudah terdaftar.',
            'code.required' => 'Kode pelanggan wajib diisi.',
            'business_name.required' => 'Nama usaha / entitas wajib diisi.',
            'pic_name.required' => 'Nama penanggung jawab (PIC) wajib diisi.',
            'phone.required' => 'Nomor telepon/WhatsApp wajib diisi.',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['payment_terms_days'] = $validated['payment_terms_days'] ?? 0;
        $validated['min_stock_alert'] = $validated['min_stock_alert'] ?? 5;
        $validated['is_active'] = true;

        // Sales constraint: sales users cannot self-approve credit limits > 0 without admin/owner
        if ($user->isSales() && $validated['credit_limit'] > 0) {
            return back()->withInput()->withErrors([
                'credit_limit' => 'Staf Sales tidak memiliki wewenang untuk menetapkan plafon kredit. Pengajuan plafon kredit harus disetujui Owner atau Admin.',
            ]);
        }

        // If Sales adds customer, automatically assign to themselves if not specified
        if ($user->isSales() && empty($validated['sales_user_id'])) {
            $validated['sales_user_id'] = $user->id;
        }

        $customer = Customer::create($validated);
        $customer->ensureQrToken();

        return redirect()->route('customers.index')->with('success', 'Pelanggan baru berhasil didaftarkan.');
    }

    public function show(Customer $customer)
    {
        $customer->load('salesUser');
        return view('master.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $salesList = User::where('role', User::ROLE_SALES)->where('is_active', true)->get();
        return view('master.customers.edit', compact('customer', 'salesList'));
    }

    public function update(Request $request, Customer $customer)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'customer_type' => ['required', 'in:kafe,toko,reseller,retail,konsinyasi'],
            'store_chain' => ['nullable', 'string', 'max:50'],
            'business_name' => ['required', 'string', 'max:255'],
            'pic_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'shipping_address' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'min_stock_alert' => ['nullable', 'integer', 'min:0'],
            'sales_user_id' => ['nullable', 'exists:users,id'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'payment_terms_days' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        // Sales constraint: cannot increase credit limit
        if ($user->isSales() && isset($validated['credit_limit']) && (float)$validated['credit_limit'] != (float)$customer->credit_limit) {
            return back()->withInput()->withErrors([
                'credit_limit' => 'Hanya Owner atau Admin yang berhak mengubah batas kredit pelanggan.',
            ]);
        }

        $customer->update($validated);
        $customer->ensureQrToken();

        return redirect()->route('customers.show', $customer)->with('success', "Data pelanggan {$customer->business_name} berhasil diperbarui.");
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);

        $status = $customer->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('customers.index')->with('success', "Pelanggan {$customer->business_name} berhasil {$status}.");
    }
}
