<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Administrateur UNCHK',
            'email' => 'admin@unchk.edu',
            'password' => Hash::make('password'),
            'phone' => '+221 33 123 45 67',
            'address' => 'Université Numérique Cheikh Hamidou Kane, Dakar',
            'date_of_birth' => '1980-01-15',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create Sample Teachers
        $teachers = [
            [
                'name' => 'Dr. Amadou Diallo',
                'email' => 'amadou.diallo@unchk.edu',
                'specialization' => 'Intelligence Artificielle',
                'department' => 'IA',
            ],
            [
                'name' => 'Prof. Fatou Sall',
                'email' => 'fatou.sall@unchk.edu',
                'specialization' => 'Génie Logiciel',
                'department' => 'GL',
            ],
            [
                'name' => 'Dr. Ousmane Ba',
                'email' => 'ousmane.ba@unchk.edu',
                'specialization' => 'Réseaux et Télécommunications',
                'department' => 'TELECOM',
            ],
            [
                'name' => 'Mme. Aïssa Ndiaye',
                'email' => 'aissa.ndiaye@unchk.edu',
                'specialization' => 'Systèmes d\'Information',
                'department' => 'SI',
            ],
            [
                'name' => 'M. Ibrahima Fall',
                'email' => 'ibrahima.fall@unchk.edu',
                'specialization' => 'Cybersécurité',
                'department' => 'CYBER',
            ],
        ];

        foreach ($teachers as $index => $teacherData) {
            $user = User::create([
                'name' => $teacherData['name'],
                'email' => $teacherData['email'],
                'password' => Hash::make('password'),
                'phone' => '+221 77 ' . str_pad($index + 100, 3, '0', STR_PAD_LEFT) . ' ' . str_pad($index + 1000, 4, '0', STR_PAD_LEFT),
                'address' => 'Dakar, Sénégal',
                'date_of_birth' => '1975-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT) . '-15',
                'is_active' => true,
            ]);
            $user->assignRole('teacher');

            $department = Department::where('code', $teacherData['department'])->first();
            
            Teacher::create([
                'user_id' => $user->id,
                'employee_number' => 'EMP' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'specialization' => $teacherData['specialization'],
                'hire_date' => '2020-09-01',
                'salary' => rand(800000, 1500000),
                'status' => 'active',
                'department_id' => $department->id,
            ]);
        }

        // Create Sample Students
        $students = [
            'Moussa Touré',
            'Awa Gueye',
            'Cheikh Sy',
            'Mariama Diop',
            'Abdou Kane',
            'Bineta Sarr',
            'Omar Cissé',
            'Khady Thiam',
            'Mamadou Diouf',
            'Ndeye Wade',
            'Alioune Mbaye',
            'Coumba Ba',
            'Saliou Niang',
            'Adama Faye',
            'Yacine Camara',
        ];

        foreach ($students as $index => $name) {
            $user = User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@unchk.edu',
                'password' => Hash::make('password'),
                'phone' => '+221 76 ' . str_pad($index + 200, 3, '0', STR_PAD_LEFT) . ' ' . str_pad($index + 2000, 4, '0', STR_PAD_LEFT),
                'address' => 'Dakar, Sénégal',
                'date_of_birth' => '2000-' . str_pad(($index % 12) + 1, 2, '0', STR_PAD_LEFT) . '-' . str_pad(($index % 28) + 1, 2, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);
            $user->assignRole('student');
        }

        // Create Demo User for each role
        $demoStudent = User::create([
            'name' => 'Étudiant Démo',
            'email' => 'student@unchk.edu',
            'password' => Hash::make('password'),
            'phone' => '+221 77 123 45 67',
            'address' => 'Dakar, Sénégal',
            'date_of_birth' => '2001-05-15',
            'is_active' => true,
        ]);
        $demoStudent->assignRole('student');

        $demoTeacher = User::create([
            'name' => 'Enseignant Démo',
            'email' => 'teacher@unchk.edu',
            'password' => Hash::make('password'),
            'phone' => '+221 77 987 65 43',
            'address' => 'Dakar, Sénégal',
            'date_of_birth' => '1985-03-20',
            'is_active' => true,
        ]);
        $demoTeacher->assignRole('teacher');

        // Create Teacher record for demo teacher
        Teacher::create([
            'user_id' => $demoTeacher->id,
            'employee_number' => 'DEMO001',
            'specialization' => 'Informatique Générale',
            'hire_date' => '2020-01-01',
            'salary' => 1000000,
            'status' => 'active',
            'department_id' => Department::first()->id,
        ]);
    }
}