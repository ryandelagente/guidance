<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clearance Requests</h2>
            <a href="{{ route('clearance.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + New Request
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
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <select name="type" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All Types</option>
                        @foreach(['graduation','departmental','scholarship','employment','other'] as $t)
                            <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(['pending','for_exit_survey','survey_done','approved','rejected','on_hold'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('clearance.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Processed By</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($requests as $req)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $req->studentProfile->full_name ?? '—' }}<br>
                                <span class="text-xs text-gray-400">{{ $req->studentProfile->student_id_number ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ ucfirst($req->clearance_type) }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $req->semester }} Sem &bull; {{ $req->academic_year }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $req->getStatusBadgeClass() }}">
                                    {{ ucwords(str_replace('_',' ',$req->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $req->processedBy->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $req->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('clearance.show', $req) }}" class="text-blue-600 hover:underline text-xs">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No clearance requests found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($requests->hasPages())
                    <div class="px-4 py-3 border-t">{{ $requests->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
