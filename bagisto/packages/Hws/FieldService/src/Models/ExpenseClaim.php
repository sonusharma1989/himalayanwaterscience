<?php

namespace Hws\FieldService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\User\Models\Admin;

class ExpenseClaim extends Model
{
    protected $table = 'hws_expense_claims';

    protected $fillable = [
        'employee_id',
        'category',
        'amount',
        'description',
        'receipt_path',
        'status',
        'reviewed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
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
