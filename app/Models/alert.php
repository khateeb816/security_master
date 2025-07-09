<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alert extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'message',
        'latitude',
        'longitude',
        'user_id',
        'status',
        'is_read',
        'time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
