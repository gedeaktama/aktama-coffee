<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'customer_type' => ['nullable', 'string', 'in:retail,toko,kafe,konsinyasi'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'Nama lengkap penanggung jawab wajib diisi.',
            'email.required' => 'Email aktif wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'phone.required' => 'Nomor WhatsApp / telepon wajib diisi untuk konfirmasi pengiriman.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
        ]);

        $customerType = $validated['customer_type'] ?? 'retail';
        $businessName = !empty($validated['business_name']) ? $validated['business_name'] : $validated['name'];

        $user = DB::transaction(function () use ($validated, $customerType, $businessName) {
            // 1. Buat User Account
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'role' => User::ROLE_CUSTOMER,
                'is_active' => true,
                'password' => Hash::make($validated['password']),
            ]);

            // 2. Generate kode pelanggan unik (contoh: CUST-TK-2610-012, CUST-KF-2610-015, CUST-RT-2610-020)
            $typePrefix = match ($customerType) {
                Customer::TYPE_TOKO => 'TK',
                Customer::TYPE_KAFE => 'KF',
                default => 'RT',
            };
            $custCode = 'CUST-' . $typePrefix . '-' . date('ym') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

            // 3. Buat Customer Profile terhubung ke User
            Customer::create([
                'code' => $custCode,
                'customer_type' => $customerType,
                'business_name' => $businessName,
                'pic_name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'address' => $validated['address'] ?? null,
                'shipping_address' => $validated['address'] ?? null,
                'sales_user_id' => null,
                'user_id' => $user->id,
                'credit_limit' => 0,
                'payment_terms_days' => 0,
                'notes' => "Pendaftaran Mandiri Web (" . strtoupper($customerType) . ")",
                'is_active' => true,
            ]);

            return $user;
        });

        Auth::login($user);

        $welcomeMessage = match ($customerType) {
            Customer::TYPE_TOKO => "Pendaftaran berhasil! Akun Anda aktif sebagai Mitra Toko. Anda otomatis mendapatkan penawaran Harga Grosir Toko pada seluruh produk kopi.",
            Customer::TYPE_KAFE => "Pendaftaran berhasil! Akun Anda aktif sebagai Mitra Kafe. Anda otomatis mendapatkan penawaran Harga Khusus Kafe / Kedai Kopi.",
            default => "Pendaftaran akun berhasil! Selamat berbelanja kopi nusantara di Kopi Distro.",
        };

        return redirect()->intended('/')->with('success', $welcomeMessage);
    }
}
