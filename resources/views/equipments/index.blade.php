@extends('layouts.app')

@section('title', 'Equipment List')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-800">Daftar Alat</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('equipments.export') }}" class="w-10 h-10 bg-white border border-gray-200 text-emerald-600 rounded-xl flex items-center justify-center hover:bg-emerald-50 transition-colors" title="Ekspor Excel">
                <i class="fas fa-file-excel"></i>
            </a>
            <a href="{{ route('equipments.print-qr') }}" target="_blank" class="w-10 h-10 bg-white border border-gray-200 text-gray-600 rounded-xl flex items-center justify-center hover:bg-gray-50 transition-colors" title="Cetak QR Code">
                <i class="fas fa-qrcode"></i>
            </a>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Cari alat..." class="pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-32">
            </div>
        </div>
    </div>

    @forelse($equipments as $equipment)
    <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400">
                <i class="fas fa-microchip text-2xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800">{{ $equipment->name }}</h4>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase 
                        @if($equipment->status == 'available') bg-emerald-50 text-emerald-600 
                        @elseif($equipment->status == 'borrowed') bg-blue-50 text-blue-600
                        @elseif($equipment->status == 'lost') bg-red-50 text-red-600
                        @else bg-orange-50 text-orange-600 @endif">
                        @if($equipment->status == 'available') Tersedia 
                        @elseif($equipment->status == 'borrowed') Dipinjam
                        @elseif($equipment->status == 'lost') Hilang
                        @elseif($equipment->status == 'damaged') Rusak
                        @else Servis @endif
                    </span>
                    <span class="text-[10px] text-gray-400">{{ $equipment->accessories_count }} kelengkapan</span>
                </div>
            </div>
<<<<<<< Updated upstream
=======
            <div class="flex items-center gap-2">
                @if(auth()->user()->isAdmin())
                <button onclick='openEditEquipmentModal({!! json_encode($equipment) !!})' class="w-10 h-10 bg-gray-50 text-gray-400 rounded-xl flex items-center justify-center hover:bg-white hover:shadow-md hover:text-indigo-600 transition-all border border-transparent hover:border-indigo-100">
                    <i class="fas fa-edit text-xs"></i>
                </button>
                <button onclick="confirmDeleteEquipment('{{ $equipment->qr_code_identifier }}')" class="w-10 h-10 bg-gray-50 text-gray-400 rounded-xl flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all border border-transparent hover:border-red-100">
                    <i class="fas fa-trash text-xs"></i>
                </button>
                @endif
                <a href="{{ route('equipments.show', $equipment) }}" class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center hover:bg-shadow-lg hover:shadow-indigo-100 transition-all active:scale-90">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </div>
>>>>>>> Stashed changes
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user()->isAdmin())
            <button onclick='openEditEquipmentModal({!! json_encode($equipment) !!})' class="w-10 h-10 bg-gray-50 text-gray-400 rounded-xl flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                <i class="fas fa-edit"></i>
            </button>
            @endif
            <a href="{{ route('equipments.show', $equipment) }}" class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center hover:bg-indigo-100 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
    @empty
    <div class="text-center py-20 text-gray-400 italic">
        <i class="fas fa-box-open text-4xl mb-3 opacity-20"></i>
        <p class="text-sm">Belum ada alat yang terdaftar.</p>
    </div>
    @endforelse

    <!-- Floating Action Button -->
    <button onclick="openAddModal()" class="fixed bottom-24 right-6 w-14 h-14 bg-indigo-600 text-white rounded-full shadow-xl shadow-indigo-200 flex items-center justify-center text-2xl active:scale-95 transition-all z-40">
        <i class="fas fa-plus"></i>
    </button>
</div>

