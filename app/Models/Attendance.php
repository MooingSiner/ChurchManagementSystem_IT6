<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';
    protected $primaryKey = 'attendance_id';

    protected $fillable = [
        'member_id',
        'event_id',
        'administrator_id',
        'attended_at',
        'status',
        'attendance_session_id',
        'time_in',
        'time_out',

    ];

    public function member()
    {
        return $this->belongsTo(Members::class, 'member_id', 'member_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function attendanceSession()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id', 'attendance_session_id');
    }

    public function administrator()
    {
        return $this->belongsTo(Administrator::class, 'administrator_id', 'administrator_id');
    }
}
