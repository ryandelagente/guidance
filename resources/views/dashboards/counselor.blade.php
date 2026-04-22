<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Guidance Counselor Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome --}}
            <div class="bg-white shadow-sm rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 font-bold text-lg">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-lg">Welcome back, {{ auth()->user()->name }}</p>
                    <p class="text-sm text-gray-400">Guidance Counselor &bull; {{ now()->format('l, F d, Y') }}</p>
                </div>
            </div>

            {{-- KPI Row --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                $kpis = [
                    ['label' => 'My Students',      'value' => $myStudents,   'color' => 'bg-teal-600',   'href' => route('students.index')],
                    ['label' => "Today's Appts",    'value' => $myToday,      'color' => 'bg-indigo-600', 'href' => route('appointments.index')],
                    ['label' => 'Upcoming',         'value' => $myUpcoming,   'color' => 'bg-blue-600',   'href' => route('appointments.index')],
                    ['label' => 'My Referrals',     'value' => $myReferrals,  'color' => 'bg-orange-600', 'href' => route('referrals.index')],
                ];
                @endphp
                @foreach($kpis as $k)
                <a href="{{ $k['href'] }}" class="{{ $k['color'] }} hover:opacity-90 text-white rounded-xl p-5 shadow-sm block transition">
                    <p class="text-3xl font-bold">{{ number_format($k['value']) }}</p>
                    <p class="text-sm opacity-90 mt-1">{{ $k['label'] }}</p>
                </a>
                @endforeach
            </div>

            {{-- Alerts --}}
            @if($unassignedReferrals || $openDisciplinary || $pendingClearance)
            <div class="flex flex-wrap gap-3">
                @if($unassignedReferrals)
                <a href="{{ route('referrals.index', ['status' => 'pending']) }}"
                   class="flex items-center gap-2 bg-orange-50 border border-orange-200 text-orange-700 px-4 py-2 rounded-lg text-sm hover:bg-orange-100 font-medium">
                    {{ $unassignedReferrals }} unassigned referral{{ $unassignedReferrals > 1 ? 's' : '' }} need attention
                </a>
                @endif
                @if($openDisciplinary)
                <a href="{{ route('disciplinary.index') }}"
                   class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded-lg text-sm hover:bg-red-100">
                    {{ $openDisciplinary }} open disciplinary record{{ $openDisciplinary > 1 ? 's' : '' }}
                </a>
                @endif
                @if($pendingClearance)
                <a href="{{ route('clearance.index') }}"
                   class="flex items-center gap-2 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-2 rounded-lg text-sm hover:bg-yellow-100">
                    {{ $pendingClearance }} clearance{{ $pendingClearance > 1 ? 's' : '' }} awaiting processing
                </a>
                @endif
            </div>
            @endif

            {{-- Modules --}}
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Modules</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                $modules = [
                    ['title' => 'Student Profiles',     'desc' => 'View and manage your assigned students',       'href' => route('students.index'),          'border' => 'border-teal-500',   'icon' => '👤'],
                    ['title' => 'Appointments',         'desc' => 'Your counseling schedule and bookings',        'href' => route('appointments.index'),      'border' => 'border-indigo-500', 'icon' => '📅'],
                    ['title' => 'Case Notes',           'desc' => 'Encrypted counseling session notes',           'href' => route('sessions.index'),          'border' => 'border-violet-500', 'icon' => '📝'],
                    ['title' => 'Referrals',            'desc' => 'Faculty referrals assigned to you',           'href' => route('referrals.index'),         'border' => 'border-orange-500', 'icon' => '🔔'],
                    ['title' => 'Disciplinary Records', 'desc' => 'Offense records you handle',                  'href' => route('disciplinary.index'),      'border' => 'border-red-500',    'icon' => '⚠️'],
                    ['title' => 'Counselor Schedule',   'desc' => 'Set your availability for student booking',   'href' => route('schedules.index'),         'border' => 'border-sky-500',    'icon' => '🗓'],
                    ['title' => 'Test Inventory',       'desc' => 'Manage psychological test catalog',           'href' => route('psych-tests.index'),       'border' => 'border-cyan-500',   'icon' => '🧪'],
                    ['title' => 'Testing Sessions',     'desc' => 'Scheduled batch testing rosters',             'href' => route('test-schedules.index'),    'border' => 'border-blue-400',   'icon' => '📋'],
                    ['title' => 'Test Results',         'desc' => 'Record and release assessment results',       'href' => route('test-results.index'),      'border' => 'border-blue-500',   'icon' => '📊'],
                    ['title' => 'Clearance Requests',   'desc' => 'Process graduation clearances',               'href' => route('clearance.index'),         'border' => 'border-green-500',  'icon' => '✅'],
                    ['title' => 'Certificates',         'desc' => 'Issue Good Moral Character certificates',     'href' => route('certificates.index'),      'border' => 'border-emerald-500','icon' => '📜'],
                    ['title' => 'Analytics & Reports',  'desc' => 'Charts, exports, and accomplishment reports', 'href' => route('analytics.index'),         'border' => 'border-purple-500', 'icon' => '📈'],
                ];
                @endphp
                @foreach($modules as $m)
                <a href="{{ $m['href'] }}"
                   class="bg-white shadow-sm rounded-xl p-5 border-l-4 {{ $m['border'] }} hover:shadow-md transition block">
                    <p class="text-2xl mb-2">{{ $m['icon'] }}</p>
                    <p class="font-semibold text-gray-800">{{ $m['title'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $m['desc'] }}</p>
                </a>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
