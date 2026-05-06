<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Grace Community Church</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Nunito', sans-serif; }

    .mode-panel {
      transition: opacity 180ms ease, transform 180ms ease;
    }

    .mode-panel.hidden {
      display: none;
    }

    .mode-panel.is-entering {
      opacity: 0;
      transform: translateY(10px);
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

<body>
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">

  <!-- Header -->
  <div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
          <img src="{{ asset('images/icons/church-icon.png') }}" alt="Church Icon" class="h-10 w-10">
          <div>
            <h1 class="text-2xl font-semibold text-gray-900">Grace Community Church</h1>
            <p class="text-sm text-gray-600">Welcome to our church family</p>
          </div>
        </div>

        <form action="{{ route('login') }}" method="GET">
          <button type="submit"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 border rounded-md text-sm font-medium text-[#111827] bg-[#F2F8FF] hover:bg-[#e8f1fb]">
            Login
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Main -->
  <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
    <div class="space-y-12">

      <!-- Hero -->
      <div class="text-center space-y-4">
        <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900">Welcome Home</h2>
        <p class="text-base sm:text-xl text-gray-600 max-w-2xl mx-auto px-2">
          We're glad you're here! Members can mark their attendance for today's service using their member ID.
        </p>
      </div>

      <div id="attendanceChoiceView" class="mode-panel max-w-2xl mx-auto space-y-4">

        @if($events->isEmpty())

          <!-- No Events -->
          <div class="bg-white rounded-lg shadow border-2 border-gray-200">
            <div class="px-6 py-10 text-center space-y-4">
              <svg class="h-16 w-16 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>

              <h3 class="text-xl font-semibold text-gray-900">No Events Ongoing</h3>
              <p class="text-gray-600">
                There are currently no events scheduled. Please check back later or contact the church office.
              </p>
            </div>
          </div>

        @else

          <!-- Event Dropdown if 2 or more events -->
          @if($events->count() >= 2)
            <div>
              <label for="eventSelector" class="block text-center text-sm font-semibold text-gray-900 mb-2">
                Select Event
              </label>

                              <select id="eventSelector"
    onchange="changeSelectedEvent()"
    class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100">

    @foreach($events as $event)
        <option value="{{ $event->event_id }}">
            {{ $event->event_name }} -
            {{ \Carbon\Carbon::parse($event->start_date)->format('n/j/Y') }}
            at
            {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
        </option>
    @endforeach

</select>
            </div>
          @endif

          <!-- Event Cards Hidden/Shown by JS -->
            @foreach($events as $index => $event)
            @php
              $startDateTime = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time, 'Asia/Manila');
              $endDateTime = \Carbon\Carbon::parse($event->end_date . ' ' . $event->end_time, 'Asia/Manila');
              $now = now('Asia/Manila');
              $minutesUntilStart = $now->diffInMinutes($startDateTime, false);
              $allEventSessions = $allAttendanceSessionsByEvent->get($event->event_id, collect());
              $openEventSessions = $attendanceSessionsByEvent->get($event->event_id, collect());
              $hasAnyAttendanceSessions = $allEventSessions->isNotEmpty();
              $hasOpenAttendanceSessions = $openEventSessions->isNotEmpty();

              if ($now->between($startDateTime, $endDateTime)) {
                  $eventStatusLabel = 'Event has started';
                  $eventStatusClasses = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
              } elseif ($minutesUntilStart >= 0 && $minutesUntilStart <= 60) {
                  $eventStatusLabel = 'Event is starting soon';
                  $eventStatusClasses = 'bg-amber-50 text-amber-700 border border-amber-200';
              } else {
                  $eventStatusLabel = 'Event starts at ' . $startDateTime->format('g:i A');
                  $eventStatusClasses = 'bg-sky-50 text-sky-700 border border-sky-200';
              }
            @endphp
            <div id="event-card-{{ $event->event_id }}"
              class="event-card {{ $index !== 0 ? 'hidden' : '' }} border-2 border-blue-100 rounded-lg bg-white shadow overflow-hidden">

              <div class="text-center bg-blue-50 px-6 py-4 border-b">
                <h3 class="flex items-center justify-center gap-2 text-2xl font-semibold">
                  <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  {{ $events->count() >= 2 ? 'Selected Event' : 'Current Event' }}
                </h3>
              </div>

              <div class="px-6 pt-6 pb-6 space-y-4">
                <div class="text-center space-y-2">
                  <h3 class="text-2xl font-semibold text-gray-900">{{ $event->event_name }}</h3>

                  <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                    {{ $event->type->type_name ?? 'No Type' }}
                  </span>

                  <div class="text-lg text-gray-700">
                    {{ \Carbon\Carbon::parse($event->start_date)->format('l, F d, Y') }}
                  </div>

                  <div class="text-lg text-gray-700">
                    {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                  </div>

                  <div class="pt-1">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $eventStatusClasses }}">
                      {{ $eventStatusLabel }}
                    </span>
                  </div>
                </div>

                <div class="space-y-3">
                  @if($hasOpenAttendanceSessions)
                  <button onclick="showScanner('{{ $event->event_id }}')"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-md text-sm font-medium text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920]">
                    Scan ID Card
                  </button>
                  @else
                  <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-4 text-left">
                    <div class="text-sm font-semibold text-amber-900">
                      {{ $hasAnyAttendanceSessions ? 'Attendance is not open yet' : 'Attendance has not been created yet' }}
                    </div>
                    <p class="mt-1 text-sm text-amber-800">
                      {{ $hasAnyAttendanceSessions
                          ? 'Please wait until the scheduled attendance session time before marking attendance for this event.'
                          : 'Please wait for a church administrator or attendance coordinator to open attendance for this event.' }}
                    </p>
                  </div>
                  <button type="button" disabled
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-md text-sm font-medium text-slate-400 bg-slate-100 cursor-not-allowed">
                    Scan ID Card Unavailable
                  </button>
                  @endif

                  <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                      <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                      <span class="px-2 bg-white text-gray-500">OR</span>
                    </div>
                  </div>

                  <button
                    @if($hasOpenAttendanceSessions)
                      onclick="showManualEntry('{{ $event->event_id }}')"
                      class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 rounded-md text-base font-medium text-gray-700 bg-white hover:bg-gray-50"
                    @else
                      type="button" disabled
                      class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 border border-slate-200 rounded-md text-base font-medium text-slate-400 bg-slate-50 cursor-not-allowed"
                    @endif>
                    Manual Entry
                  </button>
                </div>
              </div>
            </div>
          @endforeach

        @endif
      </div>

      <!-- Manual Entry -->
      <div id="manualEntryView" class="mode-panel hidden max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow border">
          <div class="px-6 py-4 border-b">
            <h3 class="text-2xl font-semibold flex items-center gap-2">
              Mark Your Attendance
            </h3>
            <p id="manualEventContext" class="text-sm text-gray-600 mt-2"></p>
          </div>

          <div class="p-6">
            <form action="{{ route('home.attendance.submit') }}" method="POST" class="space-y-6" onsubmit="validateManualForm(event)">
              @csrf

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Event</label>
                <select id="manualEventId" name="event_id" required onchange="populateManualAttendanceSessions()"
                  class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100">
                  @foreach($events as $event)
                    <option value="{{ $event->event_id }}">
                      {{ $event->event_name }} - {{ \Carbon\Carbon::parse($event->start_date)->format('n/j/Y') }} at {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div id="manualSessionContainer" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Attendance Session</label>
                <select id="manualSessionSelector" name="attendance_session_id" required
                  class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100">
                  <option value="">Choose session...</option>
                </select>
              </div>

              <div id="manualSingleSessionContainer" class="hidden rounded-md border border-yellow-100 bg-yellow-50 px-4 py-3">
                <div class="text-sm font-medium text-gray-700">Attendance Session</div>
                <div id="manualSingleSessionLabel" class="mt-1 text-sm text-gray-600"></div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Your Name</label>
                <select name="member_id" required
                  class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100">
                  <option value="">Choose your name</option>
                 @foreach($members as $member)
    <option value="{{ $member->member_id }}"
        data-member="{{ $member->member_id }}">
        {{ $member->member_fname }} {{ $member->member_lname }}
    </option>
@endforeach
                </select>
              </div>

              <div class="flex gap-3">
                <button type="button" onclick="hideManualEntry()"
                  class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium bg-white hover:bg-gray-50">
                  Cancel
                </button>

                <button type="submit"
                  class="flex-1 px-4 py-2 rounded-md text-sm font-medium text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920]">
                  Submit Attendance
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Scanner Error View -->
      <div id="scannerView" class="mode-panel hidden max-w-xl mx-auto">
        <div class="bg-white rounded-lg shadow border p-8 text-center space-y-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Scan Member ID</h3>
            <button onclick="hideScanner()" class="text-xl">&times;</button>
          </div>

          <div class="rounded-lg bg-blue-50 border border-blue-100 p-4 text-left">
            <div class="text-sm font-semibold text-gray-900">Selected Event</div>
            <div id="scannerEventContext" class="text-sm text-gray-600 mt-1"></div>
          </div>

          <div id="attendanceSessionContainer" class="rounded-lg bg-yellow-50 border border-yellow-100 p-4 text-left hidden">
            <label class="block text-sm font-semibold text-gray-900 mb-2">Select Attendance Session</label>
            <select id="attendanceSessionSelector" onchange="updateSelectedSession()" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
              <option value="">Choose session...</option>
            </select>
          </div>

          <div id="attendanceSessionSingle" class="rounded-lg bg-yellow-50 border border-yellow-100 p-4 text-left hidden">
            <div class="text-sm font-semibold text-gray-900">Attendance Session</div>
            <div id="attendanceSessionSingleLabel" class="mt-1 text-sm text-gray-700"></div>
          </div>

          <div id="qrReader" class="min-h-[260px] overflow-hidden rounded-lg border border-gray-200 bg-gray-50"></div>
          <p id="scannerStatus" class="text-sm text-gray-600">
            Point the camera at a member QR code.
          </p>
          
          <div class="rounded-lg bg-blue-50 border border-blue-100 p-3 text-sm text-gray-700">
            <strong>Tips for better scanning:</strong>
            <ul class="list-disc list-inside mt-2 space-y-1">
              <li>Ensure good lighting (natural light works best)</li>
              <li>Hold QR code 6-12 inches from camera</li>
              <li>Keep QR code fully in the scanning frame</li>
            </ul>
          </div>

          <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <button type="button" onclick="startQrScanner()"
              class="px-4 py-2 rounded-md text-sm font-medium text-[#F2F8FF] bg-[#030213] hover:bg-[#0a0920]">
              Start Camera
            </button>

            <label class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium bg-white hover:bg-gray-50 cursor-pointer">
              Upload QR Image
              <input type="file" accept="image/*" onchange="scanUploadedQr(this)" class="hidden">
            </label>
          </div>

          <form id="scannerAttendanceForm" action="{{ route('home.attendance.submit') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" id="scannerEventId" name="event_id">
            <input type="hidden" id="scannerSessionId" name="attendance_session_id">
            <input type="hidden" id="scannerMemberId" name="member_id">
          </form>

          <button onclick="hideScanner()"
            class="px-4 py-2 border border-gray-300 rounded-md bg-white hover:bg-gray-50">
            Go Back
          </button>
        </div>
      </div>

      <!-- Info Cards -->
        <div id="homeInfoCards" class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
          <!-- Mission Card -->
          <div class="bg-white rounded-lg shadow border">
            <div class="px-6 py-4 border-b">
              <h4 class="text-lg font-semibold">Our Mission</h4>
            </div>
            <div class="px-6 py-4">
              <p class="text-gray-600">
                To glorify God by making disciples who love God and love others.
              </p>
            </div>
          </div>

          <!-- Join Us Card -->
          <div class="bg-white rounded-lg shadow border">
            <div class="px-6 py-4 border-b">
              <h4 class="text-lg font-semibold">Join Us</h4>
            </div>
            <div class="px-6 py-4">
              <p class="text-gray-600">
                Everyone is welcome to worship with us. Come as you are and experience God's love.
              </p>
            </div>
          </div>

          <!-- Get Involved Card -->
          <div class="bg-white rounded-lg shadow border">
            <div class="px-6 py-4 border-b">
              <h4 class="text-lg font-semibold">Get Involved</h4>
            </div>
            <div class="px-6 py-4">
              <p class="text-gray-600">
                Discover ministries and opportunities to serve and grow in your faith journey.
              </p>
            </div>
          </div>
        </div>

    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script>
function showToast(message, type = 'success') {
    if (typeof Toastify === 'undefined') {
        const fallbackToast = document.createElement('div');
        fallbackToast.textContent = message;
        fallbackToast.className = `fixed right-4 top-4 z-[99999] max-w-sm rounded-lg px-4 py-3 text-sm shadow-lg ${type === 'error' ? 'bg-red-900' : 'bg-gray-950'} text-white`;
        document.body.appendChild(fallbackToast);
        window.setTimeout(() => fallbackToast.remove(), 4000);
        return;
    }

    Toastify({
        text: message,
        duration: 4000,
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
let qrScanner = null;
let qrScannerMode = null;
let qrVideoStream = null;
let qrVideoElement = null;
let qrScanLoopTimer = null;
let barcodeDetector = null;
let scanSubmitting = false;
let scannerStarting = false;
let lastScannedMemberId = null;
let lastScanAt = 0;

function changeSelectedEvent() {
    const selectedId = document.getElementById('eventSelector').value;

    document.querySelectorAll('.event-card').forEach(card => {
        card.classList.add('hidden');
    });

    document.getElementById('event-card-' + selectedId).classList.remove('hidden');

    // sync manual form
    const manualEvent = document.getElementById('manualEventId');
    if (manualEvent) {
        manualEvent.value = selectedId;
    }
    filterMembersByEvent(selectedId);
}

function showManualEntry(eventId) {
    showAttendanceMode('manualEntryView');

    if (eventId) {
        document.getElementById('manualEventId').value = eventId;
        filterMembersByEvent(eventId);
        setSelectedEventContext(eventId);
        populateManualAttendanceSessions();
    }

    window.scrollTo({
        top: document.getElementById('manualEntryView').offsetTop - 40,
        behavior: 'smooth'
    });
}

function hideManualEntry() {
    showAttendanceMode('attendanceChoiceView');
}

function showScanner(eventId) {
    showAttendanceMode('scannerView');

    if (eventId) {
        const manualEvent = document.getElementById('manualEventId');
        if (manualEvent) {
            manualEvent.value = eventId;
        }
        setSelectedEventContext(eventId);
        document.getElementById('scannerEventId').value = eventId;
        
        // Reset scanning flags
        scanSubmitting = false;
        scannerStarting = false;
        lastScannedMemberId = null;
        lastScanAt = 0;
        
        // Populate attendance sessions for this event
        populateAttendanceSessions(eventId);
    }

    window.scrollTo({
        top: document.getElementById('scannerView').offsetTop - 40,
        behavior: 'smooth'
    });
}

function hideScanner() {
    stopQrScanner();
    showAttendanceMode('attendanceChoiceView');
}

async function startQrScanner() {
    const status = document.getElementById('scannerStatus');

    if (scannerStarting) {
        return;
    }

    scannerStarting = true;
    status.textContent = 'Starting camera...';

    if (qrScanner || qrScannerMode) {
        await stopQrScanner();
    }

    try {
        let started = false;

        try {
            started = await startNativeQrScanner();
        } catch (error) {
            await stopQrScanner();
        }

        if (!started) {
            await startHtml5QrScanner();
        }
    } catch (error) {
        await stopQrScanner();
        status.textContent = cameraErrorMessage(error);
    } finally {
        scannerStarting = false;
    }
}

function cameraErrorMessage(error) {
    if (error?.name === 'NotAllowedError' || error?.name === 'SecurityError') {
        return 'Camera permission was blocked. Allow camera access in your browser settings, then try again.';
    }

    if (error?.name === 'NotFoundError' || error?.name === 'OverconstrainedError') {
        return 'No usable camera was found. You can upload the downloaded QR image instead.';
    }

    if (error?.name === 'NotReadableError') {
        return 'The camera is already in use or was blocked by the browser. Close other camera apps, then try again.';
    }

    return 'Unable to access camera. If you are using Brave, lower Shields for this site or allow camera access, then try again.';
}

async function canUseNativeBarcodeDetector() {
    if (!window.isSecureContext || !('BarcodeDetector' in window)) {
        return false;
    }

    try {
        const supportedFormats = await BarcodeDetector.getSupportedFormats();
        return supportedFormats.includes('qr_code');
    } catch (error) {
        return false;
    }
}

async function startNativeQrScanner() {
    const status = document.getElementById('scannerStatus');
    const reader = document.getElementById('qrReader');
    let stream = null;

    if (!await canUseNativeBarcodeDetector()) {
        return false;
    }

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            audio: false,
            video: {
                facingMode: { ideal: 'environment' },
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        });

        barcodeDetector = new BarcodeDetector({ formats: ['qr_code'] });
        qrScannerMode = 'native';
        qrVideoStream = stream;
        qrVideoElement = document.createElement('video');
        qrVideoElement.setAttribute('autoplay', '');
        qrVideoElement.setAttribute('muted', '');
        qrVideoElement.setAttribute('playsinline', '');
        qrVideoElement.className = 'h-full w-full object-cover';
        qrVideoElement.srcObject = stream;

        reader.innerHTML = '';
        reader.appendChild(qrVideoElement);

        await qrVideoElement.play();
        status.textContent = 'Camera ready. Native QR detection is active.';

        const scanFrame = async () => {
            if (qrScannerMode !== 'native' || !barcodeDetector || !qrVideoElement) {
                return;
            }

            try {
                if (qrVideoElement.readyState >= HTMLMediaElement.HAVE_ENOUGH_DATA && !scanSubmitting) {
                    const barcodes = await barcodeDetector.detect(qrVideoElement);
                    const match = barcodes.find((barcode) => barcode.rawValue);

                    if (match) {
                        await submitScannedMember(match.rawValue);
                    }
                }
            } catch (error) {
            } finally {
                if (qrScannerMode === 'native') {
                    qrScanLoopTimer = window.setTimeout(scanFrame, 90);
                }
            }
        };

        qrScanLoopTimer = window.setTimeout(scanFrame, 120);
        return true;
    } catch (error) {
        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
        }

        throw error;
    }
}

async function startHtml5QrScanner() {
    const status = document.getElementById('scannerStatus');
    const reader = document.getElementById('qrReader');

    if (typeof Html5Qrcode === 'undefined') {
        status.textContent = 'QR scanner failed to load. Please try manual entry.';
        return;
    }

    qrScanner = new Html5Qrcode('qrReader');
    qrScannerMode = 'html5-qrcode';

    const cameras = await Html5Qrcode.getCameras();

    if (!cameras || cameras.length === 0) {
        status.textContent = 'No camera found. You can upload the downloaded QR image instead.';
        qrScanner = null;
        qrScannerMode = null;
        return;
    }

    const backCamera = cameras.find(camera => /back|rear|environment/i.test(camera.label));
    const cameraConfig = backCamera ? { deviceId: { exact: backCamera.id } } : { deviceId: { exact: cameras[0].id } };

    await qrScanner.start(
        cameraConfig,
        {
            fps: 18,
            qrbox: (viewfinderWidth, viewfinderHeight) => {
                const edge = Math.floor(Math.min(viewfinderWidth, viewfinderHeight) * 0.72);
                return { width: Math.max(Math.min(edge, 280), 220), height: Math.max(Math.min(edge, 280), 220) };
            },
            aspectRatio: 1.333334,
            disableFlip: false
        },
        (decodedText) => submitScannedMember(decodedText),
        () => {}
    );

    reader.classList.remove('hidden');
    status.textContent = 'Camera ready. Fallback QR detection is active.';
}

async function stopQrScanner() {
    const reader = document.getElementById('qrReader');
    const scanner = qrScanner;
    qrScanner = null;
    qrScannerMode = null;
    barcodeDetector = null;

    if (qrScanLoopTimer) {
        window.clearTimeout(qrScanLoopTimer);
        qrScanLoopTimer = null;
    }

    if (qrVideoElement) {
        qrVideoElement.pause();
        qrVideoElement.srcObject = null;
        qrVideoElement.remove();
        qrVideoElement = null;
    }

    if (qrVideoStream) {
        qrVideoStream.getTracks().forEach((track) => track.stop());
        qrVideoStream = null;
    }

    if (reader) {
        reader.innerHTML = '';
    }

    if (!scanner) {
        return;
    }

    try {
        await scanner.stop();
        scanner.clear();
    } catch (error) {
    }
}

async function submitScannedMember(decodedText) {
    if (scanSubmitting) return;

    const sessionId = document.getElementById('scannerSessionId').value;
    const eventId   = document.getElementById('scannerEventId').value;
    const status    = document.getElementById('scannerStatus');

    // Block scan if multiple sessions exist but none selected yet
    const container = document.getElementById('attendanceSessionContainer');
    if (!container.classList.contains('hidden') && !sessionId) {
        status.textContent = 'Please select an attendance session first.';
        return;
    }

    const memberId = String(decodedText).trim().replace(/^member:/i, '');
    if (!/^\d+$/.test(memberId)) {
        status.textContent = 'This QR code is not a valid member code.';
        return;
    }

    const now = Date.now();
    if (memberId === lastScannedMemberId && (now - lastScanAt) < 2000) {
        return;
    }

    lastScannedMemberId = memberId;
    lastScanAt = now;

    scanSubmitting = true;
    status.textContent = 'Member found. Submitting attendance...';

    try {
        const response = await fetch('/api/attendance/scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                member_id:             memberId,
                event_id:              eventId,
                attendance_session_id: sessionId || null,
            }),
        });

        const data = await response.json();

        if (response.ok) {
            const memberName = data?.data?.member
                ? `${data.data.member.member_fname} ${data.data.member.member_lname}`
                : `Member ${memberId}`;
            showToast(`${memberName} marked present. Waiting for administrator approval.`, 'success');
            status.textContent = 'Point the camera at a member QR code.';
        } else {
            showToast(data.error ?? 'Something went wrong.', 'error');
            status.textContent = data.error ?? 'Error. Try again.';
        }

    } catch (err) {
        showToast('Network error. Please try again.', 'error');
        status.textContent = 'Network error. Try again.';
    } finally {
        scanSubmitting = false; // camera stays alive for next scan
    }
}

