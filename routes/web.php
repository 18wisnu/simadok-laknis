<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

    // Equipments
    Route::get('/equipments/print-qr', [EquipmentController::class, 'printQr'])->name('equipments.print-qr');
    Route::get('/equipments/export', [EquipmentController::class, 'export'])->name('equipments.export');
    Route::resource('equipments', EquipmentController::class);
    
<<<<<<< HEAD
<<<<<<< Updated upstream
=======
=======
>>>>>>> fitur-google-login
    // Temporary Fix Route - Remove after use
    Route::get('/fix-db', function() {
        try {
            // 1. Add equipment_id to schedules if missing
            if (!Schema::hasColumn('schedules', 'equipment_id')) {
                Schema::table('schedules', function (Blueprint $table) {
                    $table->foreignId('equipment_id')->nullable()->after('ends_at')->constrained('equipment')->nullOnDelete();
                });
                echo "Success: equipment_id added to schedules.<br>";
<<<<<<< HEAD
=======
            } else {
                echo "Info: equipment_id already exists in schedules.<br>";
>>>>>>> fitur-google-login
            }

            // 2. Make starts_at and ends_at nullable
            Schema::table('schedules', function (Blueprint $table) {
                $table->timestamp('starts_at')->nullable()->change();
                $table->timestamp('ends_at')->nullable()->change();
            });
            echo "Success: starts_at and ends_at are now nullable.<br>";

<<<<<<< HEAD
            // 3. Create notifications table if missing
            if (!Schema::hasTable('notifications')) {
                Schema::create('notifications', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->string('type');
                    $table->morphs('notifiable');
                    $table->text('data');
                    $table->timestamp('read_at')->nullable();
                    $table->timestamps();
                });
                echo "Success: notifications table created.<br>";
            } else {
                echo "Info: notifications table already exists.<br>";
            }

            // 4. Add whatsapp_notifications to users table if missing
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'whatsapp_notifications')) {
                \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->boolean('whatsapp_notifications')->default(false)->after('is_active');
                });
                echo "Success: whatsapp_notifications added to users.<br>";
            } else {
                echo "Info: whatsapp_notifications already exists in users.<br>";
            }

            // 5. Create settings table if missing
            if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                \Illuminate\Support\Facades\Schema::create('settings', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('key')->unique();
                    $table->text('value')->nullable();
                    $table->timestamps();
                });
                echo "Success: settings table created.<br>";
            } else {
                echo "Info: settings table already exists.<br>";
            }

=======
>>>>>>> fitur-google-login
            return "Database fix completed successfully!";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    });

<<<<<<< HEAD
>>>>>>> Stashed changes
=======
>>>>>>> fitur-google-login
    // Borrowings
    Route::post('/borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');
    Route::patch('/borrowings/{borrowing}/return', [BorrowingController::class, 'return'])->name('borrowings.return');

    // Schedules
    Route::get('/schedules/print', [ScheduleController::class, 'print'])->name('schedules.print');
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::patch('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::patch('/schedules/{schedule}/result', [ScheduleController::class, 'updateResult'])->name('schedules.result');

    // Repairs
    Route::resource('repairs', RepairController::class);

    // User Management (Superadmin Only)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.status');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
    Route::patch('/users/{user}/password', [UserController::class, 'resetPassword'])->name('users.password');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Notifications
    Route::patch('/notifications/{id}/read', function($id) {
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
        return back();
    })->name('notifications.read');

    // System Settings
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
?>
