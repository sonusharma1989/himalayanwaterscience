<?php

namespace Hws\FieldService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyInquiryType extends Model
{
    protected $table = 'hws_survey_inquiry_types';

    protected $fillable = [
        'survey_id',
        'inquiry_type',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(SiteSurvey::class, 'survey_id');
    }
}
