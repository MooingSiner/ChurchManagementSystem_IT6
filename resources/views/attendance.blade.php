<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance - Church Management System</title>
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
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 py-4 sm:h-16 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center gap-3">
            <img src="{{ asset('images/icons/church-icon.png') }}" alt="Church Icon" class="h-10 w-10">
            <h1 class="text-xl font-semibold text-gray-900">Church Management</h1>
          </div>

          <div class="flex w-full flex-wrap items-center justify-between gap-2 sm:w-auto sm:justify-end sm:gap-4">
            <div class="flex items-center gap-2 text-gray-700">
              <img src="{{ asset('images/icons/User-Icon.png') }}" alt="User Icon" class="h-6 w-6">
              <div class="leading-tight">
                <div class="font-medium">{{ Auth::user()->username }}</div>
                <div class="text-xs text-gray-500">{{ $currentRoleLabel }}</div>
              </div>
            </div>

            <form action="{{ route('auth.logout') }}" method="POST" onsubmit = "return confirmForm(this, 'Confirm Logout', 'Are you sure you want to logout?')">

              @csrf
              <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-[#111827] border border-gray-300 rounded-md bg-[#F2F8FF] hover:bg-[#e8f1fb] transition-colors duration-200">
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
          <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 border-b-2 border-transparent py-4 px-3 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 duration-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Events
          </a>
          <a href="{{ route('attendance') }}" class="inline-flex items-center gap-2 border-b-2 border-blue-600 py-4 px-3 text-sm font-medium text-blue-600 duration-200">
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          @if($isMarkingAttendance && $selectedSession)
            <div>
              <h2 class="text-3xl font-semibold text-gray-900">{{ $selectedSession->attendance_name }}</h2>
              <p class="text-gray-600 mt-2">
                {{ $selectedEvent->event_name }}
                <span class="mx-1">&bull;</span>
                {{ \Carbon\Carbon::parse($selectedSession->attendance_date)->format('l, F d, Y') }}
                <span class="mx-1">&bull;</span>
                Created: {{ optional($selectedSession->created_at)->format('m/d/Y, g:i:s A') ?? 'Not available' }}
              </p>
            </div>
            <a href="{{ route('attendance') }}" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-[#F2F8FF] bg-[#030213] rounded-md hover:bg-[#0a0920] transition-colors duration-200">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              Back to Attendance List
            </a>
          @else
            <div>
              <h2 class="text-3xl font-semibold text-gray-900">Attendance</h2>
              <p class="text-gray-600 mt-2">Manage attendance records for church events</p>
            </div>
            <button type="button" onclick="openCreateAttendanceModal()" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-[#F2F8FF] bg-[#030213] rounded-md hover:bg-[#0a0920] transition-colors duration-200">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Create Attendance
            </button>
          @endif
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

        @if($attendanceSessions->isEmpty() && ! request()->hasAny(['attendance_search', 'event_id', 'type_name', 'attendance_date']))
          <div class="bg-white border-2 border-dashed border-gray-300 rounded-lg">
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
              <svg class="h-16 w-16 text-gray-400 mb-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
              </svg>
              <h3 class="text-2xl font-semibold text-gray-900">No Attendance Yet</h3>
              <p class="mt-3 max-w-xl text-gray-600">
                Get started by creating your first attendance record. You can create multiple attendance records for the same event.
              </p>
              <button type="button" onclick="openCreateAttendanceModal()" class="mt-8 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-[#F2F8FF] bg-[#030213] rounded-md hover:bg-[#0a0920] transition-colors duration-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Your First Attendance
              </button>
            </div>
          </div>
        @elseif(!$isMarkingAttendance)
          <form method="GET" action="{{ route('attendance') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-6">
            <div class="relative lg:col-span-2">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                type="text"
                id="attendanceSessionSearch"
                name="attendance_search"
                value="{{ request('attendance_search') }}"
                placeholder="Search attendance name..."
                class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <select id="attendanceEventFilter" name="event_id" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">All Events</option>
              @foreach($events as $event)
                <option value="{{ $event->event_id }}" @selected((string) request('event_id') === (string) $event->event_id)>{{ $event->event_name }}</option>
              @endforeach
            </select>
            <select id="attendanceTypeFilter" name="type_name" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">All Types</option>
              @foreach($attendanceTypes as $typeName)
                <option value="{{ $typeName }}" @selected(request('type_name') === $typeName)>{{ $typeName }}</option>
              @endforeach
            </select>
            <input id="attendanceDateFilter" name="attendance_date" value="{{ request('attendance_date') }}" type="date" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="grid grid-cols-2 gap-2 sm:col-span-2 xl:col-span-1">
              <button type="submit" class="px-4 py-2 rounded-md text-sm font-medium text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920]">
                Search
              </button>
              <a href="{{ route('attendance') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 text-center">
                Clear
              </a>
            </div>
          </form>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($attendanceSessions as $session)
            @php
              $sessionStartDateTime = \Carbon\Carbon::parse(trim($session->attendance_date . ' ' . ($session->time_in_start ?? $session->event->start_time ?? '00:00')), 'Asia/Manila');
              $sessionEndDateTime = \Carbon\Carbon::parse(trim($session->attendance_date . ' ' . ($session->time_out_end ?? $session->event->end_time ?? '23:59')), 'Asia/Manila');
              $now = \Carbon\Carbon::now('Asia/Manila');
              $sessionState = $now->lt($sessionStartDateTime) ? 'upcoming' : ($now->gt($sessionEndDateTime) ? 'closed' : 'open');
              $sessionAttendanceCount = $session->approved_attendance_count + $session->pending_attendance_count;
              $sessionOpeningLabel = 'Attendance Opens ' . $sessionStartDateTime->format('M d, Y \\a\\t g:i A');
            @endphp
            <div class="attendance-session-card bg-white rounded-lg shadow border overflow-hidden"
                 data-search="{{ strtolower($session->attendance_name . ' ' . ($session->event->event_name ?? '') . ' ' . ($session->event->type->type_name ?? '') . ' ' . ($session->attendance_date ?? '') . ' ' . ($session->created_at ?? '')) }}"
                 data-event="{{ strtolower($session->event->event_name ?? '') }}"
                 data-type="{{ strtolower($session->event->type->type_name ?? '') }}"
                 data-date="{{ $session->attendance_date }}">
              <div class="px-6 py-5 flex items-start justify-between gap-4">
                <div>
                  <h3 class="text-xl font-semibold text-gray-900">{{ $session->attendance_name }}</h3>
                  <span class="mt-3 inline-block px-2 py-1 rounded text-sm font-medium bg-blue-100 text-blue-700">
                    {{ $session->event->type->type_name ?? 'No Type' }}
                  </span>
                </div>

                <div class="flex items-start gap-2">
                  @if($session->pending_attendance_count > 0)
                    <span class="inline-flex min-w-[1.5rem] items-center justify-center rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold leading-none text-amber-700">
                      {{ $session->pending_attendance_count }}
                    </span>
                  @endif

                  <button
                    type="button"
                    onclick="openEditAttendanceSessionModal(
                      '{{ $session->attendance_session_id }}',
                      @js($session->attendance_name),
                      @js($session->event->event_name ?? 'No event'),
                      '{{ $session->attendance_date }}',
                      '{{ $session->time_in_start ? \Carbon\Carbon::parse($session->time_in_start)->format('H:i') : '' }}',
                      '{{ $session->time_out_end ? \Carbon\Carbon::parse($session->time_out_end)->format('H:i') : '' }}'
                    )"
                    class="h-8 w-8 p-0 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded"
                    title="Edit attendance session">
                    <svg class="h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>

                  @if($sessionAttendanceCount === 0)
                    <form action="{{ route('attendance.sessions.destroy', $session->attendance_session_id) }}" method="POST"
      onsubmit="return dangerconfirmForm(this, 'Confirm Delete', 'Delete this attendance session?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="h-8 w-8 p-0 text-red-600 hover:text-red-700 hover:bg-red-50 rounded" title="Delete attendance session">
                        <svg class="h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </form>
                  @else
                    <button type="button" disabled class="h-8 w-8 p-0 text-gray-300 rounded cursor-not-allowed" title="Remove attendance records first before deleting this session">
                      <svg class="h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  @endif
                </div>
              </div>

              <div class="px-6 pb-6 space-y-3">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                  <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  <span class="font-medium text-gray-700">{{ $session->event->event_name ?? 'No event' }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                  <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  <span>{{ \Carbon\Carbon::parse($session->attendance_date)->format('l, F d, Y') }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                  <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                  </svg>
                  <span>Created: {{ optional($session->created_at)->format('m/d/Y, g:i:s A') ?? 'Not available' }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                  <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span>
                    {{ $session->time_in_start ? \Carbon\Carbon::parse($session->time_in_start)->format('g:i A') : 'No time in' }}
                    -
                    {{ $session->time_out_end ? \Carbon\Carbon::parse($session->time_out_end)->format('g:i A') : 'No time out' }}
                  </span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600 pb-3 border-b">
                  <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                  <span>{{ $session->approved_attendance_count }} Members</span>
                </div>

                @if($sessionState === 'open')
                  <a href="{{ route('attendance', ['attendance_session_id' => $session->attendance_session_id, 'view' => 'mark']) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-[#F2F8FF] bg-[#030213] rounded-md hover:bg-[#0a0920] transition-colors duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Mark Attendance
                  </a>
                @elseif($sessionState === 'closed')
                  <div class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-gray-600 bg-gray-100 rounded-md">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Attendance Closed
                  </div>
                @else
                  <div class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-yellow-700 bg-yellow-100 rounded-md">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"></path>
                    </svg>
                    {{ $sessionOpeningLabel }}
                  </div>
                @endif
              </div>
            </div>
            @endforeach
          </div>
          <div class="mt-6">
            {{ $attendanceSessions->links() }}
          </div>
          <p id="attendanceNoResults" class="{{ $attendanceSessions->isEmpty() ? '' : 'hidden' }} text-sm text-gray-500">No attendance records match your search.</p>
        @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white rounded-lg shadow border">
            <div class="px-6 py-4 border-b">
              <h3 class="text-lg font-semibold flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Add Attendance Manually
              </h3>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-4">
                  <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                      type="text"
                      id="memberSearch"
                      placeholder="Search members by name or email..."
                      class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      onkeyup="filterMembers()"
                    />
                  </div>

                  <div id="memberList" class="space-y-2 max-h-[260px] overflow-y-auto pr-2">
                    @forelse($availableMembers as $member)
                      <div class="member-item flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
                           data-name="{{ strtolower($member->member_fname . ' ' . $member->member_lname) }}"
                           data-email="{{ strtolower($member->email) }}">
                        <div class="flex-1">
                          <div class="font-medium">{{ $member->member_fname }} {{ $member->member_lname }}</div>
                          <div class="text-sm text-gray-600">{{ $member->email }}</div>
                        </div>

                        <form action="{{ route('attendance.manual') }}" method="POST"
      onsubmit="return confirmForm(this, 'Confirm Attendance', 'Add attendance for this member?')">
                          @csrf
                          <input type="hidden" name="attendance_session_id" value="{{ $selectedSession->attendance_session_id }}">
                          <input type="hidden" name="member_id" value="{{ $member->member_id }}">
                          <button type="submit" class="px-3 py-1 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded">
                            Add
                          </button>
                        </form>
                      </div>
                    @empty
                      <p class="text-sm text-gray-500">No available members to add.</p>
                    @endforelse
                  </div>
                </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow border">
            <div class="px-6 py-4 border-b">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                  </svg>
                  Pending Attendance
                </h3>
                <span class="px-2 py-1 text-xs font-medium bg-amber-100 text-amber-700 rounded">
                  {{ $totalPending }}
                </span>
              </div>
            </div>

            <div class="px-6 py-4">
              <div class="space-y-3 max-h-[260px] overflow-y-auto pr-2">
                @forelse($pendingAttendances as $attendance)
                  <div class="p-3 border border-amber-200 bg-amber-50 rounded-lg space-y-3">
                    <div>
                      <div class="font-medium">
                        {{ $attendance->member->member_fname }} {{ $attendance->member->member_lname }}
                      </div>
                      <div class="text-sm text-gray-600">
                        Submitted: {{ \Carbon\Carbon::parse($attendance->attended_at)->format('m/d/Y, g:i A') }}
                      </div>
                    </div>

                    <div class="flex gap-2">
                      <form action="{{ route('attendance.approve', $attendance->attendance_id) }}" method="POST" class="flex-1"
                      onsubmit="return confirmForm(this, 'Confirm Approve', 'Approve this attendance?')">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded">
                          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                          Approve
                        </button>
                      </form>

                      <form action="{{ route('attendance.reject', $attendance->attendance_id) }}" method="POST" class="flex-1"
      onsubmit="return confirmForm(this, 'Confirm Reject', 'Reject this attendance?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded">
                          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                          Reject
                        </button>
                      </form>
                    </div>
                  </div>
                @empty
                  <p class="text-sm text-gray-500">No pending attendance submissions.</p>
                @endforelse
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow border">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Approved Attendance ({{ $totalApproved }})</h3>
          </div>
          <div class="px-6 py-4">
            <div class="space-y-2">
              @forelse($approvedAttendances as $attendance)
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                  <div class="flex items-center gap-3">
                    <div>
                      <div class="font-medium">
                        {{ $attendance->member->member_fname }} {{ $attendance->member->member_lname }}
                      </div>
                      <div class="text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($attendance->attended_at)->format('m/d/Y, g:i A') }}
                      </div>
                    </div>
                  </div>

                  <form action="{{ route('attendance.destroy', $attendance->attendance_id) }}" method="POST"
      onsubmit="return dangerconfirmForm(this, 'Confirm Remove', 'Remove this attendance record?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="h-8 w-8 p-0 text-red-600 hover:text-red-700 hover:bg-red-50 rounded">
                      <svg class="h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </form>
                </div>
              @empty
                <p class="text-sm text-gray-500">No approved attendance records yet.</p>
              @endforelse
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>

  <div id="createAttendanceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]">
    <div class="bg-white rounded-lg shadow-xl max-w-xl w-full">
      <div class="px-6 pt-6">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h3 class="text-xl font-semibold text-gray-900">Create New Attendance</h3>
            <p class="text-sm text-gray-600 mt-2">Select an event to create an attendance record.</p>
          </div>
          <button type="button" onclick="closeCreateAttendanceModal()" class="text-gray-500 hover:text-gray-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <form method="POST" action="{{ route('attendance.sessions.store') }}" class="p-6 space-y-5">
        @csrf
        <div>
          <label for="createAttendanceEvent" class="block text-sm font-medium text-gray-700 mb-2">Event</label>
          <select id="createAttendanceEvent" name="event_id" required class="w-full px-4 py-3 border border-gray-200 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">{{ $events->isEmpty() ? 'No events available' : 'Choose an event...' }}</option>
            @foreach($events as $event)
              <option value="{{ $event->event_id }}" {{ old('event_id') == $event->event_id ? 'selected' : '' }}>
                {{ $event->event_name }} - {{ \Carbon\Carbon::parse($event->start_date)->format('m/d/Y') }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label for="attendanceName" class="block text-sm font-medium text-gray-700 mb-2">Attendance Name</label>
          <input id="attendanceName" type="text" name="attendance_name" value="{{ old('attendance_name') }}" required placeholder="e.g. Morning Attendance" class="w-full px-4 py-3 border border-gray-200 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
          <label for="attendanceDate" class="block text-sm font-medium text-gray-700 mb-2">Attendance Date</label>
          <input id="attendanceDate" type="date" name="attendance_date" min="{{ now('Asia/Manila')->toDateString() }}" value="{{ old('attendance_date', now('Asia/Manila')->toDateString()) }}" required class="w-full px-4 py-3 border border-gray-200 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="timeInStart" class="block text-sm font-medium text-gray-700 mb-2">Time In</label>
            <input id="timeInStart" type="time" name="time_in_start" value="{{ old('time_in_start', '08:00') }}" class="w-full px-4 py-3 border border-gray-200 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label for="timeOutEnd" class="block text-sm font-medium text-gray-700 mb-2">Time Out</label>
            <input id="timeOutEnd" type="time" name="time_out_end" value="{{ old('time_out_end', '18:00') }}" class="w-full px-4 py-3 border border-gray-200 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
        </div>

        @if($events->isEmpty())
          <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
            No events are available yet. Create an event first before creating attendance.
          </div>
        @endif

        <div>
          <button type="submit" {{ $events->isEmpty() ? 'disabled' : '' }} class="w-full px-4 py-3 rounded-lg text-sm font-semibold text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920] disabled:cursor-not-allowed disabled:bg-gray-300 transition">
            Create Attendance
          </button>
        </div>
      </form>
    </div>
  </div>

  <div id="editAttendanceSessionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]">
    <div class="bg-white rounded-lg shadow-xl max-w-xl w-full">
      <div class="px-6 pt-6">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h3 class="text-xl font-semibold text-gray-900">Edit Attendance Session</h3>
            <p class="text-sm text-gray-600 mt-2">Update the session details without changing the event it belongs to.</p>
          </div>
          <button type="button" onclick="closeEditAttendanceSessionModal()" class="text-gray-500 hover:text-gray-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <form id="editAttendanceSessionForm" method="POST" class="p-6 space-y-5">
        @csrf
        @method('PUT')
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Event</label>
          <input id="editAttendanceSessionEvent" type="text" readonly class="w-full px-4 py-3 border border-gray-200 bg-gray-100 rounded-lg text-gray-600">
        </div>

        <div>
          <label for="editAttendanceSessionDate" class="block text-sm font-medium text-gray-700 mb-2">Attendance Date</label>
          <input id="editAttendanceSessionDate" type="date" name="attendance_date" required class="w-full px-4 py-3 border border-gray-200 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
          <label for="editAttendanceSessionName" class="block text-sm font-medium text-gray-700 mb-2">Attendance Name</label>
          <input id="editAttendanceSessionName" type="text" name="attendance_name" required class="w-full px-4 py-3 border border-gray-200 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="editTimeInStart" class="block text-sm font-medium text-gray-700 mb-2">Time In</label>
            <input id="editTimeInStart" type="time" name="time_in_start" class="w-full px-4 py-3 border border-gray-200 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label for="editTimeOutEnd" class="block text-sm font-medium text-gray-700 mb-2">Time Out</label>
            <input id="editTimeOutEnd" type="time" name="time_out_end" class="w-full px-4 py-3 border border-gray-200 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
        </div>

        <div class="flex gap-3 pt-2">
          <button type="button" onclick="closeEditAttendanceSessionModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Cancel
          </button>
          <button type="submit" class="flex-1 px-4 py-2 rounded-md text-sm font-medium text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920]">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script>
function showToast(message, type = 'success') {
    if (typeof Toastify === 'undefined') {
        const fallbackToast = document.createElement('div');
        fallbackToast.textContent = message;
        fallbackToast.className = `fixed right-4 top-4 z-[99999] max-w-sm rounded-lg px-4 py-3 text-sm shadow-lg ${type === 'error' ? 'bg-red-900' : 'bg-green-700'} text-white`;
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
            background: type === 'error' ? "#7f1d1d" : "#16a34a",
            color: "#F2F8FF",
            borderRadius: "8px",
            padding: "12px 16px",
            fontFamily: "Nunito"
        }
    }).showToast();
}
</script>
  <script>
    

    document.addEventListener('click', function (e) {
      const dropdown = document.getElementById('notificationDropdown');

      if (dropdown && !e.target.closest('#notificationDropdown') && !e.target.closest('button')) {
        dropdown.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        dropdown.classList.add('opacity-0', 'scale-95', '-translate-y-2', 'pointer-events-none');
      }
    });

    function prepareCreateAttendanceDefaults() {
      const attendanceDate = document.getElementById('attendanceDate');
      const timeInStart = document.getElementById('timeInStart');
      const timeOutEnd = document.getElementById('timeOutEnd');
      const today = new Date().toLocaleDateString('en-CA', { timeZone: 'Asia/Manila' });

      if (attendanceDate) {
        attendanceDate.min = today;

        if (!attendanceDate.value || attendanceDate.value < today) {
          attendanceDate.value = today;
        }
      }

      if (timeInStart && !timeInStart.value) {
        timeInStart.value = '08:00';
      }

      if (timeOutEnd && !timeOutEnd.value) {
        timeOutEnd.value = '18:00';
      }
    }

    function openCreateAttendanceModal() {
      prepareCreateAttendanceDefaults();
      document.getElementById('createAttendanceModal').classList.remove('hidden');
    }

    function closeCreateAttendanceModal() {
      document.getElementById('createAttendanceModal').classList.add('hidden');
    }

    function openEditAttendanceSessionModal(id, name, eventName, eventDate, timeInStart, timeOutEnd) {
      const form = document.getElementById('editAttendanceSessionForm');
      form.action = `/attendance/sessions/${id}`;

      document.getElementById('editAttendanceSessionName').value = name ?? '';
      document.getElementById('editAttendanceSessionEvent').value = eventName ?? '';
      document.getElementById('editAttendanceSessionDate').value = eventDate ?? '';
      document.getElementById('editTimeInStart').value = timeInStart ?? '';
      document.getElementById('editTimeOutEnd').value = timeOutEnd ?? '';

      document.getElementById('editAttendanceSessionModal').classList.remove('hidden');
    }

    function closeEditAttendanceSessionModal() {
      document.getElementById('editAttendanceSessionModal').classList.add('hidden');
    }

    function selectAttendanceEvent(eventId) {
      document.getElementById('createAttendanceEvent').value = eventId;
    }

    function filterCards(inputId, cardSelector, emptyStateId) {
      const input = document.getElementById(inputId);
      const cards = document.querySelectorAll(cardSelector);
      const emptyState = document.getElementById(emptyStateId);

      if (!input) {
        return;
      }

      const search = input.value.toLowerCase();
      let visibleCount = 0;

      cards.forEach(card => {
        const haystack = card.dataset.search || card.textContent.toLowerCase();
        const isVisible = haystack.includes(search);
        card.style.display = isVisible ? '' : 'none';

        if (isVisible) {
          visibleCount++;
        }
      });

      if (emptyState) {
        emptyState.classList.toggle('hidden', visibleCount > 0);
      }
    }

    document.addEventListener('DOMContentLoaded', prepareCreateAttendanceDefaults);

    function filterAttendanceSessions() {
      const search = (document.getElementById('attendanceSessionSearch')?.value || '').toLowerCase();
      const event = (document.getElementById('attendanceEventFilter')?.value || '').toLowerCase();
      const type = (document.getElementById('attendanceTypeFilter')?.value || '').toLowerCase();
      const date = document.getElementById('attendanceDateFilter')?.value || '';
      const cards = document.querySelectorAll('.attendance-session-card');
      const emptyState = document.getElementById('attendanceNoResults');
      let visibleCount = 0;

      cards.forEach(card => {
        const matchesSearch = !search || (card.dataset.search || '').includes(search);
        const matchesEvent = !event || card.dataset.event === event;
        const matchesType = !type || card.dataset.type === type;
        const matchesDate = !date || card.dataset.date === date;
        const isVisible = matchesSearch && matchesEvent && matchesType && matchesDate;

        card.style.display = isVisible ? '' : 'none';

        if (isVisible) {
          visibleCount++;
        }
      });

      if (emptyState) {
        emptyState.classList.toggle('hidden', visibleCount > 0);
      }
    }

    function filterMembers() {
      const input = document.getElementById('memberSearch').value.toLowerCase();
      const items = document.querySelectorAll('.member-item');

      items.forEach(item => {
        const name = item.dataset.name;
        const email = item.dataset.email;

        if (name.includes(input) || email.includes(input)) {
          item.style.display = '';
        } else {
          item.style.display = 'none';
        }
      });
    }
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
