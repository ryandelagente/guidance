<?php

use App\Http\Controllers\ActionPlanController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AnonymousConcernController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CalendarFeedController;
use App\Http\Controllers\CaseloadController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\RiasecController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SessionFeedbackController;
use App\Http\Controllers\WalkInQueueController;
use App\Http\Controllers\WorkshopController;
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
use App\Http\Controllers\WellnessCheckinController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// ── Public iCal feed (token-authenticated, no login) ──
Route::get('/calendar/{token}.ics', [CalendarFeedController::class, 'feed'])->name('calendar.feed');

// ── SSO (Google + Microsoft) — public ──
Route::get('/sso/{provider}',          [\App\Http\Controllers\Auth\SsoController::class, 'redirect'])->name('sso.redirect');
Route::get('/sso/{provider}/callback', [\App\Http\Controllers\Auth\SsoController::class, 'callback'])->name('sso.callback');

// ── Two-Factor Auth Challenge (between login + dashboard) ──
Route::middleware('guest')->group(function () {
    Route::get('/two-factor/challenge',  [\App\Http\Controllers\TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
    Route::post('/two-factor/challenge', [\App\Http\Controllers\TwoFactorController::class, 'verify'])->name('two-factor.verify');
});

// ── 2FA settings (logged-in users) ──
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/two-factor',                  [\App\Http\Controllers\TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::get('/two-factor/setup',            [\App\Http\Controllers\TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('/two-factor/setup',           [\App\Http\Controllers\TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/disable',         [\App\Http\Controllers\TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::post('/two-factor/recovery-codes',  [\App\Http\Controllers\TwoFactorController::class, 'regenerateCodes'])->name('two-factor.regenerate-codes');
});

// ── Public Anonymous Concern Submission (no login required) ──
Route::get('/concerns', [AnonymousConcernController::class, 'create'])->name('anonymous-concerns.create');
Route::post('/concerns', [AnonymousConcernController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('anonymous-concerns.store');
Route::get('/concerns/track', [AnonymousConcernController::class, 'track'])->name('anonymous-concerns.track');

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
    // Audit log viewer
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
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

// Student self-service profile
Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::get('/my-profile',                       [\App\Http\Controllers\StudentSelfServiceController::class, 'edit'])->name('my-profile.edit');
    Route::put('/my-profile',                       [\App\Http\Controllers\StudentSelfServiceController::class, 'update'])->name('my-profile.update');
    Route::post('/my-profile/contacts',             [\App\Http\Controllers\StudentSelfServiceController::class, 'storeContact'])->name('my-profile.contacts.store');
    Route::delete('/my-profile/contacts/{contact}', [\App\Http\Controllers\StudentSelfServiceController::class, 'deleteContact'])->name('my-profile.contacts.delete');

    // Data privacy / data subject rights (RA 10173)
    Route::get('/my-data',                  [\App\Http\Controllers\DataPrivacyController::class, 'index'])->name('data-privacy.index');
    Route::get('/my-data/download',         [\App\Http\Controllers\DataPrivacyController::class, 'download'])->name('data-privacy.download');
    Route::post('/my-data/correction',      [\App\Http\Controllers\DataPrivacyController::class, 'requestCorrection'])->name('data-privacy.correction');
});

// Faculty / Staff
Route::middleware(['auth', 'verified', 'role:faculty'])->prefix('faculty')->name('faculty.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'faculty'])->name('dashboard');
});

// Caseload — counselors and above
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->group(function () {
        Route::get('caseload', [CaseloadController::class, 'index'])->name('caseload.index');
    });

// Student Profiles — counselors and above
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->group(function () {
        Route::resource('students', StudentProfileController::class);
        Route::get('students/{student}/timeline', [StudentProfileController::class, 'timeline'])->name('students.timeline');
        Route::get('students/{student}/cumulative-record', [StudentProfileController::class, 'cumulativeRecord'])->name('students.cumulative-record');
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
    Route::get('/search/quick', [SearchController::class, 'quick'])->name('search.quick');
});

// Announcements — staff create/edit/delete; everyone reads
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('announcements', AnnouncementController::class);
});

