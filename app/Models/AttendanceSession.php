<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $table = 'attendance_sessions';
    protected $primaryKey = 'attendance_session_id';

    protected $fillable = [
        'event_id',
        'administrator_id',
        'attendance_name',
        'attendance_date',
        'time_in_start',
        'time_out_end',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function administrator()
    {
        return $this->belongsTo(Administrator::class, 'administrator_id', 'administrator_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'attendance_session_id', 'attendance_session_id');
    }
}
