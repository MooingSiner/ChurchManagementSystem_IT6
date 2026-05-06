<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSessionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('attendance_sessions')->upsert([
            [
                'attendance_session_id' => 7,
                'event_id' => 1,
                'administrator_id' => 1,
                'attendance_name' => 'Morning Attendance',
                'attendance_date' => '2026-05-05',
                'time_in_start' => '08:00:00',
                'time_out_end' => '10:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'attendance_session_id' => 8,
                'event_id' => 2,
                'administrator_id' => 1,
                'attendance_name' => 'Evening Attendance',
                'attendance_date' => '2026-05-07',
                'time_in_start' => '18:00:00',
                'time_out_end' => '19:30:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'attendance_session_id' => 9,
                'event_id' => 3,
                'administrator_id' => 2,
                'attendance_name' => 'Afternoon Attendance',
                'attendance_date' => '2026-05-10',
                'time_in_start' => '14:00:00',
                'time_out_end' => '17:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['attendance_session_id'], [
            'event_id',
            'administrator_id',
            'attendance_name',
            'attendance_date',
            'time_in_start',
            'time_out_end',
            'updated_at',
        ]);
    }
}
