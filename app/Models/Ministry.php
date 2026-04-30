<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ministry extends Model
{
    protected $table = 'ministries';
    protected $primaryKey = 'ministry_id';

    protected $fillable = [
        'ministry_name',
    ];

    public function members()
    {
        return $this->belongsToMany(
            Members::class,
            'members_ministries',
            'ministry_id',
            'member_id'
        )->withPivot(['role_in_ministry', 'date_joined', 'status']);
    }
}
