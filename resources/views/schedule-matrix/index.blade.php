<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📊 Schedule Matrix — Office Availability</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Week navigation + summary --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 bg-white shadow-sm rounded-lg px-2 py-1">
                    <a href="{{ route('schedule-matrix.index', ['week' => $weekStart->copy()->subWeek()->toDateString()]) }}"
                       class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-gray-100 text-gray-500">‹</a>
                    <div class="px-3 text-sm font-medium text-gray-800">
                        Week of {{ $weekStart->format('M d') }} – {{ $weekEnd->format('M d, Y') }}
                    </div>
                    <a href="{{ route('schedule-matrix.index', ['week' => $weekStart->copy()->addWeek()->toDateString()]) }}"
                       class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-gray-100 text-gray-500">›</a>
                    <a href="{{ route('schedule-matrix.index') }}"
                       class="ml-2 text-xs text-blue-600 hover:underline">This week</a>
                </div>

                <div class="flex gap-3">
                    <div class="bg-white shadow-sm rounded-lg px-4 py-2 text-center">
                        <div class="text-xs text-gray-500 uppercase">Slots</div>
                        <div class="text-lg font-bold text-gray-800">{{ $totals['available_slots'] }}</div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg px-4 py-2 text-center">
                        <div class="text-xs text-gray-500 uppercase">Booked</div>
                        <div class="text-lg font-bold text-blue-600">{{ $totals['booked_slots'] }}</div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg px-4 py-2 text-center">
                        <div class="text-xs text-gray-500 uppercase">Utilization</div>
                        <div class="text-lg font-bold {{ $totals['utilization'] >= 75 ? 'text-orange-600' : 'text-green-600' }}">{{ $totals['utilization'] }}%</div>
                    </div>
                </div>
            </div>

            {{-- Matrix grid --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @if($matrix->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <div class="text-5xl mb-3">📊</div>
                    <p class="text-sm">No active counselors found.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 min-w-48">Counselor</th>
                                @foreach($days as $day)
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-32 {{ $day->isToday() ? 'bg-blue-50' : '' }}">
                                    <div>{{ $day->format('D') }}</div>
                                    <div class="font-bold text-gray-800 mt-0.5">{{ $day->format('M d') }}</div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($matrix as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 sticky left-0 bg-white hover:bg-gray-50 border-r border-gray-100">
                                    <div class="font-semibold text-gray-900">{{ $row['counselor']->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $row['counselor']->email }}</div>
                                </td>
                                @foreach($row['cells'] as $cell)
                                @php
                                    $sched = $cell['schedule'];
                                    $booked = $cell['booked_count'];
                                    $total  = $cell['total_slots'];
                                    $available = max(0, $total - $booked);

                                    if (!$sched) {
                                        $cellClass = 'bg-gray-50';
                                        $tone = 'off';
                                    } elseif ($cell['is_past']) {
                                        $cellClass = 'bg-gray-50 opacity-60';
                                        $tone = 'past';
                                    } elseif ($total === 0) {
                                        $cellClass = 'bg-gray-50';
                                        $tone = 'off';
                                    } elseif ($available === 0) {
                                        $cellClass = 'bg-red-50';
                                        $tone = 'full';
                                    } elseif ($booked / max(1, $total) >= 0.75) {
                                        $cellClass = 'bg-orange-50';
                                        $tone = 'busy';
                                    } elseif ($booked > 0) {
                                        $cellClass = 'bg-yellow-50';
                                        $tone = 'partial';
                                    } else {
                                        $cellClass = 'bg-green-50';
                                        $tone = 'open';
                                    }

                                    if ($cell['is_today']) {
                                        $cellClass .= ' ring-1 ring-blue-300';
                                    }
                                @endphp
                                <td class="px-2 py-2 align-top {{ $cellClass }}">
                                    @if(!$sched)
                                        <div class="text-xs text-gray-400 text-center py-2">Off</div>
                                    @else
                                        <div class="text-xs text-gray-700 font-medium text-center">
                                            {{ \Carbon\Carbon::parse($sched->start_time)->format('h:i A') }}
                                            – {{ \Carbon\Carbon::parse($sched->end_time)->format('h:i A') }}
                                        </div>
                                        <div class="mt-1 text-center">
                                            <span class="text-xs font-bold
                                                @if($tone === 'open') text-green-700
                                                @elseif($tone === 'partial') text-yellow-700
                                                @elseif($tone === 'busy') text-orange-700
                                                @elseif($tone === 'full') text-red-700
                                                @else text-gray-500 @endif">
                                                {{ $available }} free
                                            </span>
                                            <span class="text-xs text-gray-400">/ {{ $total }}</span>
                                        </div>
                                        @if($booked > 0)
                                        <div class="mt-1 w-full h-1 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full
                                                @if($tone === 'full') bg-red-500
                                                @elseif($tone === 'busy') bg-orange-500
                                                @else bg-yellow-500 @endif"
                                                 style="width: {{ ($booked / max(1, $total)) * 100 }}%"></div>
                                        </div>
                                        @endif
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Legend --}}
            <div class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-x-5 gap-y-2 text-xs">
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-green-100 border border-green-300 rounded"></span>Fully open</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-yellow-100 border border-yellow-300 rounded"></span>Partially booked</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-orange-100 border border-orange-300 rounded"></span>Busy (≥75%)</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-red-100 border border-red-300 rounded"></span>Fully booked</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-gray-100 border border-gray-300 rounded"></span>Not scheduled / day off</div>
            </div>

        </div>
    </div>
</x-app-layout>
