<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\Equipment;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    public function index()
    {
        $activeRepairs = Repair::with('equipment')
            ->where('status', '!=', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        $completedRepairs = Repair::with('equipment')
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        $damagedEquipment = Equipment::whereIn('status', ['damaged', 'available'])->get();

        return view('repairs.index', compact('activeRepairs', 'completedRepairs', 'damagedEquipment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'issue_description' => 'required|string',
            'service_center' => 'nullable|string|max:255',
        ]);

        Repair::create([
            'equipment_id' => $validated['equipment_id'],
            'issue_description' => $validated['issue_description'],
            'service_center' => $validated['service_center'],
            'status' => 'pending_courier',
        ]);

        Equipment::find($request->equipment_id)->update(['status' => 'in_service']);

        return redirect()->back()->with('success', 'Laporan perbaikan berhasil dibuat.');
    }

    public function update(Request $request, Repair $repair)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending_courier,in_service,returning,completed',
            'cost' => 'nullable|numeric|min:0',
            'service_center' => 'nullable|string|max:255',
        ]);

        $repair->update($validated);

        if ($validated['status'] === 'completed') {
            $repair->equipment->update(['status' => 'available']);
        }

        return redirect()->back()->with('success', 'Status perbaikan berhasil diperbarui.');
    }
}
