<?php

namespace App\Http\Controllers;

use App\Models\ClearanceRequest;
use App\Models\ExitSurveyQuestion;
use App\Models\ExitSurveyResponse;
use Illuminate\Http\Request;

class ExitSurveyController extends Controller
{
    public function show(ClearanceRequest $clearance)
    {
        $user = request()->user();

        // Only the student who owns the request may fill the survey
        if ($user->isStudent()) {
            abort_unless($clearance->student_profile_id === $user->studentProfile?->id, 403);
        }

        abort_unless(in_array($clearance->status, ['for_exit_survey','survey_done']), 403);

        $questions  = ExitSurveyQuestion::where('is_active', true)->orderBy('sort_order')->get();
        $existing   = $clearance->surveyResponses()->pluck('response', 'exit_survey_question_id');

        return view('exit-survey.show', compact('clearance', 'questions', 'existing'));
    }

    public function store(Request $request, ClearanceRequest $clearance)
    {
        $user = $request->user();
        if ($user->isStudent()) {
            abort_unless($clearance->student_profile_id === $user->studentProfile?->id, 403);
        }
        abort_unless(in_array($clearance->status, ['for_exit_survey','survey_done']), 403);

        $questions = ExitSurveyQuestion::where('is_active', true)->orderBy('sort_order')->get();

        // Validate required responses
        $rules = [];
        foreach ($questions as $q) {
            $rules["responses.{$q->id}"] = $q->is_required ? 'required|string|max:1000' : 'nullable|string|max:1000';
        }
        $validated = $request->validate($rules);

        foreach ($questions as $q) {
            ExitSurveyResponse::updateOrCreate(
                ['clearance_request_id' => $clearance->id, 'exit_survey_question_id' => $q->id],
                ['response' => $validated["responses.{$q->id}"] ?? null]
            );
        }

        $clearance->update(['status' => 'survey_done']);

        return redirect()->route('clearance.show', $clearance)
            ->with('success', 'Exit survey submitted. The guidance office will review your clearance request.');
    }
}