// Mental Health Screening (PHQ-9 / GAD-7 / K-10)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('screening',                  [\App\Http\Controllers\ScreeningController::class, 'index'])->name('screening.index');
    Route::get('screening/start/{instrument}',[\App\Http\Controllers\ScreeningController::class, 'start'])->name('screening.start');
    Route::post('screening/start/{instrument}',[\App\Http\Controllers\ScreeningController::class, 'store'])->name('screening.store');
    Route::get('screening/{screening}',      [\App\Http\Controllers\ScreeningController::class, 'show'])->name('screening.show');
    Route::patch('screening/{screening}/review',[\App\Http\Controllers\ScreeningController::class, 'review'])->name('screening.review');
});

// Group Counseling Sessions — staff only
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])->group(function () {
    Route::get('group-sessions',                  [\App\Http\Controllers\GroupSessionController::class, 'index'])->name('group-sessions.index');
    Route::get('group-sessions/create',           [\App\Http\Controllers\GroupSessionController::class, 'create'])->name('group-sessions.create');
    Route::post('group-sessions',                 [\App\Http\Controllers\GroupSessionController::class, 'store'])->name('group-sessions.store');
    Route::get('group-sessions/{groupSession}',   [\App\Http\Controllers\GroupSessionController::class, 'show'])->name('group-sessions.show');
    Route::patch('group-sessions/{groupSession}', [\App\Http\Controllers\GroupSessionController::class, 'update'])->name('group-sessions.update');
    Route::delete('group-sessions/{groupSession}',[\App\Http\Controllers\GroupSessionController::class, 'destroy'])->name('group-sessions.destroy');
    Route::post('group-sessions/{groupSession}/participants',  [\App\Http\Controllers\GroupSessionController::class, 'addParticipant'])->name('group-sessions.add-participant');
    Route::patch('group-sessions/{groupSession}/participants/{participant}', [\App\Http\Controllers\GroupSessionController::class, 'updateAttendance'])->name('group-sessions.attendance');
});

// Notification Preferences (everyone)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('notification-preferences',  [\App\Http\Controllers\NotificationPreferencesController::class, 'edit'])->name('notification-preferences.edit');
    Route::patch('notification-preferences',[\App\Http\Controllers\NotificationPreferencesController::class, 'update'])->name('notification-preferences.update');
});

// Wellness Check-ins — students submit; staff monitor
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('wellness', [WellnessCheckinController::class, 'index'])->name('wellness.index');
    Route::get('wellness/create', [WellnessCheckinController::class, 'create'])->name('wellness.create');
    Route::post('wellness', [WellnessCheckinController::class, 'store'])->name('wellness.store');
    Route::get('wellness/{wellness}', [WellnessCheckinController::class, 'show'])->name('wellness.show');
    Route::patch('wellness/{wellness}/review', [WellnessCheckinController::class, 'review'])->name('wellness.review');
});

// Resource Library — staff manage; everyone reads
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('resources', ResourceController::class);
});

// Action Plans — staff manage; students view own
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('action-plans', ActionPlanController::class);
    Route::post('action-plans/{actionPlan}/milestones', [ActionPlanController::class, 'addMilestone'])->name('action-plans.milestones.store');
    Route::patch('action-plans/{actionPlan}/milestones/{milestone}/toggle', [ActionPlanController::class, 'toggleMilestone'])->name('action-plans.milestones.toggle');
    Route::delete('action-plans/{actionPlan}/milestones/{milestone}', [ActionPlanController::class, 'deleteMilestone'])->name('action-plans.milestones.delete');
});

// Session Feedback — students submit, staff view
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('session-feedback', [SessionFeedbackController::class, 'index'])->name('session-feedback.index');
    Route::get('session-feedback/{sessionFeedback}', [SessionFeedbackController::class, 'show'])->name('session-feedback.show');
    Route::get('sessions/{session}/feedback', [SessionFeedbackController::class, 'create'])->name('session-feedback.create');
    Route::post('sessions/{session}/feedback', [SessionFeedbackController::class, 'store'])->name('session-feedback.store');
});

