<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClearanceRequest;
use App\Models\Message;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function counts(Request $request)
    {
        $user = $request->user();

        // Unread messages — applies to everyone
        $unreadMessages = Message::whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->whereHas('conversation', fn ($q) => $q->where('counselor_id', $user->id)->orWhere('student_user_id', $user->id))
            ->count();

        if ($user->isStudent()) {
            $profile = $user->studentProfile;
            $appointments = $profile
                ? Appointment::where('student_profile_id', $profile->id)
                    ->where('appointment_date', '>=', today())
                    ->whereIn('status', ['confirmed'])->count()
                : 0;

            return response()->json([
                'total'           => $appointments + $unreadMessages,
                'appointments'    => $appointments,
                'clearance'       => 0,
                'referrals'       => 0,
                'unreadMessages'  => $unreadMessages,
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

        $total = $pendingReferrals + $todayAppointments + $pendingClearance + $unreadMessages;

        return response()->json(compact('total', 'pendingReferrals', 'todayAppointments', 'pendingClearance', 'unreadMessages'));
    }
}
