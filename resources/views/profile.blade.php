@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="space-y-8 pb-10">
    <!-- Profile Card -->
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col items-center text-center relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/50 rounded-full blur-3xl -mr-16 -mt-16 tracking-tighter"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-indigo-50/30 rounded-full blur-2xl -ml-12 -mb-12"></div>
        
        <div class="relative group">
            <div class="absolute inset-0 bg-indigo-600 rounded-[2rem] blur-xl opacity-20 scale-90 group-hover:scale-110 transition-transform"></div>
            <img src="{{ $profileUser->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($profileUser->name).'&background=4f46e5&color=fff&size=200' }}" 
                 class="w-28 h-28 rounded-[2rem] border-4 border-white shadow-2xl object-cover relative z-10" 
                 alt="{{ $profileUser->name }}">
            <div class="absolute -bottom-2 -right-2 bg-emerald-500 text-white w-9 h-9 rounded-2xl flex items-center justify-center border-4 border-white shadow-lg z-20">
                <i class="fas fa-check text-xs"></i>
            </div>
        </div>
        
        <div class="mt-6">
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">{{ $profileUser->name }}</h2>
            <div class="flex items-center justify-center gap-2 mt-2">
                <span class="px-4 py-1 rounded-full bg-indigo-50 text-indigo-600 text-[9px] font-black uppercase tracking-widest border border-indigo-100/50">
                    {{ $profileUser->role }}
                </span>
                @if($profileUser->is_active)
                <span class="px-4 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest border border-emerald-100/50">
                    Akun Aktif
                </span>
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full mt-10 pt-8 border-t border-gray-50 text-left">
            <div class="p-4 bg-gray-50/50 rounded-2xl border border-gray-100 group hover:bg-white hover:border-indigo-100 transition-all">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Alamat Email</p>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-gray-400 shadow-sm group-hover:text-indigo-500">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-700 truncate">{{ $profileUser->email }}</p>
                </div>
            </div>
            <div class="p-4 bg-gray-50/50 rounded-2xl border border-gray-100 group hover:bg-white hover:border-indigo-100 transition-all">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nomor Telepon</p>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-gray-400 shadow-sm group-hover:text-indigo-500">
                        <i class="fas fa-phone"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-700">{{ $profileUser->phone_number ?? 'Not set' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- User's Active Borrowings -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-lg font-bold text-gray-800 text-left">Peminjaman Aktif Saya</h2>
            <span class="text-xs font-bold text-gray-400 bg-gray-100 px-3 py-1 rounded-full">{{ $activeBorrowings->count() }} Alat</span>
        </div>
        
        @if($activeBorrowings->count() > 0)
            @foreach($activeBorrowings as $borrowing)
            <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                    <i class="fas fa-camera-retro text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800 text-sm text-left">{{ $borrowing->equipment->name }}</h4>
                    <div class="flex items-center gap-3 mt-1 text-[10px] text-gray-400 font-medium text-left">
                        <span><i class="fas fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($borrowing->borrowed_at)->diffForHumans() }}</span>
                        <span><i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($borrowing->borrowed_at)->format('d M Y') }}</span>
                    </div>
                </div>
                <button onclick="openReturnModal({{ $borrowing->id }}, '{{ $borrowing->equipment->name }}')" 
                        class="bg-indigo-600 text-white px-5 py-2.5 rounded-2xl text-[10px] font-bold uppercase tracking-wider shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">
                    Kembalikan
                </button>
            </div>
            @endforeach
        @else
            <div class="text-center py-8 bg-white rounded-3xl border border-dashed border-gray-200 text-gray-400">
                <p class="text-xs italic">Anda sedang tidak meminjam alat apapun.</p>
            </div>
        @endif
    </div>

    <!-- User's Schedules -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-lg font-bold text-gray-800 text-left">Jadwal Liputan Saya</h2>
            <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase">{{ now()->translatedFormat('F Y') }}</span>
        </div>
        
        @if($mySchedules->count() > 0)
            @foreach($mySchedules as $schedule)
            <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden">
                @if($schedule->result_status !== 'pending')
                <div class="absolute top-0 right-0 bg-emerald-500 text-white px-4 py-1 rounded-bl-2xl text-[9px] font-bold uppercase italic">
                    Selesai
                </div>
                @endif
                
                <div class="flex items-center gap-3 mb-3">
                    <div class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                        {{ $schedule->starts_at->format('H:i') }}
                    </div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                        {{ $schedule->starts_at->format('d M Y') }}
                    </div>
                </div>
                
                <h4 class="font-bold text-gray-800 text-sm text-left">{{ $schedule->title }}</h4>
                <div class="flex flex-wrap items-center gap-4 mt-2">
                    <div class="flex items-center gap-1.5 text-[10px] text-gray-400">
                        <i class="fas fa-map-marker-alt text-indigo-400"></i> {{ $schedule->location }}
                    </div>
                    @if($schedule->equipment)
                    <div class="flex items-center gap-1.5 text-[10px] text-gray-600 font-medium">
                        <i class="fas fa-camera-retro text-indigo-400"></i> {{ $schedule->equipment->name }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-8 bg-white rounded-3xl border border-dashed border-gray-200 text-gray-400">
                <p class="text-xs italic">Tidak ada jadwal liputan bulan ini.</p>
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-4 px-2">
            {{ $mySchedules->links() }}
        </div>
    </div>

    <!-- Administrative Management (Superadmin Only) -->
    @if(auth()->user()->isSuperAdmin())
    <div class="space-y-3">
        <h2 class="text-lg font-bold text-gray-800 text-left px-2">Manajemen Sistem</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('users.index') }}" class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md hover:border-indigo-100 transition-all group">
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                    <i class="fas fa-users-cog text-xl"></i>
                </div>
                <div class="text-left">
                    <h4 class="font-bold text-gray-800 text-sm">Manajemen User</h4>
                    <p class="text-[10px] text-gray-400">Kelola akses dan akun tim</p>
                </div>
            </a>
            <a href="{{ route('settings.index') }}" class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md hover:border-indigo-100 transition-all group">
                <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    <i class="fab fa-whatsapp text-xl"></i>
                </div>
                <div class="text-left">
                    <h4 class="font-bold text-gray-800 text-sm">Pengaturan API</h4>
                    <p class="text-[10px] text-gray-400">Konfigurasi WhatsApp Gateway</p>
                </div>
            </a>
        </div>
    </div>
    @endif

    <!-- Quick Logout -->
    <div class="pt-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-red-50 text-red-600 py-4 rounded-3xl font-bold flex items-center justify-center gap-2 hover:bg-red-100 transition-colors">
                <i class="fas fa-sign-out-alt"></i>
                Keluar dari Akun
            </button>
        </form>
    </div>
