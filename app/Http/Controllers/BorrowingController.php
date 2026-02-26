<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Equipment;
use App\Notifications\BorrowingNotification;
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

        $borrowing = Borrowing::create([
            'user_id' => auth()->id(),
            'equipment_id' => $equipment->id,
            'borrowed_at' => Carbon::now(),
            'expected_return_at' => $request->expected_return_at ?? Carbon::now()->addHours(24),
            'accessories_brought_json' => json_encode($request->accessories ?? []),
            'status' => 'active',
        ]);

        $equipment->update(['status' => 'borrowed']);

        // Kirim notifikasi ke user (Defensive check)
        if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
            auth()->user()->notify(new BorrowingNotification(
                'Peminjaman Berhasil',
                'Anda telah meminjam ' . $equipment->name,
                $equipment->name,
                'borrow'
            ));
        }

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

        // Kirim notifikasi ke user (Defensive check)
        if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
            auth()->user()->notify(new BorrowingNotification(
                'Pengembalian Berhasil',
                'Anda telah mengembalikan ' . $borrowing->equipment->name,
                $borrowing->equipment->name,
                'return'
            ));
        }

        return redirect()->route('dashboard')->with('success', 'Pengembalian berhasil dicatat.');
    }
}
