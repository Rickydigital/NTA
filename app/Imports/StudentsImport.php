<?php

namespace App\Imports;

use App\Models\ProgramLevel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public function __construct(private bool $createAccounts = false)
    {
    }

    public function collection(Collection $rows): void
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $programId = $this->value($row, 'program_id');
                $programLevelId = $this->value($row, 'program_level_id');
                $regNo = strtoupper(trim((string) $this->value($row, 'reg_no')));

                if (!$programId || !$programLevelId || !$regNo) {
                    continue;
                }

                $this->ensureLevelBelongsToProgram((int) $programId, (int) $programLevelId);

                $student = Student::updateOrCreate(
                    ['reg_no' => $regNo],
                    [
                        'program_id' => (int) $programId,
                        'program_level_id' => (int) $programLevelId,
                        'first_name' => trim((string) $this->value($row, 'first_name')),
                        'second_name' => $this->nullableString($this->value($row, 'second_name')),
                        'last_name' => trim((string) $this->value($row, 'last_name')),
                        'gender' => $this->nullableString($this->value($row, 'gender')),
                        'date_of_birth' => $this->nullableString($this->value($row, 'date_of_birth')),
                        'phone_no' => $this->nullableString($this->value($row, 'phone_no')),
                        'email' => $this->nullableString($this->value($row, 'email')),
                    ]
                );

                if ($this->createAccounts) {
                    $this->createOrUpdateStudentUser($student);
                }
            }
        });
    }

    private function createOrUpdateStudentUser(Student $student): User
    {
        $username = strtoupper(trim($student->reg_no));
        $email = $student->email ? trim($student->email) : null;

        $user = User::firstOrNew(['student_id' => $student->id]);
        $user->first_name = $student->first_name;
        $user->second_name = $student->second_name;
        $user->last_name = $student->last_name;
        $user->gender = $student->gender;
        $user->phone_no = $student->phone_no;
        $user->username = $username;
        $user->email = $email;
        $user->is_active = true;

        if (!$user->exists) {
            $user->password = Hash::make($username);
        }

        $user->save();
        $user->syncRoles(['Student']);

        return $user;
    }

    private function ensureLevelBelongsToProgram(int $programId, int $programLevelId): void
    {
        $valid = ProgramLevel::where('id', $programLevelId)
            ->where('program_id', $programId)
            ->exists();

        if (!$valid) {
            throw new \RuntimeException("Program level {$programLevelId} does not belong to program {$programId}.");
        }
    }

    private function value($row, string $key): mixed
    {
        return $row[$key] ?? null;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = is_null($value) ? null : trim((string) $value);
        return $value === '' ? null : $value;
    }
}