@extends('layouts.app')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-indigo-600 p-4 rounded-3xl text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-hand-holding-heart text-2xl opacity-50"></i>
                <span class="text-xs font-bold uppercase tracking-wider">Aktif</span>
            </div>
            <div class="text-3xl font-bold">{{ $activeBorrowings->count() }}</div>
            <div class="text-[10px] opacity-70">Alat dipinjam</div>
        </div>
        <div class="bg-white p-4 rounded-3xl shadow-md border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-tools text-2xl text-orange-400 opacity-50"></i>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Servis</span>
            </div>
<<<<<<< Updated upstream
            <div class="text-3xl font-bold text-gray-800">{{ $stats['repair_equipment'] ?? 0 }}</div>
            <div class="text-[10px] text-gray-400">Unit dalam perbaikan</div>
=======
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

    <!-- Notifications Section -->
    @if($notifications->count() > 0)
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">Notifikasi Terbaru</h3>
            @if($notifications->where('read_at', null)->count() > 0)
            <span class="bg-emerald-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold animate-pulse">
                {{ $notifications->where('read_at', null)->count() }} Baru
            </span>
            @endif
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($notifications as $notif)
            <div class="flex items-start gap-3 p-3 rounded-2xl {{ $notif->read_at ? 'bg-gray-50/50 opacity-60' : 'bg-indigo-50/50 border border-indigo-100 shadow-sm shadow-indigo-100/50' }} transition-all">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 {{ $notif->read_at ? 'bg-gray-100 text-gray-400' : 'bg-indigo-600 text-white' }}">
                    <i class="fas {{ $notif->data['icon'] ?? 'fa-bell' }} text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-bold text-gray-800 truncate">{{ $notif->data['title'] }}</p>
                    <p class="text-[10px] text-gray-500 line-clamp-1 truncate">{{ $notif->data['message'] }}</p>
                    <p class="text-[9px] text-gray-400 mt-1 uppercase font-bold">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
                @if(!$notif->read_at)
                <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="flex-shrink-0">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-indigo-600 hover:text-indigo-800 p-1 bg-white rounded-lg border border-indigo-100 shadow-sm">
                        <i class="fas fa-check text-[10px]"></i>
                    </button>
                </form>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Active Borrowings Card -->
        <div class="p-6 rounded-3xl flex flex-col h-full bg-indigo-900 text-white shadow-xl shadow-indigo-200">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-hand-holding-heart text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm tracking-tight">Status Peminjaman</h3>
                        <p class="text-[10px] opacity-60 uppercase font-bold">Terbaru 2024</p>
                    </div>
                </div>
                <div class="bg-white/20 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Aktif</div>
            </div>
            
            <div class="flex items-end justify-between mt-2">
                <div>
                    <span class="text-5xl font-black">{{ $stats['active_borrowings'] }}</span>
                    <p class="text-[10px] mt-2 opacity-60 font-medium">Alat di tangan tim</p>
                </div>
                <div class="w-24 h-12">
                    <canvas id="miniChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Service Card -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600">
                    <i class="fas fa-screwdriver-wrench text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm tracking-tight text-gray-800">Pemeliharaan</h3>
                    <p class="text-[10px] text-gray-400 uppercase font-bold">Status Servis</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 flex-1">
                <div class="bg-gray-50 p-4 rounded-2xl flex flex-col justify-center">
                    <span class="text-2xl font-bold text-gray-800">{{ $stats['repair_equipment'] }}</span>
                    <p class="text-[9px] text-gray-400 font-bold uppercase mt-1">Sedang Servis</p>
                </div>
                <div class="bg-red-50 p-4 rounded-2xl flex flex-col justify-center text-red-600">
                    <span class="text-2xl font-bold">{{ $stats['lost_equipment'] }}</span>
                    <p class="text-[9px] opacity-60 font-bold uppercase mt-1">Alat Hilang</p>
                </div>
            </div>
        </div>
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
            <button onclick="startScanner()" class="relative z-20 w-14 h-14 bg-white text-emerald-600 rounded-2xl flex items-center justify-center shadow-lg active:scale-95 transition-all hover:rotate-12">
                <i class="fas fa-qrcode text-3xl"></i>
            </button>
