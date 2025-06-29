<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class incident extends Model
{
    protected $fillable = [
        'title',
        'description',
        'latitude',
        'longitude',
        'user_id',
        'time',
        'type',
        'status',
        'priority',
        'media',
        'message'
    ];

    protected $casts = [
        'media' => 'json',
        'time' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
