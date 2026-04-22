<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('appointments.show', $session->appointment) }}" class="text-gray-400 hover:text-gray-600">← Back</a>
                <h2 class="font-semibold text-xl text-gray-800">Case Notes</h2>
            </div>
            <a href="{{ route('sessions.edit', $session) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-4 py-2 rounded-lg">Edit Notes</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-start justify-between mb-4 pb-4 border-b">
                    <div>
                        <p class="font-bold text-gray-900">{{ $session->studentProfile->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $session->studentProfile->student_id_number }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Session on {{ $session->appointment->appointment_date->format('F d, Y') }}
                            · {{ substr($session->appointment->start_time,0,5) }}
                        </p>
                    </div>
                    <div class="text-right text-sm">
                        <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                            {{ ucfirst($session->session_status) }}
                        </span>
                        @if($session->presenting_concern)
                        <p class="text-gray-400 text-xs mt-1">{{ ucwords(str_replace('_',' ',$session->presenting_concern)) }}</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                            🔓 Case Notes (Decrypted)
                        </p>
                        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-800 whitespace-pre-wrap font-mono leading-relaxed">
                            {{ $session->case_notes ?? 'No notes recorded.' }}
                        </div>
                    </div>

                    @if($session->recommendations)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Recommendations</p>
                        <div class="bg-blue-50 rounded-lg p-4 text-sm text-gray-800">
                            {{ $session->recommendations }}
                        </div>
                    </div>
                    @endif

                    @if($session->follow_up_date)
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-500">Follow-up:</span>
                        <span class="font-medium text-gray-800">{{ $session->follow_up_date->format('F d, Y') }}</span>
                    </div>
                    @endif
                </div>

                <div class="mt-6 pt-4 border-t text-xs text-gray-400">
                    Recorded by {{ $session->counselor->name }} · Last updated {{ $session->updated_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