>>>>>>> Stashed changes
        </div>
    </div>

    <!-- Usage Trend Chart -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Tren Pemakaian Alat</h3>
            <span class="text-[10px] text-gray-400 font-bold uppercase">6 Bulan Terakhir</span>
        </div>
        <div class="h-40">
            <canvas id="usageChart"></canvas>
        </div>
    </div>

    <!-- QR Scan Quick Action -->
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 rounded-3xl text-white shadow-xl flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold">Pinjam Mudah</h3>
            <p class="text-xs opacity-80">Scan QR Code untuk mulai</p>
        </div>
        <button onclick="startScanner()" class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center hover:bg-white/30 transition-colors">
            <i class="fas fa-qrcode text-2xl"></i>
        </button>
    </div>

    <!-- Upcoming Schedules -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-lg font-bold text-gray-800">Jadwal Liputan</h2>
            <a href="{{ route('schedules.index') }}" class="text-xs font-semibold text-indigo-600">Lihat Semua</a>
        </div>
        @forelse($upcomingSchedules as $schedule)
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                        {{ \Carbon\Carbon::parse($schedule->starts_at)->format('H:i') }}
                    </div>
                    @if($schedule->result_status !== 'pending')
                    <span class="text-[10px] font-bold uppercase py-1 px-3 rounded-full bg-emerald-500 text-white shadow-sm shadow-emerald-100 italic">
                        <i class="fas fa-check-circle mr-1"></i> Selesai
                    </span>
                    @endif
                </div>
                <div class="flex -space-x-1">
                    @foreach($schedule->users as $officer)
                    <img class="h-6 w-6 rounded-full ring-2 ring-white object-cover shadow-sm" src="{{ $officer->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($officer->name) }}" alt="{{ $officer->name }}">
                    @endforeach
                </div>
            </div>
            <h4 class="font-bold text-gray-800">{{ $schedule->title }}</h4>
            <div class="flex flex-wrap items-center gap-4 mt-2">
                <div class="flex items-center gap-1.5 text-[10px] text-gray-400">
                    <i class="fas fa-map-marker-alt"></i> {{ $schedule->location }}
                </div>
                
                @if($schedule->equipment)
                <div class="flex items-center gap-1.5 text-[10px] font-bold {{ $schedule->equipment->status == 'available' ? 'text-emerald-500' : 'text-orange-500' }}">
                    <i class="fas fa-camera-retro"></i>
                    {{ $schedule->equipment->status == 'available' ? 'Alat Sudah Kembali' : 'Alat Belum Kembali' }}
                </div>
                @endif
                
                @if($schedule->result_status !== 'pending')
                <div class="flex items-center gap-1.5 text-[10px] font-bold text-indigo-500">
                    <i class="fas fa-cloud-upload-alt"></i>
                    @php
                        $statusLabels = [
                            'backed_up' => 'File Sudah disalin (Backup)',
                            'moved' => 'File Sudah dipindah',
                            'archived' => 'File Sudah diarsip',
                            'success' => 'Kegiatan Selesai'
                        ];
                    @endphp
                    {{ $statusLabels[$schedule->result_status] ?? 'Data Tersimpan' }}
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-6 bg-gray-50 rounded-3xl border border-dashed border-gray-200 text-gray-400">
            <p class="text-xs">Tidak ada jadwal terdekat.</p>
        </div>
        @endforelse
    </div>

    <!-- Active Borrowings -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-lg font-bold text-gray-800">Peminjaman Aktif</h2>
            <a href="#" class="text-xs font-semibold text-indigo-600">Lihat Semua</a>
        </div>
        @forelse($activeBorrowings as $borrowing)
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                <i class="fas fa-laptop text-xl"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-gray-800 text-sm">{{ $borrowing->equipment->name }}</h4>
                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($borrowing->borrowed_at)->diffForHumans() }}</p>
            </div>
            <button onclick="openReturnModal({{ $borrowing->id }}, '{{ $borrowing->equipment->name }}')" class="bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg text-xs font-bold">Kembalikan</button>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            <i class="fas fa-check-circle text-4xl mb-2 opacity-20"></i>
            <p class="text-sm">Tidak ada peminjaman aktif</p>
        </div>
        @endforelse
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
<div id="returnModal" class="fixed inset-0 bg-black/50 z-[110] hidden flex items-center justify-center p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-3xl p-8 shadow-2xl scale-95 transition-all duration-300">
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

<<<<<<< Updated upstream
<!-- Simple Scanner Modal Placeholder -->
<div id="scannerModal" class="fixed inset-0 z-[100] bg-black/90 hidden flex flex-col items-center justify-center p-6 text-white">
    <div class="w-full max-w-xs aspect-square border-2 border-dashed border-white/50 rounded-3xl mb-8 flex items-center justify-center overflow-hidden bg-gray-900">
        <div id="reader" width="600px"></div>
