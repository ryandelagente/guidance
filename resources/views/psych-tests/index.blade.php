<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Test Inventory</h2>
            @if(auth()->user()->isStaff())
            <a href="{{ route('psych-tests.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + Add Test
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="w-48">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <select name="type" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All Types</option>
                        @foreach(['iq','personality','career_aptitude','interest','mental_health','other'] as $t)
                            <option value="{{ $t }}" @selected(request('type') === $t)>
                                {{ ucwords(str_replace('_',' ',$t)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-36">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="active" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        <option value="1" @selected(request('active') === '1')>Active</option>
                        <option value="0" @selected(request('active') === '0')>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('psych-tests.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Test Name</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Publisher</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Schedules</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Results</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($tests as $test)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $test->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $test->type_label }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $test->publisher ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $test->total_items ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $test->schedules_count }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $test->results_count }}</td>
                            <td class="px-4 py-3">
                                @if($test->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('psych-tests.show', $test) }}" class="text-blue-600 hover:underline text-xs">View</a>
                                <a href="{{ route('psych-tests.edit', $test) }}" class="text-gray-500 hover:underline text-xs">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No tests in inventory.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($tests->hasPages())
                    <div class="px-4 py-3 border-t">{{ $tests->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
