@extends('layouts.app')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header Summary -->
    <div class="px-2">
        <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-[0.2em] mb-1">Status Hari Ini</p>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Ringkasan Operasional</h2>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 p-5 rounded-[2rem] text-white shadow-xl shadow-indigo-100 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="flex items-center justify-between mb-3 relative z-10">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                    <i class="fas fa-hand-holding-heart text-lg"></i>
                </div>
                <span class="text-[9px] font-bold uppercase tracking-widest opacity-80">Aktif</span>
            </div>
            <div class="text-4xl font-bold relative z-10">{{ $stats['active_borrowings'] }}</div>
            <p class="text-[10px] opacity-70 mt-1 font-medium italic">Alat di tangan tim</p>
        </div>
        
        @if($stats['overdue_borrowings'] > 0)
        <div class="bg-gradient-to-br from-red-500 to-red-600 p-5 rounded-[2rem] text-white shadow-xl shadow-red-100 animate-pulse relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/10 rounded-full blur-2xl"></div>
            <div class="flex items-center justify-between mb-3 relative z-10">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md text-white">
                    <i class="fas fa-triangle-exclamation text-lg"></i>
                </div>
                <span class="text-[9px] font-bold uppercase tracking-widest opacity-80 italic">Urgent</span>
            </div>
            <div class="text-4xl font-bold relative z-10">{{ $stats['overdue_borrowings'] }}</div>
            <p class="text-[10px] opacity-90 mt-1 font-bold">Harus segera kembali!</p>
        </div>
        @else
        <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500">
                    <i class="fas fa-screwdriver-wrench text-lg"></i>
                </div>
                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Servis</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-800">{{ $stats['repair_equipment'] ?? 0 }}</div>
                <p class="text-[10px] text-gray-400 mt-1 font-medium">Unit dalam perbaikan</p>
            </div>
        </div>
        @endif
    </div>

    <!-- QR Scan Quick Action -->
    <div class="relative group">
        <div class="absolute inset-0 bg-emerald-500 blur-xl opacity-20 group-hover:opacity-40 transition-opacity rounded-3xl"></div>
        <div class="bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 p-6 rounded-[2.5rem] text-white shadow-xl shadow-emerald-100 relative overflow-hidden flex items-center justify-between">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <h3 class="text-xl font-bold tracking-tight">Quick Scan</h3>
                <p class="text-xs opacity-90 mt-0.5">Pinjam atau cek status alat instan</p>
            </div>
            <button onclick="startScanner()" class="w-14 h-14 bg-white text-emerald-600 rounded-2xl flex items-center justify-center shadow-lg active:scale-95 transition-all hover:rotate-12">
                <i class="fas fa-qrcode text-3xl"></i>
            </button>
        </div>
    </div>

    <!-- Usage Trend Chart -->
    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100/50">
        <div class="flex items-center justify-between mb-8 px-1">
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest">Aktivitas Pinjaman</h3>
                <p class="text-[9px] text-gray-400 font-bold uppercase mt-1">Status: Normal</p>
            </div>
            <div class="bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100">
                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">6 Bulan Terakhir</span>
            </div>
        </div>
        <div class="h-44">
            <canvas id="usageChart"></canvas>
        </div>
    </div>

    <!-- Upcoming Schedules -->
    <div class="space-y-4">
        <div class="flex items-center justify-between px-2">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Jadwal Terdekat</h2>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Liputan Minggu Ini</p>
            </div>
            <a href="{{ route('schedules.index') }}" class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl hover:bg-indigo-600 hover:text-white transition-all uppercase tracking-wider">Lihat Semua</a>
        </div>
        
        <div class="grid grid-cols-1 gap-4">
            @forelse($upcomingSchedules as $schedule)
            <div class="bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-2 h-full {{ $schedule->result_status !== 'pending' ? 'bg-emerald-500' : 'bg-indigo-500' }} opacity-20"></div>
                
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="text-xs font-bold text-indigo-600 bg-indigo-50 px-4 py-1.5 rounded-2xl border border-indigo-100">
                            {{ \Carbon\Carbon::parse($schedule->starts_at)->format('H:i') }}
                        </div>
                        @if($schedule->result_status !== 'pending')
                        <span class="text-[9px] font-bold uppercase py-1 px-3 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 italic flex items-center gap-1">
                            <i class="fas fa-check-circle"></i> Selesai
                        </span>
                        @endif
                    </div>
                    <div class="flex -space-x-2">
                        @foreach($schedule->users as $officer)
                        <img class="h-7 w-7 rounded-lg ring-2 ring-white object-cover shadow-sm bg-gray-200" 
                             src="{{ $officer->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($officer->name).'&background=random' }}" 
                             alt="{{ $officer->name }}">
                        @endforeach
                    </div>
                </div>
                
                <h4 class="font-bold text-gray-800 text-base leading-tight">{{ $schedule->title }}</h4>
                
                <div class="flex flex-wrap items-center gap-y-2 gap-x-4 mt-4 py-3 border-t border-gray-50">
                    <div class="flex items-center gap-1.5 text-[10px] text-gray-400 font-medium">
                        <i class="fas fa-location-dot text-indigo-400"></i> {{ $schedule->location }}
                    </div>
                    
                    @if($schedule->equipment)
                    <div class="flex items-center gap-1.5 text-[10px] font-bold {{ $schedule->equipment->status == 'available' ? 'text-emerald-500' : 'text-orange-500' }}">
                        <i class="fas fa-camera-retro"></i>
                        <span class="uppercase tracking-tighter">{{ $schedule->equipment->status == 'available' ? 'Alat Kembali' : 'Dibawa Tim' }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-10 bg-white rounded-[2rem] border border-dashed border-gray-200 text-gray-400">
                <i class="fas fa-calendar-alt text-3xl opacity-10 mb-2"></i>
                <p class="text-xs italic font-medium">Belum ada jadwal liputan terdekat.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Active Borrowings -->
    <div class="space-y-4">
        <div class="flex items-center justify-between px-2">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Log Penguasaan Alat</h2>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Status Peminjaman Aktif</p>
            </div>
            <a href="{{ route('equipments.index') }}" class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl hover:bg-indigo-600 hover:text-white transition-all uppercase tracking-wider">Lihat Semua</a>
        </div>

        <div class="grid grid-cols-1 gap-3">
            @forelse($activeBorrowings as $borrowing)
            <div class="p-5 rounded-[2rem] shadow-sm border transition-all duration-300 {{ $borrowing->isOverdue() ? 'bg-red-50 border-red-100 shadow-red-50' : 'bg-white border-gray-100 hover:shadow-md' }} flex items-center gap-4">
                <div class="w-14 h-14 {{ $borrowing->isOverdue() ? 'bg-red-100 text-red-600' : 'bg-indigo-50 text-indigo-600' }} rounded-2xl flex items-center justify-center shadow-inner">
                    <i class="fas fa-camera-retro text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <h4 class="font-bold text-gray-800 text-sm truncate uppercase tracking-tight">{{ $borrowing->equipment->name }}</h4>
                        @if($borrowing->isOverdue())
                        <span class="bg-red-600 text-white text-[7px] font-black px-2 py-0.5 rounded-full animate-pulse uppercase tracking-[0.1em]">Overdue</span>
                        @endif
                    </div>
                    <p class="text-[10px] {{ $borrowing->isOverdue() ? 'text-red-500' : 'text-gray-500' }} font-bold uppercase tracking-tight">Peminjam: <span class="{{ $borrowing->isOverdue() ? 'text-red-700' : 'text-indigo-600' }}">{{ $borrowing->user->name }}</span></p>
                    <div class="flex items-center gap-3 mt-1.5">
                        <div class="flex items-center gap-1 text-[9px] text-gray-400">
                            <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($borrowing->borrowed_at)->diffForHumans() }}
                        </div>
                        @if($borrowing->isOverdue())
                        <div class="flex items-center gap-1 text-[9px] text-red-400 font-bold animate-pulse">
                            <i class="fas fa-calendar-xmark"></i> {{ $borrowing->expected_return_at->format('d M, H:i') }}
                        </div>
                        @endif
                    </div>
                </div>
                @if(auth()->id() == $borrowing->user_id || auth()->user()->isAdmin())
                <button onclick="openReturnModal({{ $borrowing->id }}, '{{ $borrowing->equipment->name }}')" 
                        class="{{ $borrowing->isOverdue() ? 'bg-red-600 text-white' : 'bg-indigo-600 text-white' }} p-3 rounded-2xl shadow-lg transition-transform active:scale-90 flex items-center justify-center">
                    <i class="fas fa-arrow-rotate-left"></i>
                </button>
                @endif
            </div>
            @empty
            <div class="text-center py-12 bg-white rounded-[2rem] border border-dashed border-gray-200">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-circle-check text-2xl text-gray-200"></i>
                </div>
                <p class="text-sm text-gray-400 font-medium">Semua alat sudah di rak kembali.</p>
            </div>
            @endforelse
        </div>
    </div>
    <!-- Admin Quick Actions -->
    @if(auth()->user()->isSuperAdmin())
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4">Panel Super Admin</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('audit-logs.index') }}" class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl hover:bg-indigo-50 transition-colors group">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 group-hover:text-indigo-600 transition-colors">
                    <i class="fas fa-history"></i>
                </div>
                <span class="text-xs font-bold text-gray-600 group-hover:text-indigo-600">Audit Logs</span>
            </a>
            <a href="{{ route('users.index') }}" class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl hover:bg-indigo-50 transition-colors group">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 group-hover:text-indigo-600 transition-colors">
                    <i class="fas fa-users-cog"></i>
                </div>
                <span class="text-xs font-bold text-gray-600 group-hover:text-indigo-600">Kelola User</span>
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Return Modal -->
<div id="returnModal" class="fixed inset-0 z-[110] hidden bg-black/50 backdrop-blur-sm overflow-y-auto">
    <div class="min-h-screen flex items-center justify-center p-4 text-center">
        <div class="bg-white w-full max-w-md rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300 my-8 text-left">
        <h3 class="text-xl font-bold text-gray-800 mb-2">Kembalikan Alat</h3>
        <p id="returnItemName" class="text-sm text-gray-400 mb-6 font-medium"></p>
        
        <form id="returnForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <label class="block">
                    <span class="text-xs font-bold text-gray-400 uppercase mb-2 block">Kondisi Alat</span>
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
</div>

