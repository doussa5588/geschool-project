<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $teacher = Role::create(['name' => 'teacher']);
        $student = Role::create(['name' => 'student']);

        // Create permissions
        $permissions = [
            // User Management
            'manage-users',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // Student Management
            'manage-students',
            'view-students',
            'create-students',
            'edit-students',
            'delete-students',
            
            // Teacher Management
            'manage-teachers',
            'view-teachers',
            'create-teachers',
            'edit-teachers',
            'delete-teachers',
            
            // Subject Management
            'manage-subjects',
            'view-subjects',
            'create-subjects',
            'edit-subjects',
            'delete-subjects',
            
            // Class Management
            'manage-classes',
            'view-classes',
            'create-classes',
            'edit-classes',
            'delete-classes',
            
            // Schedule Management
            'manage-schedules',
            'view-schedules',
            'create-schedules',
            'edit-schedules',
            'delete-schedules',
            
            // Grade Management
            'manage-grades',
            'view-grades',
            'create-grades',
            'edit-grades',
            'delete-grades',
            'view-own-grades',
            
            // Attendance Management
            'manage-attendance',
            'view-attendance',
            'mark-attendance',
            'view-own-attendance',
            
            // Deliberation Management
            'manage-deliberations',
            'view-deliberations',
            'create-deliberations',
            'validate-deliberations',
            
            // Report Management
            'view-reports',
            'generate-reports',
            'export-reports',
            
            // Message Management
            'send-messages',
            'receive-messages',
            'manage-messages',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $admin->givePermissionTo($permissions);
        
        $teacher->givePermissionTo([
            'view-students',
            'manage-grades',
            'view-grades',
            'create-grades',
            'edit-grades',
            'delete-grades',
            'manage-attendance',
            'view-attendance',
            'mark-attendance',
            'view-schedules',
            'send-messages',
            'receive-messages',
            'view-subjects',
        ]);
        
        $student->givePermissionTo([
            'view-own-grades',
            'view-own-attendance',
            'view-schedules',
            'send-messages',
            'receive-messages',
        ]);
    }
}