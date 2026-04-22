<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClearanceRequestController;
use App\Http\Controllers\CounselingSessionController;
use App\Http\Controllers\CounselorScheduleController;
use App\Http\Controllers\DisciplinaryRecordController;
use App\Http\Controllers\ExitSurveyController;
use App\Http\Controllers\GoodMoralCertificateController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PsychologicalTestController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\TestResultController;
use App\Http\Controllers\TestScheduleController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// Central dashboard redirect — sends each role to their own dashboard
Route::get('/dashboard', function (Request $request) {
    return match ($request->user()->role) {
        'super_admin'        => redirect()->route('admin.dashboard'),
        'guidance_director'  => redirect()->route('director.dashboard'),
        'guidance_counselor' => redirect()->route('counselor.dashboard'),
        'faculty'            => redirect()->route('faculty.dashboard'),
        default              => redirect()->route('student.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Super Admin
Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    // User management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle', [UserManagementController::class, 'toggle'])->name('users.toggle');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});

// Guidance Director
Route::middleware(['auth', 'verified', 'role:guidance_director,super_admin'])->prefix('director')->name('director.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'director'])->name('dashboard');
});

// Guidance Counselor
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])->prefix('counselor')->name('counselor.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'counselor'])->name('dashboard');
});

// Student
Route::middleware(['auth', 'verified', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');
});

// Faculty / Staff
Route::middleware(['auth', 'verified', 'role:faculty'])->prefix('faculty')->name('faculty.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'faculty'])->name('dashboard');
});

// Student Profiles — counselors and above
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->group(function () {
        Route::resource('students', StudentProfileController::class);
        // Documents
        Route::post('students/{student}/documents', [StudentProfileController::class, 'uploadDocument'])->name('students.documents.upload');
        Route::get('students/{student}/documents/{document}/download', [StudentProfileController::class, 'downloadDocument'])->name('students.documents.download');
        Route::delete('students/{student}/documents/{document}', [StudentProfileController::class, 'deleteDocument'])->name('students.documents.delete');
        // Emergency contacts
        Route::post('students/{student}/emergency-contacts', [StudentProfileController::class, 'addEmergencyContact'])->name('students.emergency-contacts.store');
        Route::delete('students/{student}/emergency-contacts/{contact}', [StudentProfileController::class, 'deleteEmergencyContact'])->name('students.emergency-contacts.delete');
    });

// Notifications (all authenticated staff)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications/counts', [NotificationController::class, 'counts'])->name('notifications.counts');
});

// Appointments — students can book; counselors/admin manage
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('appointments', AppointmentController::class);
    Route::get('appointments/slots', [AppointmentController::class, 'slots'])->name('appointments.slots');
});

// Case Notes / Counseling Sessions — counselors and above only
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->group(function () {
        Route::resource('sessions', CounselingSessionController::class);
    });

// Counselor Schedules — counselors manage own; admin manages all
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->group(function () {
        Route::resource('schedules', CounselorScheduleController::class)->except(['show', 'edit']);
    });

// Referrals — faculty can submit; counselors/admin manage
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('referrals', ReferralController::class);
    Route::post('referrals/{referral}/interventions', [ReferralController::class, 'addIntervention'])
         ->name('referrals.interventions.store');
});

// Disciplinary Records — counselors and above
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->group(function () {
        Route::resource('disciplinary', DisciplinaryRecordController::class);
    });

// Psychological Testing — counselors and above manage; students view own released results
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->group(function () {
        Route::resource('psych-tests', PsychologicalTestController::class)->parameters(['psych-tests' => 'psychTest']);
        Route::resource('test-schedules', TestScheduleController::class)->parameters(['test-schedules' => 'testSchedule']);
    });

// Test Results — staff manage; students view own released results
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('test-results', TestResultController::class)->parameters(['test-results' => 'testResult']);
});

// Analytics & Reports — counselors and above
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/report', [AnalyticsController::class, 'report'])->name('report');
        Route::get('/export/students', [AnalyticsController::class, 'exportStudents'])->name('export.students');
        Route::get('/export/appointments', [AnalyticsController::class, 'exportAppointments'])->name('export.appointments');
        Route::get('/export/referrals', [AnalyticsController::class, 'exportReferrals'])->name('export.referrals');
        Route::get('/export/disciplinary', [AnalyticsController::class, 'exportDisciplinary'])->name('export.disciplinary');
    });

// Clearance Requests — students submit; staff process
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('clearance', ClearanceRequestController::class)->except(['edit']);
    Route::get('exit-survey/{clearance}', [ExitSurveyController::class, 'show'])->name('exit-survey.show');
    Route::post('exit-survey/{clearance}', [ExitSurveyController::class, 'store'])->name('exit-survey.store');
});

// Good Moral Certificates — counselors and above
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->group(function () {
        Route::resource('certificates', GoodMoralCertificateController::class)->except(['edit','update']);
        Route::get('certificates/{certificate}/print', [GoodMoralCertificateController::class, 'print'])->name('certificates.print');
        Route::get('certificates/{certificate}/download', [GoodMoralCertificateController::class, 'download'])->name('certificates.download');
        Route::patch('certificates/{certificate}/revoke', [GoodMoralCertificateController::class, 'revoke'])->name('certificates.revoke');
    });

// Profile (all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
