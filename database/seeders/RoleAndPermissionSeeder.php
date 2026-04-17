<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [

            // Users
            'user.view',
            'user.create',
            'user.update',
            'user.delete',

            // Roles
            'role.view',
            'role.create',
            'role.update',
            'role.delete',

            // Permissions
            'permission.view',

            // Programs
            'program.view',
            'program.create',
            'program.update',
            'program.delete',

            // Program Levels
            'program-level.view',
            'program-level.create',
            'program-level.update',
            'program-level.delete',

            // Courses
            'course.view',
            'course.create',
            'course.update',
            'course.delete',

            // Students
            'student.view',
            'student.create',
            'student.update',
            'student.delete',

            // Exam Numbers
            'exam-number.view',
            'exam-number.create',
            'exam-number.update',
            'exam-number.delete',

            // Grades
            'grade.view',
            'grade.create',
            'grade.update',
            'grade.delete',

            // GPA Classifications
            'gpa-classification.view',
            'gpa-classification.create',
            'gpa-classification.update',
            'gpa-classification.delete',

            // Results Entry
            'result-entry.view',
            'result-entry.create',
            'result-entry.update',
            'result-entry.delete',
            'result-entry.approve',

            // Results Summary
            'result-summary.view',
            'result-summary.generate',
            'result-summary.publish',

            // Progression
            'progression-rule.view',
            'progression-rule.create',
            'progression-rule.update',
            'progression-rule.delete',
            'progression.execute',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $academicAdmin = Role::firstOrCreate(['name' => 'Academic Admin']);
        $examOfficer = Role::firstOrCreate(['name' => 'Examinations Officer']);
        $registrar = Role::firstOrCreate(['name' => 'Registrar']);
        $student = Role::firstOrCreate(['name' => 'Student']);

        $superAdmin->syncPermissions(Permission::all());

        $academicAdmin->syncPermissions([
            'program.view',
            'program.create',
            'program.update',
            'program.delete',

            'program-level.view',
            'program-level.create',
            'program-level.update',
            'program-level.delete',

            'course.view',
            'course.create',
            'course.update',
            'course.delete',

            'grade.view',
            'grade.create',
            'grade.update',
            'grade.delete',

            'gpa-classification.view',
            'gpa-classification.create',
            'gpa-classification.update',
            'gpa-classification.delete',

            'result-summary.view',
            'result-summary.generate',
        ]);

        $examOfficer->syncPermissions([
            'result-entry.view',
            'result-entry.create',
            'result-entry.update',
            'result-entry.delete',
            'result-entry.approve',

            'result-summary.view',
            'result-summary.generate',
            'result-summary.publish',
        ]);

        $registrar->syncPermissions([
            'student.view',
            'student.create',
            'student.update',
            'student.delete',

            'exam-number.view',
            'exam-number.create',
            'exam-number.update',
            'exam-number.delete',
        ]);

        $student->syncPermissions([
            'result-summary.view',
        ]);

        $user = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'first_name' => 'System',
                'second_name' => 'Super',
                'last_name' => 'Admin',
                'gender' => 'male',
                'phone_no' => '0700000000',
                'email' => 'admin@nta.test',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $user->syncRoles([$superAdmin]);
    }
}