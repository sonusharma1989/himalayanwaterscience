<?php

namespace Hws\FieldService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskPhoto extends Model
{
    protected $table = 'hws_task_photos';

    protected $fillable = [
        'task_id',
        'type',
        'file_path',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
