<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClearanceRequest;
use App\Models\DisciplinaryRecord;
use App\Models\GoodMoralCertificate;
use App\Models\Referral;
use App\Models\StudentProfile;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboards.admin', [
            'totalStudents'     => StudentProfile::count(),
            'totalCounselors'   => User::where('role', 'guidance_counselor')->where('is_active', true)->count(),
            'totalFaculty'      => User::where('role', 'faculty')->where('is_active', true)->count(),
            'pendingReferrals'  => Referral::where('status', 'pending')->count(),
            'pendingClearance'  => ClearanceRequest::whereIn('status', ['pending','survey_done'])->count(),
            'todayAppointments' => Appointment::whereDate('appointment_date', today())->count(),
            'openDisciplinary'  => DisciplinaryRecord::whereIn('status', ['pending','under_review'])->count(),
            'certThisMonth'     => GoodMoralCertificate::whereMonth('issued_at', now()->month)
                                                        ->whereYear('issued_at', now()->year)->count(),
        ]);
    }

    public function director()
    {
        return view('dashboards.director', [
            'totalStudents'       => StudentProfile::count(),
            'totalAppointments'   => Appointment::whereYear('appointment_date', now()->year)->count(),
            'completedSessions'   => Appointment::where('status', 'completed')->whereYear('appointment_date', now()->year)->count(),
            'pendingReferrals'    => Referral::whereIn('status', ['pending','acknowledged'])->count(),
            'criticalReferrals'   => Referral::where('urgency', 'critical')->whereNotIn('status', ['resolved','closed'])->count(),
            'pendingClearance'    => ClearanceRequest::whereIn('status', ['pending','survey_done'])->count(),
            'certThisYear'        => GoodMoralCertificate::whereYear('issued_at', now()->year)->count(),
            'openDisciplinary'    => DisciplinaryRecord::whereIn('status', ['pending','under_review'])->count(),
        ]);
    }

    public function counselor(Request $request)
    {
        $counselorId = $request->user()->id;

        return view('dashboards.counselor', [
            'myToday'           => Appointment::where('counselor_id', $counselorId)
                                              ->whereDate('appointment_date', today())
                                              ->whereIn('status', ['confirmed','pending'])->count(),
            'myUpcoming'        => Appointment::where('counselor_id', $counselorId)
                                              ->where('appointment_date', '>=', today())
                                              ->whereIn('status', ['confirmed','pending'])->count(),
            'myReferrals'       => Referral::where('assigned_counselor_id', $counselorId)
                                           ->whereNotIn('status', ['resolved','closed'])->count(),
            'unassignedReferrals'=> Referral::whereNull('assigned_counselor_id')->where('status', 'pending')->count(),
            'myStudents'        => StudentProfile::where('assigned_counselor_id', $counselorId)->count(),
            'pendingClearance'  => ClearanceRequest::whereIn('status', ['pending','survey_done'])->count(),
            'openDisciplinary'  => DisciplinaryRecord::where('handled_by', $counselorId)
                                                     ->whereIn('status', ['pending','under_review'])->count(),
        ]);
    }

    public function student(Request $request)
    {
        $user    = $request->user();
        $profile = $user->studentProfile;

        $upcomingAppt   = null;
        $myResults      = 0;
        $myClearance    = 0;

        if ($profile) {
            $upcomingAppt = Appointment::where('student_profile_id', $profile->id)
                ->where('appointment_date', '>=', today())
                ->whereIn('status', ['pending','confirmed'])
                ->orderBy('appointment_date')->first();

            $myResults   = TestResult::where('student_profile_id', $profile->id)->where('is_released', true)->count();
            $myClearance = ClearanceRequest::where('student_profile_id', $profile->id)->count();
        }

        return view('dashboards.student', compact('profile', 'upcomingAppt', 'myResults', 'myClearance'));
    }

    public function faculty(Request $request)
    {
        $user = $request->user();

        return view('dashboards.faculty', [
            'myTotal'     => Referral::where('referred_by', $user->id)->count(),
            'myPending'   => Referral::where('referred_by', $user->id)->where('status', 'pending')->count(),
            'myActive'    => Referral::where('referred_by', $user->id)->whereIn('status', ['acknowledged','in_progress'])->count(),
            'myResolved'  => Referral::where('referred_by', $user->id)->whereIn('status', ['resolved','closed'])->count(),
            'recentReferrals' => Referral::where('referred_by', $user->id)->with('studentProfile')->latest()->take(5)->get(),
        ]);
    }
}
