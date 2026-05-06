<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Members - Church Management System</title>
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
  @php($canManageMembers = Auth::user()->role === 'super_admin')
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
        <button type="submit" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-[#111827] border border-gray-300 rounded-md bg-[#F2F8FF] hover:bg-[#e8f1fb] transition-colors duration-200">          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
          <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 border-b-2 border-blue-600 py-4 px-3 text-sm font-medium text-blue-600 duration-200">
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
            <h2 class="text-3xl font-semibold text-gray-900">Members</h2>
            <p class="text-gray-600 mt-2">{{ $canManageMembers ? 'Manage your church members' : 'Browse members and view their QR codes' }}</p>
          </div>
          @if($canManageMembers)
            <button onclick="openAddMemberModal()" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-[#F2F8FF] bg-[#030213] rounded-md hover:bg-[#0a0920] transition-colors duration-200">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
              </svg>
              Add Member
            </button>
          @else
            <div class="inline-flex items-center rounded-md border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700">
              Read-only access
            </div>
          @endif
        </div>
      
<!-- Member Toggle -->
<div class="bg-gray-200 rounded-2xl p-1 grid grid-cols-2 gap-1">
    <button type="button" id="approvedBtn" onclick="showApproved()"
        class="py-2 text-sm font-semibold rounded-xl bg-white shadow text-[#030213] transition">
        Approved Members
    </button>

    <button type="button" id="archivedBtn" onclick="showArchived()"
        class="py-2 text-sm font-semibold rounded-xl text-[#030213] transition">
        Archived Members
    </button>
</div>

<form method="GET" action="{{ route('members.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
    <div class="relative lg:col-span-2">
        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input
            type="text"
            id="memberSearch"
            name="member_search"
            value="{{ request('member_search') }}"
            placeholder="Search name, email, phone, or address..."
            class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
    </div>
    <select id="memberMinistryFilter" name="ministry_id" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">All Ministries</option>
        @foreach($ministries as $ministry)
            <option value="{{ $ministry->ministry_id }}" @selected((string) request('ministry_id') === (string) $ministry->ministry_id)>{{ $ministry->ministry_name }}</option>
        @endforeach
    </select>
    <select id="memberGenderFilter" name="gender" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">All Genders</option>
        <option value="Male" @selected(request('gender') === 'Male')>Male</option>
        <option value="Female" @selected(request('gender') === 'Female')>Female</option>
    </select>
    <div class="grid grid-cols-2 gap-2 sm:col-span-2 xl:col-span-1">
        <button type="submit" class="px-4 py-2 rounded-md text-sm font-medium text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920]">
            Search
        </button>
        <a href="{{ route('members.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 text-center">
            Clear
        </a>
    </div>
</form>

<!-- Approved Members Section -->
<div id="approvedSection">
    @if($members->isEmpty())
        <div class="bg-white border border-gray-200 rounded-lg p-12 mt-6">
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>

                <p class="text-gray-500 text-sm mb-4">
                    No members yet. Add your first member to get started.
                </p>

                @if($canManageMembers)
                  <button onclick="openAddMemberModal()"
                      class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-[#F2F8FF] bg-[#030213] rounded-md hover:bg-[#0a0920]">
                      Add Member
                  </button>
                @endif
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($members as $member)
                <div class="member-card bg-white rounded-lg shadow border w-full"
                     data-search="{{ strtolower($member->member_fname . ' ' . $member->member_mname . ' ' . $member->member_lname . ' ' . $member->gender . ' ' . $member->birth_date . ' ' . $member->email . ' ' . $member->phone_number . ' ' . $member->street . ' ' . $member->city . ' ' . $member->province . ' ' . $member->ministries->pluck('ministry_name')->join(' ')) }}"
                     data-gender="{{ strtolower($member->gender) }}"
                     data-ministry="{{ $member->ministries->pluck('ministry_id')->join('|') }}">
                    <div class="px-6 py-4 border-b">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">
                                    {{ $member->member_fname }} {{ $member->member_lname }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $member->gender }}
                                </p>
                            </div>

                            <div class="flex gap-1">
                                <button
                                    type="button"
                                    onclick="openMemberQrModal('{{ $member->member_id }}', @js($member->member_fname . ' ' . $member->member_lname), @js($member->email))"
                                    class="h-8 w-8 flex items-center justify-center text-[#030213] hover:text-blue-700 hover:bg-blue-50 rounded transition-colors"
                                    title="View QR Code">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h2v2h-2v-2zm4 0h2v2h-2v-2zm-4 4h2v2h-2v-2zm4 0h2v2h-2v-2z"/>
                                    </svg>
                                </button>

                                @if($canManageMembers)
                                  <button 
                                      onclick="openEditMemberModal(
                                          '{{ $member->member_id }}',
                                          '{{ $member->member_fname }}',
                                          '{{ $member->member_mname }}',
                                          '{{ $member->member_lname }}',
                                          '{{ $member->gender }}',
                                          '{{ $member->birth_date }}',
                                          '{{ $member->email }}',
                                          '{{ $member->phone_number }}',
                                          '{{ $member->street }}',
                                          '{{ $member->city }}',
                                          '{{ $member->province }}',
                                          '{{ $member->ministries->first()->ministry_id ?? '' }}',
                                          '{{ $member->ministries->first()->pivot->status ?? 'active' }}'
                                      )"
                                      class="h-8 w-8 p-0 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded">
                                      <svg class="h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                      </svg>
                                  </button>

                                  <form action="{{ route('members.destroy', $member->member_id) }}" method="POST"
        onsubmit="return dangerconfirmForm(this, 'Confirm Archive', 'Are you sure you want to archive this member?')">
                                      @csrf
                                      @method('DELETE')

                                      <button type="submit" class="h-8 w-8 flex items-center justify-center text-gray-600 hover:text-gray-700 hover:bg-gray-50 rounded transition-colors">
                                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                      </svg>
                                  </button>
                                  </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 space-y-2">
                        <div class="flex items-center gap-2 text-sm text-gray-600" >
                          <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                          {{ $member->email }}</div>
                        <div class="flex items-center gap-2 text-sm text-gray-600" >
                          <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                          {{ $member->phone_number }}</div>

                        <div class="flex items-center gap-2 text-sm text-gray-600" >
                          <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            '{{ $member->street }}',
                            '{{ $member->province }}',
                            '{{ $member->city }}'
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-600" >
                          <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"/>
                                </svg>
                            {{ \Carbon\Carbon::parse($member->birth_date)->format('F d, Y') }}
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-600" >
                          <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            @forelse($member->ministries as $ministry)
                                <span class="inline-block px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ $ministry->ministry_name }} - {{ ucfirst($ministry->pivot->status ?? 'active') }}
                                </span>
                            @empty
                                <span class="text-gray-400">No ministry</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $members->links() }}
        </div>
        <p id="approvedNoResults" class="hidden mt-4 text-sm text-gray-500">No approved members match your search.</p>
    @endif
