<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">💭 Wellness Monitoring</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Last 7 Days</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['last_7_days'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-red-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">High Risk (7d)</div>
                    <div class="text-2xl font-bold text-red-600 mt-1">{{ $stats['high_risk'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-blue-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Want Counselor</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['wants_counselor'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-yellow-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Unreviewed</div>
                    <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['unreviewed'] }}</div>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="w-40">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Risk Level</label>
                    <select name="risk" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        <option value="high" @selected(request('risk')==='high')>High</option>
                        <option value="medium" @selected(request('risk')==='medium')>Medium</option>
                        <option value="low" @selected(request('risk')==='low')>Low</option>
                    </select>
                </div>
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <label class="inline-flex items-center text-sm pb-2">
                    <input type="checkbox" name="wants_counselor" value="1" @checked(request('wants_counselor')) class="rounded border-gray-300 mr-2">
                    Wants counselor
                </label>
                <label class="inline-flex items-center text-sm pb-2">
                    <input type="checkbox" name="unreviewed" value="1" @checked(request('unreviewed')) class="rounded border-gray-300 mr-2">
                    Unreviewed only
                </label>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('wellness.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            {{-- Check-ins Table --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mood</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stress</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sleep</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Risk</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($checkins as $c)
                        <tr class="hover:bg-gray-50 {{ $c->wants_counselor && !$c->reviewed ? 'bg-amber-50' : '' }}">
                            <td class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">
                                {{ $c->created_at->format('M d') }}<br>
                                <span class="text-gray-400">{{ $c->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('students.show', $c->studentProfile) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $c->studentProfile->full_name }}</a>
                                <div class="text-xs text-gray-400">{{ $c->studentProfile->student_id_number ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center text-2xl">{{ \App\Models\WellnessCheckin::moodEmoji($c->mood) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ \App\Models\WellnessCheckin::intensityLabel($c->stress_level) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ ['Poor','Fair','Okay','Good','Excellent'][$c->sleep_quality - 1] }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ \App\Models\WellnessCheckin::intensityLabel($c->academic_stress) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $c->getRiskBadgeClass() }}">{{ ucfirst($c->risk_level) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs">
                                @if($c->wants_counselor)
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Wants Counselor</span><br>
                                @endif
                                @if($c->reviewed)
                                    <span class="text-green-600">✓ Reviewed</span>
                                @else
                                    <span class="text-gray-400">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('wellness.show', $c) }}" class="text-blue-600 hover:underline text-xs">View</a>
                                @if(!$c->reviewed)
                                    <form method="POST" action="{{ route('wellness.review', $c) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-green-600 hover:underline text-xs">Mark Reviewed</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="px-4 py-12 text-center text-gray-400">No check-ins match your filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($checkins->hasPages())
                    <div class="px-4 py-3 border-t">{{ $checkins->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
