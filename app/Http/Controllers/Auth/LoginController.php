<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isDriver()) {
                return redirect()->route('driver.dashboard');
            }

            return Auth::user()->canAccessDashboard()
                ? redirect()->route('dashboard')
                : redirect()->route('home');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda dinonaktifkan oleh Administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            if ($user->isDriver()) {
                return redirect()->intended(route('driver.dashboard'))
                    ->with('success', 'Selamat bertugas, ' . $user->name . '! Silakan scan toko untuk memulai.');
            }

            if ($user->canAccessDashboard()) {
                return redirect()->intended(route('dashboard'))
                    ->with('success', 'Selamat datang kembali, ' . $user->name . ' (' . ucfirst($user->role) . ')');
            }

            return redirect()->intended(route('home'))
                ->with('success', 'Selamat datang kembali, ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
