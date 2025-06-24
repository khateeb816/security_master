<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'contact_person',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'status',
        'logo',
        'notes',
        'arc_id',
        'language',
        'incident_report_email',
        'mobile_form_email',
        'additional_recipients'
    ];

    protected $casts = [
        'status' => 'string',
        'incident_report_email' => 'boolean',
        'mobile_form_email' => 'boolean'
    ];

    /**
     * Get the users associated with this client.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Get the branches associated with this client.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
