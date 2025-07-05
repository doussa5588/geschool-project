<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    public function run()
    {
        $levels = [
            [
                'name' => 'Licence 1',
                'code' => 'L1',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Licence 2',
                'code' => 'L2',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Licence 3',
                'code' => 'L3',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Master 1',
                'code' => 'M1',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Master 2',
                'code' => 'M2',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($levels as $level) {
            Level::firstOrCreate($level);
        }
    }
}