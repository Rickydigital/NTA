<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamResultController;
use App\Http\Controllers\ExamResultGenerationController;
use App\Http\Controllers\ExamSessionController;
use App\Http\Controllers\GpaClassificationController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgramLevelController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgressionExecutionController;
use App\Http\Controllers\ProgressionRuleController;
use App\Http\Controllers\ResultApprovalController;
use App\Http\Controllers\ResultPublicationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentAccountController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentCourseResultController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentExamNumberController;
use App\Http\Controllers\StudentLevelPlacementController;
use App\Http\Controllers\StudentProgramEnrollmentController;
use App\Http\Controllers\StudentResultPortalController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

 Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['auth', 'verified']);

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

    Route::post('/students/{student}/portal-account', [StudentAccountController::class, 'createOrUpdateAccount'])->name('students.portal-account');
    Route::post('/students/{student}/reset-password', [StudentAccountController::class, 'resetPassword'])->name('students.reset-password');
    Route::post('/students/bulk-reset-passwords', [StudentAccountController::class, 'bulkResetPasswords'])->name('students.bulk-reset-passwords');
    Route::get('/student-portal/dashboard', [StudentDashboardController::class, 'index'])->name('student.portal.dashboard');

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

    Route::get('/gpa-classifications', [GpaClassificationController::class, 'index'])->name('gpa-classifications.index');
    Route::post('/gpa-classifications', [GpaClassificationController::class, 'store'])->name('gpa-classifications.store');
    Route::put('/gpa-classifications/{gpaClassification}', [GpaClassificationController::class, 'update'])->name('gpa-classifications.update');
    Route::delete('/gpa-classifications/{gpaClassification}', [GpaClassificationController::class, 'destroy'])->name('gpa-classifications.destroy');

    Route::get('/course-results', [StudentCourseResultController::class, 'index'])->name('course-results.index');
    Route::post('/course-results', [StudentCourseResultController::class, 'store'])->name('course-results.store');
    Route::put('/course-results/{courseResult}', [StudentCourseResultController::class, 'update'])->name('course-results.update');
    Route::delete('/course-results/{courseResult}', [StudentCourseResultController::class, 'destroy'])->name('course-results.destroy');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/students/template/download', [StudentController::class, 'downloadTemplate'])->name('students.template.download');

    Route::get('/exam-results', [ExamResultController::class, 'index'])->name('exam-results.index');
    Route::post('/exam-results', [ExamResultController::class, 'store'])->name('exam-results.store');
    Route::put('/exam-results/{examResult}', [ExamResultController::class, 'update'])->name('exam-results.update');
    Route::delete('/exam-results/{examResult}', [ExamResultController::class, 'destroy'])->name('exam-results.destroy');
    Route::post('/exam-results/generate', [ExamResultGenerationController::class, 'generate'])->name('exam-results.generate');
    Route::post('/course-results/{courseResult}/approve', [ResultApprovalController::class, 'approve'])->name('course-results.approve');
    Route::post('/course-results/{courseResult}/unapprove', [ResultApprovalController::class, 'unapprove'])->name('course-results.unapprove');
    Route::post('/exam-results/{examResult}/publish', [ResultPublicationController::class, 'publish'])->name('exam-results.publish');
    Route::post('/exam-results/{examResult}/unpublish', [ResultPublicationController::class, 'unpublish'])->name('exam-results.unpublish');

    Route::get('/progression-rules', [ProgressionRuleController::class, 'index'])->name('progression-rules.index');
    Route::post('/progression-rules', [ProgressionRuleController::class, 'store'])->name('progression-rules.store');
    Route::put('/progression-rules/{progressionRule}', [ProgressionRuleController::class, 'update'])->name('progression-rules.update');
    Route::delete('/progression-rules/{progressionRule}', [ProgressionRuleController::class, 'destroy'])->name('progression-rules.destroy');
    Route::post('/exam-results/{examResult}/execute-progression', [ProgressionExecutionController::class, 'execute'])->name('exam-results.execute-progression');

    Route::get('/student-portal/results', [StudentResultPortalController::class, 'index'])->name('student.portal.results.index');
    Route::get('/student-portal/results/{examResult}', [StudentResultPortalController::class, 'show'])->name('student.portal.results.show');
});

require __DIR__.'/auth.php';
