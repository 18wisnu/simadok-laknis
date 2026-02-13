@extends('layouts.app')

@section('title', 'Jadwal Liputan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between px-2">
        <h2 class="text-2xl font-bold text-gray-800">Jadwal Liputan</h2>
        <div class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-xs font-bold uppercase">
            {{ $schedules->count() }} Kegiatan
        </div>
    </div>

    <div class="space-y-4">
        @forelse($schedules as $schedule)
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm transition-all active:scale-[0.98]">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="flex items-center gap-2">
                    @if(auth()->user()->isAdmin())
                    <button onclick="openEditModal({{ json_encode($schedule) }}, {{ json_encode($schedule->users->pluck('id')) }})" class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                    <button onclick="confirmDelete({{ $schedule->id }})" class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-600 transition-all">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                    @endif
                    <span class="ml-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $schedule->result_status != 'pending' ? 'bg-emerald-50 text-emerald-600' : 'bg-orange-50 text-orange-600' }}">
                        @if($schedule->result_status == 'pending') Belum Mulai
                        @else Selesai @endif
                    </span>
                </div>
            </div>
            
            <h3 class="font-bold text-gray-800 text-lg">{{ $schedule->title }}</h3>
            <p class="text-sm text-gray-400 mt-1 line-clamp-2">{{ $schedule->description }}</p>
            
            <div class="mt-4 pt-4 border-t border-gray-50 flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-indigo-400"></div>
                        <span class="text-xs font-bold text-gray-600">{{ \Carbon\Carbon::parse($schedule->starts_at)->format('H:i') }}</span>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-2">
                    @foreach($schedule->users as $officer)
                    <div class="flex items-center gap-2 bg-gray-50 pr-3 rounded-full border border-gray-100">
                        <img class="h-7 w-7 rounded-full object-cover shadow-sm bg-gray-200" src="{{ $officer->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($officer->name) }}" alt="{{ $officer->name }}">
                        <span class="text-[10px] font-bold text-gray-600">{{ $officer->name }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if($schedule->equipment)
                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">
                        <i class="fas fa-camera-retro"></i>
                        {{ $schedule->equipment->name }}
                    </div>
                    @endif
                    <div class="text-[10px] text-gray-400 font-medium">
                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $schedule->location }}
                    </div>
                </div>
            </div>

            @if($schedule->result_link)
            <a href="{{ $schedule->result_link }}" target="_blank" class="mt-4 block w-full py-3 bg-indigo-50 text-indigo-600 text-center rounded-2xl text-xs font-bold hover:bg-gray-100 transition-colors">
                <i class="fas fa-link mr-1"></i> Lihat Hasil Liputan
            </a>
            @elseif($schedule->result_status == 'pending')
            <button onclick="openResultModal({{ $schedule->id }}, '{{ $schedule->title }}', {{ $schedule->equipment_id ? 'true' : 'false' }})" class="mt-4 block w-full py-3 bg-indigo-600 text-white text-center rounded-2xl text-xs font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                <i class="fas fa-check-double mr-1"></i> Selesaikan Kegiatan
            </button>
            @endif
        </div>
        @empty
        <div class="text-center py-20 text-gray-400">
            <i class="fas fa-calendar-times mb-4 text-5xl opacity-10"></i>
            <p class="text-sm">Belum ada jadwal kegiatan.</p>
        </div>
        @endforelse
    </div>

    <!-- Floating Action Button -->
    <div class="fixed bottom-24 right-6 flex flex-col items-end gap-3 z-40">
        @if(auth()->user()->isAdmin())
        <a href="{{ route('schedules.print') }}" target="_blank" class="w-12 h-12 bg-white text-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-100 hover:scale-110 transition-all border border-indigo-50" title="Cetak Laporan">
            <i class="fas fa-print"></i>
        </a>
        <button onclick="openAddModal()" class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-100 hover:scale-110 transition-all">
            <i class="fas fa-plus"></i>
        </button>
        @endif
    </div>
</div>