<!-- Edit Equipment Modal -->
<div id="editModal" class="fixed inset-0 bg-black/50 z-[60] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Edit Data Alat</h3>
        
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nama Alat</label>
                <input type="text" name="name" id="edit_name" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Serial Number</label>
                    <input type="text" name="serial_number" id="edit_serial_number" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">QR Identifier</label>
                    <input type="text" name="qr_code_identifier" id="edit_qr_code_identifier" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Status</label>
                <select name="status" id="edit_status" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="available">Tersedia</option>
                    <option value="borrowed" disabled>Dipinjam (Otomatis)</option>
                    <option value="damaged">Rusak</option>
                    <option value="in_service">Dalam Perbaikan</option>
                    <option value="lost">Hilang (Arsip)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Kelengkapan</label>
                <textarea name="description" id="edit_description" rows="2" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm @error('description') border-red-300 @enderror"></textarea>
                @error('description')
                    <p class="text-[10px] text-red-500 font-bold mt-1 ml-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Deskripsi</label>
                <textarea name="description" id="edit_description" rows="2" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"></textarea>
            </div>
            
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-colors">
                    Perbarui Data
                </button>
                <button type="button" onclick="closeEditEquipmentModal()" class="w-full text-gray-400 text-sm font-bold py-2">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Equipment Modal -->
<div id="addModal" class="fixed inset-0 bg-black/50 z-[60] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Tambah Alat Baru</h3>
        
        <form action="{{ route('equipments.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nama Alat</label>
                <input type="text" name="name" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Contoh: Sony A7iv">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Serial Number</label>
                    <input type="text" name="serial_number" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="SN-123456">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">QR Identifier</label>
                    <input type="text" name="qr_code_identifier" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="CAM-01">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Status</label>
                <select name="status" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="available">Tersedia</option>
                    <option value="damaged">Rusak</option>
                    <option value="in_service">Dalam Perbaikan</option>
                    <option value="lost">Hilang (Arsip)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Kelengkapan Satu Set (Pisahkan dengan koma)</label>
                <textarea name="accessories" rows="2" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Contoh: Baterai, Charger, Lens Cap, Bag"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Deskripsi (Opsional)</label>
                <textarea name="description" rows="2" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Tambahkan catatan detail alat..."></textarea>
            </div>
            
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-colors">
                    Simpan Alat
                </button>
                <button type="button" onclick="closeAddModal()" class="w-full text-gray-400 text-sm font-bold py-2">
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

<<<<<<< Updated upstream
    function openEditEquipmentModal(equipment) {
        document.getElementById('editForm').action = `/equipments/${equipment.id}`;
        document.getElementById('edit_name').value = equipment.name;
        document.getElementById('edit_serial_number').value = equipment.serial_number;
        document.getElementById('edit_qr_code_identifier').value = equipment.qr_code_identifier;
        document.getElementById('edit_status').value = equipment.status;
        document.getElementById('edit_description').value = equipment.description || '';
        
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.querySelector('div').classList.remove('scale-95'), 10);
=======
    function openEditEquipmentModal(equipment, accessoriesString = '') {
    document.getElementById('editForm').action = `/equipments/${equipment.qr_code_identifier}`;
    document.getElementById('edit_name').value = equipment.name;
    document.getElementById('edit_serial_number').value = equipment.serial_number;
    document.getElementById('edit_qr_code_identifier').value = equipment.qr_code_identifier;
    document.getElementById('edit_status').value = equipment.status;
    document.getElementById('edit_description').value = equipment.description || '';
    
    // KODE TAMBAHAN: Memasukkan data aksesoris ke dalam textarea
    if (document.getElementById('edit_accessories')) {
        document.getElementById('edit_accessories').value = accessoriesString;
    }

    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden');
    setTimeout(() => modal.querySelector('.bg-white').classList.remove('scale-95'), 10);
>>>>>>> Stashed changes
    }

    function closeEditEquipmentModal() {
        const modal = document.getElementById('editModal');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
<<<<<<< Updated upstream
=======

    function confirmDeleteEquipment(qr) {
        if (confirm('Apakah Anda yakin ingin menghapus alat ini? Semua data terkait (peminjaman, perbaikan, aksesoris) akan ikut terhapus.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/equipments/${qr}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Search Filtering
    document.getElementById('searchEquipment').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.equipment-card').forEach(card => {
            const name = card.getAttribute('data-name');
            if (name.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Handle validation errors by re-opening modals
    @if($errors->any())
        @if(old('_method') == 'PATCH')
            // Edit modal handling would require more complex state tracking
        @else
            window.addEventListener('DOMContentLoaded', () => {
                openAddModal();
            });
        @endif
    @endif
>>>>>>> Stashed changes
</script>
@endsection