<!-- Simple Scanner Modal Placeholder -->
<div id="scannerModal" class="fixed inset-0 z-[120] bg-black/90 hidden flex flex-col items-center justify-center p-6 text-white">
    <div class="w-full max-w-xs aspect-square border-2 border-dashed border-white/50 rounded-3xl mb-8 flex items-center justify-center overflow-hidden bg-gray-900">
        <div id="reader" width="600px"></div>
    </div>
    <p class="text-sm text-center mb-8 opacity-70">Align QR code within the frame</p>
    <button onclick="stopScanner()" class="px-8 py-3 bg-white text-black font-bold rounded-2xl">Close Scanner</button>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    // Usage Chart
    const ctx = document.getElementById('usageChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                label: 'Jumlah Pinjaman',
                data: {!! json_encode($chartData['values']) !!},
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#4f46e5',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });

    let html5QrCode;

    function startScanner() {
        document.getElementById('scannerModal').classList.remove('hidden');
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                document.getElementById('scannerModal').classList.add('hidden');
            });
        } else {
            document.getElementById('scannerModal').classList.add('hidden');
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Redirect to equipment show page using the scanned QR code identifier
        window.location.href = `/equipments/${decodedText}`;
        stopScanner();
    }

    function openReturnModal(id, name) {
        document.getElementById('returnItemName').innerText = name;
        document.getElementById('returnForm').action = `/borrowings/${id}/return`;
        const modal = document.getElementById('returnModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.querySelector('.bg-white').classList.remove('scale-95'), 10);
    }

    function closeReturnModal() {
        const modal = document.getElementById('returnModal');
        modal.querySelector('.bg-white').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
</script>
@endpush
