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
            <div class="text-3xl font-bold text-gray-800">{{ $stats['repair_equipment'] ?? 0 }}</div>
            <div class="text-[10px] text-gray-400">Unit dalam perbaikan</div>
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

<!-- Simple Scanner Modal Placeholder -->
<div id="scannerModal" class="fixed inset-0 z-[100] bg-black/90 hidden flex flex-col items-center justify-center p-6 text-white">
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
        setTimeout(() => modal.querySelector('div').classList.remove('scale-95'), 10);
    }

    function closeReturnModal() {
        const modal = document.getElementById('returnModal');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
</script>
@endpush
