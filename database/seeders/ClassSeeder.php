<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;
use App\Models\Level;

class ClassSeeder extends Seeder
{
    public function run()
    {
        $levels = Level::all();
        $currentYear = date('Y');
        $academicYear = $currentYear . '-' . ($currentYear + 1);

        $classNames = ['A', 'B', 'C'];

        foreach ($levels as $level) {
            foreach ($classNames as $className) {
                Classe::create([
                    'name' => $level->code . $className,
                    'level_id' => $level->id,
                    'academic_year' => $academicYear,
                    'capacity' => 30,
                    'room' => 'Salle ' . $level->code . $className,
                    'is_active' => true,
                ]);
            }
        }

        // Classes spéciales pour certains niveaux
        $specialClasses = [
            [
                'name' => 'L3-INFO',
                'level_id' => Level::where('code', 'L3')->first()->id,
                'academic_year' => $academicYear,
                'capacity' => 25,
                'room' => 'Labo Informatique 1',
                'is_active' => true,
            ],
            [
                'name' => 'M1-IA',
                'level_id' => Level::where('code', 'M1')->first()->id,
                'academic_year' => $academicYear,
                'capacity' => 20,
                'room' => 'Labo IA',
                'is_active' => true,
            ],
            [
                'name' => 'M2-CYBER',
                'level_id' => Level::where('code', 'M2')->first()->id,
                'academic_year' => $academicYear,
                'capacity' => 15,
                'room' => 'Labo Sécurité',
                'is_active' => true,
            ],
        ];

        foreach ($specialClasses as $class) {
            Classe::create($class);
        }
    }
}