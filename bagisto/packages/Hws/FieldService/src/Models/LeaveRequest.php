<?php

namespace Hws\FieldService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\User\Models\Admin;

class LeaveRequest extends Model
{
    protected $table = 'hws_leave_requests';

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'reviewed_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'employee_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }
}
