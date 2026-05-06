<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('events')->upsert([
            [
                'event_id' => 1,
                'event_name' => 'Sunday Worship Service',
                'start_date' => '2026-05-05',
                'end_date' => '2026-05-05',
                'start_time' => '08:00:00',
                'end_time' => '10:00:00',
                'description' => 'Weekly worship service',
                'status' => 'upcoming',
                'type_id' => 1,
                'administrator_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'event_id' => 2,
                'event_name' => 'Prayer Meeting',
                'start_date' => '2026-05-07',
                'end_date' => '2026-05-07',
                'start_time' => '18:00:00',
                'end_time' => '19:30:00',
                'description' => 'Evening prayer meeting',
                'status' => 'upcoming',
                'type_id' => 2,
                'administrator_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'event_id' => 3,
                'event_name' => 'Youth Fellowship',
                'start_date' => '2026-05-10',
                'end_date' => '2026-05-10',
                'start_time' => '14:00:00',
                'end_time' => '17:00:00',
                'description' => 'Youth gathering activity',
                'status' => 'upcoming',
                'type_id' => 3,
                'administrator_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'event_id' => 4,
                'event_name' => 'Bible Study Session',
                'start_date' => '2026-05-12',
                'end_date' => '2026-05-12',
                'start_time' => '17:00:00',
                'end_time' => '18:30:00',
                'description' => 'Bible sharing and discussion',
                'status' => 'upcoming',
                'type_id' => 5,
                'administrator_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'event_id' => 5,
                'event_name' => 'Community Outreach',
                'start_date' => '2026-05-15',
                'end_date' => '2026-05-15',
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'description' => 'Barangay outreach program',
                'status' => 'upcoming',
                'type_id' => 6,
                'administrator_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['event_id'], [
            'event_name',
            'start_date',
            'end_date',
            'start_time',
            'end_time',
            'description',
            'status',
            'type_id',
            'administrator_id',
            'updated_at',
        ]);
    }
}
