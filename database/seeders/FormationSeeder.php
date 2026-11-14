<?php

namespace Database\Seeders;

use App\Models\Formation;
use Illuminate\Database\Seeder;

class FormationSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Guru',
            'Tenaga Kesehatan',
            'Analis Kebijakan',
            'Pranata Komputer',
            'Auditor',
        ] as $name) {
            Formation::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
