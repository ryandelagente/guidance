<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Referral Details</h2>
            <a href="{{ route('referrals.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to list</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Main Details --}}
            <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $referral->studentProfile->full_name ?? '—' }}
                        </h3>
                        <p class="text-sm text-gray-500">{{ $referral->studentProfile->student_id_number ?? '' }} &bull; {{ $referral->studentProfile->college ?? '' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $referral->getUrgencyBadgeClass() }}">
                            {{ ucfirst($referral->urgency) }} urgency
                        </span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $referral->getStatusBadgeClass() }}">
                            {{ ucwords(str_replace('_',' ',$referral->status)) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-500">Category</span>
                        <p class="text-gray-800 mt-0.5">{{ ucwords(str_replace('_',' ',$referral->reason_category)) }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-500">Referred By</span>
                        <p class="text-gray-800 mt-0.5">{{ $referral->referredBy->name ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-500">Assigned Counselor</span>
                        <p class="text-gray-800 mt-0.5">{{ $referral->assignedCounselor->name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-500">Date Submitted</span>
                        <p class="text-gray-800 mt-0.5">{{ $referral->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($referral->acknowledged_at)
                    <div>
                        <span class="font-medium text-gray-500">Acknowledged</span>
                        <p class="text-gray-800 mt-0.5">{{ $referral->acknowledged_at->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($referral->resolved_at)
                    <div>
                        <span class="font-medium text-gray-500">Resolved</span>
                        <p class="text-gray-800 mt-0.5">{{ $referral->resolved_at->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>

                <div class="text-sm">
                    <span class="font-medium text-gray-500">Description</span>
                    <p class="text-gray-800 mt-1 whitespace-pre-wrap">{{ $referral->description }}</p>
                </div>

                @if($referral->faculty_feedback)
                <div class="text-sm bg-blue-50 border border-blue-100 p-3 rounded-md">
                    <span class="font-medium text-blue-700">Feedback to Faculty</span>
                    <p class="text-blue-900 mt-1">{{ $referral->faculty_feedback }}</p>
                </div>
                @endif

                {{-- Faculty: edit pending referral --}}
                @if(auth()->user()->isFaculty() && $referral->referred_by === auth()->id() && $referral->status === 'pending')
                <div class="pt-2">
                    <a href="{{ route('referrals.edit', $referral) }}"
                       class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md">Edit Referral</a>
                </div>
                @endif

                {{-- Counselor/Admin: assign counselor + feedback --}}
                @if(auth()->user()->isStaff())
                <div class="border-t pt-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Assign / Update Feedback</h4>
                    <form method="POST" action="{{ route('referrals.update', $referral) }}" class="flex flex-wrap gap-3 items-end">
                        @csrf @method('PATCH')
                        <div class="w-48">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Counselor</label>
                            <select name="assigned_counselor_id" class="w-full border-gray-300 rounded-md text-sm">
                                <option value="">— Unassigned —</option>
                                @foreach($counselors as $c)
                                    <option value="{{ $c->id }}" @selected($referral->assigned_counselor_id == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-48">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Feedback to Faculty</label>
                            <input type="text" name="faculty_feedback" value="{{ $referral->faculty_feedback }}"
                                   maxlength="1000"
                                   class="w-full border-gray-300 rounded-md text-sm"
                                   placeholder="Brief update for the referring faculty...">
                        </div>
                        <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Update</button>
                    </form>
                </div>
                @endif
            </div>

            {{-- Interventions --}}
            @if(auth()->user()->isStaff())
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Intervention Log</h3>

                {{-- Add intervention form --}}
                @if(in_array($referral->status, ['pending','acknowledged','in_progress']))
                <form method="POST" action="{{ route('referrals.interventions.store', $referral) }}" class="space-y-3 mb-6 pb-6 border-b">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status Label <span class="text-red-500">*</span></label>
                            <input type="text" name="status_label" required maxlength="100"
                                   class="w-full border-gray-300 rounded-md text-sm"
                                   placeholder="e.g. Student Contacted">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">New Status <span class="text-red-500">*</span></label>
                            <select name="new_status" required class="w-full border-gray-300 rounded-md text-sm">
                                @foreach(['pending','acknowledged','in_progress','resolved','closed'] as $s)
                                    <option value="{{ $s }}" @selected($s === $referral->status)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Internal Notes (confidential)</label>
                        <textarea name="internal_notes" rows="2" maxlength="2000"
                                  class="w-full border-gray-300 rounded-md text-sm"
                                  placeholder="Not visible to faculty..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Faculty Feedback (optional)</label>
                        <input type="text" name="faculty_feedback" maxlength="1000"
                               class="w-full border-gray-300 rounded-md text-sm"
                               placeholder="Update visible to the referring faculty...">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md">
                        Log Intervention
                    </button>
                </form>
                @endif

                {{-- Log entries --}}
                @forelse($referral->interventions as $iv)
                <div class="border-l-2 border-blue-200 pl-4 py-1 mb-4">
                    <p class="text-sm font-medium text-gray-800">{{ $iv->status_label }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $iv->counselor->name ?? '—' }} &bull; {{ $iv->created_at->format('M d, Y h:i A') }}
                        &bull; Status → <span class="font-medium">{{ ucwords(str_replace('_',' ',$iv->new_status)) }}</span>
                    </p>
                    @if($iv->internal_notes)
                    <p class="text-sm text-gray-600 mt-1 italic">{{ $iv->internal_notes }}</p>
                    @endif
                </div>
                @empty
                <p class="text-sm text-gray-400">No interventions logged yet.</p>
                @endforelse
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
