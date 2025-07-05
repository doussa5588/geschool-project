<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\Classe;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $users = User::role('student')->get();
        $classes = Classe::all();
        $currentYear = date('Y');
        $academicYear = $currentYear . '-' . ($currentYear + 1);

        foreach ($users as $index => $user) {
            // Assigner une classe aléatoire
            $class = $classes->random();
            
            Student::create([
                'user_id' => $user->id,
                'student_number' => 'STU' . date('Y') . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'class_id' => $class->id,
                'academic_year' => $academicYear,
                'enrollment_date' => '2024-09-15',
                'status' => 'active',
                'parent_contact' => '+221 77 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                'emergency_contact' => '+221 76 ' . rand(100, 999) . ' ' . rand(1000, 9999),
            ]);
        }

        // Créer quelques étudiants supplémentaires pour une meilleure distribution
        $additionalStudents = [
            'Fatima Mbengue',
            'Modou Diagne',
            'Rama Thiaw',
            'Babacar Seck',
            'Astou Badji',
            'Mouhamadou Sy',
            'Khadija Fall',
            'Serigne Diouf',
            'Mame Diarra',
            'Ibou Samb',
        ];

        foreach ($additionalStudents as $index => $name) {
            $user = User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . rand(100, 999) . '@unchk.edu',
                'password' => bcrypt('password'),
                'phone' => '+221 78 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                'address' => 'Dakar, Sénégal',
                'date_of_birth' => '2000-' . rand(1, 12) . '-' . rand(1, 28),
                'is_active' => true,
            ]);
            $user->assignRole('student');

            $class = $classes->random();
            
            Student::create([
                'user_id' => $user->id,
                'student_number' => 'STU' . date('Y') . str_pad(1000 + $index, 4, '0', STR_PAD_LEFT),
                'class_id' => $class->id,
                'academic_year' => $academicYear,
                'enrollment_date' => '2024-09-15',
                'status' => 'active',
                'parent_contact' => '+221 77 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                'emergency_contact' => '+221 76 ' . rand(100, 999) . ' ' . rand(1000, 9999),
            ]);
        }
    }
}