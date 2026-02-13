@extends('layouts.app')

@section('title', 'Laporan Perbaikan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between px-2">
        <h2 class="text-2xl font-bold text-gray-800">Perbaikan Alat</h2>
        <div class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-xs font-bold uppercase">
            {{ $repairs->count() }} Laporan
        </div>
    </div>

    <div class="space-y-4">
        @forelse($repairs as $repair)
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600">
                    <i class="fas fa-wrench"></i>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $repair->status == 'completed' ? 'bg-emerald-50 text-emerald-600' : ($repair->status == 'in_progress' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600') }}">
                    @if($repair->status == 'completed') Selesai
                    @elseif($repair->status == 'in_progress') Proses
                    @else Menunggu @endif
                </span>
            </div>
            
            <h3 class="font-bold text-gray-800 text-lg">{{ $repair->equipment->name }}</h3>
            <p class="text-xs text-gray-400">ID: {{ $repair->equipment->qr_code_identifier }}</p>
            <p class="text-sm text-gray-600 mt-4 leading-relaxed">{{ $repair->issue_description }}</p>
            
            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                <div class="text-xs text-gray-400">
                    <i class="fas fa-calendar-alt mr-1"></i> {{ $repair->created_at->format('d M Y') }}
                </div>
                @if($repair->cost)
                <div class="text-xs font-bold text-gray-700">
                    Rp {{ number_format($repair->cost, 0, ',', '.') }}
                </div>
                @endif
            </div>

            @if($repair->service_center)
            <div class="mt-3 text-[10px] text-gray-400 italic">
                <i class="fas fa-hospital mr-1"></i> Servis di: {{ $repair->service_center }}
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-20 text-gray-400">
            <i class="fas fa-tools mb-4 text-5xl opacity-10"></i>
            <p class="text-sm">Tidak ada alat dalam perbaikan.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
