<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('role', 'admin')->exists()) {
            User::create([
                'name'     => 'Admin',
                'email'    => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'status'   => true,
            ]);
            $this->command->info('Admin created: admin@admin.com / admin123');
        } else {
            $this->command->info('Admin already exists — skipped.');
        }
    }
}
