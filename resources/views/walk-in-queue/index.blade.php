<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">🚪 Walk-in Queue — {{ now()->format('F d, Y') }}</h2>
            <button onclick="document.getElementById('add-walkin').showModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ Check In</button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Total Today</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_today'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-yellow-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Waiting</div>
                    <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['waiting'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-blue-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Being Seen</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['being_seen'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-green-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Completed</div>
                    <div class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completed'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Avg Wait (min)</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['avg_wait'] }}</div>
                </div>
            </div>

            {{-- ── Currently Being Seen ── --}}
            @if($beingSeen->isNotEmpty())
            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-5">
                <h3 class="font-semibold text-blue-900 text-sm uppercase tracking-wider mb-3">📞 Currently Being Seen</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($beingSeen as $w)
                    <div class="bg-white rounded-lg p-4 border border-blue-100">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $w->display_name }}</div>
                                <div class="text-xs text-gray-500">with {{ $w->counselor?->name ?? 'unassigned' }}</div>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full border {{ $w->getPriorityBadgeClass() }}">{{ ucfirst($w->priority) }}</span>
                        </div>
                        <p class="text-xs text-gray-600 line-clamp-2 mb-3">{{ $w->reason }}</p>
                        <div class="text-xs text-gray-400 mb-2">Called {{ $w->called_at?->diffForHumans() }}</div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('walk-in.complete', $w) }}" class="flex-1">
                                @csrf @method('PATCH')
                                <button class="w-full bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-1.5 rounded-md">✓ Complete</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── Waiting Queue ── --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 bg-yellow-50 border-b border-yellow-100">
                    <h3 class="font-semibold text-gray-800 text-sm uppercase tracking-wider">⏳ Waiting Queue</h3>
                </div>

                @if($waiting->isEmpty())
                    <div class="p-8 text-center text-gray-400 text-sm">No one waiting. Queue is clear. ✨</div>
                @else
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">#</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitor</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wait</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Counselor</th>
                            <th class="px-4 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($waiting as $i => $w)
                        <tr class="hover:bg-gray-50 {{ $w->priority === 'crisis' ? 'bg-red-50' : ($w->priority === 'urgent' ? 'bg-orange-50' : '') }}">
                            <td class="px-4 py-3 font-bold text-gray-700 text-lg">#{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                @if($w->studentProfile)
                                    <a href="{{ route('students.show', $w->studentProfile) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $w->display_name }}</a>
                                    <div class="text-xs text-gray-400">{{ $w->studentProfile->student_id_number ?? '' }}</div>
                                @else
                                    <span class="font-medium text-gray-900">{{ $w->display_name }}</span>
                                    <div class="text-xs text-gray-400">{{ $w->contact_number ?? 'Walk-in (non-student)' }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 max-w-sm">
                                <div class="truncate">{{ $w->reason }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full border {{ $w->getPriorityBadgeClass() }}">
                                    {{ $w->priority === 'crisis' ? '🚨 Crisis' : ucfirst($w->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="font-medium text-gray-700">{{ $w->wait_minutes }}m</span>
                                <div class="text-gray-400">{{ $w->arrived_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $w->counselor?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                                <form method="POST" action="{{ route('walk-in.call', $w) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-3 py-1 rounded-md">📞 Call</button>
                                </form>
                                <form method="POST" action="{{ route('walk-in.no-show', $w) }}" class="inline" onsubmit="return confirm('Mark as no-show?')">
                                    @csrf @method('PATCH')
                                    <button class="text-gray-400 hover:text-gray-600 text-xs">No-show</button>
                                </form>
                                <form method="POST" action="{{ route('walk-in.destroy', $w) }}" class="inline" onsubmit="return confirm('Cancel this entry?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-600 text-xs">×</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            {{-- ── Completed Today ── --}}
            @if($completed->isNotEmpty())
            <details class="bg-white shadow-sm rounded-lg overflow-hidden">
                <summary class="px-5 py-3 bg-gray-50 cursor-pointer hover:bg-gray-100 font-semibold text-gray-700 text-sm uppercase tracking-wider">
                    ✓ Completed Today ({{ $completed->count() }})
                </summary>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($completed as $w)
                        <tr>
                            <td class="px-4 py-2.5 font-medium text-gray-800">{{ $w->display_name }}</td>
                            <td class="px-4 py-2.5 text-xs text-gray-500">{{ $w->reason }}</td>
                            <td class="px-4 py-2.5 text-xs text-gray-500">{{ $w->counselor?->name ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-xs text-gray-400">
                                {{ $w->arrived_at->format('h:i A') }} – {{ $w->completed_at?->format('h:i A') }}
                                ({{ $w->arrived_at->diffInMinutes($w->completed_at ?? now()) }}m)
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $w->getStatusBadgeClass() }}">{{ ucwords(str_replace('_', ' ', $w->status)) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </details>
            @endif

        </div>
    </div>

    {{-- ── Add Walk-in Modal ── --}}
    <dialog id="add-walkin" class="rounded-xl shadow-2xl backdrop:bg-black/50 p-0 max-w-2xl w-full">
        <form method="POST" action="{{ route('walk-in.store') }}" class="bg-white">
            @csrf
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 text-lg">Check In Walk-in</h3>
                <button type="button" onclick="document.getElementById('add-walkin').close()" class="text-gray-400 hover:text-gray-600 text-xl">×</button>
            </div>
            <div class="p-6 space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student (if registered)</label>
                    <select name="student_profile_id" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">— Walk-in / non-student —</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->last_name }}, {{ $s->first_name }} ({{ $s->student_id_number ?? 'No ID' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-gray-400">(if not in dropdown)</span></label>
                        <input type="text" name="name" maxlength="200" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <input type="text" name="contact_number" maxlength="50" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Visit <span class="text-red-500">*</span></label>
                    <input type="text" name="reason" required maxlength="200" placeholder="e.g. Academic concerns, follow-up, crisis, certificate request"
                           class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority" class="w-full border-gray-300 rounded-md text-sm">
                            <option value="normal">Normal</option>
                            <option value="urgent">Urgent</option>
                            <option value="crisis">🚨 Crisis</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign Counselor (optional)</label>
                        <select name="assigned_counselor_id" class="w-full border-gray-300 rounded-md text-sm">
                            <option value="">— Auto-assign —</option>
                            @foreach($counselors as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-walkin').close()" class="text-sm px-4 py-2 text-gray-600">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-md">Add to Queue</button>
            </div>
        </form>
    </dialog>
</x-app-layout>
