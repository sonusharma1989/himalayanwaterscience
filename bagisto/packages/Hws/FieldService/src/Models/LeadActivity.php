<?php

namespace Hws\FieldService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\User\Models\Admin;

class LeadActivity extends Model
{
    protected $table = 'hws_lead_activities';

    protected $fillable = [
        'survey_id',
        'action_by',
        'activity_type',
        'notes',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(SiteSurvey::class, 'survey_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'action_by');
    }
}
