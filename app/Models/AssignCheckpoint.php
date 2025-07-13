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
        'date_from',
        'date_to',
        'notes',
    ];

    public function checkpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }

    public function assignedGuard()
    {
        return $this->belongsTo(User::class, 'guard_id');
    }

    public function user_guard()
    {
        return $this->belongsTo(User::class, 'guard_id');
    }
}
