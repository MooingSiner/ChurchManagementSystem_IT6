<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        Administrator::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'username' => 'superadmin',
                'password' => Hash::make(env('DEFAULT_SUPERADMIN_PASSWORD', 'ChangeMe-SuperAdmin-2026!')),
                'role' => 'super_admin',
            ]
        );
    }
}
