<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Guidance Director Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-lg">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-lg">Welcome back, {{ auth()->user()->name }}</p>
                    <p class="text-sm text-gray-400">Guidance Director &bull; {{ now()->format('l, F d, Y') }}</p>
                </div>
            </div>

            {{-- KPI Row --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                $kpis = [
                    ['label' => 'Total Students',    'value' => $totalStudents,      'color' => 'bg-blue-600',   'href' => route('students.index')],
                    ['label' => 'Appts This Year',   'value' => $totalAppointments,  'color' => 'bg-indigo-600', 'href' => route('appointments.index')],
                    ['label' => 'Sessions Done',     'value' => $completedSessions,  'color' => 'bg-green-600',  'href' => route('sessions.index')],
                    ['label' => 'Certs This Year',   'value' => $certThisYear,       'color' => 'bg-emerald-600','href' => route('certificates.index')],
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
            @if($pendingReferrals || $criticalReferrals || $pendingClearance)
            <div class="flex flex-wrap gap-3">
                @if($criticalReferrals)
                <a href="{{ route('referrals.index', ['urgency' => 'critical']) }}"
                   class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded-lg text-sm hover:bg-red-100 font-medium">
                    🚨 {{ $criticalReferrals }} critical referral{{ $criticalReferrals > 1 ? 's' : '' }} open
                </a>
                @endif
                @if($pendingReferrals)
                <a href="{{ route('referrals.index', ['status' => 'pending']) }}"
                   class="flex items-center gap-2 bg-orange-50 border border-orange-200 text-orange-700 px-4 py-2 rounded-lg text-sm hover:bg-orange-100">
                    {{ $pendingReferrals }} pending referral{{ $pendingReferrals > 1 ? 's' : '' }}
                </a>
                @endif
                @if($pendingClearance)
                <a href="{{ route('clearance.index') }}"
                   class="flex items-center gap-2 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-2 rounded-lg text-sm hover:bg-yellow-100">
                    {{ $pendingClearance }} clearance{{ $pendingClearance > 1 ? 's' : '' }} awaiting approval
                </a>
                @endif
            </div>
            @endif

            {{-- Modules --}}
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Modules</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                $modules = [
                    ['title' => 'Student Profiles',     'desc' => 'Browse all student cumulative records',  'href' => route('students.index'),       'border' => 'border-blue-500',   'icon' => '👤'],
                    ['title' => 'Appointments',         'desc' => 'All counseling appointments',            'href' => route('appointments.index'),   'border' => 'border-indigo-500', 'icon' => '📅'],
                    ['title' => 'Case Notes',           'desc' => 'Counselor session notes',               'href' => route('sessions.index'),       'border' => 'border-violet-500', 'icon' => '📝'],
                    ['title' => 'Referrals',            'desc' => 'Track faculty referrals',               'href' => route('referrals.index'),      'border' => 'border-orange-500', 'icon' => '🔔'],
                    ['title' => 'Disciplinary Records', 'desc' => 'Offense records and sanctions',         'href' => route('disciplinary.index'),   'border' => 'border-red-500',    'icon' => '⚠️'],
                    ['title' => 'Clearance Requests',   'desc' => 'Process graduation clearances',        'href' => route('clearance.index'),      'border' => 'border-green-500',  'icon' => '✅'],
                    ['title' => 'Certificates',         'desc' => 'Issued Good Moral certificates',        'href' => route('certificates.index'),   'border' => 'border-emerald-500','icon' => '📜'],
                    ['title' => 'Test Results',         'desc' => 'Psychological assessment results',      'href' => route('test-results.index'),   'border' => 'border-cyan-500',   'icon' => '🧪'],
                    ['title' => 'Analytics & Reports',  'desc' => 'Charts, exports, and CHED reports',     'href' => route('analytics.index'),      'border' => 'border-purple-500', 'icon' => '📈'],
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
