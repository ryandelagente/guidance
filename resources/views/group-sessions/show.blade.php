<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('group-sessions.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $session->title }}</h2>
            </div>
            <form method="POST" action="{{ route('group-sessions.destroy', $session) }}" onsubmit="return confirm('Delete this group session and all participation records?')">
                @csrf @method('DELETE')
                <button class="text-red-500 hover:text-red-700 text-sm">Delete</button>
            </form>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center gap-2 mb-3 flex-wrap">
                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">{{ \App\Models\GroupSession::FOCUSES[$session->focus] }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ ['scheduled' => 'bg-blue-100 text-blue-700', 'in_progress' => 'bg-yellow-100 text-yellow-700', 'completed' => 'bg-green-100 text-green-700', 'cancelled' => 'bg-red-100 text-red-700'][$session->status] }}">{{ ucwords(str_replace('_', ' ', $session->status)) }}</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500">Date:</span> {{ $session->session_date->format('l, F d, Y') }} • {{ substr($session->start_time, 0, 5) }} – {{ substr($session->end_time, 0, 5) }}</div>
                    <div><span class="text-gray-500">Venue:</span> {{ $session->venue }}</div>
                    <div><span class="text-gray-500">Counselor:</span> {{ $session->counselor?->name }}</div>
                    <div><span class="text-gray-500">Capacity:</span> {{ $session->registered_count }} / {{ $session->max_participants }}</div>
                </div>
                @if($session->description)
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-500 uppercase mb-1">Goals</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $session->description }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">Participants ({{ $session->participants->count() }})</h3>
                @if($session->participants->isNotEmpty())
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($session->participants as $p)
                        <tr>
                            <td class="py-2.5">
                                <a href="{{ route('students.show', $p->studentProfile) }}" class="font-medium text-gray-800 hover:text-blue-600">{{ $p->studentProfile?->full_name }}</a>
                                <div class="text-xs text-gray-400">{{ $p->studentProfile?->student_id_number ?? '—' }}</div>
                            </td>
                            <td class="py-2.5 text-right">
                                <form method="POST" action="{{ route('group-sessions.attendance', [$session, $p]) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <select name="attendance" onchange="this.form.submit()" class="text-xs border-gray-300 rounded">
                                        @foreach(['registered' => 'Registered','attended' => '✓ Attended','no_show' => 'No-show','withdrew' => 'Withdrew'] as $v => $l)
                                            <option value="{{ $v }}" @selected($p->attendance === $v)>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    <p class="text-sm text-gray-400">No participants yet.</p>
                @endif

                @if(!$session->isFull())
                <details class="mt-4 border border-gray-100 rounded-lg">
                    <summary class="px-4 py-2.5 text-sm font-medium text-gray-600 cursor-pointer hover:bg-gray-50 rounded-lg">+ Add Participant</summary>
                    <form method="POST" action="{{ route('group-sessions.add-participant', $session) }}" class="px-4 pb-4 pt-3 flex gap-2">
                        @csrf
                        <select name="student_profile_id" required class="flex-1 border-gray-300 rounded-md text-sm">
                            <option value="">— Select student —</option>
                            @foreach(\App\Models\StudentProfile::orderBy('last_name')->get() as $sp)
                                @if(!$session->participants->where('student_profile_id', $sp->id)->count())
                                    <option value="{{ $sp->id }}">{{ $sp->full_name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 rounded-md">Add</button>
                    </form>
                </details>
                @endif
            </div>

            <form method="POST" action="{{ route('group-sessions.update', $session) }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf @method('PATCH')
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Group Notes & Status</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="border-gray-300 rounded-md text-sm">
                        @foreach(['scheduled','in_progress','completed','cancelled'] as $s)
                            <option value="{{ $s }}" @selected($session->status === $s)>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Group Notes <span class="text-gray-400">(themes discussed, key takeaways — not individual disclosures)</span></label>
                    <textarea name="group_notes" rows="6" maxlength="5000" class="w-full border-gray-300 rounded-md text-sm">{{ $session->group_notes }}</textarea>
                </div>
                <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-md">Save</button>
            </form>
        </div>
    </div>
</x-app-layout>
