<?php

namespace App\Http\Controllers;

use App\Models\PsychologicalTest;
use Illuminate\Http\Request;

class PsychologicalTestController extends Controller
{
    public function index(Request $request)
    {
        $query = PsychologicalTest::withCount(['schedules','results'])->latest();

        if ($request->filled('type')) {
            $query->where('test_type', $request->type);
        }
        if ($request->filled('active')) {
            $query->where('is_active', $request->active === '1');
        }

        $tests = $query->paginate(20)->withQueryString();
        return view('psych-tests.index', compact('tests'));
    }

    public function create()
    {
        return view('psych-tests.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:200',
            'test_type'    => 'required|in:iq,personality,career_aptitude,interest,mental_health,other',
            'category'     => 'nullable|string|max:100',
            'description'  => 'nullable|string|max:2000',
            'total_items'  => 'nullable|integer|min:1|max:9999',
            'publisher'    => 'nullable|string|max:200',
            'edition_year' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'is_active'    => 'boolean',
        ]);

        PsychologicalTest::create($data);

        return redirect()->route('psych-tests.index')
            ->with('success', 'Test added to inventory.');
    }

    public function show(PsychologicalTest $psychTest)
    {
        $psychTest->load(['schedules.administeredBy', 'results.studentProfile']);
        return view('psych-tests.show', compact('psychTest'));
    }

    public function edit(PsychologicalTest $psychTest)
    {
        return view('psych-tests.edit', compact('psychTest'));
    }

    public function update(Request $request, PsychologicalTest $psychTest)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:200',
            'test_type'    => 'required|in:iq,personality,career_aptitude,interest,mental_health,other',
            'category'     => 'nullable|string|max:100',
            'description'  => 'nullable|string|max:2000',
            'total_items'  => 'nullable|integer|min:1|max:9999',
            'publisher'    => 'nullable|string|max:200',
            'edition_year' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'is_active'    => 'boolean',
        ]);

        $psychTest->update($data);

        return redirect()->route('psych-tests.show', $psychTest)
            ->with('success', 'Test updated.');
    }

    public function destroy(PsychologicalTest $psychTest)
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);
        $psychTest->delete();
        return redirect()->route('psych-tests.index')->with('success', 'Test deleted.');
    }
}
