<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    protected function validateEvent(Request $request, bool $preventPastStartDate = false): array
    {
        $validator = Validator::make($request->all(), [
            'event_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'description' => 'nullable|string',
            'type_id' => 'required|integer|exists:types,type_id',
        ]);

        $validator->after(function ($validator) use ($request, $preventPastStartDate) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $startTime = $request->input('start_time');
            $endTime = $request->input('end_time');

            if ($preventPastStartDate && $startDate) {
                try {
                    if (Carbon::parse($startDate, 'Asia/Manila')->lt(Carbon::today('Asia/Manila'))) {
                        $validator->errors()->add('start_date', 'Start date cannot be in the past.');
                    }
                } catch (\Throwable) {
                    // The date rule will report malformed dates.
                }
            }

            if ($startDate && $endDate && $startTime && $endTime && $startDate === $endDate && $endTime <= $startTime) {
                $validator->errors()->add('end_time', 'End time must be later than start time for same-day events.');
            }
        });

        return $validator->validate();
    }

    protected function resolveStatus(array $validatedData): string
    {
        $now = Carbon::now('Asia/Manila');
        $startDateTime = Carbon::parse($validatedData['start_date'] . ' ' . $validatedData['start_time'], 'Asia/Manila');
        $endDateTime = Carbon::parse($validatedData['end_date'] . ' ' . $validatedData['end_time'], 'Asia/Manila');

        if ($now->lt($startDateTime)) {
            return 'upcoming';
        }

        if ($now->between($startDateTime, $endDateTime)) {
            return 'ongoing';
        }

        return 'finished';
    }

    protected function eventsQuery()
    {
        return DB::table('events')
            ->leftJoin('types', 'events.type_id', '=', 'types.type_id')
            ->leftJoin('administrators', 'events.administrator_id', '=', 'administrators.administrator_id')
            ->select(
                'events.*',
                'types.type_id as related_type_id',
                'types.type_name',
                'administrators.administrator_id as related_administrator_id',
                'administrators.username as administrator_username',
                'administrators.role as administrator_role',
                DB::raw("CASE
                    WHEN administrators.role = 'super_admin' THEN 'Church Administrator'
                    WHEN administrators.role = 'admin' THEN 'Attendance Coordinator'
                    ELSE 'Administrator'
                END as administrator_role_label")
            );
    }

    protected function hydrate($events)
    {
        return collect($events)->map(function ($event) {
            $event->type = $event->related_type_id ? (object) [
                'type_id' => $event->related_type_id,
                'type_name' => $event->type_name,
            ] : null;

            $event->administrator = $event->related_administrator_id ? (object) [
                'administrator_id' => $event->related_administrator_id,
                'username' => $event->administrator_username,
                'role' => $event->administrator_role,
                'role_label' => $event->administrator_role_label,
            ] : null;

            return $event;
        });
    }

    protected function filteredEvents(Request $request)
    {
        $search = trim((string) $request->query('search', $request->query('event_search', '')));
        $typeId = $request->query('type_id');
        $status = $request->query('status');
        $eventDate = $request->query('event_date');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $administratorId = $request->query('administrator_id');

        return $this->eventsQuery()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('events.event_name', 'like', "%{$search}%")
                        ->orWhere('events.description', 'like', "%{$search}%")
                        ->orWhere('types.type_name', 'like', "%{$search}%")
                        ->orWhere('administrators.username', 'like', "%{$search}%");
                });
            })
            ->when($typeId, fn ($query) => $query->where('events.type_id', $typeId))
            ->when($status, fn ($query) => $query->where('events.status', $status))
            ->when($administratorId, fn ($query) => $query->where('events.administrator_id', $administratorId))
            ->when($eventDate, function ($query) use ($eventDate) {
                $query->whereDate('events.start_date', '<=', $eventDate)
                    ->whereDate('events.end_date', '>=', $eventDate);
            })
            ->when($dateFrom, fn ($query) => $query->whereDate('events.end_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('events.start_date', '<=', $dateTo));
    }

    public function index(Request $request)
    {
        $sorts = [
            'created_at' => 'events.created_at',
            'start_date' => 'events.start_date',
            'event_name' => 'events.event_name',
            'status' => 'events.status',
        ];
        $sortBy = $sorts[$request->query('sort_by', 'created_at')] ?? 'events.created_at';
        $sortDirection = $request->query('sort_direction') === 'asc' ? 'asc' : 'desc';

        $eventsQuery = $this->filteredEvents($request)
            ->orderBy($sortBy, $sortDirection)
            ->orderByDesc('events.event_id');

        if ($request->filled('per_page')) {
            $perPage = min(max((int) $request->integer('per_page', 15), 1), 100);
            $events = $eventsQuery->paginate($perPage)->withQueryString();
            $events->setCollection($this->hydrate($events->getCollection()));

            return response()->json([
                'success' => true,
                'data' => $events->items(),
                'meta' => [
                    'current_page' => $events->currentPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                    'last_page' => $events->lastPage(),
                ],
            ]);
        }

        $events = $this->hydrate($eventsQuery->get());

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateEvent($request, true);

        $eventId = DB::table('events')->insertGetId([
            'event_name' => $validatedData['event_name'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
            'description' => $validatedData['description'] ?? null,
            'type_id' => $validatedData['type_id'],
            'administrator_id' => Auth::user()->administrator_id,
            'status' => $this->resolveStatus($validatedData),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $event = $this->hydrate([
            $this->eventsQuery()->where('events.event_id', $eventId)->first(),
        ])->first();

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully.',
            'data' => $event,
        ], 201);
    }

    public function show(string $id)
    {
        $event = $this->hydrate([
            $this->eventsQuery()->where('events.event_id', $id)->firstOrFail(),
        ])->first();
        $event->attendances = DB::table('attendances')->where('event_id', $id)->get();

        return response()->json([
            'success' => true,
            'data' => $event,
        ]);
    }

    public function update(Request $request, string $id)
    {
        DB::table('events')->where('event_id', $id)->firstOrFail();
        $validatedData = $this->validateEvent($request);

        DB::table('events')
            ->where('event_id', $id)
            ->update([
                'event_name' => $validatedData['event_name'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'description' => $validatedData['description'] ?? null,
                'type_id' => $validatedData['type_id'],
                'status' => $this->resolveStatus($validatedData),
                'updated_at' => now(),
            ]);

        $event = $this->hydrate([
            $this->eventsQuery()->where('events.event_id', $id)->first(),
        ])->first();

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully.',
            'data' => $event,
        ]);
    }

    public function destroy(string $id)
    {
        DB::table('events')->where('event_id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully.',
        ]);
    }
}
