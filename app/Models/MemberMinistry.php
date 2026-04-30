<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberMinistry extends Model
{
    protected $table = 'members_ministries';
    protected $primaryKey = 'members_ministry_id';
    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'ministry_id',
        'role_in_ministry',
        'date_joined',
        'status',
    ];
}
