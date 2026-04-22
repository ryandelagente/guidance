<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExitSurveyResponse extends Model
{
    protected $fillable = [
        'clearance_request_id', 'exit_survey_question_id', 'response',
    ];

    public function clearanceRequest()
    {
        return $this->belongsTo(ClearanceRequest::class);
    }

    public function question()
    {
        return $this->belongsTo(ExitSurveyQuestion::class, 'exit_survey_question_id');
    }
}
