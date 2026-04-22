<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Test Results</h2>
            <a href="{{ route('test-results.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + Record Result
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-52">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search Student</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full border-gray-300 rounded-md text-sm" placeholder="Name or student ID...">
                </div>
                <div class="w-56">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Test</label>
                    <select name="test_id" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All Tests</option>
                        @foreach($tests as $t)
                            <option value="{{ $t->id }}" @selected(request('test_id') == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('test-results.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Test</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Percentile</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Level</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Released</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($results as $res)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $res->studentProfile->full_name ?? '—' }}<br>
                                <span class="text-xs text-gray-400">{{ $res->studentProfile->student_id_number ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $res->test->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $res->test_date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $res->raw_score ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $res->percentile ? $res->percentile . '%' : '—' }}</td>
                            <td class="px-4 py-3">
                                @if($res->interpretation_level)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $res->getInterpretationBadgeClass() }}">
                                    {{ ucwords(str_replace('_',' ',$res->interpretation_level)) }}
                                </span>
                                @else —
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($res->is_released)
                                    <span class="text-green-600 text-xs font-medium">Yes</span>
                                @else
                                    <span class="text-gray-400 text-xs">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('test-results.show', $res) }}" class="text-blue-600 hover:underline text-xs">View</a>
                                <a href="{{ route('test-results.edit', $res) }}" class="text-gray-500 hover:underline text-xs">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No results found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($results->hasPages())
                    <div class="px-4 py-3 border-t">{{ $results->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
