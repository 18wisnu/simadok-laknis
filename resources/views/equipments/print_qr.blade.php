<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code Alat - SIMADOK</title>
    <style>
        body { font-family: 'Arial', sans-serif; padding: 20px; background: #fff; }
        .print-btn { 
            background: #4f46e5; 
            color: white; 
            padding: 12px 24px; 
            border-radius: 12px; 
            border: none; 
            cursor: pointer; 
            font-weight: bold; 
            margin-bottom: 30px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }
        .qr-card {
            border: 1px solid #e5e7eb;
            padding: 15px;
            text-align: center;
            border-radius: 12px;
            page-break-inside: avoid;
        }
        .qr-image {
            width: 120px;
            height: 120px;
            margin-bottom: 10px;
        }
        .equipment-name {
            font-weight: bold;
            font-size: 14px;
            margin: 5px 0;
            color: #1f2937;
        }
        .equipment-id {
            font-family: monospace;
            font-size: 12px;
            color: #6b7280;
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
        }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
            .qr-card { border: 1px solid #ccc; }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn">Cetak Sekarang</button>

    <div class="container">
        @foreach($equipments as $equipment)
        <div class="qr-card">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $equipment->qr_code_identifier }}" alt="QR Code" class="qr-image">
            <div class="equipment-name">{{ $equipment->name }}</div>
            <div class="equipment-id">{{ $equipment->qr_code_identifier }}</div>
        </div>
        @endforeach
    </div>
</body>
</html>