=======
<!-- Scanner Modal -->
<div id="scannerModal" class="fixed inset-0 z-[9999] bg-black/95 flex flex-col items-center justify-center p-6 text-white" style="display: none; pointer-events: auto;">
    <div class="w-full max-w-sm flex flex-col items-center relative z-[10000]">
        <!-- Top Close Button -->
        <button onclick="document.getElementById('scannerModal').style.display='none'; stopScanner();" class="absolute -top-12 right-0 w-12 h-12 bg-white/20 hover:bg-white/30 border border-white/30 rounded-full flex items-center justify-center cursor-pointer transition-all active:scale-90 z-[10001]">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="w-full aspect-square border-2 border-indigo-500/50 rounded-[2.5rem] mb-6 flex items-center justify-center overflow-hidden bg-gray-900/50 backdrop-blur-xl relative shadow-2xl shadow-indigo-500/20">
            <div id="reader" class="w-full h-full"></div>
            <!-- Scanner Overlay -->
            <div class="absolute inset-0 border-[40px] border-black/40 pointer-events-none"></div>
            <div class="absolute inset-[40px] border-2 border-indigo-500 rounded-2xl pointer-events-none">
                <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-white rounded-tl-lg"></div>
                <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-white rounded-tr-lg"></div>
                <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-white rounded-bl-lg"></div>
                <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-white rounded-br-lg"></div>
                <!-- Scanning Line Animation -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent animate-[scan_2s_linear_infinite] opacity-50"></div>
            </div>
        </div>
        
        <p id="scannerStatus" class="text-xs text-center mb-8 text-gray-400 font-medium px-4">
            Arahkan kamera ke QR Code alat
        </p>

        <!-- Manual Input Fallback -->
        <div class="w-full bg-white/5 p-4 rounded-3xl border border-white/10 mb-8">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 text-center">Atau Masukkan ID Manual</p>
            <div class="flex gap-2">
                <input type="text" id="manualQrInput" placeholder="Contoh: CAM-001" 
                       class="flex-1 bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder:text-gray-600">
                <button onclick="handleManualSearch()" class="bg-indigo-600 text-white px-5 py-3 rounded-xl font-bold text-sm active:scale-95 transition-all cursor-pointer relative z-[10002]">
                    Cari
                </button>
            </div>
        </div>

        <button onclick="stopScanner()" class="w-full py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-2xl transition-all border border-white/10 active:scale-95 cursor-pointer relative z-[10002]">
            Tutup Scanner
        </button>
        
        <p id="httpsWarning" class="hidden mt-6 text-[10px] text-red-400 bg-red-400/10 px-4 py-2 rounded-lg text-center font-medium">
            <i class="fas fa-triangle-exclamation mr-1"></i> Kamera butuh koneksi HTTPS (Secure)
        </p>
>>>>>>> Stashed changes
    </div>
</div>

<style>
@keyframes scan {
    0% { top: 0; }
    100% { top: 100%; }
}
</style>

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
        const modal = document.getElementById('scannerModal');
        modal.style.display = 'flex';
        document.getElementById('httpsWarning').classList.add('hidden');
        document.getElementById('scannerStatus').innerText = "Memuat kamera...";
        
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .then(() => {
                document.getElementById('scannerStatus').innerText = "Arahkan kamera ke QR Code alat";
            })
            .catch(err => {
                console.error("Scanner error:", err);
                document.getElementById('scannerStatus').innerText = "Kamera tidak dapat diakses.";
                if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                    document.getElementById('httpsWarning').classList.remove('hidden');
                }
            });
    }

    function stopScanner() {
        const modal = document.getElementById('scannerModal');
        console.log("Stopping scanner...");
        
        if (html5QrCode) {
            try {
                const state = typeof html5QrCode.getState === 'function' ? html5QrCode.getState() : 2;
                
                if (state >= 2) { 
                    html5QrCode.stop().then(() => {
                        console.log("Scanner stopped successfully");
                        html5QrCode.clear();
                    }).catch(err => {
                        console.warn("Error stopping scanner:", err);
                    }).finally(() => {
                        modal.style.display = 'none';
                    });
                } else {
                    modal.style.display = 'none';
                }
            } catch (e) {
                console.error("Critical scanner stop error:", e);
                modal.style.display = 'none';
            }
        } else {
            modal.style.display = 'none';
        }
    }

<<<<<<< Updated upstream
    function onScanSuccess(decodedText, decodedResult) {
        // Redirect to equipment show page using the scanned QR code identifier
        window.location.href = `/equipments/${decodedText}`;
        stopScanner();
=======
    function onScanSuccess(decodedText) {
        if (decodedText.trim() !== "") {
            window.location.href = `/equipments/${encodeURIComponent(decodedText.trim())}`;
            stopScanner();
        }
>>>>>>> Stashed changes
    }

    function handleManualSearch() {
        const input = document.getElementById('manualQrInput');
        const id = input.value.trim();
        if (id) {
            window.location.href = `/equipments/${encodeURIComponent(id)}`;
        } else {
            input.focus();
        }
    }

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
