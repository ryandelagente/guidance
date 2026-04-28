<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📈 Counselor Performance</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Period filter --}}
            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="w-32">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Year</label>
                    <select name="year" class="w-full border-gray-300 rounded-md text-sm">
                        @for($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Month</label>
                    <select name="month" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="0" @selected($month == 0)>All months</option>
                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $i => $m)
                            <option value="{{ $i + 1 }}" @selected($month == $i + 1)>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Apply</button>
                <a href="{{ route('performance.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            {{-- Office-wide totals --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Counselors</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totals['team_size'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-blue-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Total Appts</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($totals['total_appts']) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-green-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Completed</div>
                    <div class="text-2xl font-bold text-green-600 mt-1">{{ number_format($totals['completed_appts']) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-purple-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Case Notes</div>
                    <div class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($totals['sessions_logged']) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-orange-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Referrals</div>
                    <div class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($totals['referrals_handled']) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-yellow-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Avg Rating</div>
                    <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $totals['avg_rating'] ?? '—' }}{{ $totals['avg_rating'] ? '/5' : '' }}</div>
                </div>
            </div>

            {{-- Per-counselor cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($rows as $row)
                @php
                    // Performance "score" for visual indicator
                    $score = 0;
                    if ($row['completion_rate'] >= 80) $score += 25;
                    elseif ($row['completion_rate'] >= 60) $score += 15;
                    if ($row['no_show_rate'] <= 10) $score += 15;
                    if (($row['avg_rating'] ?? 0) >= 4.5) $score += 25;
                    elseif (($row['avg_rating'] ?? 0) >= 4) $score += 15;
                    if ($row['resolution_rate'] >= 70) $score += 20;
                    elseif ($row['resolution_rate'] >= 50) $score += 10;
                    if (($row['avg_response_hrs'] ?? 999) <= 24) $score += 15;

                    $tone = $score >= 70 ? 'green' : ($score >= 50 ? 'blue' : 'gray');
                    $toneClasses = [
                        'green' => 'border-l-green-500 bg-green-50/40',
                        'blue'  => 'border-l-blue-500 bg-blue-50/40',
                        'gray'  => 'border-l-gray-300 bg-gray-50/40',
                    ];
                @endphp
                <div class="bg-white shadow-sm rounded-lg border-l-4 {{ $toneClasses[$tone] }} p-5">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $row['counselor']->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $row['counselor']->email }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500">Active caseload</div>
                            <div class="text-2xl font-bold text-gray-800">{{ $row['active_students'] }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                        <div>
                            <div class="text-xs text-gray-500">Appts</div>
                            <div class="font-semibold text-gray-800">{{ $row['total_appts'] }}</div>
                            <div class="text-xs text-gray-400">{{ $row['completion_rate'] }}% done</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Sessions</div>
                            <div class="font-semibold text-gray-800">{{ $row['sessions_logged'] }}</div>
                            <div class="text-xs text-gray-400">case notes</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Referrals</div>
                            <div class="font-semibold text-gray-800">{{ $row['referrals_handled'] }}</div>
                            <div class="text-xs text-gray-400">{{ $row['resolution_rate'] }}% resolved</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">No-show</div>
                            <div class="font-semibold {{ $row['no_show_rate'] > 15 ? 'text-orange-600' : 'text-gray-800' }}">{{ $row['no_show_rate'] }}%</div>
                            <div class="text-xs text-gray-400">of appts</div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-3 grid grid-cols-3 gap-2 text-center">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Avg Rating</div>
                            <div class="text-lg font-bold {{ ($row['avg_rating'] ?? 0) >= 4 ? 'text-green-600' : 'text-gray-700' }}">
                                @if($row['avg_rating'])
                                    {{ str_repeat('★', floor($row['avg_rating'])) }}<span class="text-gray-300">{{ str_repeat('★', 5 - floor($row['avg_rating'])) }}</span>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $row['avg_rating'] }}/5 ({{ $row['feedback_count'] }})</div>
                                @else
                                    <span class="text-gray-300 text-base">No data</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Recommend</div>
                            <div class="text-lg font-bold {{ ($row['recommend_pct'] ?? 0) >= 80 ? 'text-green-600' : 'text-gray-700' }}">
                                {{ $row['recommend_pct'] !== null ? $row['recommend_pct'] . '%' : '—' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Avg Response</div>
                            <div class="text-lg font-bold {{ ($row['avg_response_hrs'] ?? 999) <= 24 ? 'text-green-600' : (($row['avg_response_hrs'] ?? 0) > 48 ? 'text-orange-600' : 'text-gray-700') }}">
                                {{ $row['avg_response_hrs'] !== null ? $row['avg_response_hrs'] . 'h' : '—' }}
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="md:col-span-2 bg-white shadow-sm rounded-lg p-12 text-center">
                    <div class="text-5xl mb-3">📈</div>
                    <p class="text-gray-400 text-sm">No active counselors found.</p>
                </div>
                @endforelse
            </div>

            {{-- Comparison table --}}
            @if($rows->isNotEmpty())
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wider">Side-by-side Comparison</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Counselor</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase">Caseload</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase">Appts</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase">Done %</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase">Notes</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase">Refs</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase">Resolve %</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase">Rating</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase">Response</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($rows as $row)
                        <tr>
                            <td class="px-4 py-2.5 font-medium text-gray-800">{{ $row['counselor']->name }}</td>
                            <td class="px-4 py-2.5 text-center">{{ $row['active_students'] }}</td>
                            <td class="px-4 py-2.5 text-center">{{ $row['total_appts'] }}</td>
                            <td class="px-4 py-2.5 text-center">{{ $row['completion_rate'] }}%</td>
                            <td class="px-4 py-2.5 text-center">{{ $row['sessions_logged'] }}</td>
                            <td class="px-4 py-2.5 text-center">{{ $row['referrals_handled'] }}</td>
                            <td class="px-4 py-2.5 text-center">{{ $row['resolution_rate'] }}%</td>
                            <td class="px-4 py-2.5 text-center font-medium text-yellow-600">{{ $row['avg_rating'] ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-center">{{ $row['avg_response_hrs'] !== null ? $row['avg_response_hrs'] . 'h' : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
