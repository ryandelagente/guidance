<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Student Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome --}}
            <div class="bg-white shadow-sm rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-lg">Welcome back, {{ auth()->user()->name }}</p>
                    <p class="text-sm text-gray-400">
                        Student
                        @if($profile)
                            &bull; {{ $profile->student_id_number }}
                            &bull; {{ $profile->program }} {{ $profile->year_level }}
                        @else
                            &bull; Profile not yet set up
                        @endif
                        &bull; {{ now()->format('l, F d, Y') }}
                    </p>
                </div>
            </div>

            {{-- Upcoming Appointment Banner --}}
            @if($upcomingAppt)
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 flex items-start gap-3">
                <span class="text-2xl">📅</span>
                <div>
                    <p class="font-semibold text-indigo-800">Upcoming Appointment</p>
                    <p class="text-sm text-indigo-700 mt-0.5">
                        {{ $upcomingAppt->appointment_date->format('l, F d, Y') }}
                        at {{ \Carbon\Carbon::parse($upcomingAppt->start_time)->format('h:i A') }}
                        &mdash; {{ ucwords(str_replace('_', ' ', $upcomingAppt->appointment_type)) }}
                    </p>
                    <a href="{{ route('appointments.show', $upcomingAppt) }}" class="text-xs text-indigo-600 hover:underline mt-1 inline-block">View details &rarr;</a>
                </div>
            </div>
            @endif

            {{-- No Profile Notice --}}
            @if(!$profile)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
                Your student profile has not been created yet. Please visit the Guidance Office to complete your cumulative record.
            </div>
            @endif

            {{-- Stats Row --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <a href="{{ route('appointments.index') }}" class="bg-indigo-600 hover:opacity-90 text-white rounded-xl p-5 shadow-sm block transition">
                    <p class="text-3xl font-bold">{{ $upcomingAppt ? '1+' : '0' }}</p>
                    <p class="text-sm opacity-90 mt-1">Upcoming Appts</p>
                </a>
                <a href="{{ route('test-results.index') }}" class="bg-cyan-600 hover:opacity-90 text-white rounded-xl p-5 shadow-sm block transition">
                    <p class="text-3xl font-bold">{{ number_format($myResults) }}</p>
                    <p class="text-sm opacity-90 mt-1">My Test Results</p>
                </a>
                <a href="{{ route('clearance.index') }}" class="bg-green-600 hover:opacity-90 text-white rounded-xl p-5 shadow-sm block transition">
                    <p class="text-3xl font-bold">{{ number_format($myClearance) }}</p>
                    <p class="text-sm opacity-90 mt-1">Clearances</p>
                </a>
            </div>

            {{-- Quick Actions --}}
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                $actions = [
                    ['title' => 'Book Appointment',  'desc' => 'Schedule a counseling session with your counselor', 'href' => route('appointments.create'), 'border' => 'border-indigo-500', 'icon' => '📅'],
                    ['title' => 'My Appointments',   'desc' => 'View and manage your appointments',                'href' => route('appointments.index'),  'border' => 'border-blue-500',   'icon' => '🗓'],
                    ['title' => 'Test Results',      'desc' => 'View your released psychological test results',    'href' => route('test-results.index'),  'border' => 'border-cyan-500',   'icon' => '📊'],
                    ['title' => 'Clearance Request', 'desc' => 'Apply for graduation or departmental clearance',   'href' => route('clearance.index'),     'border' => 'border-green-500',  'icon' => '✅'],
                    ['title' => 'Submit a Referral', 'desc' => 'Report a concern or request counseling support',  'href' => route('appointments.create'), 'border' => 'border-violet-500', 'icon' => '📝'],
                ];
                @endphp
                @foreach($actions as $a)
                <a href="{{ $a['href'] }}"
                   class="bg-white shadow-sm rounded-xl p-5 border-l-4 {{ $a['border'] }} hover:shadow-md transition block">
                    <p class="text-2xl mb-2">{{ $a['icon'] }}</p>
                    <p class="font-semibold text-gray-800">{{ $a['title'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $a['desc'] }}</p>
                </a>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
