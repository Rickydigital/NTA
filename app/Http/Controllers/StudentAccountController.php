<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:student.update')->only(
            'resetPassword',
            'bulkResetPasswords',
            'createOrUpdateAccount'
        );
    }

    public function createOrUpdateAccount(Student $student): RedirectResponse
    {
        DB::transaction(function () use ($student) {
            $user = User::firstOrNew(['student_id' => $student->id]);

            $user->first_name = $student->first_name;
            $user->second_name = $student->second_name;
            $user->last_name = $student->last_name;
            $user->gender = $student->gender;
            $user->phone_no = $student->phone_no;
            $user->username = strtoupper(trim($student->reg_no));
            $user->email = $student->email ? trim($student->email) : null;
            $user->is_active = true;

            if (!$user->exists) {
                $user->password = Hash::make(strtoupper(trim($student->reg_no)));
            }

            $user->save();
            $user->syncRoles(['Student']);
        });

        return redirect()
            ->route('students.index')
            ->with('success', 'Student portal account created or updated successfully.');
    }

    public function resetPassword(Student $student): RedirectResponse
    {
        $user = $student->user;

        if (!$user) {
            return redirect()
                ->route('students.index')
                ->with('error', 'This student does not have a portal account yet.');
        }

        $user->update([
            'password' => Hash::make(strtoupper(trim($student->reg_no))),
            'is_active' => true,
        ]);

        return redirect()
            ->route('students.index')
            ->with('success', 'Student password reset successfully to registration number.');
    }

    public function bulkResetPasswords(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
        ]);

        $count = 0;

        DB::transaction(function () use ($validated, &$count) {
            $students = Student::with('user')
                ->whereIn('id', $validated['student_ids'])
                ->get();

            foreach ($students as $student) {
                if (!$student->user) {
                    continue;
                }

                $student->user->update([
                    'password' => Hash::make(strtoupper(trim($student->reg_no))),
                    'is_active' => true,
                ]);

                $count++;
            }
        });

        return redirect()
            ->route('students.index')
            ->with('success', "{$count} student account password(s) reset to registration number.");
    }
}