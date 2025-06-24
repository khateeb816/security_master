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
        'name',
        'point_code',
        'description',
        'qr_code',
        'nfc_tag',
        'is_active',
        'latitude',
        'longitude',
        'geofence_radius',
        'geofence_enabled',
        'site',
        'client_site_code',
        'checkpoint_code',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'geofence_enabled' => 'boolean',
        'geofence_radius' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    /**
     * Get the branch that owns the checkpoint.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
