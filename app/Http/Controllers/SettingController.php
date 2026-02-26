<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $settings = [
            'whatsapp_token' => Setting::get('whatsapp_token'),
            'whatsapp_business_number' => Setting::get('whatsapp_business_number'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return redirect()->back()->with('error', 'Tabel sistem belum siap. Silakan jalankan /fix-db terlebih dahulu.');
        }

        $validated = $request->validate([
            'whatsapp_token' => 'nullable|string',
            'whatsapp_business_number' => 'nullable|string|max:20',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