// Walk-in Queue — staff only
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])->group(function () {
    Route::get('walk-in', [WalkInQueueController::class, 'index'])->name('walk-in.index');
    Route::post('walk-in', [WalkInQueueController::class, 'store'])->name('walk-in.store');
    Route::patch('walk-in/{walkInQueue}/call', [WalkInQueueController::class, 'call'])->name('walk-in.call');
    Route::patch('walk-in/{walkInQueue}/complete', [WalkInQueueController::class, 'complete'])->name('walk-in.complete');
    Route::patch('walk-in/{walkInQueue}/no-show', [WalkInQueueController::class, 'noShow'])->name('walk-in.no-show');
    Route::delete('walk-in/{walkInQueue}', [WalkInQueueController::class, 'destroy'])->name('walk-in.destroy');
});

// Anonymous Concerns — staff dashboard
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])->group(function () {
    Route::get('admin/concerns', [AnonymousConcernController::class, 'index'])->name('anonymous-concerns.index');
    Route::get('admin/concerns/{anonymousConcern}', [AnonymousConcernController::class, 'show'])->name('anonymous-concerns.show');
    Route::patch('admin/concerns/{anonymousConcern}', [AnonymousConcernController::class, 'update'])->name('anonymous-concerns.update');
});

// Workshops — staff manage; everyone reads
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('workshops', WorkshopController::class);
    Route::post('workshops/{workshop}/rsvp', [WorkshopController::class, 'rsvp'])->name('workshops.rsvp');
    Route::delete('workshops/{workshop}/rsvp', [WorkshopController::class, 'cancelRsvp'])->name('workshops.rsvp.cancel');
    Route::patch('workshops/{workshop}/rsvps/{rsvp}/attended', [WorkshopController::class, 'markAttended'])->name('workshops.attended');
});

// RIASEC Career Interest Inventory
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('riasec', [RiasecController::class, 'index'])->name('riasec.index');
    Route::get('riasec/create', [RiasecController::class, 'create'])->name('riasec.create');
    Route::post('riasec', [RiasecController::class, 'store'])->name('riasec.store');
    Route::get('riasec/{riasec}', [RiasecController::class, 'show'])->name('riasec.show');
});

// Messaging — counselors initiate; both parties can reply
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('messages/{conversation}/reply', [MessageController::class, 'reply'])->name('messages.reply');
});

// Appointments — students can book; counselors/admin manage
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('appointments', AppointmentController::class);
    Route::get('appointments/slots', [AppointmentController::class, 'slots'])->name('appointments.slots');
});

// Case-Note PIN setup + verify (NOT PIN-gated themselves)
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->group(function () {
        Route::get('case-note-pin/setup',   [\App\Http\Controllers\CaseNotePinController::class, 'setup'])->name('case-note-pin.setup');
        Route::post('case-note-pin/setup',  [\App\Http\Controllers\CaseNotePinController::class, 'store'])->name('case-note-pin.store');
        Route::get('case-note-pin/verify',  [\App\Http\Controllers\CaseNotePinController::class, 'verify'])->name('case-note-pin.verify');
        Route::post('case-note-pin/verify', [\App\Http\Controllers\CaseNotePinController::class, 'check'])->name('case-note-pin.check');
        Route::post('case-note-pin/lock',   [\App\Http\Controllers\CaseNotePinController::class, 'lock'])->name('case-note-pin.lock');
    });

// Case Notes / Counseling Sessions — PIN-gated for confidentiality
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin', 'case_note_pin'])
    ->group(function () {
        Route::resource('sessions', CounselingSessionController::class);
    });

// Counselor Schedules — counselors manage own; admin manages all
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->group(function () {
        Route::resource('schedules', CounselorScheduleController::class)->except(['show', 'edit']);
        Route::get('schedule-matrix', [\App\Http\Controllers\ScheduleMatrixController::class, 'index'])->name('schedule-matrix.index');
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

// Counselor Performance Dashboard — staff
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->group(function () {
        Route::get('performance', [PerformanceController::class, 'index'])->name('performance.index');
    });

// Calendar Feed (iCal) settings — staff manage their own subscription
Route::middleware(['auth', 'verified', 'role:guidance_counselor,guidance_director,super_admin'])
    ->group(function () {
        Route::get('calendar-feed', [CalendarFeedController::class, 'settings'])->name('calendar-feed.settings');
        Route::post('calendar-feed/regenerate', [CalendarFeedController::class, 'regenerate'])->name('calendar-feed.regenerate');
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

// Help / User Guide (all authenticated users)
Route::middleware(['auth', 'verified'])->get('/help', [HelpController::class, 'index'])->name('help.index');

// Profile (all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
