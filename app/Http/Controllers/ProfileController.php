<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing;

class ProfileController extends Controller
{
    public function show()
    {
        $profileUser = auth()->user();
        
        $activeBorrowings = Borrowing::with('equipment')
            ->where('user_id', $profileUser->id)
            ->whereNull('returned_at')
            ->get();
            
        $mySchedules = $profileUser->schedules()
            ->with(['equipment'])
            ->whereMonth('starts_at', now()->month)
            ->whereYear('starts_at', now()->year)
            ->orderBy('starts_at', 'desc')
            ->paginate(10);
            
        return view('profile', compact('profileUser', 'activeBorrowings', 'mySchedules'));
    }
}