async function scanUploadedQr(input) {
    const file = input.files?.[0];
    const status = document.getElementById('scannerStatus');

    if (!file) {
        return;
    }

    try {
        await stopQrScanner();
        let decodedText = null;

        if (await canUseNativeBarcodeDetector()) {
            const nativeDetector = new BarcodeDetector({ formats: ['qr_code'] });
            const bitmap = await createImageBitmap(file);
            const barcodes = await nativeDetector.detect(bitmap);
            bitmap.close();
            decodedText = barcodes.find((barcode) => barcode.rawValue)?.rawValue ?? null;
        }

        if (!decodedText) {
            if (typeof Html5Qrcode === 'undefined') {
                status.textContent = 'QR scanner failed to load. Please try manual entry.';
                input.value = '';
                return;
            }

            const fileScanner = new Html5Qrcode('qrReader');
            decodedText = await fileScanner.scanFile(file, true);
            fileScanner.clear();
        }

        if (!decodedText) {
            throw new Error('No QR code found');
        }

        submitScannedMember(decodedText);
    } catch (error) {
        status.textContent = 'Could not read that QR image. Try a clearer downloaded member QR.';
    } finally {
        input.value = '';
    }
}

function showAttendanceMode(modeId) {
    ['attendanceChoiceView', 'manualEntryView', 'scannerView'].forEach(id => {
        const element = document.getElementById(id);

        if (element) {
            if (id === modeId) {
                element.classList.remove('hidden');
                element.classList.add('is-entering');
                requestAnimationFrame(() => {
                    element.classList.remove('is-entering');
                });
            } else {
                element.classList.add('hidden');
                element.classList.add('is-entering');
            }
        }
    });

    const infoCards = document.getElementById('homeInfoCards');
    if (infoCards) {
        infoCards.classList.toggle('hidden', modeId !== 'attendanceChoiceView');
    }
}

