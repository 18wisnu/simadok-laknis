@extends('layouts.app')

@section('title', 'Manajemen Perbaikan')

@section('content')
<div class="space-y-6 pb-20">
    <!-- Header -->
    <div class="flex items-center justify-between px-2">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Perbaikan Alat</h2>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Maintenance & Service</p>
        </div>
        <div class="flex items-center gap-2">
             <div class="bg-orange-50 text-orange-600 px-4 py-2 rounded-2xl text-xs font-bold shadow-sm">
                {{ $activeRepairs->count() }} Aktif
            </div>
        </div>
    </div>

    <!-- Active Repairs Section -->
    <div class="space-y-4">
        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest px-2">Sedang Diperbaiki</h3>
        
        @if($activeRepairs->count() > 0)
            @foreach($activeRepairs as $repair)
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden group">
                <!-- Status Badge Top Right -->
                <div class="absolute top-0 right-0">
                    <span class="px-4 py-1.5 rounded-bl-2xl text-[10px] font-bold uppercase italic shadow-sm
                        @if($repair->status == 'in_service') bg-blue-500 text-white
                        @elseif($repair->status == 'returning') bg-indigo-500 text-white
                        @else bg-orange-500 text-white @endif">
                        @if($repair->status == 'in_service') Di Service Center
                        @elseif($repair->status == 'returning') Proses Kembali
                        @else Menunggu Kurir @endif
                    </span>
                </div>

                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600">
                        <i class="fas fa-tools text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">{{ $repair->equipment->name }}</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">SN: {{ $repair->equipment->serial_number }}</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-4 mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase mb-1 tracking-tight">Keluhan / Kerusakan:</p>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $repair->issue_description }}</p>
                </div>

                @if($repair->service_center)
                <div class="flex items-center gap-2 mb-4 text-[10px] text-gray-500 font-medium">
                    <i class="fas fa-hospital text-orange-400"></i>
                    <span>Lokasi: <span class="text-gray-800">{{ $repair->service_center }}</span></span>
                </div>
                @endif

                <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                    <div class="text-[10px] text-gray-400">
                        <i class="fas fa-clock mr-1"></i> Masuk: {{ $repair->created_at->format('d M Y') }}
                    </div>
                    @if(auth()->user()->isAdmin())
                    <button onclick='openManageRepairModal({!! json_encode($repair) !!})' 
                            class="bg-indigo-50 text-indigo-600 px-5 py-2 rounded-xl text-xs font-bold hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                        Kelola Status
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-10 bg-white rounded-3xl border border-dashed border-gray-200 text-gray-400">
                <i class="fas fa-check-circle mb-2 text-3xl opacity-20"></i>
                <p class="text-sm">Tidak ada alat yang sedang diperbaiki.</p>
            </div>
        @endif
    </div>

    <!-- History Section -->
    <div class="space-y-4 mt-8">
        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest px-2">Riwayat Selesai</h3>
        
        @if($completedRepairs->count() > 0)
            <div class="space-y-3">
                @foreach($completedRepairs as $repair)
                <div class="bg-white p-4 rounded-3xl border border-gray-50 flex items-center justify-between opacity-80 hover:opacity-100 transition-opacity">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-emerald-500">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">{{ $repair->equipment->name }}</h4>
                            <p class="text-[10px] text-gray-400">{{ $repair->updated_at->format('d M Y') }} • Rp {{ number_format($repair->cost, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">{{ $repair->service_center ?? 'Vendor Unknown' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
             <div class="text-center py-6 text-gray-400 italic">
                <p class="text-[10px]">Belum ada data riwayat selesai.</p>
            </div>
        @endif
    </div>

    <!-- Floating Action Button for reporting damage -->
    @if(auth()->user()->isAdmin())
    <button onclick="openAddRepairModal()" 
            class="fixed bottom-24 right-6 w-14 h-14 bg-orange-500 text-white rounded-full shadow-xl shadow-orange-100 flex items-center justify-center text-2xl active:scale-95 transition-all z-40">
        <i class="fas fa-plus"></i>
    </button>
    @endif
</div>

<!-- Modal: Add New Repair -->
<div id="addRepairModal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-2 text-left">Lapor Perbaikan Alat</h3>
        <p class="text-xs text-gray-400 mb-6 font-medium text-left">Pilih alat yang rusak untuk masuk ke antrean servis.</p>
        
        <form action="{{ route('repairs.store') }}" method="POST" class="space-y-4 text-left">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Pilih Alat</label>
                <select name="equipment_id" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                    <option value="">-- Pilih Alat --</option>
                    @foreach($damagedEquipment as $eq)
                        <option value="{{ $eq->id }}">{{ $eq->name }} ({{ $eq->qr_code_identifier }})</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-400 mt-1 italic">* Hanya alat dengan status 'Rusak' yang muncul.</p>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Deskripsi Kerusakan</label>
                <textarea name="issue_description" required rows="3" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm" placeholder="Jelaskan detail kerusakannya..."></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Service Center (Opsi)</label>
                <input type="text" name="service_center" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm" placeholder="Contoh: Sony Center / Bengkel Aris">
            </div>
            
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" class="w-full bg-orange-500 text-white py-4 rounded-2xl font-bold font-bold shadow-lg shadow-orange-100 hover:bg-orange-600 transition-colors">
                    Kirim ke Antrean Servis
                </button>
                <button type="button" onclick="closeAddRepairModal()" class="w-full text-gray-400 text-sm font-bold py-2">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Manage/Update Repair -->
<div id="manageRepairModal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-2 text-left">Update Status Servis</h3>
        <p id="repairItemName" class="text-sm text-indigo-600 mb-6 font-bold text-left"></p>
        
        <form id="updateRepairForm" method="POST" class="space-y-4 text-left">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Status Saat Ini</label>
                <select name="status" id="edit_status" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="pending_courier">Menunggu Kurir</option>
                    <option value="in_service">Di Service Center (Proses)</option>
                    <option value="returning">Proses Kembali</option>
                    <option value="completed">Selesai (Siap Digunakan)</option>
                </select>
            </div>

            <div id="completion_fields" class="space-y-4 hidden">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Service Center / Vendor</label>
                    <input type="text" name="service_center" id="edit_service_center" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Biaya Perbaikan (Rp)</label>
                    <input type="number" name="cost" id="edit_cost" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="0">
                </div>
            </div>
            
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-colors">
                    Perbarui Status
                </button>
                <button type="button" onclick="closeManageRepairModal()" class="w-full text-gray-400 text-sm font-bold py-2">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAddRepairModal() {
        const modal = document.getElementById('addRepairModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.querySelector('div').classList.remove('scale-95'), 10);
    }

    function closeAddRepairModal() {
        const modal = document.getElementById('addRepairModal');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function openManageRepairModal(repair) {
        document.getElementById('repairItemName').innerText = repair.equipment.name;
        document.getElementById('updateRepairForm').action = `/repairs/${repair.id}`;
        document.getElementById('edit_status').value = repair.status;
        document.getElementById('edit_service_center').value = repair.service_center || '';
        document.getElementById('edit_cost').value = repair.cost || 0;
        
        toggleCompletionFields(repair.status);

        const modal = document.getElementById('manageRepairModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.querySelector('div').classList.remove('scale-95'), 10);
    }

    function closeManageRepairModal() {
        const modal = document.getElementById('manageRepairModal');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function toggleCompletionFields(status) {
        const fields = document.getElementById('completion_fields');
        if (status === 'completed') {
            fields.classList.remove('hidden');
        } else {
            fields.classList.add('hidden');
        }
    }

    document.getElementById('edit_status').addEventListener('change', function() {
        toggleCompletionFields(this.value);
    });
</script>
@endpush
@endsection
