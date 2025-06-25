<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkpoint extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'user_id',
        'client_id',
        'name',
        'description',
        'nfc_tag',
        'is_active',
        'latitude',
        'date_to_check',
        'time_to_check',
        'longitude',
        'radius',
        'priority',
        'checked_time',
        'media',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'radius' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'priority' => 'integer',
        'checked_time' => 'datetime',
        'media' => 'json',
        'status' => 'string',
    ];

    /**
     * Get the branch that owns the checkpoint.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