function setSelectedEventContext(eventId) {
    const option = document.querySelector(`#eventSelector option[value="${eventId}"]`);
    const card = document.getElementById('event-card-' + eventId);
    const title = card?.querySelector('h3.text-2xl')?.textContent.trim();
    const context = option ? option.textContent.trim() : (title || 'Selected event');

    const manualContext = document.getElementById('manualEventContext');
    const scannerContext = document.getElementById('scannerEventContext');

    if (manualContext) {
        manualContext.textContent = context;
    }

    if (scannerContext) {
        scannerContext.textContent = context;
    }
}
</script>
<script>
  const attendanceRecords = @json($attendanceMemberIds);
  const attendanceSessionIdsByEvent = @json($attendanceSessionIdsByEvent);
  const allAttendanceSessions = @json($attendanceSessionsByEvent);

  // Initialize first event's sessions on page load
  document.addEventListener('DOMContentLoaded', function() {
    const firstEventId = Object.keys(allAttendanceSessions)[0];
    if (firstEventId) {
      populateManualAttendanceSessions();
    }
  });

function filterMembersByEvent(eventId) {
    const memberSelect = document.querySelector('select[name="member_id"]');
    const options = memberSelect.querySelectorAll('option[data-member]');
    const attendanceSessionId = attendanceSessionIdsByEvent[eventId];

    options.forEach(option => {
        const memberId = option.getAttribute('data-member');

        const alreadySubmitted = attendanceRecords.some(record =>
            record.attendance_session_id == attendanceSessionId && record.member_id == memberId
        );

        option.hidden = alreadySubmitted;
    });

    memberSelect.value = '';
}

