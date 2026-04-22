<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Referrals</h2>
            @if(auth()->user()->isFaculty() || auth()->user()->isStaff())
                <a href="{{ route('referrals.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                    + Submit Referral
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="w-40">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(['pending','acknowledged','in_progress','resolved','closed'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-36">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Urgency</label>
                    <select name="urgency" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(['low','medium','high','critical'] as $u)
                            <option value="{{ $u }}" @selected(request('urgency') === $u)>{{ ucfirst($u) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('referrals.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            {{-- Table --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Urgency</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Referred By</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Counselor</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($referrals as $ref)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $ref->studentProfile->full_name ?? '—' }}<br>
                                <span class="text-xs text-gray-400">{{ $ref->studentProfile->student_id_number ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ ucwords(str_replace('_',' ',$ref->reason_category)) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $ref->getUrgencyBadgeClass() }}">
                                    {{ ucfirst($ref->urgency) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $ref->referredBy->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $ref->assignedCounselor->name ?? 'Unassigned' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $ref->getStatusBadgeClass() }}">
                                    {{ ucwords(str_replace('_',' ',$ref->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $ref->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('referrals.show', $ref) }}" class="text-blue-600 hover:underline text-xs">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No referrals found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($referrals->hasPages())
                    <div class="px-4 py-3 border-t">{{ $referrals->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
