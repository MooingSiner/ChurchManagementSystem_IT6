<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ministry;

class MinistrySeeder extends Seeder
{
    public function run(): void
    {
        $ministries = [
            'Youth',
            'Choir',
            'Usher',
            'Prayer Team',
            'Media',
            'Children',
        ];

        foreach ($ministries as $name) {
            Ministry::updateOrCreate(['ministry_name' => $name]);
        }
    }
}
