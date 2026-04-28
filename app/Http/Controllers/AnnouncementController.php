<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $isStaff = in_array($user->role, ['guidance_counselor', 'guidance_director', 'super_admin']);

        if ($isStaff && $request->filled('manage')) {
            // Staff full management list (drafts, expired, all)
            $announcements = Announcement::with('author')
                ->orderByDesc('is_pinned')
                ->latest('published_at')
                ->latest('created_at')
                ->paginate(20);
        } else {
            $announcements = Announcement::with('author')
                ->publishedFor($user)
                ->orderByDesc('is_pinned')
                ->orderByDesc('published_at')
                ->paginate(20);
        }

        return view('announcements.index', compact('announcements', 'isStaff'));
    }

    public function create()
    {
        $this->authorizeStaff();
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $this->authorizeStaff();

        $data = $request->validate([
            'title'        => 'required|string|max:200',
            'body'         => 'required|string|max:5000',
            'audience'     => 'required|in:all,students,staff,counselors,faculty',
            'priority'     => 'required|in:info,warning,urgent',
            'is_pinned'    => 'sometimes|boolean',
            'is_published' => 'sometimes|boolean',
            'expires_at'   => 'nullable|date|after:today',
        ]);

        $data['created_by']   = $request->user()->id;
        $data['is_pinned']    = $request->boolean('is_pinned');
        $data['is_published'] = $request->boolean('is_published', true);
        $data['published_at'] = $data['is_published'] ? now() : null;

        Announcement::create($data);

        return redirect()->route('announcements.index')->with('success', 'Announcement posted.');
    }

    public function show(Announcement $announcement)
    {
        $announcement->load('author');
        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        $this->authorizeStaff();
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorizeStaff();

        $data = $request->validate([
            'title'        => 'required|string|max:200',
            'body'         => 'required|string|max:5000',
            'audience'     => 'required|in:all,students,staff,counselors,faculty',
            'priority'     => 'required|in:info,warning,urgent',
            'is_pinned'    => 'sometimes|boolean',
            'is_published' => 'sometimes|boolean',
            'expires_at'   => 'nullable|date',
        ]);

        $data['is_pinned']    = $request->boolean('is_pinned');
        $data['is_published'] = $request->boolean('is_published', true);
        if ($data['is_published'] && !$announcement->published_at) {
            $data['published_at'] = now();
        }

        $announcement->update($data);

        return redirect()->route('announcements.index')->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorizeStaff();
        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Announcement deleted.');
    }

    private function authorizeStaff(): void
    {
        $user = auth()->user();
        abort_unless(in_array($user->role, ['guidance_counselor', 'guidance_director', 'super_admin']), 403);
    }
}
