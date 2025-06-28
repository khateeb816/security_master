<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignCheckpoint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'checkpoint_id',
        'guard_id',
        'date_to_check',
        'time_to_check',
        'checked_time',
        'status',
        'priority',
        'media',
        'nfc_tag',
        'notes',
        'longitude',
        'latitude',
    ];

    public function checkpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }

    public function user_guard()
    {
        return $this->belongsTo(User::class, 'guard_id');
    }
}
