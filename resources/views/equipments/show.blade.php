@extends('layouts.app')

@section('title', $equipment->name)

@section('content')
<div class="space-y-6">
    <!-- Equipment Card -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-start justify-between mb-4">
            <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                <i class="fas fa-microchip text-3xl"></i>
            </div>
            <div class="flex items-center gap-2">
                @if(auth()->user()->isAdmin() && !in_array($equipment->status, ['borrowed', 'in_service']))
                <a href="{{ route('repairs.index') }}" class="bg-orange-50 text-orange-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase hover:bg-orange-600 hover:text-white transition-all">
                    <i class="fas fa-wrench mr-1"></i> Lapor Kerusakan
                </a>
                @endif
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase 
                    @if($equipment->status == 'available') bg-emerald-50 text-emerald-600 
                    @elseif($equipment->status == 'borrowed') bg-blue-50 text-blue-600
                    @elseif($equipment->status == 'lost') bg-red-50 text-red-600
                    @else bg-orange-50 text-orange-600 @endif">
                    @if($equipment->status == 'available') Tersedia 
                    @elseif($equipment->status == 'borrowed') Dipinjam
                    @elseif($equipment->status == 'lost') Hilang
                    @else Rusak @endif
                </span>
            </div>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">{{ $equipment->name }}</h2>
        <p class="text-sm text-gray-400 mt-1">SN: {{ $equipment->serial_number }} • QR ID: {{ $equipment->qr_code_identifier }}</p>
        <p class="text-gray-600 mt-4 text-sm leading-relaxed">{{ $equipment->description ?? 'Tidak ada deskripsi untuk alat ini.' }}</p>
    </div>

    <!-- Accessories Section -->
    <div class="space-y-3">
        <h3 class="text-lg font-bold text-gray-800 px-2">Kelengkapan Satu Set</h3>
        <form action="{{ route('borrowings.store') }}" method="POST" id="borrowForm">
            @csrf
            <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
            
            <div class="grid grid-cols-1 gap-3">
                @forelse($equipment->accessories as $accessory)
                <label class="bg-white p-4 rounded-2xl border border-gray-100 flex items-center justify-between cursor-pointer active:scale-[0.98] transition-all">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <input type="checkbox" name="accessories[]" value="{{ $accessory->name }}" checked class="peer h-5 w-5 cursor-pointer appearance-none rounded border border-gray-300 checked:bg-indigo-600 checked:border-indigo-600 transition-all">
                            <i class="fas fa-check absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[10px] text-white opacity-0 peer-checked:opacity-100 pointer-events-none"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ $accessory->name }}</span>
                    </div>
                    @if($accessory->is_removable)
                    <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-bold uppercase">Bisa Dilepas</span>
                    @endif
                </label>
                @empty
                <p class="text-center py-4 text-gray-400 text-sm italic">Tidak ada daftar kelengkapan.</p>
                @endforelse
            </div>

            <!-- Borrow Form / Button -->
            @if($equipment->status == 'available')
            <div class="mt-8 space-y-4">
                <div class="px-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Rencana Pengembalian</label>
                    <input type="datetime-local" name="expected_return_at" value="{{ now()->addHours(24)->format('Y-m-d\TH:i') }}" class="w-full p-4 rounded-2xl border border-gray-100 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                </div>

                <div class="bg-indigo-600 p-6 rounded-3xl text-white shadow-xl shadow-indigo-200">
                    <h3 class="text-lg font-bold mb-2">Konfirmasi Peminjaman</h3>
                    <p class="text-sm opacity-80 mb-6">Dengan menekan tombol di bawah, Anda bertanggung jawab penuh atas alat yang dipilih.</p>
                    <button type="submit" class="w-full bg-white text-indigo-600 py-4 rounded-2xl font-bold hover:bg-gray-50 transition-colors shadow-lg">
                        Pinjam Sekarang
                    </button>
                </div>
            </div>
            @else
            <div class="mt-8 bg-gray-100 p-6 rounded-3xl text-gray-500 text-center">
                <i class="fas fa-lock mb-2 text-2xl opacity-30"></i>
                <h3 class="font-bold">Alat Sedang Tidak Tersedia</h3>
                <p class="text-xs">Alat ini sedang dipinjam atau dalam proses perbaikan.</p>
            </div>
            @endif
        </form>
    </div>

    <!-- Custody Timeline (Rantai Penjagaan) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between px-2">
            <h3 class="text-lg font-bold text-gray-800">Rantai Penjagaan (Custody)</h3>
            <span class="text-[10px] text-gray-400 font-bold uppercase">Melacak Tanggung Jawab</span>
        </div>
        
        <div class="space-y-6 relative before:absolute before:left-5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
            @php
                $timeline = collect();
                
                // Add Borrowings to timeline
                foreach($borrowingHistory as $b) {
                    $timeline->push([
                        'type' => 'borrowing',
                        'date' => $b->borrowed_at,
                        'user' => $b->user,
                        'title' => 'Peminjaman Alat',
                        'status' => $b->status == 'returned' ? 'Selesai' : 'Sedang Digunakan',
                        'color' => $b->status == 'returned' ? 'text-emerald-500' : 'text-blue-500',
                        'icon' => 'fa-hand-holding'
                    ]);
                }
                
                // Add Audit Logs to timeline
                foreach($equipment->auditLogs as $l) {
                    $timeline->push([
                        'type' => 'audit',
                        'date' => $l->created_at,
                        'user' => $l->user,
                        'title' => 'Perubahan Data: ' . ucfirst($l->action),
                        'desc' => $l->description,
                        'color' => 'text-gray-400',
                        'icon' => 'fa-cog'
                    ]);
                }
                
                $sortedTimeline = $timeline->sortByDesc('date');
            @endphp

            @foreach($sortedTimeline as $item)
            <div class="relative pl-12">
                <div class="absolute left-0 w-10 h-10 bg-white border-2 border-gray-50 rounded-full flex items-center justify-center z-10">
                    <i class="fas {{ $item['icon'] }} text-xs {{ $item['color'] }}"></i>
                </div>
                <div class="bg-gray-50/50 p-4 rounded-2xl border border-transparent hover:border-indigo-100 transition-colors">
                    <div class="flex items-start justify-between mb-1">
                        <h4 class="text-xs font-bold text-gray-700">{{ $item['title'] }}</h4>
                        <span class="text-[9px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse($item['date'])->format('d M Y, H:i') }}</span>
                    </div>
                    
                    @if($item['type'] == 'borrowing')
                        <div class="flex items-center gap-2 mt-2">
                            <img src="{{ $item['user']->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($item['user']->name) }}" class="w-5 h-5 rounded-full">
                            <span class="text-[10px] font-bold text-gray-600">{{ $item['user']->name }}</span>
                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-white border border-gray-100 {{ $item['color'] }} font-bold uppercase">{{ $item['status'] }}</span>
                        </div>
                    @else
                        <p class="text-[10px] text-gray-500 mt-1 leading-relaxed">{{ $item['desc'] }}</p>
                        <div class="flex items-center gap-1.5 mt-2 opacity-60">
                            <i class="fas fa-user-shield text-[9px]"></i>
                            <span class="text-[9px] font-bold text-gray-400">Admin: {{ $item['user']->name ?? 'System' }}</span>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Maintenance History -->
    <div class="space-y-4">
        <div class="flex items-center justify-between px-2">
            <h3 class="text-lg font-bold text-gray-800">Riwayat Maintenance</h3>
            <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $equipment->repairs->count() }} Data</span>
        </div>
        <div class="space-y-3">
            @forelse($equipment->repairs as $repair)
            <div class="bg-white/50 p-4 rounded-2xl border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg
                        @if($repair->status == 'completed') bg-emerald-50 text-emerald-600
                        @elseif($repair->status == 'in_service') bg-blue-50 text-blue-600
                        @else bg-orange-50 text-orange-600 @endif uppercase">
                        {{ $repair->status }}
                    </span>
                    <span class="text-[10px] text-gray-400">{{ $repair->created_at->format('d M Y') }}</span>
                </div>
                <p class="text-xs text-gray-700 font-medium">{{ $repair->issue_description }}</p>
                <div class="mt-2 flex items-center justify-between text-[10px] text-gray-400">
                    <span>Vendor: {{ $repair->service_center ?? '-' }}</span>
                    <span class="font-bold text-gray-600">Rp {{ number_format($repair->cost, 0, ',', '.') }}</span>
                </div>
            </div>
            @empty
            <p class="text-center py-4 text-gray-400 text-sm italic">Belum ada riwayat perbaikan.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
