<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Administrator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RegularAdminSeeder extends Seeder
{
    public function run(): void
    {
        $administrator = Administrator::updateOrCreate(
            ['username' => 'admin'],
            [
                'username' => 'admin',
                'password' => Hash::make(env('DEFAULT_ADMIN_PASSWORD', 'ChangeMe-Admin-2026!')),
                'role' => 'admin',
            ]
        );

        Admin::updateOrCreate(
            ['administrator_id' => $administrator->administrator_id],
            ['permission' => 'dashboard, events, attendance, read-only members']
        );
    }
}
