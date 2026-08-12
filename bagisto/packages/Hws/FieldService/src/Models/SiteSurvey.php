<?php

namespace Hws\FieldService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteSurvey extends Model
{
    protected $table = 'hws_site_surveys';

    protected $fillable = [
        'task_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'property_type',
        'floors',
        'built_up_area_sqft',
        'rooms_units',
        'water_use_kld',
        'water_source',
        'wastewater_disposal',
        'space_available',
        'notes',
        'follow_up_date',
        'status',
        'temperature',
        'source',
        'assigned_to',
        'next_follow_up_at',
        'latitude',
        'longitude',
        'photos',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'water_use_kld'  => 'decimal:2',
        'latitude'       => 'decimal:7',
        'longitude'      => 'decimal:7',
        'photos'         => 'array',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function inquiryTypes(): HasMany
    {
        return $this->hasMany(SurveyInquiryType::class, 'survey_id');
    }
}