function populateAttendanceSessions(eventId) {
    const sessions = allAttendanceSessions[eventId];
    const container = document.getElementById('attendanceSessionContainer');
    const selector = document.getElementById('attendanceSessionSelector');
    const singleContainer = document.getElementById('attendanceSessionSingle');
    const singleLabel = document.getElementById('attendanceSessionSingleLabel');
    
    if (!sessions || sessions.length === 0) {
        container.classList.add('hidden');
        singleContainer.classList.add('hidden');
        document.getElementById('scannerSessionId').value = '';
        return;
    }
    
    if (sessions.length === 1) {
        container.classList.add('hidden');
        singleContainer.classList.remove('hidden');
        singleLabel.textContent = sessions[0].attendance_name || `Session ${sessions[0].attendance_session_id}`;
        document.getElementById('scannerSessionId').value = sessions[0].attendance_session_id;
        return;
    }
    
    singleContainer.classList.add('hidden');
    selector.innerHTML = '<option value="">Choose session...</option>';
    
    sessions.forEach(session => {
        const option = document.createElement('option');
        option.value = session.attendance_session_id;
        option.textContent = session.attendance_name || `Session ${session.attendance_session_id}`;
        selector.appendChild(option);
    });
    
    container.classList.remove('hidden');
    selector.value = '';
    document.getElementById('scannerSessionId').value = '';
}

