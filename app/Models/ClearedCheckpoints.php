<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClearedCheckpoints extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'checkpoint_id',
        'user_id',
        'status',
        'round',
        'value',
        'time',
        'date',
        'type',
        'longitude',
        'latitude',
    ];


    public function checkpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
