<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    protected function normalizeDateInput(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        foreach (['Y-m-d', 'm/d/Y', 'n/j/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (Exception) {
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (Exception) {
            return $value;
        }
    }

    protected function normalizeTimeInput(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        foreach (['H:i', 'H:i:s', 'g:i A', 'g:iA', 'h:i A', 'h:iA'] as $format) {
            try {
                return Carbon::createFromFormat($format, trim($value))->format('H:i');
            } catch (Exception) {
            }
        }

        try {
            return Carbon::parse($value)->format('H:i');
        } catch (Exception) {
            return $value;
        }
    }

    protected function eventRow($eventId)
    {
        return DB::table('events')->where('event_id', $eventId)->first();
    }

    protected function validateSessionData(Request $request): array
    {
        $request->merge([
            'attendance_date' => $this->normalizeDateInput($request->input('attendance_date')),
            'time_in_start' => $this->normalizeTimeInput($request->input('time_in_start')),
            'time_out_end' => $this->normalizeTimeInput($request->input('time_out_end')),
        ]);

        $eventId = $request->input('event_id');
        $event = $eventId ? $this->eventRow($eventId) : null;

        $validator = validator($request->all(), [
            'attendance_name' => 'required|string|max:255',
            'attendance_date' => 'required|date',
            'time_in_start' => 'nullable|date_format:H:i',
            'time_out_end' => 'nullable|date_format:H:i',
        ]);

        $validator->after(function ($validator) use ($request, $event) {
            $timeIn = $request->input('time_in_start');
            $timeOut = $request->input('time_out_end');

            if ($timeIn && $timeOut && $timeOut <= $timeIn) {
                $validator->errors()->add('time_out_end', 'Time out must be later than time in.');
            }

            if ($event && $request->filled('attendance_date')) {
                $attendanceDate = Carbon::parse($request->input('attendance_date'));
                $eventStartDate = Carbon::parse($event->start_date)->startOfDay();
                $eventEndDate = Carbon::parse($event->end_date)->startOfDay();

                if ($attendanceDate->lt($eventStartDate) || $attendanceDate->gt($eventEndDate)) {
                    $validator->errors()->add('attendance_date', 'Attendance date must fall within the selected event date range.');
                }
            }
        });

        return $validator->validate();
    }

    protected function sessionOpeningDateTime(object $session): Carbon
    {
        $event = $session->event;

        return Carbon::parse(
            trim(($session->attendance_date ?? $event?->start_date ?? now()->toDateString()) . ' ' . ($session->time_in_start ?? $event?->start_time ?? '00:00')),
            'Asia/Manila'
        );
    }

    protected function sessionClosingDateTime(object $session): Carbon
    {
        $event = $session->event;
        $closingDate = $session->attendance_date ?? $event?->start_date ?? now()->toDateString();
        $closingTime = $session->time_out_end
            ?? $event?->end_time
            ?? '23:59';

        return Carbon::parse(
            trim($closingDate . ' ' . $closingTime),
            'Asia/Manila'
        );
    }

    protected function sessionAvailabilityState(object $session): string
    {
        $now = Carbon::now('Asia/Manila');

        if ($now->lt($this->sessionOpeningDateTime($session))) {
            return 'upcoming';
        }

        if ($now->gt($this->sessionClosingDateTime($session))) {
            return 'closed';
        }

        return 'open';
    }

    protected function attendanceErrorMessage(Exception $e, string $action): string
    {
        if ($e instanceof ModelNotFoundException) {
            return 'The selected record could not be found. Refresh the page and try again.';
        }

        if ($e instanceof QueryException) {
            return match ($action) {
                'manual' => 'That member already has attendance recorded for this session. Search for another member or review the existing record.',
                default => 'That attendance record conflicts with existing data. Refresh the page and try again.',
            };
        }

        return match ($action) {
            'session' => 'Could not create the attendance session. Check the event, date, and time fields, then try again.',
            'session_update' => 'Could not update the attendance session. Review the session name and time range, then try again.',
            'session_delete' => 'Could not delete the attendance session. Remove related attendance records first or refresh the page and try again.',
            'manual' => 'Could not add attendance for that member. Make sure the member and session are valid, then try again.',
            'approve' => 'Could not approve this attendance. Refresh the list and try again.',
            'reject' => 'Could not reject this attendance. Refresh the list and try again.',
            'remove' => 'Could not remove this attendance record. Refresh the list and try again.',
            default => 'Could not save attendance right now. Please try again.',
        };
    }

    protected function eventsForAttendance()
    {
        return DB::table('vw_events_full')
            ->select('*', DB::raw('type_id as related_type_id'), DB::raw('computed_status as status'))
            ->orderByDesc('start_date')
            ->get()
            ->map(function ($event) {
                $event->type = $event->related_type_id ? (object) [
                    'type_id' => $event->related_type_id,
                    'type_name' => $event->type_name,
                ] : null;

                return $event;
            });
    }

    protected function sessionQuery()
    {
        return DB::table('vw_attendance_session_summary')
            ->select(
                'attendance_session_id',
                'event_id',
                'administrator_id',
                'attendance_name',
                'attendance_date',
                'time_in_start',
                'time_out_end',
                'created_at',
                'updated_at',
                'event_name',
                'start_date',
                'end_date',
                'start_time',
                'end_time',
                'type_id as related_type_id',
                'type_name',
                'availability_status',
                'approved_attendance_count',
                'pending_attendance_count',
                'total_attendance_count'
            );
    }

    protected function hydrateSessions($sessions)
    {
        return collect($sessions)->map(function ($session) {
            $session->event = (object) [
                'event_id' => $session->event_id,
                'event_name' => $session->event_name,
                'start_date' => $session->start_date,
                'end_date' => $session->end_date,
                'start_time' => $session->start_time,
                'end_time' => $session->end_time,
                'type' => $session->related_type_id ? (object) [
                    'type_id' => $session->related_type_id,
                    'type_name' => $session->type_name,
                ] : null,
            ];

            return $session;
        });
    }

    protected function sessionOrFail($id): object
    {
        $session = $this->sessionQuery()
            ->where('attendance_session_id', $id)
            ->first();

        if (! $session) {
            throw new ModelNotFoundException();
        }

        return $this->hydrateSessions([$session])->first();
    }

    protected function attendanceWithMembers(int $sessionId, string $status)
    {
        return DB::table('vw_attendance_records_full')
            ->where('attendance_session_id', $sessionId)
            ->where('status', $status)
            ->select(
                'attendance_id',
                'member_id',
                'event_id',
                'administrator_id',
                'attendance_session_id',
                'attended_at',
                'time_in',
                'time_out',
                'status',
                'member_fname',
                'member_mname',
                'member_lname',
                'email',
                'phone_number'
            )
            ->orderByDesc('attended_at')
            ->get()
            ->map(function ($attendance) {
                $attendance->member = (object) [
                    'member_id' => $attendance->member_id,
                    'member_fname' => $attendance->member_fname,
                    'member_mname' => $attendance->member_mname,
                    'member_lname' => $attendance->member_lname,
                    'email' => $attendance->email,
                    'phone_number' => $attendance->phone_number,
                ];

                return $attendance;
            });
    }

    public function attendance(Request $request)
    {
        $events = $this->eventsForAttendance();

        $attendanceSessions = $this->sessionQuery()
            ->when(trim((string) $request->query('attendance_search', '')) !== '', function ($query) use ($request) {
                $search = trim((string) $request->query('attendance_search'));

                $query->where(function ($query) use ($search) {
                    $query->where('attendance_name', 'like', "%{$search}%")
                        ->orWhereDate('attendance_date', $search)
                        ->orWhere('event_name', 'like', "%{$search}%")
                        ->orWhere('type_name', 'like', "%{$search}%");
                });
            })
            ->when($request->query('event_id'), function ($query, $eventId) {
                $query->where('event_id', $eventId);
            })
            ->when($request->query('type_name'), function ($query, $typeName) {
                $query->where('type_name', $typeName);
            })
            ->when($request->query('attendance_date'), function ($query, $date) {
                $query->whereDate('attendance_date', $date);
            })
            ->orderByDesc('created_at')
            ->paginate(4, ['*'], 'sessions_page')
            ->withQueryString();
        $attendanceSessions->setCollection($this->hydrateSessions($attendanceSessions->getCollection()));

        $attendanceTypes = $attendanceSessions->getCollection()
            ->pluck('event.type.type_name')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if ($attendanceTypes->isEmpty()) {
            $attendanceTypes = $this->sessionQuery()
                ->get()
                ->pluck('type_name')
                ->filter()
                ->unique()
                ->sort()
                ->values();
        }

        $selectedSessionId = $request->attendance_session_id;
        $isMarkingAttendance = false;

        $selectedSession = null;
        $selectedEvent = null;
        $approvedAttendances = collect();
        $pendingAttendances = collect();
        $availableMembers = collect();

        if ($selectedSessionId) {
            $selectedSession = $this->sessionOrFail($selectedSessionId);
            $selectedEvent = $selectedSession->event;
            $isMarkingAttendance = $request->view === 'mark' && $selectedSession;

            if ($isMarkingAttendance && $this->sessionAvailabilityState($selectedSession) !== 'open') {
                $message = $this->sessionAvailabilityState($selectedSession) === 'closed'
                    ? 'This attendance session has already ended.'
                    : 'This attendance session is not open yet. You can start marking attendance once the event begins.';

                return redirect()
                    ->route('attendance', ['attendance_session_id' => $selectedSession->attendance_session_id])
                    ->with('error', $message);
            }

            $approvedAttendances = $this->attendanceWithMembers((int) $selectedSessionId, 'Present');
            $pendingAttendances = $this->attendanceWithMembers((int) $selectedSessionId, 'Pending');

            $alreadyAddedMemberIds = DB::table('vw_attendance_records_full')
                ->where('attendance_session_id', $selectedSessionId)
                ->pluck('member_id');

            $availableMembers = DB::table('members')
                ->where('is_archived', false)
                ->whereNotIn('member_id', $alreadyAddedMemberIds)
                ->get();
        }

        $totalApproved = $approvedAttendances->count();
        $totalPending = $pendingAttendances->count();
        $totalRecords = $totalApproved + $totalPending;

        return view('attendance', compact(
            'events',
            'attendanceSessions',
            'attendanceTypes',
            'selectedSession',
            'selectedEvent',
            'approvedAttendances',
            'pendingAttendances',
            'availableMembers',
            'totalApproved',
            'totalPending',
            'totalRecords',
            'isMarkingAttendance'
        ));
    }

    public function storeSession(Request $request)
    {
        try {
            $validated = $this->validateSessionData($request);
            $request->validate([
                'event_id' => 'required|exists:events,event_id',
            ]);

            $event = $this->eventRow($request->event_id);

            if (! $event) {
                throw new ModelNotFoundException();
            }

            $result = DB::select('CALL sp_create_attendance_session(?, ?, ?, ?, ?, ?)', [
                $event->event_id,
                Auth::id(),
                $validated['attendance_name'],
                $validated['attendance_date'],
                $validated['time_in_start'] ?? null,
                $validated['time_out_end'] ?? null,
            ]);
            $sessionId = $result[0]->attendance_session_id ?? null;

            return redirect()
                ->route('attendance', ['attendance_session_id' => $sessionId])
                ->with('success', 'Attendance created successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->attendanceErrorMessage($e, 'session'));
        }
    }

    public function updateSession(Request $request, $id)
    {
        try {
            $session = $this->sessionOrFail($id);
            $request->merge(['event_id' => $session->event_id]);
            $validated = $this->validateSessionData($request);

            DB::statement('CALL sp_update_attendance_session(?, ?, ?, ?, ?)', [
                $id,
                $validated['attendance_name'],
                $validated['attendance_date'],
                $validated['time_in_start'] ?? null,
                $validated['time_out_end'] ?? null,
            ]);

            return redirect()->route('attendance')->with('success', 'Attendance session updated successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the highlighted attendance session details and try again.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->attendanceErrorMessage($e, 'session_update'));
        }
    }

    public function destroySession($id)
    {
        try {
            $session = DB::table('vw_attendance_session_summary')
                ->where('attendance_session_id', $id)
                ->first();

            if (! $session) {
                throw new ModelNotFoundException();
            }

            if ($session->total_attendance_count > 0) {
                return redirect()->back()->with('error', 'This attendance session already has attendance records. Remove those records first before deleting the session.');
            }

            DB::table('attendance_sessions')->where('attendance_session_id', $id)->delete();

            return redirect()->route('attendance')->with('success', 'Attendance session deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->attendanceErrorMessage($e, 'session_delete'));
        }
    }

    public function addManual(Request $request)
    {
        try {
            $validated = $request->validate([
                'attendance_session_id' => 'required|exists:attendance_sessions,attendance_session_id',
                'member_id' => 'required|exists:members,member_id',
            ]);

            $session = $this->sessionOrFail($validated['attendance_session_id']);
            $member = DB::table('members')
                ->where('member_id', $validated['member_id'])
                ->where('is_archived', false)
                ->first();

            if (! $member) {
                throw new ModelNotFoundException();
            }

            $availability = $this->sessionAvailabilityState($session);

            if ($availability !== 'open') {
                return redirect()->back()->with('error', $availability === 'closed'
                    ? 'This attendance session has already ended. Attendance can no longer be added.'
                    : 'This attendance session is not open yet. You can add attendance once the event begins.');
            }

            DB::select('CALL sp_save_attendance_record(?, ?, ?, ?, ?, ?)', [
                $session->attendance_session_id,
                $member->member_id,
                $session->event_id,
                Auth::id(),
                'Present',
                1,
            ]);

            return redirect()->back()->with('success', 'Attendance added successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->attendanceErrorMessage($e, 'manual'));
        }
    }

    public function approve($id)
    {
        try {
            $attendance = DB::table('attendances')->where('attendance_id', $id)->first();

            if (! $attendance) {
                throw new ModelNotFoundException();
            }

            $session = $this->sessionOrFail($attendance->attendance_session_id);
            $availability = $this->sessionAvailabilityState($session);

            if ($availability !== 'open') {
                return redirect()->back()->with('error', $availability === 'closed'
                    ? 'This attendance session has already ended. Approval is no longer available here.'
                    : 'This attendance session is not open yet. Approval is available once the event begins.');
            }

            DB::select('CALL sp_save_attendance_record(?, ?, ?, ?, ?, ?)', [
                $attendance->attendance_session_id,
                $attendance->member_id,
                $attendance->event_id,
                Auth::id(),
                'Present',
                0,
            ]);

            return redirect()->back()->with('success', 'Attendance approved.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->attendanceErrorMessage($e, 'approve'));
        }
    }

    public function reject($id)
    {
        try {
            $deleted = DB::table('attendances')->where('attendance_id', $id)->exists();

            if (! $deleted) {
                throw new ModelNotFoundException();
            }

            DB::statement('CALL sp_delete_attendance_record(?)', [$id]);

            return redirect()->back()->with('error', 'Attendance rejected.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->attendanceErrorMessage($e, 'reject'));
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = DB::table('attendances')->where('attendance_id', $id)->exists();

            if (! $deleted) {
                throw new ModelNotFoundException();
            }

            DB::statement('CALL sp_delete_attendance_record(?)', [$id]);

            return redirect()->back()->with('error', 'Attendance removed.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->attendanceErrorMessage($e, 'remove'));
        }
    }
}
