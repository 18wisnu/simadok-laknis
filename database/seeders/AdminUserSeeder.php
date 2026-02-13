<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.net'],
            [
                'name' => 'Super Admin',
                'password' => 'admin', // Model cast 'hashed' will handle this
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );
    }
}
