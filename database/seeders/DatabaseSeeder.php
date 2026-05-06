<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $this->call([
        TypeSeeder::class,
        MinistrySeeder::class,
        SuperAdminSeeder::class,
        RegularAdminSeeder::class,
        MemberSeeder::class,
        EventSeeder::class,
        AttendanceSessionSeeder::class,
        AttendanceSeeder::class,
    ]);
}
}
