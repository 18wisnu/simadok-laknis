<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::withCount('accessories')->get();
        return view('equipments.index', compact('equipments'));
        
    }

    public function show(Equipment $equipment)
    {
        $equipment->load([
            'accessories', 
            'borrowings.user', 
            'repairs',
            'auditLogs.user'
        ]);
        
        $borrowingHistory = $equipment->borrowings()->with('user')->orderBy('borrowed_at', 'desc')->get();
        
        return view('equipments.show', compact('equipment', 'borrowingHistory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|unique:equipment,serial_number',
            'qr_code_identifier' => 'required|string|unique:equipment,qr_code_identifier',
            'description' => 'nullable|string',
            'status' => 'required|in:available,borrowed,damaged,in_service,lost',
            'accessories' => 'nullable|string', // Comma separated accessories
        ]);

        $equipment = Equipment::create($validated);

        if ($request->accessories) {
            $accessories = array_filter(array_map('trim', explode(',', $request->accessories)));
            foreach ($accessories as $name) {
                $equipment->accessories()->create([
                    'name' => $name,
                    'is_removable' => true, // default
                ]);
            }
        }

        return redirect()->route('equipments.index')->with('success', 'Alat dan kelengkapannya berhasil ditambahkan');
    }

    public function update(Request $request, Equipment $equipment)
    {
        // 1. Validasi inputan
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|unique:equipment,serial_number,' . $equipment->id,
            'qr_code_identifier' => 'required|string|unique:equipment,qr_code_identifier,' . $equipment->id,
            'description' => 'nullable|string',
            'status' => 'required|in:available,borrowed,damaged,in_service,lost',
        ]);

        // 2. Update data alat utama
        $equipment->update($validated);

        // 3. Update data aksesoris/kelengkapan
        if ($request->has('accessories')) {
            // Hapus semua aksesoris lama di database untuk alat ini
            $equipment->accessories()->delete();

            // Pecah inputan koma jadi array dan bersihkan spasi
            if (!empty(trim($request->accessories))) {
                $accessoriesArray = array_filter(array_map('trim', explode(',', $request->accessories)));
                
                // Simpan aksesoris yang baru sebagai pengganti
                foreach ($accessoriesArray as $accName) {
                    if (!empty($accName)) {
                        $equipment->accessories()->create([
                            'name' => $accName,
                            'is_removable' => true // Sesuaikan dengan kolom default di migrasi kamu
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Data alat dan kelengkapan berhasil diperbarui!');
    }

    public function destroy(Equipment $equipment)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // Manually delete related records that don't have cascade delete in migration
        $equipment->borrowings()->delete();
        $equipment->repairs()->delete();

        // Accessories will be deleted automatically due to its migration definition
        $equipment->delete();

        return redirect()->back()->with('success', 'Alat berhasil dihapus.');
    }

    public function export()
    {
        $equipments = Equipment::withCount('accessories')->get();
        $fileName = 'data_alat_' . date('Ymd_His') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Nama Alat', 'Serial Number', 'QR Identifier', 'Status', 'Jumlah Aksesoris', 'Dibuat Pada'];

        $callback = function() use($equipments, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($equipments as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->name,
                    $item->serial_number,
                    $item->qr_code_identifier,
                    $item->status,
                    $item->accessories_count,
                    $item->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
