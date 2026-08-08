<?php

namespace Hws\FieldService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\User\Models\Admin;

class Attendance extends Model
{
    protected $table = 'hws_attendance';

    protected $fillable = [
        'employee_id',
        'date',
        'check_in_time',
        'check_in_lat',
        'check_in_lng',
        'check_in_selfie_path',
        'check_out_time',
        'check_out_lat',
        'check_out_lng',
    ];

    protected $casts = [
        'date'           => 'date',
        'check_in_time'  => 'datetime',
        'check_out_time' => 'datetime',
        'check_in_lat'   => 'decimal:7',
        'check_in_lng'   => 'decimal:7',
        'check_out_lat'  => 'decimal:7',
        'check_out_lng'  => 'decimal:7',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'employee_id');
    }
}
