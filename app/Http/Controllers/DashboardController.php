<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Equipment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $activeBorrowings = Borrowing::with(['user', 'equipment'])
            ->whereNull('returned_at')
            ->where('user_id', auth()->id())
            ->get();

        $today = Carbon::today();
        $upcomingSchedules = Schedule::with(['users', 'equipment'])
            ->whereDate('starts_at', $today)
            ->orWhere(function($query) {
                $query->where('starts_at', '>', Carbon::now())
                      ->where('starts_at', '<', Carbon::now()->today()->addDays(7));
            })
            ->orderBy('starts_at', 'asc')
            ->get();

        $stats = [
            'active_borrowings' => Borrowing::whereNull('returned_at')->count(),
            'available_equipment' => Equipment::where('status', 'available')->count(),
            'repair_equipment' => Equipment::where('status', 'in_service')->count(),
            'lost_equipment' => Equipment::where('status', 'lost')->count(),
        ];

        // Chart Data: Usage per Month (6 Months)
        $chartData = [
            'labels' => [],
            'values' => []
        ];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Borrowing::whereMonth('borrowed_at', $month->month)
                ->whereYear('borrowed_at', $month->year)
                ->count();
            
            $chartData['labels'][] = $month->format('M');
            $chartData['values'][] = $count;
        }

        return view('dashboard', compact('activeBorrowings', 'stats', 'upcomingSchedules', 'chartData'));
    }
}