<!-- Add Schedule Modal -->
<div id="addModal" class="fixed inset-0 bg-black/50 z-[60] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Tambah Jadwal Baru</h3>
        
        <form action="{{ route('schedules.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Kegiatan</label>
                <input type="text" name="title" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Contoh: Liputan HUT Kota">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Lokasi</label>
                <input type="text" name="location" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Contoh: Alun-alun">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Waktu Mulai</label>
                <input type="datetime-local" name="starts_at" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Petugas Peliput (Bisa pilih > 1)</label>
                <div class="grid grid-cols-1 gap-2 mt-2 max-h-40 overflow-y-auto p-2 bg-gray-50 rounded-2xl border border-gray-100">
                    @foreach($users ?? [] as $user)
                    <label class="flex items-center gap-3 p-2 hover:bg-white rounded-xl transition-colors cursor-pointer group">
                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="w-5 h-5 rounded-lg border-gray-200 text-indigo-600 focus:ring-indigo-500">
                        <div class="flex items-center gap-2">
                            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" class="w-6 h-6 rounded-full object-cover">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600 transition-colors">{{ $user->name }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Alat yang Digunakan (Opsional)</label>
                <select name="equipment_id" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">-- Tanpa Alat --</option>
                    @foreach($equipments ?? [] as $equipment)
                    <option value="{{ $equipment->id }}">{{ $equipment->name }} ({{ $equipment->serial_number }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Detail (Opsional)</label>
                <textarea name="description" rows="3" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Tambahkan catatan kegiatan..."></textarea>
            </div>
            
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-colors">
                    Simpan Jadwal
                </button>
                <button type="button" onclick="closeAddModal()" class="w-full text-gray-400 text-sm font-bold py-2">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div id="editModal" class="fixed inset-0 bg-black/50 z-[60] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Edit Jadwal</h3>
        
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Kegiatan</label>
                <input type="text" name="title" id="edit_title" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Lokasi</label>
                <input type="text" name="location" id="edit_location" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Waktu Mulai</label>
                <input type="datetime-local" name="starts_at" id="edit_starts_at" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Petugas Peliput</label>
                <div class="grid grid-cols-1 gap-2 mt-2 max-h-40 overflow-y-auto p-2 bg-gray-50 rounded-2xl border border-gray-100">
                    @foreach($users ?? [] as $user)
                    <label class="flex items-center gap-3 p-2 hover:bg-white rounded-xl transition-colors cursor-pointer group">
                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="edit-user-checkbox w-5 h-5 rounded-lg border-gray-200 text-indigo-600 focus:ring-indigo-500">
                        <div class="flex items-center gap-2">
                            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" class="w-6 h-6 rounded-full object-cover">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600 transition-colors">{{ $user->name }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Alat yang Digunakan</label>
                <select name="equipment_id" id="edit_equipment_id" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">-- Tanpa Alat --</option>
                    @foreach($equipments ?? [] as $equipment)
                    <option value="{{ $equipment->id }}">{{ $equipment->name }} ({{ $equipment->serial_number }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Detail (Opsional)</label>
                <textarea name="description" id="edit_description" rows="3" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"></textarea>
            </div>
            
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-colors">
                    Simpan Perubahan
                </button>
                <button type="button" onclick="closeEditModal()" class="w-full text-gray-400 text-sm font-bold py-2">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.querySelector('div').classList.remove('scale-95'), 10);
    }

    function closeAddModal() {
        const modal = document.getElementById('addModal');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function openResultModal(id, title, hasEquipment = false) {
        document.getElementById('resultItemName').innerText = title;
        document.getElementById('resultForm').action = `/schedules/${id}/result`;
        
        const returnOption = document.getElementById('returnOptionDiv');
        if (hasEquipment) {
            returnOption.classList.remove('hidden');
        } else {
            returnOption.classList.add('hidden');
        }

        const modal = document.getElementById('resultModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.querySelector('div').classList.remove('scale-95'), 10);
    }

    function closeResultModal() {
        const modal = document.getElementById('resultModal');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function openEditModal(schedule, userIds) {
        const form = document.getElementById('editForm');
        form.action = `/schedules/${schedule.id}`;
        
        document.getElementById('edit_title').value = schedule.title;
        document.getElementById('edit_description').value = schedule.description || '';
        document.getElementById('edit_location').value = schedule.location;
        document.getElementById('edit_starts_at').value = schedule.starts_at.substring(0, 16);
        document.getElementById('edit_equipment_id').value = schedule.equipment_id || '';

        // Reset and Set checkboxes
        const checkboxes = document.querySelectorAll('.edit-user-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = userIds.includes(parseInt(cb.value));
        });
        
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.querySelector('div').classList.remove('scale-95'), 10);
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/schedules/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

<!-- Update Result Modal -->
<div id="resultModal" class="fixed inset-0 bg-black/50 z-[60] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-sm rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-2">Selesaikan Kegiatan</h3>
        <p id="resultItemName" class="text-xs text-gray-400 mb-6 font-medium"></p>
        
        <form id="resultForm" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Status Hasil</label>
                <select name="result_status" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="backed_up">Sudah Backup</option>
                    <option value="moved">Sudah Dipindah</option>
                    <option value="archived">Sudah Diarsipkan</option>
                    <option value="success">Selesai (Lainnya)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Link Hasil (Opsional)</label>
                <input type="url" name="result_link" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="https://youtube.com/...">
            </div>

            <div id="returnOptionDiv" class="bg-indigo-50/50 p-4 rounded-2xl flex items-center justify-between border border-indigo-100/50">
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-indigo-900">Kembalikan Alat?</span>
                    <span class="text-[10px] text-indigo-500">Centang jika alat sudah tidak digunakan lagi</span>
                </div>
                <input type="checkbox" name="return_equipment" value="1" class="w-6 h-6 rounded-lg border-indigo-200 text-indigo-600 focus:ring-indigo-500" checked>
            </div>
            
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-colors">
                    Update Laporan
                </button>
                <button type="button" onclick="closeResultModal()" class="w-full text-gray-400 text-sm font-bold py-2">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
