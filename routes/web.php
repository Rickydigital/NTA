<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ExamSessionController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgramLevelController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentExamNumberController;
use App\Http\Controllers\StudentLevelPlacementController;
use App\Http\Controllers\StudentProgramEnrollmentController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    //PROGRAMS

    Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');
    Route::put('/programs/{program}', [ProgramController::class, 'update'])->name('programs.update');
    Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])->name('programs.destroy');

    Route::get('/program-levels', [ProgramLevelController::class, 'index'])->name('program-levels.index');
    Route::post('/program-levels', [ProgramLevelController::class, 'store'])->name('program-levels.store');
    Route::put('/program-levels/{programLevel}', [ProgramLevelController::class, 'update'])->name('program-levels.update');
    Route::delete('/program-levels/{programLevel}', [ProgramLevelController::class, 'destroy'])->name('program-levels.destroy');

    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::put('/academic-years/{academicYear}', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::delete('/academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');

    Route::get('/exam-sessions', [ExamSessionController::class, 'index'])->name('exam-sessions.index');
    Route::post('/exam-sessions', [ExamSessionController::class, 'store'])->name('exam-sessions.store');
    Route::put('/exam-sessions/{examSession}', [ExamSessionController::class, 'update'])->name('exam-sessions.update');
    Route::delete('/exam-sessions/{examSession}', [ExamSessionController::class, 'destroy'])->name('exam-sessions.destroy');

    Route::get('/student-exam-numbers', [StudentExamNumberController::class, 'index'])->name('student-exam-numbers.index');
    Route::post('/student-exam-numbers', [StudentExamNumberController::class, 'store'])->name('student-exam-numbers.store');
    Route::put('/student-exam-numbers/{studentExamNumber}', [StudentExamNumberController::class, 'update'])->name('student-exam-numbers.update');
    Route::delete('/student-exam-numbers/{studentExamNumber}', [StudentExamNumberController::class, 'destroy'])->name('student-exam-numbers.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');

    Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
    Route::post('/grades', [GradeController::class, 'store'])->name('grades.store');
    Route::put('/grades/{grade}', [GradeController::class, 'update'])->name('grades.update');
    Route::delete('/grades/{grade}', [GradeController::class, 'destroy'])->name('grades.destroy');

    Route::get('/student-program-enrollments', [StudentProgramEnrollmentController::class, 'index'])->name('student-program-enrollments.index');
    Route::post('/student-program-enrollments', [StudentProgramEnrollmentController::class, 'store'])->name('student-program-enrollments.store');
    Route::put('/student-program-enrollments/{studentProgramEnrollment}', [StudentProgramEnrollmentController::class, 'update'])->name('student-program-enrollments.update');
    Route::delete('/student-program-enrollments/{studentProgramEnrollment}', [StudentProgramEnrollmentController::class, 'destroy'])->name('student-program-enrollments.destroy');

    Route::get('/student-level-placements', [StudentLevelPlacementController::class, 'index'])->name('student-level-placements.index');
    Route::post('/student-level-placements', [StudentLevelPlacementController::class, 'store'])->name('student-level-placements.store');
    Route::put('/student-level-placements/{studentLevelPlacement}', [StudentLevelPlacementController::class, 'update'])->name('student-level-placements.update');
    Route::delete('/student-level-placements/{studentLevelPlacement}', [StudentLevelPlacementController::class, 'destroy'])->name('student-level-placements.destroy');
});

require __DIR__.'/auth.php';
