<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Branch;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'cnic',
        'nfc_uid',
        'designation',
        'role',
        'client_id',
        'latitude',
        'longitude',
        'status',
        'password',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'latitude' => 'float',
        'longitude' => 'float',
    ];
    
    /**
     * Get the client that owns the user.
     */
    /**
     * Get the branch that the user belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Get the client that owns the user.
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class);
    }
    
    /**
     * Find a branch that matches the user's coordinates
     *
     * @return \App\Models\Branch|null
     */
    public function findMatchingBranch()
    {
        if (!$this->client_id || !$this->latitude || !$this->longitude) {
            return null;
        }
        
        return \App\Models\Branch::where('client_id', $this->client_id)
            ->where('latitude', $this->latitude)
            ->where('longitude', $this->longitude)
            ->first();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
