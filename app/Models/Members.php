<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    protected $table = 'members';
    protected $primaryKey = 'member_id';
    protected $casts = ['is_archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    protected $fillable = [
    'member_fname',
    'member_mname',
    'member_lname',
    'gender',
    'birth_date',
    'email',
    'phone_number',
    'province',
    'city',
    'street',
    'is_archived',
    'archived_at',
];

    public function ministries()
    {
        return $this->belongsToMany(
            Ministry::class,
            'members_ministries',
            'member_id',
            'ministry_id',
        )->withPivot(['role_in_ministry', 'date_joined', 'status']);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'member_id', 'member_id');
    }
}
