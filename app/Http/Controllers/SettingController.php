<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Tampilan Halaman Pengaturan Sistem & Mode Pengembang
     */
    public function index()
    {
        $isDevMode = AppSetting::isDevMode();

        return view('settings.index', compact('isDevMode'));
    }

    /**
     * Toggle On / Off Mode Pengembang
     */
    public function toggleDevMode(Request $request)
    {
        $current = AppSetting::isDevMode();

        // Jika request mengirim parameter spesifik 'state', gunakan itu. Jika tidak, balikkan nilai sekarang.
        if ($request->has('state')) {
            $newState = filter_var($request->input('state'), FILTER_VALIDATE_BOOLEAN);
        } else {
            $newState = !$current;
        }

        AppSetting::setDevMode($newState);

        $statusText = $newState ? 'DIAKTIFKAN (Semua pembelian produk dinonaktifkan)' : 'DINONAKTIFKAN (Toko kembali buka normal)';
        $message = "Mode Pengembang berhasil {$statusText}.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'is_dev_mode' => $newState,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
