<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send system reminders for borrowings and return due dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        // 1. Overdue Notifications
        $overdue = Borrowing::whereNull('returned_at')
            ->where('expected_return_at', '<', $today)
            ->with(['user', 'equipment'])
            ->get();

        foreach ($overdue as $borrow) {
            if ($borrow->user) {
                $message = "SYSTEM NOTIF (Overdue): User {$borrow->user->name} has not returned {$borrow->equipment->name} (Due: {$borrow->expected_return_at->format('d/m/Y')})";
                Log::channel('single')->info($message);
                $this->info("Logged overdue notification for {$borrow->user->name}");
            }
        }

        // 2. Upcoming Return Reminders (Due today)
        $dueToday = Borrowing::whereNull('returned_at')
            ->whereDate('expected_return_at', $today)
            ->with(['user', 'equipment'])
            ->get();

        foreach ($dueToday as $borrow) {
            if ($borrow->user) {
                $message = "SYSTEM NOTIF (Due Today): User {$borrow->user->name} should return {$borrow->equipment->name} today";
                Log::channel('single')->info($message);
                $this->info("Logged due-today reminder for {$borrow->user->name}");
            }
        }
    }
}
