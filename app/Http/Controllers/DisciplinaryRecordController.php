<?php

namespace App\Http\Controllers;

use App\Models\DisciplinaryRecord;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;

class DisciplinaryRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = DisciplinaryRecord::with(['studentProfile', 'reportedBy', 'handledBy'])
                                   ->latest('incident_date');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('studentProfile', fn ($q) =>
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('student_id_number', 'like', "%$search%")
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('offense_type')) {
            $query->where('offense_type', $request->offense_type);
        }

        $records = $query->paginate(20)->withQueryString();

        return view('disciplinary.index', compact('records'));
    }

    public function create()
    {
        $students = StudentProfile::orderBy('last_name')->get();
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();
        return view('disciplinary.create', compact('students', 'counselors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_profile_id' => 'required|exists:student_profiles,id',
            'offense_type'       => 'required|in:minor,major',
            'offense_category'   => 'required|in:tardiness,absences,misconduct,cheating,property_damage,harassment,substance,other',
            'incident_date'      => 'required|date|before_or_equal:today',
            'description'        => 'required|string|min:20|max:3000',
            'action_taken'       => 'nullable|string|max:2000',
            'sanction'           => 'nullable|string|max:200',
            'sanction_end_date'  => 'nullable|date|after_or_equal:incident_date',
            'handled_by'         => 'nullable|exists:users,id',
        ]);

        DisciplinaryRecord::create(array_merge($data, [
            'reported_by' => $request->user()->id,
            'status'      => 'pending',
        ]));

        return redirect()->route('disciplinary.index')
            ->with('success', 'Disciplinary record filed successfully.');
    }

    public function show(DisciplinaryRecord $disciplinary)
    {
        $disciplinary->load(['studentProfile', 'reportedBy', 'handledBy']);
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();
        return view('disciplinary.show', compact('disciplinary', 'counselors'));
    }

    public function edit(DisciplinaryRecord $disciplinary)
    {
        $students   = StudentProfile::orderBy('last_name')->get();
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();
        return view('disciplinary.edit', compact('disciplinary', 'students', 'counselors'));
    }

    public function update(Request $request, DisciplinaryRecord $disciplinary)
    {
        $data = $request->validate([
            'offense_type'      => 'required|in:minor,major',
            'offense_category'  => 'required|in:tardiness,absences,misconduct,cheating,property_damage,harassment,substance,other',
            'incident_date'     => 'required|date|before_or_equal:today',
            'description'       => 'required|string|min:20|max:3000',
            'action_taken'      => 'nullable|string|max:2000',
            'status'            => 'required|in:pending,under_review,resolved,escalated',
            'sanction'          => 'nullable|string|max:200',
            'sanction_end_date' => 'nullable|date|after_or_equal:incident_date',
            'handled_by'        => 'nullable|exists:users,id',
        ]);

        if (empty($data['handled_by'])) {
            $data['handled_by'] = $request->user()->id;
        }

        $disciplinary->update($data);

        return redirect()->route('disciplinary.show', $disciplinary)
            ->with('success', 'Disciplinary record updated.');
    }

    public function destroy(DisciplinaryRecord $disciplinary)
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);
        $disciplinary->delete();
        return redirect()->route('disciplinary.index')
            ->with('success', 'Record deleted.');
    }
}
