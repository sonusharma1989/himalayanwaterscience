<?php

namespace Hws\FieldService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $table = 'hws_quotations';

    protected $fillable = [
        'lead_id',
        'quote_no',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'subtotal',
        'discount',
        'tax_amount',
        'grand_total',
        'status',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(SiteSurvey::class, 'lead_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }
}
