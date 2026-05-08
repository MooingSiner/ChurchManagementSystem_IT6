<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Administrator;
use App\Models\Superadmin;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $administrator = Administrator::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'username' => 'superadmin',
                'password' => Hash::make(env('DEFAULT_SUPERADMIN_PASSWORD', 'ChangeMe-SuperAdmin-2026!')),
                'role' => 'super_admin',
            ]
        );

        Superadmin::updateOrCreate(
            ['administrator_id' => $administrator->administrator_id],
            ['permission' => 'full system access']
        );
    }
}
