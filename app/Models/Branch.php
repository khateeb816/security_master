<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'branch_name',
        'manager_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'latitude',
        'longitude',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the client that owns the branch.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the checkpoints for the branch.
     */
    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }
}