</div>

<!-- Return Modal (Copied from Dashboard) -->
<div id="returnModal" class="fixed inset-0 bg-black/50 z-[110] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-2">Kembalikan Alat</h3>
        <p id="returnItemName" class="text-sm text-gray-400 mb-6 font-medium"></p>
        
        <form id="returnForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <label class="block">
                    <span class="text-xs font-bold text-gray-400 uppercase mb-2 block text-left">Kondisi Alat</span>
                    <select name="condition_on_return" class="w-full p-4 rounded-2xl border border-gray-100 text-gray-700 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="good">Bagus (Normal)</option>
                        <option value="damaged">Rusak / Perlu Perbaikan</option>
                    </select>
                </label>
                
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold mt-4 shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-colors">
                    Konfirmasi Pengembalian
                </button>
                <button type="button" onclick="closeReturnModal()" class="w-full text-gray-400 text-sm font-bold py-2">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openReturnModal(id, name) {
        document.getElementById('returnItemName').innerText = name;
        document.getElementById('returnForm').action = `/borrowings/${id}/return`;
        const modal = document.getElementById('returnModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.querySelector('div').classList.remove('scale-95'), 10);
    }

    function closeReturnModal() {
        const modal = document.getElementById('returnModal');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
</script>
@endpush
@endsection
