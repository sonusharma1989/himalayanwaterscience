<?php

namespace Hws\FieldService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskMaterial extends Model
{
    protected $table = 'hws_task_materials';

    protected $fillable = [
        'task_id',
        'name',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
