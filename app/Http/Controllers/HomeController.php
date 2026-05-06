<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    protected function sessionIsOpenForMarking(object $session): bool
    {
        $openingDateTime = Carbon::parse(
            trim(($session->attendance_date ?? $session->event?->start_date ?? now()->toDateString()) . ' ' . ($session->time_in_start ?? $session->event?->start_time ?? '00:00')),
            'Asia/Manila'
        );
        $closingDateTime = Carbon::parse(
            trim(($session->attendance_date ?? $session->event?->start_date ?? now()->toDateString()) . ' ' . ($session->time_out_end ?? $session->event?->end_time ?? '23:59')),
            'Asia/Manila'
        );
        $now = Carbon::now('Asia/Manila');

        return $now->between($openingDateTime, $closingDateTime);
    }

    public function home()
    {
        $now = Carbon::now('Asia/Manila');

        $events = DB::table('vw_events_full')
            ->select('*', DB::raw('type_id as related_type_id'), DB::raw('computed_status as status'))
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->get()
            ->map(function ($event) {
                $event->type = $event->related_type_id ? (object) [
                    'type_id' => $event->related_type_id,
                    'type_name' => $event->type_name,
                ] : null;

                return $event;
            })
            ->filter(function ($event) use ($now) {
                $startDateTime = Carbon::parse($event->start_date . ' ' . ($event->start_time ?? '00:00'), 'Asia/Manila');
                $endDateTime = Carbon::parse($event->end_date . ' ' . ($event->end_time ?? '23:59'), 'Asia/Manila');

                return $now->between($startDateTime, $endDateTime);
            })
            ->values();

        $members = DB::table('members')
            ->where('is_archived', false)
            ->orderBy('member_fname')
            ->get();

        $eventIds = $events->pluck('event_id');

        $allAttendanceSessionsByEvent = DB::table('vw_attendance_session_summary')
            ->whereIn('event_id', $eventIds)
            ->orderByDesc('created_at')
            ->orderByDesc('attendance_session_id')
            ->get()
            ->map(function ($session) {
                $session->event = (object) [
                    'event_id' => $session->event_id,
                    'event_name' => $session->event_name,
                    'start_date' => $session->start_date,
                    'end_date' => $session->end_date,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                ];

                return $session;
            })
            ->groupBy('event_id');

        $attendanceSessionsByEvent = $allAttendanceSessionsByEvent
            ->map(function ($sessions) {
                return collect($sessions)
                    ->filter(fn ($session) => $this->sessionIsOpenForMarking($session))
                    ->values();
            });

        $attendanceSessionIdsByEvent = $attendanceSessionsByEvent
            ->map(fn ($sessions) => $sessions->first()?->attendance_session_id)
            ->filter();

        $sessionIds = $attendanceSessionsByEvent
            ->map(fn ($sessions) => $sessions->pluck('attendance_session_id'))
            ->flatten()
            ->filter()
            ->values();

        $attendanceMemberIds = DB::table('vw_attendance_records_full')
            ->select('attendance_session_id', 'event_id', 'member_id')
            ->whereIn('attendance_session_id', $sessionIds)
            ->get();

        return view('home', compact(
            'events',
            'members',
            'attendanceMemberIds',
            'attendanceSessionsByEvent',
            'attendanceSessionIdsByEvent',
            'allAttendanceSessionsByEvent'
        ));
    }

    public function submitAttendance(Request $request)
    {
        try {
            $request->validate([
                'event_id' => 'required|exists:events,event_id',
                'member_id' => 'required|exists:members,member_id',
                'attendance_session_id' => 'nullable|exists:attendance_sessions,attendance_session_id',
            ]);

            $member = DB::table('members')
                ->where('member_id', $request->member_id)
                ->where('is_archived', false)
                ->first();

            if (! $member) {
                throw new ModelNotFoundException();
            }

            $attendanceSession = $request->attendance_session_id
                ? DB::table('vw_attendance_session_summary')
                    ->where('attendance_session_id', $request->attendance_session_id)
                    ->where('event_id', $request->event_id)
                    ->first()
                : DB::table('vw_attendance_session_summary')
                    ->where('event_id', $request->event_id)
                    ->orderByDesc('attendance_session_id')
                    ->first();

            if (! $attendanceSession) {
                return redirect()->route('home')
                    ->with('error', 'No attendance has been created for this event yet.');
            }

            $attendanceSession->event = (object) [
                'event_id' => $attendanceSession->event_id,
                'event_name' => $attendanceSession->event_name,
                'start_date' => $attendanceSession->start_date,
                'end_date' => $attendanceSession->end_date,
                'start_time' => $attendanceSession->start_time,
                'end_time' => $attendanceSession->end_time,
            ];

            if (! $this->sessionIsOpenForMarking($attendanceSession)) {
                return redirect()->route('home')
                    ->with('error', 'Attendance for this session is unavailable right now. Please check the event attendance time window.');
            }

            DB::select('CALL sp_save_attendance_record(?, ?, ?, ?, ?, ?)', [
                $attendanceSession->attendance_session_id,
                $member->member_id,
                $request->event_id,
                1,
                'Pending',
                0,
            ]);

            return redirect()->route('home')
                ->with('success', 'Attendance submitted! Waiting for administrator approval.');
        } catch (Exception $e) {
            $message = match (true) {
                $e instanceof ModelNotFoundException => 'That member is no longer active. Choose another member or ask an administrator to restore the record.',
                $e instanceof QueryException => 'Attendance has already been submitted for this session. Wait for approval or ask an administrator to review it.',
                default => 'Could not submit attendance right now. Check the selected event/session and try again.',
            };

            return redirect()->route('home')
                ->with('error', $message);
        }
    }
}
