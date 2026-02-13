<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jadwal Liputan - {{ $month }}/{{ $year }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; padding: 40px; color: #333; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 3px double #333; padding-bottom: 20px; }
        .header h1 { margin: 0; text-transform: uppercase; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; font-size: 11px; }
        th { bg-color: #f8f9fa; font-weight: bold; text-transform: uppercase; }
        .status-success { color: #059669; font-weight: bold; }
        .status-pending { color: #d97706; font-weight: bold; }
        .footer { margin-top: 50px; text-align: right; font-size: 12px; }
        .print-btn { background: #4f46e5; color: white; padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; margin-bottom: 20px; }
        @media print { .print-btn { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn">Cetak Sekarang</button>
    
    <div class="header">
        <h1>Laporan Kegiatan Liputan</h1>
        <p>Bidang Informasi dan Komunikasi - LAKNIS</p>
        <p>Periode: {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Kegiatan</th>
                <th>Lokasi</th>
                <th>Petugas</th>
                <th>Alat</th>
                <th>Status Laporan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $schedule)
            <tr>
                <td>{{ \Carbon\Carbon::parse($schedule->starts_at)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($schedule->starts_at)->format('H:i') }}</td>
                <td><strong>{{ $schedule->title }}</strong></td>
                <td>{{ $schedule->location }}</td>
                <td>
                    {{ $schedule->users->pluck('name')->implode(', ') }}
                </td>
                <td>{{ $schedule->equipment->name ?? '-' }}</td>
                <td class="{{ $schedule->result_status == 'pending' ? 'status-pending' : 'status-success' }}">
                    {{ $schedule->result_status == 'pending' ? 'Belum Selesai' : 'Selesai' }}
                </td>
                <td>
                    @if($schedule->result_status != 'pending')
                        @php
                            $labels = [
                                'backed_up' => 'Backup',
                                'moved' => 'Pindah',
                                'archived' => 'Arsip',
                                'success' => 'Selesai'
                            ];
                        @endphp
                        {{ $labels[$schedule->result_status] ?? '' }} 
                        {{ $schedule->result_link ? '(Link Ada)' : '' }}
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada data kegiatan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
        <br><br><br>
        <p>__________________________</p>
        <p>Admin LAKNIS</p>
    </div>
</body>
</html>