</div>

<!-- Archived Members Section -->
<div id="archivedSection" class="hidden">
    @if($archivedMembers->isEmpty())
        <div class="bg-white border border-gray-200 rounded-lg p-12 mt-6">
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>

                <p class="text-gray-500 text-sm">
                    No archived members yet.
                </p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($archivedMembers as $member)
                <div class="member-card bg-white rounded-lg shadow border w-full opacity-80"
                     data-search="{{ strtolower($member->member_fname . ' ' . $member->member_mname . ' ' . $member->member_lname . ' ' . $member->gender . ' ' . $member->birth_date . ' ' . $member->email . ' ' . $member->phone_number . ' ' . $member->street . ' ' . $member->city . ' ' . $member->province . ' ' . $member->ministries->pluck('ministry_name')->join(' ')) }}"
                     data-gender="{{ strtolower($member->gender) }}"
                     data-ministry="{{ $member->ministries->pluck('ministry_id')->join('|') }}">
                    <div class="px-6 py-4 border-b">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">
                                    {{ $member->member_fname }} {{ $member->member_lname }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $member->gender }}
                                </p>
                            </div>
                            <div class="flex gap-1">
                            <button
                              type="button"
                              onclick="openMemberQrModal('{{ $member->member_id }}', @js($member->member_fname . ' ' . $member->member_lname), @js($member->email))"
                              class="h-8 w-8 flex items-center justify-center text-[#030213] hover:text-blue-700 hover:bg-blue-50 rounded transition-colors"
                              title="View QR Code">
                              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h2v2h-2v-2zm4 0h2v2h-2v-2zm-4 4h2v2h-2v-2zm4 0h2v2h-2v-2z"/>
                              </svg>
                          </button>

                            @if($canManageMembers)
                              <button 
                                onclick="openEditMemberModal(
                                    '{{ $member->member_id }}',
                                    '{{ $member->member_fname }}',
                                    '{{ $member->member_mname }}',
                                    '{{ $member->member_lname }}',
                                    '{{ $member->gender }}',
                                    '{{ $member->birth_date }}',
                                    '{{ $member->email }}',
                                    '{{ $member->phone_number }}',
                                    '{{ $member->street }}',
                                    '{{ $member->city }}',
                                    '{{ $member->province }}',
                                    '{{ $member->ministries->first()->ministry_id ?? '' }}',
                                    '{{ $member->ministries->first()->pivot->status ?? 'active' }}'
                                )"
                                class="h-8 w-8 p-0 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded">
                                <svg class="h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                              <form action="{{ route('members.restore', $member->member_id) }}" method="POST"
        onsubmit="return confirmForm(this, 'Confirm Restore', 'Are you sure you want to restore this member?')">
                                  @csrf
                                  @method('PUT')

                                  <button type="submit"
                                      class="h-8 w-8 flex items-center justify-center text-green-600 hover:text-green-700 hover:bg-green-50 rounded transition-colors">
                                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                      </svg>
                                  </button>
                              </form>
                            @endif
                        </div>
                    </div>
                  </div>

                     <div class="px-6 py-4 space-y-2">
                        <div class="flex items-center gap-2 text-sm text-gray-600" >
                          <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                          {{ $member->email }}</div>
                        <div class="flex items-center gap-2 text-sm text-gray-600" >
                          <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                          {{ $member->phone_number }}</div>

                        <div class="flex items-center gap-2 text-sm text-gray-600" >
                          <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            '{{ $member->street }}',
                            '{{ $member->province }}',
                            '{{ $member->city }}'
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-600" >
                          <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"/>
                                </svg>
                            {{ \Carbon\Carbon::parse($member->birth_date)->format('F d, Y') }}
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-600" >
                          <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            @forelse($member->ministries as $ministry)
                                <span class="inline-block px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ $ministry->ministry_name }}
                                </span>
                            @empty
                                <span class="text-gray-400">No ministry</span>
                            @endforelse
                        </div>
                        <div class="flex items-center gap-2 text-sm text-orange-600 pt-2 border-t">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>

                            <span>
                                Archived
                                {{ $member->archived_at ? $member->archived_at->format('F d, Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $archivedMembers->links() }}
        </div>
        <p id="archivedNoResults" class="hidden mt-4 text-sm text-gray-500">No archived members match your search.</p>
    @endif
</div>
      </div>
    </div>
  <!-- Add Member Modal -->
  <div id="memberModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-xl w-full max-h-[90vh] flex flex-col">
      <div class="px-6 py-3 border-b">
        <h3 class="text-xl font-semibold" id="modalTitle">Add New Member</h3>
        <p class="text-sm text-gray-600" id="modalDescription">Enter the details of the new member.</p>
      </div>
      <div class="px-6 py-2 overflow-y-auto">
        <form action="{{ route('members.store') }}" method="POST"
      onsubmit="return confirmForm(this, 'Confirm Add', 'Are you sure you want to add this member?')">
         @csrf
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex-col">
            <label for="member_fname" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
            <input
              id="member_fname"
              name="member_fname"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="John"
              required
            />
          </div>
          <div class="flex-col">
            <label for="member_mname" class="block text-sm font-medium text-gray-700 mb-2">Middle Name (Optional)</label>
            <input
              id="member_mname"
              name="member_mname"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Michael"
            />
          </div>
          <div>
            <label for="member_lname" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
            <input
              id="member_lname"
              name="member_lname"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Doe"
              required
            />
          </div>
          </div>
          

          <div>
  <label for="gender" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Gender</label>
  <div class="relative">
    <select id="gender" name="gender"
      class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
      <option value="Male">Male</option>
      <option value="Female">Female</option>
    </select>

    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </div>
  </div>
</div>

          <div>
            <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Birth Date</label>
            <input
              id="birth_date"
              name="birth_date"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Email</label>
            <input
              id="email"
              name="email"

              type="email"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="john@example.com"
              required
            />
          </div>

          <div>
            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Phone Number</label>
            <input
              id="phone_number"
              name="phone_number"
              type="tel"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="+1234567890"
              required
            />
          </div>

          <div>
            <label for="street" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Street Address</label>
            <textarea
              id="street"
              name="street"
              rows="2"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
              placeholder="123 Main St"
            ></textarea>
          </div>

           <div class="grid grid-cols-1 md:grid-cols-2 md:gap-24">
            <div class="flex-col">
            <label for="province" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Province</label>
            <input
              id="province"
              name="province"
              type="text"
              
              class=" px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2"
              placeholder="Anytown"
            />
            </div>
            <div class="flex-col">
              <label for="city" class="block text-sm font-medium text-gray-700 mb-2 mt-2">City</label>
              <input
                id="city"
                name="city"
                type="text"
               
                class=" px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2"
                placeholder="City"
              />
            </div>
          </div>

          <div>
  <label for="ministry" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Ministry</label>
  <div class="relative">
    <select id="ministry_id" name="ministry_id"
      class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
      <option value="">None</option>
      @foreach($ministries as $ministry)
        <option value="{{ $ministry->ministry_id }}">
          {{ $ministry->ministry_name }}
        </option>
      @endforeach
    </select>

    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500 mb-2">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </div>
  </div>
</div>

<div>
  <label for="ministry_status" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Ministry Status</label>
  <div class="relative">
    <select id="ministry_status" name="ministry_status"
      class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
      <option value="active">Active</option>
      <option value="inactive">Inactive</option>
      <option value="left">Left</option>
    </select>

    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </div>
  </div>
</div>

<div class="flex gap-3 pt-2">
  <button type="button" onclick="closeMemberModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 mb-1">
    Cancel
  </button>
  <button type="submit" class="flex-1 px-4 py-2 rounded-md text-sm font-medium text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920]">
    Add Member
  </button>
</div>
        </form>
      </div>
    </div>
  </div>

  <div id="editMemberModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-lg shadow-xl max-w-xl w-full max-h-[90vh] flex flex-col">
    <div class="px-6 py-3 border-b">
      <h3 class="text-xl font-semibold">Edit Member</h3>
      <p class="text-sm text-gray-600">Update the details of this member.</p>
    </div>

    <div class="px-6 py-2 overflow-y-auto">
      <form id="editMemberForm" method="POST"
      onsubmit="return confirmForm(this, 'Confirm Update', 'Are you sure you want to update this member?')">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="flex-col">
            <label for="edit_member_fname" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
            <input id="edit_member_fname" name="member_fname" type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
          </div>

          <div class="flex-col">
            <label for="edit_member_mname" class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
            <input id="edit_member_mname" name="member_mname" type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>

          <div>
            <label for="edit_member_lname" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
            <input id="edit_member_lname" name="member_lname" type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
          </div>
        </div>

        <div>
  <label for="edit_gender" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Gender</label>
  <div class="relative">
    <select id="edit_gender" name="gender"
      class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
      <option value="Male">Male</option>
      <option value="Female">Female</option>
    </select>

    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </div>
  </div>
</div>

        <div>
          <label for="edit_birth_date" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Birth Date</label>
          <input id="edit_birth_date" name="birth_date" type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        </div>

        <div>
          <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Email</label>
          <input id="edit_email" name="email" type="email"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        </div>

        <div>
          <label for="edit_phone_number" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Phone Number</label>
          <input id="edit_phone_number" name="phone_number" type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        </div>

        <div>
          <label for="edit_street" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Street Address</label>
          <textarea id="edit_street" name="street" rows="2"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
        </div>

        <div class="flex items-end gap-24">
          <div class="flex-col">
            <label for="edit_province" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Province</label>
            <input id="edit_province" name="province" type="text"
              class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2" />
          </div>

          <div class="flex-col">
            <label for="edit_city" class="block text-sm font-medium text-gray-700 mb-2 mt-2">City</label>
            <input id="edit_city" name="city" type="text"
              class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2" />
          </div>
        </div>

        <div>
            <label for="ministry" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Ministry</label>
            <div class="relative">
    <select id="edit_ministry_id" name="ministry_id"
    class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
        
        <option value="">None</option>
        @foreach($ministries as $ministry)
            <option value="{{ $ministry->ministry_id }}">
                {{ $ministry->ministry_name }}
            </option>
        @endforeach
    </select>

    <!-- Custom arrow -->
    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
</div>
      
          </div>

        <div>
          <label for="edit_ministry_status" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Ministry Status</label>
          <div class="relative">
            <select id="edit_ministry_status" name="ministry_status"
              class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="left">Left</option>
            </select>

            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="flex gap-3 pt-2 mt-2">
          <button type="button" onclick="closeEditMemberModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 mb-1">
            Cancel
          </button>
          <button type="submit" class="flex-1 px-4 py-2 rounded-md text-sm font-medium text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920]">
            Update Member
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="memberQrModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-lg shadow-xl max-w-sm w-full">
    <div class="px-6 py-4 border-b flex items-start justify-between gap-4">
      <div>
        <h3 class="text-xl font-semibold">Member QR Code</h3>
        <p id="qrMemberName" class="text-sm text-gray-600 mt-1"></p>
      </div>
      <button type="button" onclick="closeMemberQrModal()" class="h-8 w-8 flex items-center justify-center text-gray-500 hover:text-gray-700 rounded">
        &times;
      </button>
    </div>

    <div class="p-6 space-y-4">
      <div class="rounded-lg border border-gray-200 bg-white p-8 flex justify-center">
        <div id="memberQrCode" class="bg-white p-12 rounded-md shadow-lg"></div>
      </div>

      <div class="rounded-md bg-blue-50 border border-blue-100 px-4 py-3">
        <div class="text-xs uppercase tracking-wide text-blue-700 font-semibold">Member ID</div>
        <div id="qrMemberId" class="text-lg font-semibold text-gray-900 mt-1"></div>
        <div id="qrMemberEmail" class="text-sm text-gray-600 mt-1"></div>
      </div>

      <div class="flex gap-3">
        <button type="button" onclick="closeMemberQrModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
          Close
        </button>
        <button type="button" onclick="downloadMemberQr()" class="flex-1 px-4 py-2 rounded-md text-sm font-medium text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920]">
          Download
        </button>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
    

document.addEventListener('click', function (e) {
    const dropdown = document.getElementById('notificationDropdown');

    if (!e.target.closest('#notificationDropdown') && !e.target.closest('button')) {
        if (!dropdown) {
            return;
        }
        dropdown.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        dropdown.classList.add('opacity-0', 'scale-95', '-translate-y-2', 'pointer-events-none');
    }
});
  function openAddMemberModal() {
    const form = document.querySelector('#memberModal form');
    form.reset();

    document.getElementById('memberModal').classList.remove('hidden');
  }

  function closeMemberModal() {
    document.getElementById('memberModal').classList.add('hidden');
  }

  function openEditMemberModal(id, fname, mname, lname, gender, birthDate, email, phone, street, city, province, ministryId = '', ministryStatus = 'active') {
    const form = document.getElementById('editMemberForm');
    form.action = `/members/${id}`;

    document.getElementById('edit_member_fname').value = fname;
    document.getElementById('edit_member_mname').value = mname;
    document.getElementById('edit_member_lname').value = lname;
    document.getElementById('edit_gender').value = gender;
    document.getElementById('edit_birth_date').value = birthDate;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone_number').value = phone;
    document.getElementById('edit_street').value = street;
    document.getElementById('edit_province').value = province;
    document.getElementById('edit_city').value = city;
    document.getElementById('edit_ministry_id').value = ministryId;
    document.getElementById('edit_ministry_status').value = ministryStatus || 'active';

    document.getElementById('editMemberModal').classList.remove('hidden');
  }

  function closeEditMemberModal() {
    document.getElementById('editMemberModal').classList.add('hidden');
  }

  let currentQrMemberId = null;
  let currentQrMemberName = null;

  function openMemberQrModal(memberId, memberName, memberEmail) {
    currentQrMemberId = memberId;
    currentQrMemberName = memberName;

    document.getElementById('qrMemberName').textContent = memberName;
    document.getElementById('qrMemberId').textContent = `#${memberId}`;
    document.getElementById('qrMemberEmail').textContent = memberEmail || 'No email';

    const qrTarget = document.getElementById('memberQrCode');
    qrTarget.innerHTML = '';

    if (typeof QRCode === 'undefined') {
        qrTarget.innerHTML = '<p class="text-sm text-red-600 text-center">QR generator failed to load.</p>';
    } else {
        new QRCode(qrTarget, {
            text: `member:${memberId}`,
            width: 320,
            height: 320,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    document.getElementById('memberQrModal').classList.remove('hidden');
  }

  function closeMemberQrModal() {
    document.getElementById('memberQrModal').classList.add('hidden');
  }

  function downloadMemberQr() {
    const qrTarget = document.getElementById('memberQrCode');
    const canvas = qrTarget.querySelector('canvas');
    const image = qrTarget.querySelector('img');
    const source = canvas ? canvas.toDataURL('image/png') : image?.src;

    if (!source) {
        showToast('QR code is not ready yet.', 'error');
        return;
    }

    const link = document.createElement('a');
    const safeName = (currentQrMemberName || 'member').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    link.href = source;
    link.download = `${safeName || 'member'}-${currentQrMemberId}-qr.png`;
    link.click();
  }

  function showApproved() {
    document.getElementById('approvedSection').classList.remove('hidden');
    document.getElementById('archivedSection').classList.add('hidden');
    filterMemberCards();

    document.getElementById('approvedBtn').classList.add('bg-white', 'shadow', 'text-[#030213]');
    document.getElementById('approvedBtn').classList.remove('text-gray-500');

    document.getElementById('archivedBtn').classList.remove('bg-white', 'shadow', 'text-[#030213]');
    document.getElementById('archivedBtn').classList.add('text-gray-500');
}

function showArchived() {
    document.getElementById('approvedSection').classList.add('hidden');
    document.getElementById('archivedSection').classList.remove('hidden');
    filterMemberCards();

    document.getElementById('archivedBtn').classList.add('bg-white', 'shadow', 'text-[#030213]');
    document.getElementById('archivedBtn').classList.remove('text-gray-500');

    document.getElementById('approvedBtn').classList.remove('bg-white', 'shadow', 'text-[#030213]');
    document.getElementById('approvedBtn').classList.add('text-gray-500');
}

function filterMemberCards() {
    const input = document.getElementById('memberSearch');
    const search = input ? input.value.toLowerCase() : '';
    const ministry = (document.getElementById('memberMinistryFilter')?.value || '').toLowerCase();
    const gender = (document.getElementById('memberGenderFilter')?.value || '').toLowerCase();

    filterSectionCards('approvedSection', search, ministry, gender, 'approvedNoResults');
    filterSectionCards('archivedSection', search, ministry, gender, 'archivedNoResults');
}

function filterSectionCards(sectionId, search, ministry, gender, emptyStateId) {
    const section = document.getElementById(sectionId);
    const emptyState = document.getElementById(emptyStateId);

    if (!section) {
        return;
    }

    const cards = section.querySelectorAll('.member-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const haystack = card.dataset.search || card.textContent.toLowerCase();
        const matchesSearch = !search || haystack.includes(search);
        const matchesMinistry = !ministry || (card.dataset.ministry || '').split('|').includes(ministry);
        const matchesGender = !gender || card.dataset.gender === gender;
        const isVisible = matchesSearch && matchesMinistry && matchesGender;
        card.style.display = isVisible ? '' : 'none';

        if (isVisible) {
            visibleCount++;
        }
    });

    if (emptyState) {
        emptyState.classList.toggle('hidden', visibleCount > 0);
    }
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
