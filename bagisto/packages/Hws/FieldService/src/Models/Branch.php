<?php

namespace Hws\FieldService\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\Admin;

class Branch extends Model
{
    protected $table = 'hws_branches';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Staff/Employees belonging to this branch
     */
    public function employees()
    {
        return $this->hasMany(Admin::class, 'branch_id');
    }

    /**
     * Scope active branches
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
