<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function report(Request $request)
    {
        $range = $request->get('range', '30days');

        $fromDate = match ($range) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            default => null,
        };

        $membersQuery = DB::table('members');
        $eventsQuery = DB::table('vw_events_full');
        $attendanceSessionsQuery = DB::table('vw_attendance_session_summary');
        $attendanceQuery = DB::table('vw_attendance_records_full')
            ->where('status', 'Present');

        if ($fromDate) {
            $eventsQuery->whereDate('start_date', '>=', $fromDate);
            $attendanceSessionsQuery->whereDate('attendance_date', '>=', $fromDate);
            $attendanceQuery->whereDate('attendance_date', '>=', $fromDate);
        }

        $totalMembers = $membersQuery->count();
        $totalEvents = $eventsQuery->count();
        $totalAttendanceSessions = $attendanceSessionsQuery->count();
        $totalAttendance = $attendanceQuery->count();
        $avgAttendance = $totalAttendanceSessions > 0 ? round($totalAttendance / $totalAttendanceSessions) : 0;

        $reportHistoryQuery = DB::table('vw_attendance_session_summary')
            ->orderByDesc('attendance_date')
            ->orderByDesc('created_at');

        if ($fromDate) {
            $reportHistoryQuery->whereDate('attendance_date', '>=', $fromDate);
        }

        $reportHistory = $reportHistoryQuery->get()->map(function ($session) {
            $session->event = (object) [
                'event_name' => $session->event_name,
                'type' => $session->type_id ? (object) [
                    'type_id' => $session->type_id,
                    'type_name' => $session->type_name,
                ] : null,
            ];

            return $session;
        });

        $attendanceByEventQuery = DB::table('vw_attendance_records_full')
            ->where('status', 'Present');

        if ($fromDate) {
            $attendanceByEventQuery->whereDate('attendance_date', '>=', $fromDate);
        }

        $attendanceByEvent = $attendanceByEventQuery
            ->select('event_name as label', DB::raw('COUNT(attendance_id) as count'))
            ->groupBy('event_id', 'event_name')
            ->orderBy('event_name')
            ->get();

        $attendanceByTypeQuery = DB::table('vw_attendance_session_summary');

        if ($fromDate) {
            $attendanceByTypeQuery->whereDate('attendance_date', '>=', $fromDate);
        }

        $attendanceByTypeQuery = $attendanceByTypeQuery
            ->select('type_name', DB::raw('SUM(approved_attendance_count) as total'))
            ->groupBy('type_id', 'type_name')
            ->get();

        $attendedMembersQuery = DB::table('vw_attendance_records_full')
            ->where('status', 'Present');

        if ($fromDate) {
            $attendedMembersQuery->whereDate('attendance_date', '>=', $fromDate);
        }

        $attendedMembers = $attendedMembersQuery->distinct('member_id')->count('member_id');

        $memberStatus = [
            'attended' => $attendedMembers,
            'not_attended' => max(0, $totalMembers - $attendedMembers),
        ];

        return view('report', compact(
            'range',
            'totalMembers',
            'totalEvents',
            'totalAttendance',
            'avgAttendance',
            'reportHistory',
            'attendanceByEvent',
            'attendanceByTypeQuery',
            'memberStatus'
        ));
    }
}
