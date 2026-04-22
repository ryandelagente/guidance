<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Testing Sessions</h2>
            <a href="{{ route('test-schedules.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + Schedule Session
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(['scheduled','ongoing','completed','cancelled'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('test-schedules.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Test</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Target Group</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Administered By</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Expected</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($schedules as $sched)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $sched->test->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $sched->scheduled_date->format('M d, Y') }}<br>
                                <span class="text-xs text-gray-400">{{ substr($sched->start_time,0,5) }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $sched->college ?? 'All' }}
                                @if($sched->year_level) — {{ $sched->year_level }} @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $sched->venue ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $sched->administeredBy->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $sched->expected_participants ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $sched->getStatusBadgeClass() }}">
                                    {{ ucfirst($sched->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('test-schedules.show', $sched) }}" class="text-blue-600 hover:underline text-xs">View</a>
                                <a href="{{ route('test-schedules.edit', $sched) }}" class="text-gray-500 hover:underline text-xs">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No sessions scheduled.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($schedules->hasPages())
                    <div class="px-4 py-3 border-t">{{ $schedules->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
