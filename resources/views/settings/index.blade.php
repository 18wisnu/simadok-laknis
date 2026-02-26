@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="px-2">
        <h2 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h2>
        <p class="text-sm text-gray-400">Konfigurasi API dan integrasi pihak ketiga.</p>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 text-emerald-600 p-4 rounded-2xl text-sm font-medium border border-emerald-100 mx-2">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        <!-- WhatsApp API Settings -->
        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <i class="fab fa-whatsapp text-9xl"></i>
            </div>
            
            <div class="relative">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                        <i class="fab fa-whatsapp text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">WhatsApp Gateway</h3>
                        <p class="text-xs text-gray-400">Hubungkan sistem dengan layanan pengiriman pesan WhatsApp.</p>
                    </div>
                </div>

                <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase ml-1">API Token</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <i class="fas fa-key text-xs"></i>
                                </span>
                                <input type="password" name="whatsapp_token" value="{{ $settings['whatsapp_token'] }}" 
                                    class="w-full pl-10 pr-4 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all"
                                    placeholder="Masukkan Token API...">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase ml-1">Nomor Bisnis (Sender)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <i class="fas fa-phone text-xs"></i>
                                </span>
                                <input type="text" name="whatsapp_business_number" value="{{ $settings['whatsapp_business_number'] }}" 
                                    class="w-full pl-10 pr-4 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all"
                                    placeholder="628xxxxxxxxxx">
                            </div>
                            <p class="text-[10px] text-gray-400 ml-1 italic">Format: 6281234567890</p>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- System Information -->
        <div class="bg-indigo-900 p-8 rounded-[2rem] text-white shadow-xl shadow-indigo-100 overflow-hidden relative">
            <div class="absolute bottom-0 right-0 p-8 opacity-10">
                <i class="fas fa-server text-8xl"></i>
            </div>
            
            <div class="relative">
                <h3 class="text-lg font-bold mb-4">Informasi Sistem</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="space-y-1">
                        <p class="text-[10px] uppercase font-bold opacity-60">Versi App</p>
                        <p class="font-bold">v2.1.0-stable</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] uppercase font-bold opacity-60">PHP Version</p>
                        <p class="font-bold">{{ PHP_VERSION }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] uppercase font-bold opacity-60">Environment</p>
                        <p class="font-bold px-2 py-0.5 bg-emerald-500 rounded-lg text-[10px] inline-block uppercase tracking-wider">Production</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] uppercase font-bold opacity-60">Database</p>
                        <p class="font-bold">MySQL 8.0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
