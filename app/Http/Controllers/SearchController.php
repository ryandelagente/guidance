<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Appointment;
use App\Models\GoodMoralCertificate;
use App\Models\Referral;
use App\Models\Resource;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Live search JSON for the top-bar search.
     */
    public function quick(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['groups' => []]);
        }

        $user = $request->user();
        $like = '%' . $q . '%';
        $groups = [];

        // ── Students (staff only) ──
        if ($user->isStaff()) {
            $students = StudentProfile::where(function ($qb) use ($like) {
                $qb->where('first_name', 'like', $like)
                   ->orWhere('last_name', 'like', $like)
                   ->orWhere('student_id_number', 'like', $like)
                   ->orWhere('program', 'like', $like);
            })
            ->limit(5)
            ->get(['id','first_name','last_name','student_id_number','program','year_level']);

            if ($students->isNotEmpty()) {
                $groups[] = [
                    'label' => 'Students',
                    'icon'  => '👤',
                    'items' => $students->map(fn ($s) => [
                        'title'    => trim($s->first_name . ' ' . $s->last_name),
                        'subtitle' => trim(($s->student_id_number ?? 'No ID') . ' • ' . ($s->program ?? '')) . ' ' . ($s->year_level ?? ''),
                        'url'      => route('students.show', $s),
                    ])->all(),
                ];
            }
        }

        // ── Appointments ──
        $apptQuery = Appointment::with('studentProfile', 'counselor');
        if ($user->isStudent()) {
            $apptQuery->where('student_profile_id', $user->studentProfile?->id ?? 0);
        } elseif ($user->isCounselor()) {
            $apptQuery->where('counselor_id', $user->id);
        }
        $appts = $apptQuery->where(function ($qb) use ($like) {
            $qb->where('appointment_type', 'like', $like)
               ->orWhere('student_concern', 'like', $like)
               ->orWhereHas('studentProfile', fn ($s) => $s->where('first_name', 'like', $like)->orWhere('last_name', 'like', $like));
        })->latest('appointment_date')->limit(5)->get();

        if ($appts->isNotEmpty()) {
            $groups[] = [
                'label' => 'Appointments',
                'icon'  => '📅',
                'items' => $appts->map(fn ($a) => [
                    'title'    => $a->studentProfile?->full_name ?? 'Unknown',
                    'subtitle' => ucwords(str_replace('_', ' ', $a->appointment_type)) . ' • ' . $a->appointment_date->format('M d, Y') . ' • ' . ucfirst($a->status),
                    'url'      => route('appointments.show', $a),
                ])->all(),
            ];
        }

        // ── Referrals (staff/faculty) ──
        if (!$user->isStudent()) {
            $refQuery = Referral::with('studentProfile');
            if ($user->isFaculty()) {
                $refQuery->where('referred_by', $user->id);
            }
            $refs = $refQuery->where(function ($qb) use ($like) {
                $qb->where('description', 'like', $like)
                   ->orWhere('reason_category', 'like', $like)
                   ->orWhereHas('studentProfile', fn ($s) => $s->where('first_name', 'like', $like)->orWhere('last_name', 'like', $like));
            })->latest()->limit(5)->get();

            if ($refs->isNotEmpty()) {
                $groups[] = [
                    'label' => 'Referrals',
                    'icon'  => '🚩',
                    'items' => $refs->map(fn ($r) => [
                        'title'    => $r->studentProfile?->full_name ?? 'Unknown',
                        'subtitle' => ucwords(str_replace('_', ' ', $r->reason_category)) . ' • ' . ucfirst($r->urgency) . ' • ' . ucfirst($r->status),
                        'url'      => route('referrals.show', $r),
                    ])->all(),
                ];
            }
        }

        // ── Certificates (staff only) ──
        if ($user->isStaff()) {
            $certs = GoodMoralCertificate::with('studentProfile')
                ->where(function ($qb) use ($like) {
                    $qb->where('certificate_number', 'like', $like)
                       ->orWhere('purpose', 'like', $like)
                       ->orWhereHas('studentProfile', fn ($s) => $s->where('first_name', 'like', $like)->orWhere('last_name', 'like', $like));
                })->latest()->limit(5)->get();

            if ($certs->isNotEmpty()) {
                $groups[] = [
                    'label' => 'Certificates',
                    'icon'  => '🎓',
                    'items' => $certs->map(fn ($c) => [
                        'title'    => $c->certificate_number,
                        'subtitle' => ($c->studentProfile?->full_name ?? '') . ' • ' . $c->purpose,
                        'url'      => route('certificates.show', $c),
                    ])->all(),
                ];
            }
        }

        // ── Announcements ──
        $anns = Announcement::publishedFor($user)->where('title', 'like', $like)->latest()->limit(3)->get();
        if ($anns->isNotEmpty()) {
            $groups[] = [
                'label' => 'Announcements',
                'icon'  => '📢',
                'items' => $anns->map(fn ($a) => [
                    'title'    => $a->title,
                    'subtitle' => ucwords($a->audience) . ' • ' . $a->published_at?->diffForHumans(),
                    'url'      => route('announcements.show', $a),
                ])->all(),
            ];
        }

        // ── Resources ──
        $resources = Resource::published()
            ->where(function ($qb) use ($like) {
                $qb->where('title', 'like', $like)
                   ->orWhere('description', 'like', $like);
            })->limit(3)->get();
        if ($resources->isNotEmpty()) {
            $groups[] = [
                'label' => 'Resources',
                'icon'  => '📚',
                'items' => $resources->map(fn ($r) => [
                    'title'    => $r->title,
                    'subtitle' => $r->category_label . ' • ' . $r->type_label,
                    'url'      => route('resources.show', $r),
                ])->all(),
            ];
        }

        // ── Workshops ──
        $workshops = Workshop::where('status', 'published')
            ->where('title', 'like', $like)
            ->orderBy('starts_at')
            ->limit(3)->get();
        if ($workshops->isNotEmpty()) {
            $groups[] = [
                'label' => 'Workshops',
                'icon'  => '🎓',
                'items' => $workshops->map(fn ($w) => [
                    'title'    => $w->title,
                    'subtitle' => $w->starts_at->format('M d') . ' • ' . $w->venue,
                    'url'      => route('workshops.show', $w),
                ])->all(),
            ];
        }

        return response()->json(['groups' => $groups]);
    }
}
