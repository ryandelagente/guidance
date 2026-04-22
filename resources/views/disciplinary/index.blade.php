<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Disciplinary Records</h2>
            <a href="{{ route('disciplinary.create') }}"
               class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + File Record
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-52">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search Student</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full border-gray-300 rounded-md text-sm"
                           placeholder="Name or student ID...">
                </div>
                <div class="w-40">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(['pending','under_review','resolved','escalated'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-36">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <select name="offense_type" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        <option value="minor" @selected(request('offense_type') === 'minor')>Minor</option>
                        <option value="major" @selected(request('offense_type') === 'major')>Major</option>
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('disciplinary.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            {{-- Table --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Incident Date</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Handled By</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($records as $rec)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $rec->studentProfile->full_name ?? '—' }}<br>
                                <span class="text-xs text-gray-400">{{ $rec->studentProfile->student_id_number ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $rec->getOffenseTypeBadgeClass() }}">
                                    {{ ucfirst($rec->offense_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ ucwords(str_replace('_',' ',$rec->offense_category)) }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $rec->incident_date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $rec->handledBy->name ?? 'Unassigned' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $rec->getStatusBadgeClass() }}">
                                    {{ ucwords(str_replace('_',' ',$rec->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('disciplinary.show', $rec) }}" class="text-blue-600 hover:underline text-xs">View</a>
                                <a href="{{ route('disciplinary.edit', $rec) }}" class="text-gray-500 hover:underline text-xs">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No disciplinary records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($records->hasPages())
                    <div class="px-4 py-3 border-t">{{ $records->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
