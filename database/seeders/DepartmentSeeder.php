<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            [
                'name' => 'Informatique et Réseaux',
                'code' => 'INFO',
                'description' => 'Département spécialisé dans les technologies de l\'information et des communications',
                'is_active' => true,
            ],
            [
                'name' => 'Génie Logiciel',
                'code' => 'GL',
                'description' => 'Département de développement et conception de logiciels',
                'is_active' => true,
            ],
            [
                'name' => 'Télécommunications',
                'code' => 'TELECOM',
                'description' => 'Département des systèmes de télécommunications et réseaux',
                'is_active' => true,
            ],
            [
                'name' => 'Systèmes d\'Information',
                'code' => 'SI',
                'description' => 'Département de gestion et analyse des systèmes d\'information',
                'is_active' => true,
            ],
            [
                'name' => 'Intelligence Artificielle',
                'code' => 'IA',
                'description' => 'Département spécialisé en intelligence artificielle et machine learning',
                'is_active' => true,
            ],
            [
                'name' => 'Cybersécurité',
                'code' => 'CYBER',
                'description' => 'Département de sécurité informatique et protection des données',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}