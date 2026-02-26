<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrowing;
use App\Notifications\BorrowingNotification;
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
                $borrow->user->notify(new BorrowingNotification(
                    'Peringatan Terlambat!',
                    "Satu set alat {$borrow->equipment->name} belum dikembalikan sejak " . $borrow->expected_return_at->format('d/m/Y'),
                    $borrow->equipment->name,
                    'overdue'
                ));
                $this->info("Sent overdue notification to {$borrow->user->name}");
            }
        }

        // 2. Upcoming Return Reminders (Due today)
        $dueToday = Borrowing::whereNull('returned_at')
            ->whereDate('expected_return_at', $today)
            ->with(['user', 'equipment'])
            ->get();

        foreach ($dueToday as $borrow) {
            if ($borrow->user) {
                $borrow->user->notify(new BorrowingNotification(
                    'Jadwal Pengembalian',
                    "Hari ini adalah tenggat waktu pengembalian " . $borrow->equipment->name,
                    $borrow->equipment->name,
                    'due_today'
                ));
                $this->info("Sent due-today reminder to {$borrow->user->name}");
            }
        }
    }
}
