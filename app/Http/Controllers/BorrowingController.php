<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'expected_return_at' => 'nullable|date|after:now',
            'accessories' => 'array',
        ]);

        $equipment = Equipment::findOrFail($request->equipment_id);
        
        if ($equipment->status !== 'available') {
            return redirect()->back()->with('error', 'Alat sedang tidak tersedia.');
        }

        Borrowing::create([
            'user_id' => auth()->id(),
            'equipment_id' => $equipment->id,
            'borrowed_at' => Carbon::now(),
            'expected_return_at' => $request->expected_return_at ?? Carbon::now()->addHours(24),
            'accessories_brought_json' => json_encode($request->accessories ?? []),
            'status' => 'active',
        ]);

        $equipment->update(['status' => 'borrowed']);

        return redirect()->route('dashboard')->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function return(Request $request, Borrowing $borrowing)
    {
        $request->validate([
            'condition_on_return' => 'required|in:good,damaged',
        ]);

        $borrowing->update([
            'returned_at' => Carbon::now(),
            'status' => 'returned',
            'condition_on_return' => $request->condition_on_return,
        ]);

        $status = $request->condition_on_return === 'damaged' ? 'damaged' : 'available';
        $borrowing->equipment->update(['status' => $status]);

        return redirect()->route('dashboard')->with('success', 'Pengembalian berhasil dicatat.');
    }
}
