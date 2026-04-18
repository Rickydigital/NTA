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

            // Dashboard
            'dashboard.view',

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

            // Academic Years
            'academic-year.view',
            'academic-year.create',
            'academic-year.update',
            'academic-year.delete',

            // Exam Sessions
            'exam-session.view',
            'exam-session.create',
            'exam-session.update',
            'exam-session.delete',

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

            // Result Entry
            'result-entry.view',
            'result-entry.create',
            'result-entry.update',
            'result-entry.delete',
            'result-entry.approve',

            // Result Summary
            'result-summary.view',
            'result-summary.generate',
            'result-summary.publish',

            // Progression Rules
            'progression-rule.view',
            'progression-rule.create',
            'progression-rule.update',
            'progression-rule.delete',

            // Progression Execution
            'progression.execute',

            // Reports
            'report.view',
            'report.generate',

            // Student Portal
            'student-portal.view',
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
            'dashboard.view',

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

            'academic-year.view',
            'academic-year.create',
            'academic-year.update',
            'academic-year.delete',

            'exam-session.view',
            'exam-session.create',
            'exam-session.update',
            'exam-session.delete',

            'grade.view',
            'grade.create',
            'grade.update',
            'grade.delete',

            'gpa-classification.view',
            'gpa-classification.create',
            'gpa-classification.update',
            'gpa-classification.delete',

            'progression-rule.view',
            'progression-rule.create',
            'progression-rule.update',
            'progression-rule.delete',

            'result-summary.view',
            'result-summary.generate',

            'report.view',
            'report.generate',
        ]);

        $examOfficer->syncPermissions([
            'dashboard.view',

            'exam-session.view',

            'result-entry.view',
            'result-entry.create',
            'result-entry.update',
            'result-entry.delete',
            'result-entry.approve',

            'result-summary.view',
            'result-summary.generate',
            'result-summary.publish',

            'progression.execute',

            'report.view',
            'report.generate',
        ]);

        $registrar->syncPermissions([
            'dashboard.view',

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
            'student-portal.view',
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

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}