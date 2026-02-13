<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Equipment;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['users', 'equipment'])->orderBy('starts_at', 'asc')->get();
        $users = User::all();
        $equipments = Equipment::all();
        return view('schedules.index', compact('schedules', 'users', 'equipments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'equipment_id' => 'nullable|exists:equipment,id',
        ]);

        $schedule = Schedule::create($validated);
        $schedule->users()->sync($request->user_ids);

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function updateResult(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'result_status' => 'required|in:backed_up,moved,archived,success',
            'result_link' => 'nullable|url',
            'return_equipment' => 'nullable|boolean',
        ]);

        $schedule->update([
            'result_status' => $validated['result_status'],
            'result_link' => $validated['result_link'],
        ]);

        // Logic for returning equipment if requested and exists
        if ($request->boolean('return_equipment') && $schedule->equipment_id) {
            // Find active borrowing for this equipment (simple logic: find latest active)
            $borrowing = \App\Models\Borrowing::where('equipment_id', $schedule->equipment_id)
                ->where('status', 'active')
                ->first();

            if ($borrowing) {
                $borrowing->update([
                    'returned_at' => now(),
                    'status' => 'returned',
                    'condition_on_return' => 'good' // Defaulting to good for quick report
                ]);
                $borrowing->equipment->update(['status' => 'available']);
            }
        }

        return redirect()->back()->with('success', 'Laporan kegiatan berhasil diperbarui.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'equipment_id' => 'nullable|exists:equipment,id',
        ]);

        $schedule->update($validated);
        $schedule->users()->sync($request->user_ids);

        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui');
    }

    public function destroy(Schedule $schedule)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $schedule->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus');
    }

    public function print(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $schedules = Schedule::with(['users', 'equipment'])
            ->whereMonth('starts_at', $month)
            ->whereYear('starts_at', $year)
            ->orderBy('starts_at', 'asc')
            ->get();

        return view('schedules.print', compact('schedules', 'month', 'year'));
    }
}
