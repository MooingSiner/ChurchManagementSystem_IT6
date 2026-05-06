<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected function filteredEvents(Request $request)
    {
        $eventId = $request->query('event_id');
        $typeId = $request->query('type_id');
        $status = $request->query('status');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        return DB::table('events')
            ->when($eventId, fn ($query) => $query->where('event_id', $eventId))
            ->when($typeId, fn ($query) => $query->where('type_id', $typeId))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($dateFrom, fn ($query) => $query->whereDate('end_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('start_date', '<=', $dateTo));
    }

    protected function filteredAttendances(Request $request)
    {
        $eventId = $request->query('event_id');
        $memberId = $request->query('member_id');
        $sessionId = $request->query('attendance_session_id');
        $status = $request->query('attendance_status', $request->query('status'));
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        return DB::table('attendances')
            ->join('events', 'attendances.event_id', '=', 'events.event_id')
            ->when($eventId, fn ($query) => $query->where('attendances.event_id', $eventId))
            ->when($memberId, fn ($query) => $query->where('attendances.member_id', $memberId))
            ->when($sessionId, fn ($query) => $query->where('attendances.attendance_session_id', $sessionId))
            ->when($status, fn ($query) => $query->where('attendances.status', $status))
            ->when($request->query('type_id'), fn ($query, $typeId) => $query->where('events.type_id', $typeId))
            ->when($dateFrom, fn ($query) => $query->whereDate('attendances.attended_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('attendances.attended_at', '<=', $dateTo));
    }

    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_members' => DB::table('members')
                    ->when(! $request->boolean('include_archived'), fn ($query) => $query->where('is_archived', false))
                    ->count(),
                'total_events' => $this->filteredEvents($request)->count(),
                'total_attendances' => $this->filteredAttendances($request)->count(),
                'attendance_by_status' => $this->filteredAttendances($request)
                    ->select('attendances.status', DB::raw('COUNT(*) as total'))
                    ->groupBy('attendances.status')
                    ->orderBy('attendances.status')
                    ->get(),
                'events_by_type' => $this->filteredEvents($request)
                    ->leftJoin('types', 'events.type_id', '=', 'types.type_id')
                    ->select('types.type_id', 'types.type_name', DB::raw('COUNT(*) as total'))
                    ->groupBy('types.type_id', 'types.type_name')
                    ->orderBy('types.type_name')
                    ->get(),
            ],
        ]);
    }
}
