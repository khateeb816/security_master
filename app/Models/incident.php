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
        'images',
        'videos',
        'audios',
        'message'
    ];

    protected $casts = [
        'images' => 'json',
        'videos' => 'json',
        'audios' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
