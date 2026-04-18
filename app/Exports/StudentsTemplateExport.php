<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentsTemplateExport implements FromCollection
{
    public function collection(): Collection
    {
        return collect([
            [
                'program',
                'level',
                'reg_no',
                'first_name',
                'second_name',
                'last_name',
                'gender',
                'date_of_birth',
                'phone_no',
                'email',
            ],
            [
                'Diploma in Information Technology',
                'NTA 4',
                'DIT/2025/001',
                'John',
                'Mark',
                'Doe',
                'male',
                '2002-05-10',
                '0712345678',
                'john@example.com',
            ],
        ]);
    }
}