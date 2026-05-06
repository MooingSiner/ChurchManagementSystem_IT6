<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Administrator extends Authenticatable
{
    protected $table = 'administrators';
    protected $primaryKey = 'administrator_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected $appends = [
        'role_label',
    ];

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'Church Administrator',
            'admin' => 'Attendance Coordinator',
            default => 'Administrator',
        };
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'administrator_id', 'administrator_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'administrator_id', 'administrator_id');
    }

    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class, 'administrator_id', 'administrator_id');
    }
}
