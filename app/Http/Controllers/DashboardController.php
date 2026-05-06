<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $totalMembers = DB::table('members')->where('is_archived', false)->count();
        $archivedMembers = DB::table('members')->where('is_archived', true)->count();
        $totalEvents = DB::table('vw_events_full')->count();
        $attendanceRecords = DB::table('attendances')
            ->where('status', 'Present')
            ->whereNotNull('attendance_session_id')
            ->count();
        $totalAttendanceSessions = DB::table('vw_attendance_session_summary')->count();
        $averageAttendance = $totalAttendanceSessions > 0
            ? round($attendanceRecords / $totalAttendanceSessions)
            : 0;

        $recentAttendanceSessions = DB::table('vw_attendance_session_summary')
            ->orderByDesc('attendance_date')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($session) {
                $session->event = (object) [
                    'event_id' => $session->event_id,
                    'event_name' => $session->event_name,
                    'type_id' => $session->type_id,
                ];
                $session->attendance_count = $session->approved_attendance_count;

                return $session;
            });

        return view('dashboard', compact(
            'totalMembers',
            'archivedMembers',
            'totalEvents',
            'attendanceRecords',
            'totalAttendanceSessions',
            'averageAttendance',
            'recentAttendanceSessions'
        ));
    }
}
