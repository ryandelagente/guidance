<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Testing Session</h2>
            <div class="flex gap-3">
                <a href="{{ route('test-results.create', ['schedule_id' => $testSchedule->id]) }}"
                   class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    + Add Results
                </a>
                <a href="{{ route('test-schedules.edit', $testSchedule) }}"
                   class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md">Edit</a>
                <a href="{{ route('test-schedules.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $testSchedule->test->name ?? '—' }}</h3>
                        <p class="text-sm text-gray-500">{{ $testSchedule->test->type_label ?? '' }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $testSchedule->getStatusBadgeClass() }}">
                        {{ ucfirst($testSchedule->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div><span class="font-medium text-gray-500">Date</span><p class="mt-0.5 text-gray-800">{{ $testSchedule->scheduled_date->format('M d, Y') }}</p></div>
                    <div><span class="font-medium text-gray-500">Start Time</span><p class="mt-0.5 text-gray-800">{{ substr($testSchedule->start_time,0,5) }}</p></div>
                    <div><span class="font-medium text-gray-500">Venue</span><p class="mt-0.5 text-gray-800">{{ $testSchedule->venue ?? '—' }}</p></div>
                    <div><span class="font-medium text-gray-500">College</span><p class="mt-0.5 text-gray-800">{{ $testSchedule->college ?? 'All' }}</p></div>
                    <div><span class="font-medium text-gray-500">Year Level</span><p class="mt-0.5 text-gray-800">{{ $testSchedule->year_level ?? 'All' }}</p></div>
                    <div><span class="font-medium text-gray-500">Expected</span><p class="mt-0.5 text-gray-800">{{ $testSchedule->expected_participants ?? '—' }}</p></div>
                    <div><span class="font-medium text-gray-500">Administered By</span><p class="mt-0.5 text-gray-800">{{ $testSchedule->administeredBy->name ?? '—' }}</p></div>
                </div>

                @if($testSchedule->notes)
                <div class="text-sm"><span class="font-medium text-gray-500">Notes</span><p class="mt-1 text-gray-700">{{ $testSchedule->notes }}</p></div>
                @endif
            </div>

            {{-- Results roster --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">
                    Results ({{ $testSchedule->results->count() }} recorded)
                </h3>
                @if($testSchedule->results->count())
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-500">Student</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-500">Raw Score</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-500">Percentile</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-500">Level</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-500">Released</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($testSchedule->results as $res)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium text-gray-900">{{ $res->studentProfile->full_name ?? '—' }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $res->raw_score ?? '—' }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $res->percentile ? $res->percentile . '%' : '—' }}</td>
                            <td class="px-3 py-2">
                                @if($res->interpretation_level)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $res->getInterpretationBadgeClass() }}">
                                    {{ ucwords(str_replace('_',' ',$res->interpretation_level)) }}
                                </span>
                                @else —
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if($res->is_released)
                                    <span class="text-green-600 text-xs">Yes</span>
                                @else
                                    <span class="text-gray-400 text-xs">No</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('test-results.show', $res) }}" class="text-blue-600 hover:underline text-xs">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-sm text-gray-400">No results recorded yet. Use the button above to add results.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
