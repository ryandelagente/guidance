<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Appointments</h2>
            @if(auth()->user()->isStudent())
                <a href="{{ route('appointments.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                    + Book Appointment
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4"
             x-data="{ view: '{{ request('view', 'list') }}' }">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- View Toggle --}}
            <div class="flex items-center gap-1 bg-white shadow-sm rounded-lg p-1 w-fit">
                <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100'"
                        class="px-4 py-1.5 rounded-md text-sm font-medium transition">
                    ☰ List
                </button>
                <button @click="view = 'calendar'"
                        :class="view === 'calendar' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100'"
                        class="px-4 py-1.5 rounded-md text-sm font-medium transition">
                    📅 Calendar
                </button>
            </div>

            {{-- ═══════════════ LIST VIEW ═══════════════ --}}
            <div x-show="view === 'list'" x-transition>

                {{-- Filters --}}
                <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end mb-4">
                    <input type="hidden" name="view" value="list">
                    <div class="w-40">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                            <option value="">All</option>
                            @foreach(['pending','confirmed','in_progress','completed','cancelled','no_show'] as $s)
                                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-44">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}"
                               class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                    <a href="{{ route('appointments.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
                </form>

                {{-- Table --}}
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Counselor</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($appointments as $appt)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $appt->studentProfile->full_name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ ucwords(str_replace('_',' ', $appt->appointment_type)) }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $appt->appointment_date->format('M d, Y') }}<br>
                                    <span class="text-xs text-gray-400">{{ substr($appt->start_time,0,5) }} – {{ substr($appt->end_time,0,5) }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $appt->counselor->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if($appt->meeting_type === 'virtual')
                                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Virtual</span>
                                    @else
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">In-Person</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $appt->getStatusBadgeClass() }}">
                                        {{ ucwords(str_replace('_',' ',$appt->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('appointments.show', $appt) }}" class="text-blue-600 hover:underline text-xs">View</a>
                                    @if($appt->isCompleted() && !$appt->session && auth()->user()->isCounselor())
                                        <a href="{{ route('sessions.create', ['appointment_id' => $appt->id]) }}"
                                           class="text-green-600 hover:underline text-xs">Add Notes</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No appointments found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($appointments->hasPages())
                        <div class="px-4 py-3 border-t">{{ $appointments->links() }}</div>
                    @endif
                </div>
            </div>

            {{-- ═══════════════ CALENDAR VIEW ═══════════════ --}}
            <div x-show="view === 'calendar'" x-transition>
                @php
                    $startDow    = $calendarMonth->copy()->startOfMonth()->dayOfWeek;
                    $daysInMonth = $calendarMonth->daysInMonth;
                    $prevMonth   = $calendarMonth->copy()->subMonth()->format('Y-m');
                    $nextMonth   = $calendarMonth->copy()->addMonth()->format('Y-m');
                    $todayStr    = now()->format('Y-m-d');
                    $statusDot   = [
                        'pending'     => 'bg-yellow-400',
                        'confirmed'   => 'bg-blue-500',
                        'in_progress' => 'bg-purple-500',
                        'completed'   => 'bg-green-500',
                        'cancelled'   => 'bg-red-400',
                        'no_show'     => 'bg-gray-400',
                    ];
                    $statusLabel = [
                        'pending'     => 'bg-yellow-100 text-yellow-800',
                        'confirmed'   => 'bg-blue-100 text-blue-800',
                        'in_progress' => 'bg-purple-100 text-purple-800',
                        'completed'   => 'bg-green-100 text-green-800',
                        'cancelled'   => 'bg-red-100 text-red-700',
                        'no_show'     => 'bg-gray-100 text-gray-600',
                    ];
                @endphp

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    {{-- Month navigation --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <a href="{{ route('appointments.index', ['view' => 'calendar', 'month' => $prevMonth]) }}"
                           class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition text-lg">‹</a>
                        <div class="text-center">
                            <h3 class="font-semibold text-gray-800 text-lg">{{ $calendarMonth->format('F Y') }}</h3>
                            <a href="{{ route('appointments.index', ['view' => 'calendar', 'month' => now()->format('Y-m')]) }}"
                               class="text-xs text-blue-500 hover:underline">Today</a>
                        </div>
                        <a href="{{ route('appointments.index', ['view' => 'calendar', 'month' => $nextMonth]) }}"
                           class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition text-lg">›</a>
                    </div>

                    {{-- Day-of-week headers --}}
                    <div class="grid grid-cols-7 border-b border-gray-100">
                        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dow)
                            <div class="py-2 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $dow }}</div>
                        @endforeach
                    </div>

                    {{-- Calendar grid --}}
                    <div class="grid grid-cols-7 border-b border-gray-100">
                        {{-- Leading blank cells --}}
                        @for($i = 0; $i < $startDow; $i++)
                            <div class="min-h-28 bg-gray-50/50 border-r border-b border-gray-100 last:border-r-0"></div>
                        @endfor

                        {{-- Day cells --}}
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $col     = ($startDow + $day - 1) % 7;
                                $dateStr = $calendarMonth->format('Y-m') . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                                $appts   = $calendarAppointments[$dateStr] ?? collect();
                                $isToday = $dateStr === $todayStr;
                                $isWeekend = in_array($col, [0, 6]);
                            @endphp
                            <div class="min-h-28 border-r border-b border-gray-100 last:border-r-0 p-1.5 {{ $isWeekend ? 'bg-gray-50/60' : 'bg-white' }} hover:bg-blue-50/30 transition-colors group relative">

                                {{-- Day number --}}
                                <div class="flex justify-between items-start mb-1">
                                    <span class="text-sm font-medium {{ $isToday ? 'w-6 h-6 flex items-center justify-center bg-blue-600 text-white rounded-full text-xs font-bold' : ($isWeekend ? 'text-gray-400' : 'text-gray-700') }}">
                                        {{ $day }}
                                    </span>
                                    @if($appts->count() > 0)
                                        <a href="{{ route('appointments.index', ['date' => $dateStr]) }}"
                                           class="hidden group-hover:flex items-center text-xs text-blue-500 hover:underline leading-none">
                                            all
                                        </a>
                                    @endif
                                </div>

                                {{-- Appointment pills --}}
                                <div class="space-y-0.5">
                                    @foreach($appts->take(4) as $appt)
                                        <a href="{{ route('appointments.show', $appt) }}"
                                           class="block text-xs truncate rounded px-1 py-0.5 leading-tight hover:opacity-80 transition {{ $statusLabel[$appt->status] ?? 'bg-gray-100 text-gray-600' }}"
                                           title="{{ $appt->studentProfile?->full_name }} — {{ substr($appt->start_time, 0, 5) }}">
                                            <span class="font-medium">{{ substr($appt->start_time, 0, 5) }}</span>
                                            {{ $appt->studentProfile?->first_name ?? 'Student' }}
                                        </a>
                                    @endforeach
                                    @if($appts->count() > 4)
                                        <a href="{{ route('appointments.index', ['date' => $dateStr]) }}"
                                           class="block text-xs text-gray-400 hover:text-blue-500 pl-1">
                                            +{{ $appts->count() - 4 }} more
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endfor

                        {{-- Trailing blank cells to complete last row --}}
                        @php $total = $startDow + $daysInMonth; $trailing = (7 - ($total % 7)) % 7; @endphp
                        @for($i = 0; $i < $trailing; $i++)
                            <div class="min-h-28 bg-gray-50/50 border-r border-b border-gray-100 last:border-r-0"></div>
                        @endfor
                    </div>

                    {{-- Legend --}}
                    <div class="px-6 py-3 flex flex-wrap gap-x-5 gap-y-1.5">
                        @foreach([
                            'pending'     => 'Pending',
                            'confirmed'   => 'Confirmed',
                            'in_progress' => 'In Progress',
                            'completed'   => 'Completed',
                            'cancelled'   => 'Cancelled',
                            'no_show'     => 'No Show',
                        ] as $status => $label)
                            <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                <span class="inline-block w-2.5 h-2.5 rounded-sm {{ $statusDot[$status] }}"></span>
                                {{ $label }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