function updateSelectedSession() {
    const sessionId = document.getElementById('attendanceSessionSelector').value;
    document.getElementById('scannerSessionId').value = sessionId;
}

function populateManualAttendanceSessions() {
    const eventId = document.getElementById('manualEventId').value;
    const sessions = allAttendanceSessions[eventId];
    const container = document.getElementById('manualSessionContainer');
    const selector = document.getElementById('manualSessionSelector');
    const singleContainer = document.getElementById('manualSingleSessionContainer');
    const singleLabel = document.getElementById('manualSingleSessionLabel');
    
    if (!sessions || sessions.length === 0) {
        container.classList.add('hidden');
        singleContainer.classList.add('hidden');
        selector.value = '';
        return;
    }
    
    if (sessions.length === 1) {
        container.classList.add('hidden');
        singleContainer.classList.remove('hidden');
        singleLabel.textContent = sessions[0].attendance_name || `Session ${sessions[0].attendance_session_id}`;
        selector.value = sessions[0].attendance_session_id;
        return;
    }
    
    singleContainer.classList.add('hidden');
    selector.innerHTML = '<option value="">Choose session...</option>';
    
    sessions.forEach(session => {
        const option = document.createElement('option');
        option.value = session.attendance_session_id;
        option.textContent = session.attendance_name || `Session ${session.attendance_session_id}`;
        selector.appendChild(option);
    });
    
    container.classList.remove('hidden');
    selector.value = '';
}

function validateManualForm(event) {
    const sessionId = document.getElementById('manualSessionSelector').value;
    const container = document.getElementById('manualSessionContainer');
    
    // Check if session selector is visible and a session is selected
    if (!container.classList.contains('hidden') && !sessionId) {
        event.preventDefault();
        alert('Please select an attendance session.');
        return false;
    }
    
    return true;
}

// Show success/error toasts on page load if messages exist
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showToast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
        showToast(@json(session('error')), 'error');
    @endif
});
</script>
</body>
</html>
