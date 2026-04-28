<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\WorkshopRsvp;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isStaff = $user->isStaff();

        $query = Workshop::with('organizer')
            ->withCount(['rsvps as registered_count' => fn ($q) => $q->whereIn('status', ['registered','attended'])]);

        if (!$isStaff) {
            $query->where('status', 'published')
                  ->where(function ($q) use ($user) {
                      $aud = ['all'];
                      if ($user->isStudent())  $aud[] = 'students';
                      if ($user->isFaculty())  $aud[] = 'faculty';
                      if ($user->isStaff())    $aud[] = 'staff';
                      $q->whereIn('audience', $aud);
                  });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('view') && $request->view === 'past') {
            $query->where('ends_at', '<', now())->orderByDesc('starts_at');
        } else {
            $query->where('ends_at', '>=', now())->orderBy('starts_at');
        }

        $workshops = $query->paginate(12)->withQueryString();

        // Pre-load user's RSVP status for each
        $userRsvps = $user
            ? WorkshopRsvp::where('user_id', $user->id)
                ->whereIn('workshop_id', $workshops->pluck('id'))
                ->pluck('status', 'workshop_id')
            : collect();

        return view('workshops.index', compact('workshops', 'isStaff', 'userRsvps'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isStaff(), 403);
        return view('workshops.create');
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);
        $data = $this->validateData($request);
        $data['organizer_id'] = $request->user()->id;
        Workshop::create($data);
        return redirect()->route('workshops.index')->with('success', 'Workshop created.');
    }

    public function show(Workshop $workshop)
    {
        $workshop->load('organizer', 'rsvps.user');
        return view('workshops.show', compact('workshop'));
    }

    public function edit(Workshop $workshop)
    {
        abort_unless(auth()->user()->isStaff(), 403);
        return view('workshops.edit', compact('workshop'));
    }

    public function update(Request $request, Workshop $workshop)
    {
        abort_unless($request->user()->isStaff(), 403);
        $workshop->update($this->validateData($request));
        return redirect()->route('workshops.show', $workshop)->with('success', 'Workshop updated.');
    }

    public function destroy(Workshop $workshop)
    {
        abort_unless(auth()->user()->isStaff(), 403);
        $workshop->delete();
        return redirect()->route('workshops.index')->with('success', 'Workshop deleted.');
    }

    public function rsvp(Workshop $workshop, Request $request)
    {
        $user = $request->user();
        abort_unless($workshop->status === 'published', 403, 'Workshop is not open for RSVP.');
        abort_if($workshop->rsvpsClosed(), 422, 'RSVP deadline has passed.');
        abort_if($workshop->isFull() && !$workshop->userIsRegistered($user), 422, 'Workshop is full.');

        $rsvp = WorkshopRsvp::firstOrNew([
            'workshop_id' => $workshop->id,
            'user_id'     => $user->id,
        ]);

        if ($rsvp->exists && $rsvp->status === 'cancelled') {
            $rsvp->status = 'registered';
            $rsvp->save();
        } elseif (!$rsvp->exists) {
            $rsvp->status = 'registered';
            $rsvp->save();
        }

        return back()->with('success', 'You\'re registered for ' . $workshop->title);
    }

    public function cancelRsvp(Workshop $workshop, Request $request)
    {
        $rsvp = WorkshopRsvp::where('workshop_id', $workshop->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($rsvp) {
            $rsvp->update(['status' => 'cancelled']);
        }

        return back()->with('success', 'RSVP cancelled.');
    }

    public function markAttended(Workshop $workshop, WorkshopRsvp $rsvp, Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);
        abort_unless($rsvp->workshop_id === $workshop->id, 404);

        $rsvp->update([
            'status'      => 'attended',
            'attended_at' => now(),
        ]);

        return back()->with('success', 'Marked as attended.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title'         => 'required|string|max:200',
            'description'   => 'required|string|max:5000',
            'category'      => 'required|in:' . implode(',', array_keys(Workshop::CATEGORIES)),
            'venue'         => 'required|string|max:200',
            'mode'          => 'required|in:in_person,virtual,hybrid',
            'meeting_link'  => 'nullable|url|max:500',
            'starts_at'     => 'required|date',
            'ends_at'       => 'required|date|after:starts_at',
            'capacity'      => 'nullable|integer|min:1|max:9999',
            'rsvp_deadline' => 'nullable|date|before_or_equal:starts_at',
            'audience'      => 'required|in:all,students,staff,faculty',
            'status'        => 'required|in:draft,published,cancelled,completed',
            'cover_color'   => 'required|in:' . implode(',', array_keys(Workshop::COVER_COLORS)),
        ]);
    }
}
