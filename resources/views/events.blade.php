<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events - Church Management System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
  body {
    font-family: 'Nunito', sans-serif;
  }

  .scrollbar-hide::-webkit-scrollbar {
    display: none;
  }

  .scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }

  @keyframes pageFadeUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes cardFadeUp {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes modalPop {
    from { opacity: 0; transform: translateY(8px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
  }

  @media (prefers-reduced-motion: no-preference) {
    .min-h-screen > .max-w-7xl { animation: pageFadeUp 260ms ease-out both; }
    .rounded-lg.shadow, .rounded-lg.shadow-sm { animation: cardFadeUp 240ms ease-out both; }
    .fixed:not(.hidden) > .bg-white { animation: modalPop 180ms ease-out both; }
    button, a.inline-flex { transition-property: transform, color, background-color, border-color, box-shadow, opacity; }
    button:hover, a.inline-flex:hover { transform: translateY(-1px); }
  }
</style>
</head>
<body class="bg-gray-50 bg-gradient-to-br from-blue-50 via-white to-purple-50">
  <div class="min-h-screen">
    <div class="sticky top-0 z-40">
    <!-- Header with Navigation -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 py-4 sm:h-16 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center gap-3">
            <img src="{{ asset('images/icons/church-icon.png') }}" alt="Church Icon" class="h-10 w-10">
            <h1 class="text-xl font-semibold text-gray-900">Church Management</h1>
          </div>
          <div class="flex w-full flex-wrap items-center justify-between gap-2 sm:w-auto sm:justify-end sm:gap-4">
   <!-- Logged-in User -->
    <div class="flex items-center gap-2 text-gray-700">
        <img src="{{ asset('images/icons/User-Icon.png') }}" alt="User Icon" class="h-6 w-6">
        <div class="leading-tight">
          <div class="font-medium">{{ Auth::user()->username }}</div>
          <div class="text-xs text-gray-500">{{ $currentRoleLabel }}</div>
        </div>
    </div>

    <!-- Logout -->
    <form action="{{ route('auth.logout') }}" method="POST" onsubmit = "return confirmForm(this, 'Confirm Logout', 'Are you sure you want to logout?')">

        @csrf
        <button type="submit" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-[#111827] border border-gray-300 rounded-md bg-[#F2F8FF] hover:bg-[#e8f1fb] transition-colors duration-200">        
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
            Logout
        </button>
    </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex gap-4 overflow-x-auto whitespace-nowrap scrollbar-hide">
          <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 border-b-2 border-transparent py-4 px-3 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 duration-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Dashboard
          </a>
          <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 border-b-2 border-transparent py-4 px-3 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 duration-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Members
          </a>
          <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 border-b-2 border-blue-600 py-4 px-3 text-sm font-medium text-blue-600 duration-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Events
          </a>
          <a href="{{ route('attendance') }}" class="inline-flex items-center gap-2 border-b-2 border-transparent py-4 px-3 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 duration-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            Attendance
            @if(($navigationBadges['attendance_pending'] ?? 0) > 0)
              <span class="inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-amber-100 px-1.5 py-0.5 text-xs font-semibold leading-none text-amber-700">
                {{ $navigationBadges['attendance_pending'] }}
              </span>
            @endif
          </a>
          @if(Auth::user()->role === 'super_admin')
            <a href="{{ route('report') }}" class="inline-flex items-center gap-2 border-b-2 border-transparent py-4 px-3 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 duration-200">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
              Reports
            </a>
          @endif
        </nav>
      </div>
    </div>

    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-3xl font-semibold text-gray-900">Events</h2>
            <p class="text-gray-600 mt-2">Manage church events and activities</p>
          </div>
          <button onclick="openEventModal()" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-[#F2F8FF] bg-[#030213] rounded-md hover:bg-[#0a0920] transition-colors duration-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create Event
          </button>
        </div>

        @if($errors->any())
          <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm space-y-1">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Events Grid -->
        
          @if($events->isEmpty() && ! request()->hasAny(['event_search', 'type_id', 'status', 'event_date']))
    <div class="bg-white border border-gray-200 rounded-lg p-12 mt-6">
        <div class="flex flex-col items-center justify-center py-12">
            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>

            <p class="text-gray-500 text-sm mb-4">
                No events yet. Create your first event to get started.
            </p>

            <button onclick="openEventModal()"
                class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-[#F2F8FF] bg-[#030213] rounded-md hover:bg-[#0a0920] transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Event
            </button>
        </div>
    </div>
@else
    <form method="GET" action="{{ route('events.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-6">
        <div class="relative lg:col-span-2">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
                type="text"
                id="eventSearch"
                name="event_search"
                value="{{ request('event_search') }}"
                placeholder="Search event name or description..."
                class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </div>
        <select id="eventTypeFilter" name="type_id" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Types</option>
            @foreach($types as $type)
                <option value="{{ $type->type_id }}" @selected((string) request('type_id') === (string) $type->type_id)>{{ $type->type_name }}</option>
            @endforeach
        </select>
        <select id="eventStatusFilter" name="status" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Statuses</option>
            <option value="upcoming" @selected(request('status') === 'upcoming')>Upcoming</option>
            <option value="ongoing" @selected(request('status') === 'ongoing')>Ongoing</option>
            <option value="finished" @selected(request('status') === 'finished')>Finished</option>
        </select>
        <input id="eventDateFilter" name="event_date" value="{{ request('event_date') }}" type="date" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div class="grid grid-cols-2 gap-2 sm:col-span-2 xl:col-span-1">
            <button type="submit" class="px-4 py-2 rounded-md text-sm font-medium text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920]">
                Search
            </button>
            <a href="{{ route('events.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 text-center">
                Clear
            </a>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($events as $event)
            <div class="event-card bg-white rounded-lg shadow border"
                  data-end="{{ $event->end_date }} {{ $event->end_time }}"
                  data-id="{{ $event->event_id }}"
                  data-search="{{ strtolower($event->event_name . ' ' . ($event->type->type_name ?? '') . ' ' . $event->status . ' ' . $event->start_date . ' ' . $event->end_date . ' ' . $event->start_time . ' ' . $event->end_time . ' ' . $event->description) }}"
                  data-type="{{ strtolower($event->type->type_name ?? '') }}"
                  data-status="{{ strtolower($event->status) }}"
                  data-start-date="{{ $event->start_date }}"
                  data-end-date="{{ $event->end_date }}">
                <div class="px-6 py-4 border-b">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="space-y-1 flex-1">
                            @php
                            $now = now();
                            $start = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
                            $end = \Carbon\Carbon::parse($event->end_date . ' ' . $event->end_time);

                            if ($now->lt($start)) {
                                $status = 'Upcoming';
                                $color = 'bg-yellow-100 text-yellow-700';
                            } elseif ($now->between($start, $end)) {
                                $status = 'Ongoing';
                                $color = 'bg-green-100 text-green-700';
                            } else {
                                $status = 'Finished';
                                $color = 'bg-red-100 text-red-700';
                            }
                        @endphp

                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-lg font-semibold">{{ $event->event_name }}</h3>

                            <span class="px-2 py-1 rounded text-xs font-medium {{ $color }}">
                                {{ $status }}
                            </span>
                        </div>
                            <div>
                                <span class="inline-block px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ $event->type->type_name ?? 'No Type' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-1">
                            <button
                                onclick="openEditEventModal(
                                    '{{ $event->event_id }}',
                                    '{{ $event->event_name }}',
                                    '{{ $event->type?->type_id }}',
                                    '{{ $event->start_date }}',
                                    '{{ $event->end_date }}',
                                    '{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}',
                                    '{{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}',
                                    `{{ $event->description }}`
                                )"
                                class="h-8 w-8 p-0 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded">
                                <svg class="h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>

                            <form action="{{ route('events.destroy', $event->event_id) }}" method="POST"
      onsubmit="return dangerconfirmForm(this, 'Confirm Delete', 'Are you sure you want to delete this event?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="h-8 w-8 p-0 text-red-600 hover:text-red-700 hover:bg-red-50 rounded">
                                    <svg class="h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 space-y-3">
                    <div class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <div class="font-medium text-gray-700">Starts</div>
                            <div>{{ \Carbon\Carbon::parse($event->start_date)->format('l, F d, Y') }} at {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <div class="font-medium text-gray-700">Ends</div>
                            <div>{{ \Carbon\Carbon::parse($event->end_date)->format('l, F d, Y') }} at {{ \Carbon\Carbon::parse($event->end_time)->format('h:i A') }}</div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 pt-2 border-t">
                        {{ $event->description ?? 'No description available.' }}
                    </p>
        <div id="event-status-{{ $event->event_id }}">
            @if($event->status === 'finished')
                <div class="pt-3 border-t">
                    <span class="block w-full text-center px-4 py-2 text-sm font-medium text-white bg-gray-400 rounded-md">
                        Event Finished
                    </span>
                </div>
            @elseif($event->status === 'ongoing')
                <div class="pt-3 border-t">
                    <form action="{{ route('events.finish', $event->event_id) }}" method="POST"
      onsubmit="return confirmForm(this, 'Confirm Event End', 'Are you sure you want to mark this event as ended?')">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                            class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                            End Event
                        </button>
                    </form>
                </div>
            @else
                <div class="pt-3 border-t">
                    <span class="block w-full text-center px-4 py-2 text-sm font-medium text-yellow-700 bg-yellow-100 rounded-md">
                        Upcoming Event
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>
@endforeach
            </div>
            <div class="mt-6">
                {{ $events->links() }}
            </div>
            <p id="eventNoResults" class="{{ $events->isEmpty() ? '' : 'hidden' }} text-sm text-gray-500">No events match your search.</p>
        @endif
          
        </div>
      </div>
    </div>
  </div>

  <!-- Create Event Modal -->
  <div id="eventModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
      <div class="px-6 py-4 border-b">
        <h3 class="text-xl font-semibold">Create New Event</h3>
        <p class="text-sm text-gray-600 mt-1">Enter the details of the new event.</p>
      </div>
      <div class="px-6 py-4">
        <form class="space-y-4" action="{{ route('events.store') }}" method="POST"
      onsubmit="return confirmForm(this, 'Confirm Create', 'Are you sure you want to create this event?')">
    @csrf
          <div>
            <label for="eventName" class="block text-sm font-medium text-gray-700 mb-1">Event Name</label>
            <input
              id="eventName"
              name="event_name"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Sunday Worship Service"
              required
            />
          </div>

          <div>
            <label for="eventType" class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
            <select id="eventType" name="type_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
    @foreach($types as $type)
        <option value="{{ $type->type_id }}">{{ $type->type_name }}</option>
    @endforeach
</select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
              <input
                id="startDate"
                name="start_date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
            <div>
              <label for="startTime" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
              <input
                id="startTime"
                name="start_time"
                type="time"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
              <input
                id="endDate"
                name="end_date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
            <div>
              <label for="endTime" class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
              <input
                id="endTime"
                name="end_time"
                type="time"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              id="description"
              name="description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
              placeholder="Event details and information..."
            ></textarea>
          </div>

          <div class="flex gap-3 pt-2">
            <button type="button" onclick="closeEventModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
              Cancel
            </button>
            <button type="submit" class="flex-1 px-4 py-2 rounded-md text-sm font-medium text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920]">
              Create Event
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
    <!-- Edit Event Modal -->
    <div id="editEventModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
        <div class="px-6 py-4 border-b">
            <h3 class="text-xl font-semibold">Edit Event</h3>
            <p class="text-sm text-gray-600 mt-1">Update the event details.</p>
        </div>

        <div class="px-6 py-4">
            <form id="editEventForm" method="POST" class="space-y-4"
      onsubmit="return confirmForm(this, 'Confirm Update', 'Are you sure you want to update this event?')">
                @csrf
                @method('PUT')

                <div>
                    <label for="edit_event_name" class="block text-sm font-medium text-gray-700 mb-1">Event Name</label>
                    <input id="edit_event_name" name="event_name" type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label for="edit_type_id" class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                    <select id="edit_type_id" name="type_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($types as $type)
                            <option value="{{ $type->type_id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input id="edit_start_date" name="start_date" type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="edit_start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                        <input id="edit_start_time" name="start_time" type="time"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input id="edit_end_date" name="end_date" type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="edit_end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                        <input id="edit_end_time" name="end_time" type="time"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <div>
                    <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="edit_description" name="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeEditEventModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 rounded-md text-sm font-medium text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920]">
                        Update Event
                    </button>
                </div>
            </form>
        </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script>
function showToast(message, type = 'success') {
    if (typeof Toastify === 'undefined') {
        const fallbackToast = document.createElement('div');
        fallbackToast.textContent = message;
        fallbackToast.className = `fixed right-4 top-4 z-[99999] max-w-sm rounded-lg px-4 py-3 text-sm shadow-lg ${type === 'error' ? 'bg-red-900' : 'bg-gray-950'} text-white`;
        document.body.appendChild(fallbackToast);
        window.setTimeout(() => fallbackToast.remove(), 3000);
        return;
    }

    Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "right",
        close: true,
        style: {
            background: type === 'error' ? "#7f1d1d" : "#030213",
            color: "#F2F8FF",
            borderRadius: "8px",
            padding: "12px 16px",
            fontFamily: "Nunito"
        }
    }).showToast();
}
</script>
  <script>
    function openEventModal() {
      document.getElementById('eventModal').classList.remove('hidden');
    }

    function closeEventModal() {
      document.getElementById('eventModal').classList.add('hidden');
    }

    function openEventModal() {
        document.getElementById('eventModal').classList.remove('hidden');
    }

    function closeEventModal() {
        document.getElementById('eventModal').classList.add('hidden');
    }

    function openEditEventModal(id, eventName, typeId, startDate, endDate, startTime, endTime, description) {
        const form = document.getElementById('editEventForm');
        form.action = `/events/${id}`;

        document.getElementById('edit_event_name').value = eventName;
        document.getElementById('edit_type_id').value = typeId;
        document.getElementById('edit_start_date').value = startDate;
        document.getElementById('edit_end_date').value = endDate;
        document.getElementById('edit_start_time').value = startTime;
        document.getElementById('edit_end_time').value = endTime;
        document.getElementById('edit_description').value = description ?? '';

        document.getElementById('editEventModal').classList.remove('hidden');
    }

    function closeEditEventModal() {
        document.getElementById('editEventModal').classList.add('hidden');
    }

    function filterEventCards() {
        const input = document.getElementById('eventSearch');
        const type = (document.getElementById('eventTypeFilter')?.value || '').toLowerCase();
        const status = (document.getElementById('eventStatusFilter')?.value || '').toLowerCase();
        const date = document.getElementById('eventDateFilter')?.value || '';
        const cards = document.querySelectorAll('.event-card');
        const emptyState = document.getElementById('eventNoResults');

        if (!input) {
            return;
        }

        const search = input.value.toLowerCase();
        let visibleCount = 0;

        cards.forEach(card => {
            const haystack = card.dataset.search || card.textContent.toLowerCase();
            const matchesSearch = !search || haystack.includes(search);
            const matchesType = !type || card.dataset.type === type;
            const matchesStatus = !status || card.dataset.status === status;
            const matchesDate = !date || (card.dataset.startDate <= date && card.dataset.endDate >= date);
            const isVisible = matchesSearch && matchesType && matchesStatus && matchesDate;
            card.style.display = isVisible ? '' : 'none';

            if (isVisible) {
                visibleCount++;
            }
        });

        if (emptyState) {
            emptyState.classList.toggle('hidden', visibleCount > 0);
        }
    }

    function checkEvents() {
    const events = document.querySelectorAll('[data-end]');

    events.forEach(event => {
        const endTime = new Date(event.getAttribute('data-end'));
        const now = new Date();

        const eventId = event.getAttribute('data-id');
        const statusDiv = document.getElementById('event-status-' + eventId);

        if (now >= endTime) {
            // Change UI instantly
            if (statusDiv) {
                statusDiv.innerHTML = `
                    <div class="pt-3 border-t">
                        <span class="block w-full text-center px-4 py-2 text-sm font-medium text-white bg-gray-400 rounded-md">
                            Event Finished
                        </span>
                    </div>
                `;
            }
        }
    });
}

// Run every 5 seconds
setInterval(checkEvents, 5000);

// Run immediately
checkEvents();

</script>
@if(session('success'))
<script>
    showToast(@json(session('success')), "success");
</script>
@endif

@if(session('error'))
<script>
    showToast(@json(session('error')), "error");
</script>
@endif
<div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">

        <h3 id="confirmTitle" class="text-xl font-semibold text-gray-900 mb-3">
            Confirm Action
        </h3>

        <p id="confirmMessage" class="text-sm text-gray-600 mb-6">
            Are you sure?
        </p>

        <div class="flex justify-end gap-3">

            <!-- Cancel -->
            <button type="button" onclick="closeConfirmModal()"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </button>

            <!-- Confirm (UPDATED COLOR) -->
            <button type="button" id="confirmButton"
                class="px-4 py-2 rounded-md text-sm font-medium bg-[#030213] text-[#F2F8FF] hover:bg-[#0a0920] transition">
                Confirm
            </button>

        </div>
    </div>
</div>
<div id="dangerConfirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 border border-red-200">

        <h3 id="dangerTitle" class="text-xl font-semibold text-red-600 mb-3">
            Confirm Action
        </h3>

        <p id="dangerMessage" class="text-sm text-gray-600 mb-6">
            This action cannot be undone.
        </p>

        <div class="flex justify-end gap-3">

            <!-- Cancel -->
            <button type="button" onclick="closeDangerModal()"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </button>

            <!-- Confirm (RED) -->
            <button type="button" id="dangerConfirmButton"
                class="px-4 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition">
                Confirm
            </button>

        </div>
    </div>
</div>

<script>
let selectedForm = null;

function confirmForm(form, title, message) {
    selectedForm = form;

    document.getElementById('confirmTitle').innerText = title;
    document.getElementById('confirmMessage').innerText = message;
    document.getElementById('confirmModal').classList.remove('hidden');
    

    return false;
}
function dangerconfirmForm(form, title, message) {
    selectedForm = form;

    document.getElementById('dangerTitle').innerText = title;
    document.getElementById('dangerMessage').innerText = message;
    document.getElementById('dangerConfirmModal').classList.remove('hidden');
    document.getElementById('dangerConfirmButton').addEventListener('click', function () {
    if (selectedForm) {
        selectedForm.submit();
    }
});
    

    return false;
}

function closeDangerModal() {
    selectedForm = null;
    document.getElementById('dangerConfirmModal').classList.add('hidden');


    return false;
}

function closeConfirmModal() {
    selectedForm = null;
    document.getElementById('confirmModal').classList.add('hidden');
}

document.getElementById('confirmButton').addEventListener('click', function () {
    if (selectedForm) {
        selectedForm.submit();
    }
});
</script>
</body>
</html>
