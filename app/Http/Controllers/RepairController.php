<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\Equipment;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    public function index()
    {
        $repairs = Repair::with('equipment')->orderBy('created_at', 'desc')->get();
        return view('repairs.index', compact('repairs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'issue_description' => 'required|string',
        ]);

        Repair::create($validated);
        Equipment::find($request->equipment_id)->update(['status' => 'in_service']);

        return redirect()->back()->with('success', 'Laporan kerusakan berhasil dikirim.');
    }
}
