<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            LevelSeeder::class,
            UserSeeder::class,
            ClassSeeder::class,
            SubjectSeeder::class,
            StudentSeeder::class,
            TeacherSeeder::class,
            ScheduleSeeder::class,
            GradeSeeder::class,
            AttendanceSeeder::class,
        ]);
    }
}