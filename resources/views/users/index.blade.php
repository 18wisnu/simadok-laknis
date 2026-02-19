@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between px-2">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Akses</h2>
        <button onclick="openAddUserModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-2xl text-xs font-bold shadow-lg shadow-indigo-100 flex items-center gap-2">
            <i class="fas fa-user-plus"></i> Tambah User
        </button>
    </div>

    <!-- Stats summary... -->
    <div class="bg-indigo-50 text-indigo-600 px-3 py-2 rounded-2xl text-[10px] font-bold uppercase inline-block ml-2">
        {{ $users->count() }} Terdaftar
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 text-emerald-600 p-4 rounded-2xl text-sm font-medium border border-emerald-100 mx-2">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-sm font-medium border border-red-100 mx-2">
        {{ session('error') }}
    </div>
    @endif

    <div class="space-y-4">
        @foreach($users as $user)
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" class="w-12 h-12 rounded-full border border-gray-100 shadow-sm" alt="">
                <div>
                    <h4 class="font-bold text-gray-800">{{ $user->name }}</h4>
                    <p class="text-[10px] text-gray-400 font-medium">
                        <i class="fas fa-envelope mr-1"></i> {{ $user->email }}
                        @if($user->phone_number)
                        <span class="mx-1 text-gray-200">|</span>
                        <i class="fab fa-whatsapp mr-1"></i> {{ $user->phone_number }}
                        @endif
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $user->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                            {{ $user->is_active ? 'Aktif' : 'Non-aktif' }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-indigo-50 text-indigo-600">
                            {{ $user->role }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Edit User -->
                <button onclick="openEditUserModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->phone_number }}')" class="p-3 rounded-2xl bg-gray-50 text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors" title="Edit Profil">
                    <i class="fas fa-edit"></i>
                </button>

                <!-- Set Password -->
                <button onclick="openPasswordModal({{ $user->id }}, '{{ $user->name }}')" class="p-3 rounded-2xl bg-gray-50 text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors" title="Set Password">
                    <i class="fas fa-key"></i>
                </button>

                <!-- Toggle Status -->
                <form action="{{ route('users.status', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="p-3 rounded-2xl {{ $user->is_active ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }} transition-colors" title="{{ $user->is_active ? 'Non-aktifkan' : 'Aktifkan' }}">
                        <i class="fas {{ $user->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                    </button>
                </form>

                <!-- Change Role -->
                <form action="{{ route('users.role', $user) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="role" onchange="this.form.submit()" class="text-xs font-bold bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 py-2 pr-8">
                        <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>STAFF</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>ADMIN</option>
                        <option value="superadmin" {{ $user->role === 'superadmin' ? 'selected' : '' }}>SUPERADMIN</option>
                    </select>
                </form>

                <!-- Delete User -->
                @if($user->id !== auth()->id())
                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-3 rounded-2xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Hapus User">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-black/50 z-[60] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-sm rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Edit Profil User</h3>

        <form id="editUserForm" action="" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Email</label>
                <input type="email" name="email" id="edit_email" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nomor WhatsApp</label>
                <input type="text" name="phone_number" id="edit_phone_number" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-colors">
                    Simpan Perubahan
                </button>
                <button type="button" onclick="closeEditUserModal()" class="w-full text-gray-400 text-sm font-bold py-2">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Password Modal -->
<div id="passwordModal" class="fixed inset-0 bg-black/50 z-[60] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-sm rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-2">Set Kata Sandi</h3>
        <p class="text-xs text-gray-400 mb-6" id="passwordModalUser"></p>

        <form id="passwordForm" action="" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Kata Sandi Baru</label>
                <input type="password" name="password" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-colors">
                    Simpan Kata Sandi
                </button>
                <button type="button" onclick="closePasswordModal()" class="w-full text-gray-400 text-sm font-bold py-2">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 bg-black/50 z-[60] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-sm rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Tambah User Manual</h3>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Contoh: Budi Santoso">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Email</label>
                <input type="email" name="email" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="budi@email.com">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nomor WhatsApp</label>
                <input type="text" name="phone_number" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="628123456789 (Awali dengan 62)">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Role</label>
                <select name="role" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="staff">STAFF</option>
                    <option value="admin">ADMIN</option>
                    <option value="superadmin">SUPERADMIN</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Kata Sandi</label>
                <input type="password" name="password" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="••••••••">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" required class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="••••••••">
            </div>
            
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-colors">
                    Buat Akun
                </button>
                <button type="button" onclick="closeAddUserModal()" class="w-full text-gray-400 text-sm font-bold py-2">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddUserModal() {
        const modal = document.getElementById('addUserModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.querySelector('div').classList.remove('scale-95'), 10);
    }

    function closeAddUserModal() {
        const modal = document.getElementById('addUserModal');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function openEditUserModal(userId, name, email, phone) {
        const modal = document.getElementById('editUserModal');
        const form = document.getElementById('editUserForm');
        
        form.action = `/users/${userId}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_phone_number').value = phone || '';
        
        modal.classList.remove('hidden');
        setTimeout(() => modal.querySelector('div').classList.remove('scale-95'), 10);
    }

    function closeEditUserModal() {
        const modal = document.getElementById('editUserModal');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function openPasswordModal(userId, userName) {
        const modal = document.getElementById('passwordModal');
        const form = document.getElementById('passwordForm');
        document.getElementById('passwordModalUser').innerText = "Untuk: " + userName;
        form.action = `/users/${userId}/password`;
        modal.classList.remove('hidden');
        setTimeout(() => modal.querySelector('div').classList.remove('scale-95'), 10);
    }

    function closePasswordModal() {
        const modal = document.getElementById('passwordModal');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
</script>
@endsection

