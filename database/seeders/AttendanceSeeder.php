<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('attendances')->upsert([
            [
                'attendance_id' => 1,
                'member_id' => 1,
                'event_id' => 1,
                'administrator_id' => 1,
                'attendance_session_id' => 7,
                'attended_at' => $now,
                'time_in' => null,
                'time_out' => null,
                'status' => 'Present',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'attendance_id' => 2,
                'member_id' => 2,
                'event_id' => 2,
                'administrator_id' => 1,
                'attendance_session_id' => 8,
                'attended_at' => $now,
                'time_in' => null,
                'time_out' => null,
                'status' => 'Present',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'attendance_id' => 3,
                'member_id' => 3,
                'event_id' => 3,
                'administrator_id' => 2,
                'attendance_session_id' => 9,
                'attended_at' => $now,
                'time_in' => null,
                'time_out' => null,
                'status' => 'Pending',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['attendance_id'], [
            'member_id',
            'event_id',
            'administrator_id',
            'attendance_session_id',
            'attended_at',
            'time_in',
            'time_out',
            'status',
            'updated_at',
        ]);
    }
}
