<?php

namespace App\Http\Controllers;

use App\Models\AnonymousConcern;
use Illuminate\Http\Request;

class AnonymousConcernController extends Controller
{
    /**
     * Public form — anyone can submit (no login needed).
     */
    public function create()
    {
        return view('anonymous-concerns.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'concern_type'          => 'required|in:' . implode(',', array_keys(AnonymousConcern::TYPES)),
            'urgency'               => 'required|in:' . implode(',', array_keys(AnonymousConcern::URGENCIES)),
            'description'           => 'required|string|min:20|max:5000',
            'about_who'             => 'nullable|string|max:200',
            'location'              => 'nullable|string|max:200',
            'reporter_relationship' => 'nullable|string|max:80',
            'contact_email'         => 'nullable|email|max:200',
        ]);

        $data['ip_address'] = $request->ip();

        $concern = AnonymousConcern::create($data);

        return view('anonymous-concerns.submitted', ['code' => $concern->reference_code]);
    }

    /**
     * Public lookup by reference code.
     */
    public function track(Request $request)
    {
        $code = trim((string) $request->input('code'));
        $concern = $code ? AnonymousConcern::where('reference_code', $code)->first() : null;

        return view('anonymous-concerns.track', compact('concern', 'code'));
    }

    /**
     * Staff dashboard — list all concerns.
     */
    public function index(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);

        $query = AnonymousConcern::with('handler')
            ->orderByRaw("FIELD(status, 'new','reviewing','action_taken','resolved','dismissed')")
            ->orderByRaw("FIELD(urgency, 'critical','high','medium','low')")
            ->latest();

        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('urgency'))     $query->where('urgency', $request->urgency);
        if ($request->filled('concern_type')) $query->where('concern_type', $request->concern_type);

        $concerns = $query->paginate(30)->withQueryString();

        $stats = [
            'new'       => AnonymousConcern::where('status', 'new')->count(),
            'reviewing' => AnonymousConcern::where('status', 'reviewing')->count(),
            'critical'  => AnonymousConcern::where('urgency', 'critical')->whereIn('status', ['new','reviewing'])->count(),
            'total'     => AnonymousConcern::count(),
        ];

        return view('anonymous-concerns.index', compact('concerns', 'stats'));
    }

    public function show(AnonymousConcern $anonymousConcern)
    {
        abort_unless(auth()->user()?->isStaff(), 403);
        $anonymousConcern->load('handler');
        return view('anonymous-concerns.show', ['concern' => $anonymousConcern]);
    }

    public function update(Request $request, AnonymousConcern $anonymousConcern)
    {
        abort_unless($request->user()->isStaff(), 403);

        $data = $request->validate([
            'status'      => 'required|in:' . implode(',', array_keys(AnonymousConcern::STATUSES)),
            'staff_notes' => 'nullable|string|max:5000',
        ]);

        if ($data['status'] === 'resolved' && !$anonymousConcern->resolved_at) {
            $data['resolved_at'] = now();
        }

        if (in_array($data['status'], ['reviewing', 'action_taken']) && !$anonymousConcern->handled_by) {
            $data['handled_by'] = $request->user()->id;
        }

        $anonymousConcern->update($data);

        return back()->with('success', 'Concern updated.');
    }
}
