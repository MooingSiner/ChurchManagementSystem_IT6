<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    protected function eventErrorMessage(Exception $e, string $action): string
    {
        if ($e instanceof ModelNotFoundException) {
            return 'That event could not be found. Refresh the page and try again.';
        }

        return match ($action) {
            'create' => 'Could not create the event. Check the event dates, times, and type, then try again.',
            'update' => 'Could not update the event. Make sure the end date/time is after the start and try again.',
            'delete' => 'Could not delete the event right now. Refresh the list and try again.',
            'finish' => 'Could not mark the event as completed. Refresh the page and try again.',
            default => 'Could not save the event right now. Please try again.',
        };
    }

    protected function validateEvent(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'event_name' => 'required|string|max:255',
            'type_id' => 'required|integer|exists:types,type_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'description' => 'nullable|string',
        ]);

        $validator->after(function ($validator) use ($request) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $startTime = $request->input('start_time');
            $endTime = $request->input('end_time');

            if ($startDate && $endDate && $startTime && $endTime && $startDate === $endDate && $endTime <= $startTime) {
                $validator->errors()->add('end_time', 'End time must be later than start time for same-day events.');
            }
        });

        return $validator->validate();
    }

    protected function resolveEventStatus(array $validatedData): string
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

    protected function eventBaseQuery()
    {
        return DB::table('vw_events_full')
            ->select(
                'event_id',
                'event_name',
                'start_date',
                'end_date',
                'start_time',
                'end_time',
                'description',
                DB::raw('computed_status as status'),
                'type_id as related_type_id',
                'type_name',
                'administrator_id as related_administrator_id',
                'administrator_username',
                'administrator_role',
                'administrator_role_label',
                'created_at',
                'updated_at'
            );
    }

    protected function hydrateEventRelations($events)
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
        $search = trim((string) $request->query('event_search', ''));
        $typeId = $request->query('type_id');
        $status = $request->query('status');
        $date = $request->query('event_date');

        return $this->eventBaseQuery()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('event_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('type_name', 'like', "%{$search}%");
                });
            })
            ->when($typeId, function ($query) use ($typeId) {
                $query->where('type_id', $typeId);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('computed_status', $status);
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date);
            });
    }

    protected function typeOptions()
    {
        return DB::table('types')->orderBy('type_name')->get();
    }

    public function event()
    {
        return $this->index(request());
    }

    public function index(Request $request)
    {
        $events = $this->filteredEvents($request)
            ->orderByDesc('created_at')
            ->paginate(6)
            ->withQueryString();
        $events->setCollection($this->hydrateEventRelations($events->getCollection()));

        $types = $this->typeOptions();

        return view('events', compact('events', 'types'));
    }

    public function create()
    {
        $types = $this->typeOptions();
        return view('events.create', compact('types'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $this->validateEvent($request);

            DB::select('CALL sp_create_event(?, ?, ?, ?, ?, ?, ?, ?)', [
                $validatedData['event_name'],
                $validatedData['type_id'],
                $validatedData['start_date'],
                $validatedData['end_date'],
                $validatedData['start_time'],
                $validatedData['end_time'],
                $validatedData['description'] ?? null,
                Auth::user()->administrator_id,
            ]);

            return redirect()->back()->with('success', 'Event created successfully');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the highlighted event details and try again.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->eventErrorMessage($e, 'create'));
        }
    }

    protected function eventWithRelationsOrFail($id)
    {
        $event = $this->eventBaseQuery()->where('event_id', $id)->first();

        if (! $event) {
            throw new ModelNotFoundException();
        }

        return $this->hydrateEventRelations([$event])->first();
    }

    public function show($id)
    {
        $event = $this->eventWithRelationsOrFail($id);
        $event->attendances = DB::table('attendances')->where('event_id', $id)->get();

        return view('events.show', compact('event'));
    }

    public function edit($id)
    {
        $event = DB::table('vw_events_full')->where('event_id', $id)->first();

        if (! $event) {
            throw new ModelNotFoundException();
        }

        $types = $this->typeOptions();

        return view('events.edit', compact('event', 'types'));
    }

    public function update(Request $request, $id)
    {
        try {
            $event = DB::table('vw_events_full')->where('event_id', $id)->first();

            if (! $event) {
                throw new ModelNotFoundException();
            }

            $validatedData = $this->validateEvent($request);

            DB::statement('CALL sp_update_event(?, ?, ?, ?, ?, ?, ?, ?)', [
                $id,
                $validatedData['event_name'],
                $validatedData['type_id'],
                $validatedData['start_date'],
                $validatedData['end_date'],
                $validatedData['start_time'],
                $validatedData['end_time'],
                $validatedData['description'] ?? null,
            ]);

            return redirect()->back()->with('success', 'Event Updated successfully');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the highlighted event details and try again.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->eventErrorMessage($e, 'update'));
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = DB::table('events')->where('event_id', $id)->delete();

            if (! $deleted) {
                throw new ModelNotFoundException();
            }

            return redirect()->back()->with('error', 'Event deleted successfully');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->eventErrorMessage($e, 'delete'));
        }
    }

    public function finish($id)
    {
        try {
            $event = DB::table('vw_events_full')->where('event_id', $id)->first();

            if (! $event) {
                throw new ModelNotFoundException();
            }

            $now = Carbon::now('Asia/Manila');
            $startDateTime = Carbon::parse($event->start_date . ' ' . $event->start_time, 'Asia/Manila');
            $endDateTime = Carbon::parse($event->end_date . ' ' . $event->end_time, 'Asia/Manila');

            if ($event->status === 'finished' || $now->gte($endDateTime)) {
                return redirect()->back()->with('error', 'This event is already finished.');
            }

            if ($now->lt($startDateTime)) {
                return redirect()->back()->with('error', 'This event has not started yet. Wait until the start time before marking it as completed.');
            }

            DB::statement('CALL sp_finish_event(?)', [$id]);

            return redirect()->back()->with('success', 'Event marked as ended');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->eventErrorMessage($e, 'finish'));
        }
    }
}
