<?php

namespace Hws\FieldService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Webkul\User\Models\Admin;

class Task extends Model
{
    protected $table = 'hws_tasks';

    protected static function boot()
    {
        parent::boot();

        static::created(function ($task) {
            if ($task->assigned_to) {
                \Hws\FieldService\Models\Notification::create([
                    'admin_id' => $task->assigned_to,
                    'title'    => 'New Task Assigned',
                    'message'  => "New task assigned: #{$task->task_no} ({$task->customer_name}).",
                    'is_read'  => false,
                ]);
            }
        });

        static::updated(function ($task) {
            if ($task->wasChanged('assigned_to') && $task->assigned_to) {
                \Hws\FieldService\Models\Notification::create([
                    'admin_id' => $task->assigned_to,
                    'title'    => 'New Task Assigned',
                    'message'  => "New task assigned: #{$task->task_no} ({$task->customer_name}).",
                    'is_read'  => false,
                ]);
            }
        });
    }

    protected $fillable = [
        'task_no',
        'customer_id',
        'order_id',
        'type',
        'source',
        'reference_no',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'priority',
        'step',
        'scheduled_at',
        'assigned_to',
        'work_description',
        'signature_path',
        'rating',
        'sale_amount',
        'amc_renewal_date',
    ];

    protected $casts = [
        'scheduled_at'      => 'datetime',
        'step'              => 'integer',
        'rating'            => 'integer',
        'sale_amount'       => 'decimal:2',
        'amc_renewal_date'  => 'date',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(TaskMaterial::class, 'task_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(TaskPhoto::class, 'task_id');
    }

    public function survey(): HasOne
    {
        return $this->hasOne(SiteSurvey::class, 'task_id');
    }

    public function isSurvey(): bool
    {
        return $this->type === 'site_survey';
    }
}
