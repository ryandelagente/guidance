<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClearanceRequest;
use App\Models\Referral;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function counts(Request $request)
    {
        $user = $request->user();

        if ($user->isStudent()) {
            $profile = $user->studentProfile;
            return response()->json([
                'total'        => 0,
                'appointments' => $profile
                    ? Appointment::where('student_profile_id', $profile->id)
                        ->where('appointment_date', '>=', today())
                        ->whereIn('status', ['confirmed'])->count()
                    : 0,
                'clearance'    => 0,
                'referrals'    => 0,
            ]);
        }

        $counselorId = $user->isCounselor() ? $user->id : null;

        $pendingReferrals = Referral::where('status', 'pending')
            ->when($counselorId, fn ($q) => $q->where(fn ($q2) =>
                $q2->where('assigned_counselor_id', $counselorId)->orWhereNull('assigned_counselor_id')
            ))
            ->count();

        $todayAppointments = Appointment::whereDate('appointment_date', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($counselorId, fn ($q) => $q->where('counselor_id', $counselorId))
            ->count();

        $pendingClearance = ClearanceRequest::whereIn('status', ['pending', 'survey_done'])->count();

        $total = $pendingReferrals + $todayAppointments + $pendingClearance;

        return response()->json(compact('total', 'pendingReferrals', 'todayAppointments', 'pendingClearance'));
    }
}
