<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">📝 Case Notes</h2>
            <div class="flex items-center gap-2">
                @php
                    $verifiedAt = session('case_note_pin_verified_at');
                    $remaining = $verifiedAt ? max(0, 15 * 60 - (now()->timestamp - $verifiedAt)) : 0;
                @endphp
                @if($remaining > 0)
                <span class="text-xs bg-green-100 text-green-700 px-2.5 py-1 rounded-full font-medium" title="PIN verified — re-prompts in {{ ceil($remaining/60) }} min">
                    🔓 Unlocked ({{ ceil($remaining/60) }}m)
                </span>
                <form method="POST" action="{{ route('case-note-pin.lock') }}">
                    @csrf
                    <button class="text-xs text-gray-500 hover:text-red-600">🔒 Lock now</button>
                </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                @if($sessions->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <p class="text-4xl mb-3">📝</p>
                    <p class="font-medium text-gray-500">No case notes recorded yet</p>
                    <p class="text-sm mt-1">Case notes are created when you save notes for a completed appointment.</p>
                    <a href="{{ route('appointments.index') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">Go to Appointments &rarr;</a>
                </div>
                @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Student</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Concern</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Follow-up</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Session Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($sessions as $session)
                        @php
                        $statusColors = [
                            'initial'    => 'bg-blue-100 text-blue-700',
                            'ongoing'    => 'bg-indigo-100 text-indigo-700',
                            'terminated' => 'bg-gray-100 text-gray-600',
                            'referred'   => 'bg-purple-100 text-purple-700',
                        ];
                        $concernColors = [
                            'academic'        => 'bg-cyan-100 text-cyan-700',
                            'personal_social' => 'bg-pink-100 text-pink-700',
                            'career'          => 'bg-green-100 text-green-700',
                            'financial'       => 'bg-yellow-100 text-yellow-700',
                            'family'          => 'bg-orange-100 text-orange-700',
                            'mental_health'   => 'bg-red-100 text-red-700',
                            'behavioral'      => 'bg-violet-100 text-violet-700',
                            'other'           => 'bg-gray-100 text-gray-600',
                        ];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $session->studentProfile?->full_name ?? '—' }}</p>
                                <p class="text-xs text-gray-400">{{ $session->studentProfile?->student_id_number }}</p>
                            </td>
                            <td class="px-5 py-3">
                                @if($session->presenting_concern)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $concernColors[$session->presenting_concern] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucwords(str_replace('_', ' ', $session->presenting_concern)) }}
                                </span>
                                @else
                                <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$session->session_status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($session->session_status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ $session->follow_up_date?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-gray-400 text-xs">
                                {{ $session->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-5 py-3 text-right space-x-3">
                                <a href="{{ route('sessions.show', $session) }}" class="text-indigo-600 hover:underline text-xs">View</a>
                                <a href="{{ route('sessions.edit', $session) }}" class="text-gray-500 hover:underline text-xs">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($sessions->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $sessions->links() }}
                </div>
                @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
