<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
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

    protected function attendanceQuery()
    {
        return DB::table('attendances')
            ->join('members', 'attendances.member_id', '=', 'members.member_id')
            ->join('events', 'attendances.event_id', '=', 'events.event_id')
            ->leftJoin('attendance_sessions', 'attendances.attendance_session_id', '=', 'attendance_sessions.attendance_session_id')
            ->leftJoin('types', 'events.type_id', '=', 'types.type_id')
            ->leftJoin('administrators', 'attendances.administrator_id', '=', 'administrators.administrator_id')
            ->select(
                'attendances.*',
                'members.member_fname',
                'members.member_mname',
                'members.member_lname',
                'members.email',
                'members.phone_number',
                'events.event_name',
                'events.type_id',
                'types.type_name',
                'attendance_sessions.attendance_name',
                'attendance_sessions.attendance_date',
                'administrators.username as administrator_username'
            );
    }

    protected function hydrateAttendances($rows)
    {
        return collect($rows)->map(function ($attendance) {
            $attendance->member = (object) [
                'member_id' => $attendance->member_id,
                'member_fname' => $attendance->member_fname,
                'member_mname' => $attendance->member_mname,
                'member_lname' => $attendance->member_lname,
                'email' => $attendance->email,
                'phone_number' => $attendance->phone_number,
            ];
            $attendance->event = (object) [
                'event_id' => $attendance->event_id,
                'event_name' => $attendance->event_name,
                'type' => $attendance->type_id ? (object) [
                    'type_id' => $attendance->type_id,
                    'type_name' => $attendance->type_name,
                ] : null,
            ];
            $attendance->attendanceSession = $attendance->attendance_session_id ? (object) [
                'attendance_session_id' => $attendance->attendance_session_id,
                'attendance_name' => $attendance->attendance_name,
                'attendance_date' => $attendance->attendance_date,
            ] : null;
            $attendance->administrator = $attendance->administrator_id ? (object) [
                'administrator_id' => $attendance->administrator_id,
                'username' => $attendance->administrator_username,
            ] : null;

            return $attendance;
        });
    }

    protected function sessionWithEventQuery()
    {
        return DB::table('attendance_sessions')
            ->join('events', 'attendance_sessions.event_id', '=', 'events.event_id')
            ->select(
                'attendance_sessions.*',
                'events.event_name',
                'events.start_date',
                'events.end_date',
                'events.start_time',
                'events.end_time'
            );
    }

    protected function hydrateSession($session)
    {
        if (! $session) {
            return null;
        }

        $session->event = (object) [
            'event_id' => $session->event_id,
            'event_name' => $session->event_name,
            'start_date' => $session->start_date,
            'end_date' => $session->end_date,
            'start_time' => $session->start_time,
            'end_time' => $session->end_time,
        ];

        return $session;
    }

    protected function filteredAttendances(Request $request)
    {
        $search = trim((string) $request->query('search', $request->query('attendance_search', '')));
        $eventId = $request->query('event_id');
        $memberId = $request->query('member_id');
        $sessionId = $request->query('attendance_session_id');
        $status = $request->query('status');
        $typeName = $request->query('type_name');
        $attendanceDate = $request->query('attendance_date');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        return $this->attendanceQuery()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('members.member_fname', 'like', "%{$search}%")
                        ->orWhere('members.member_mname', 'like', "%{$search}%")
                        ->orWhere('members.member_lname', 'like', "%{$search}%")
                        ->orWhere('members.email', 'like', "%{$search}%")
                        ->orWhere('members.phone_number', 'like', "%{$search}%")
                        ->orWhere('events.event_name', 'like', "%{$search}%")
                        ->orWhere('attendance_sessions.attendance_name', 'like', "%{$search}%")
                        ->orWhere('types.type_name', 'like', "%{$search}%");
                });
            })
            ->when($eventId, fn ($query) => $query->where('attendances.event_id', $eventId))
            ->when($memberId, fn ($query) => $query->where('attendances.member_id', $memberId))
            ->when($sessionId, fn ($query) => $query->where('attendances.attendance_session_id', $sessionId))
            ->when($status, fn ($query) => $query->where('attendances.status', $status))
            ->when($typeName, fn ($query) => $query->where('types.type_name', $typeName))
            ->when($attendanceDate, fn ($query) => $query->whereDate('attendance_sessions.attendance_date', $attendanceDate))
            ->when($dateFrom, fn ($query) => $query->whereDate('attendances.attended_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('attendances.attended_at', '<=', $dateTo));
    }

    public function index(Request $request)
    {
        $sorts = [
            'attended_at' => 'attendances.attended_at',
            'created_at' => 'attendances.created_at',
            'status' => 'attendances.status',
            'member_name' => 'members.member_lname',
            'event_name' => 'events.event_name',
            'attendance_date' => 'attendance_sessions.attendance_date',
        ];
        $sortBy = $sorts[$request->query('sort_by', 'attended_at')] ?? 'attendances.attended_at';
        $sortDirection = $request->query('sort_direction') === 'asc' ? 'asc' : 'desc';

        $attendancesQuery = $this->filteredAttendances($request)
            ->orderBy($sortBy, $sortDirection)
            ->orderByDesc('attendances.attendance_id');

        if ($request->filled('per_page')) {
            $perPage = min(max((int) $request->integer('per_page', 15), 1), 100);
            $attendances = $attendancesQuery->paginate($perPage)->withQueryString();
            $attendances->setCollection($this->hydrateAttendances($attendances->getCollection()));

            return response()->json([
                'success' => true,
                'data' => $attendances->items(),
                'meta' => [
                    'current_page' => $attendances->currentPage(),
                    'per_page' => $attendances->perPage(),
                    'total' => $attendances->total(),
                    'last_page' => $attendances->lastPage(),
                ],
            ]);
        }

        $attendances = $this->hydrateAttendances($attendancesQuery->get());

        return response()->json([
            'success' => true,
            'data' => $attendances,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'member_id' => 'required|exists:members,member_id',
            'status' => 'nullable|string',
        ]);

        $member = DB::table('members')
            ->where('member_id', $validated['member_id'])
            ->where('is_archived', false)
            ->first();

        if (! $member) {
            return response()->json([
                'success' => false,
                'error' => 'Member is not active.',
            ], 422);
        }

        $attendance = DB::table('attendances')
            ->where('event_id', $validated['event_id'])
            ->where('member_id', $member->member_id)
            ->first();

        if ($attendance) {
            DB::table('attendances')->where('attendance_id', $attendance->attendance_id)->update([
                'administrator_id' => Auth::id(),
                'attended_at' => now(),
                'status' => $validated['status'] ?? 'Present',
                'updated_at' => now(),
            ]);
            $attendanceId = $attendance->attendance_id;
        } else {
            $attendanceId = DB::table('attendances')->insertGetId([
                'event_id' => $validated['event_id'],
                'member_id' => $member->member_id,
                'administrator_id' => Auth::id(),
                'attended_at' => now(),
                'status' => $validated['status'] ?? 'Present',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $attendance = $this->hydrateAttendances([
            $this->attendanceQuery()->where('attendances.attendance_id', $attendanceId)->first(),
        ])->first();

        return response()->json([
            'success' => true,
            'message' => 'Attendance saved successfully.',
            'data' => $attendance,
        ], 201);
    }

    public function show(string $id)
    {
        $attendance = $this->hydrateAttendances([
            $this->attendanceQuery()->where('attendances.attendance_id', $id)->firstOrFail(),
        ])->first();

        return response()->json([
            'success' => true,
            'data' => $attendance,
        ]);
    }

    public function scanAttendance(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'member_id' => 'required|integer|exists:members,member_id',
            'attendance_session_id' => 'nullable|exists:attendance_sessions,attendance_session_id',
        ]);

        $member = DB::table('members')
            ->where('member_id', $validated['member_id'])
            ->where('is_archived', false)
            ->first();

        if (! $member) {
            return response()->json([
                'success' => false,
                'error' => 'Member is not active.',
            ], 404);
        }

        $attendanceSessionQuery = $this->sessionWithEventQuery()->where('attendance_sessions.event_id', $validated['event_id']);

        if (! empty($validated['attendance_session_id'])) {
            $attendanceSessionQuery->where('attendance_sessions.attendance_session_id', $validated['attendance_session_id']);
        }

        $attendanceSession = $this->hydrateSession(
            $attendanceSessionQuery->orderByDesc('attendance_sessions.attendance_session_id')->first()
        );

        if (! $attendanceSession) {
            return response()->json([
                'success' => false,
                'error' => 'No attendance session is available for this event yet.',
            ], 422);
        }

        if (! $this->sessionIsOpenForMarking($attendanceSession)) {
            return response()->json([
                'success' => false,
                'error' => 'Attendance for this session is unavailable right now. Please check the event attendance time window.',
            ], 422);
        }

        $existingAttendance = DB::table('attendances')
            ->where('attendance_session_id', $attendanceSession->attendance_session_id)
            ->where('member_id', $member->member_id)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'error' => 'Attendance already submitted for this session.',
                'data' => $existingAttendance,
            ], 409);
        }

        try {
            $attendanceId = DB::table('attendances')->insertGetId([
                'attendance_session_id' => $attendanceSession->attendance_session_id,
                'event_id' => $attendanceSession->event_id,
                'member_id' => $member->member_id,
                'administrator_id' => Auth::id() ?: 1,
                'attended_at' => now(),
                'time_in' => now(),
                'status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (QueryException $exception) {
            return response()->json([
                'success' => false,
                'error' => 'Attendance already submitted for this session.',
            ], 409);
        }

        $attendance = $this->hydrateAttendances([
            $this->attendanceQuery()->where('attendances.attendance_id', $attendanceId)->first(),
        ])->first();
        $attendance->attendanceSession = $attendanceSession;

        return response()->json([
            'success' => true,
            'message' => 'Attendance submitted successfully.',
            'data' => $attendance,
        ], 201);
    }
}
