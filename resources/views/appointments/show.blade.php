<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('appointments.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
                <h2 class="font-semibold text-xl text-gray-800">Appointment Details</h2>
            </div>
            @if(!auth()->user()->isStudent())
            <a href="{{ route('appointments.edit', $appointment) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Update Status
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Main card --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $appointment->appointment_date->format('F d, Y') }}
                        </p>
                        <p class="text-gray-500 mt-1">
                            {{ substr($appointment->start_time,0,5) }} – {{ substr($appointment->end_time,0,5) }}
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $appointment->getStatusBadgeClass() }}">
                        {{ ucwords(str_replace('_',' ',$appointment->status)) }}
                    </span>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-medium">Student</p>
                        <p class="text-gray-900 mt-1 font-medium">{{ $appointment->studentProfile->full_name ?? '—' }}</p>
                        <p class="text-gray-400 text-xs">{{ $appointment->studentProfile->student_id_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-medium">Counselor</p>
                        <p class="text-gray-900 mt-1 font-medium">{{ $appointment->counselor->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-medium">Type</p>
                        <p class="text-gray-900 mt-1">{{ ucwords(str_replace('_',' ',$appointment->appointment_type)) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-medium">Mode</p>
                        <p class="text-gray-900 mt-1">
                            {{ ucfirst($appointment->meeting_type) }}
                            @if($appointment->meeting_link)
                                — <a href="{{ $appointment->meeting_link }}" target="_blank" class="text-blue-600 hover:underline text-xs">Join Link</a>
                            @endif
                        </p>
                    </div>
                </div>

                @if($appointment->student_concern)
                <div class="mt-5 p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Student's Concern</p>
                    <p class="text-sm text-gray-700">{{ $appointment->student_concern }}</p>
                </div>
                @endif

                @if($appointment->notes_for_student)
                <div class="mt-3 p-4 bg-blue-50 rounded-lg">
                    <p class="text-xs font-medium text-blue-600 uppercase mb-1">Note from Counselor</p>
                    <p class="text-sm text-gray-700">{{ $appointment->notes_for_student }}</p>
                </div>
                @endif

                @if($appointment->isCancelled())
                <div class="mt-3 p-4 bg-red-50 rounded-lg">
                    <p class="text-xs font-medium text-red-600 uppercase mb-1">Cancellation Reason</p>
                    <p class="text-sm text-gray-700">{{ $appointment->cancelled_reason ?? '—' }}</p>
                    <p class="text-xs text-gray-400 mt-1">By: {{ $appointment->cancelledBy?->name }}</p>
                </div>
                @endif
            </div>

            {{-- Case Notes (counselor only) --}}
            @if(!auth()->user()->isStudent())
                @if($appointment->session)
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Case Notes</h4>
                        <a href="{{ route('sessions.show', $appointment->session) }}" class="text-blue-600 text-sm hover:underline">View Full Notes</a>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">Session Status</p>
                            <p class="text-gray-900 mt-0.5">{{ ucfirst($appointment->session->session_status) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">Presenting Concern</p>
                            <p class="text-gray-900 mt-0.5">{{ ucwords(str_replace('_',' ',$appointment->session->presenting_concern ?? '—')) }}</p>
                        </div>
                        @if($appointment->session->follow_up_date)
                        <div>
                            <p class="text-gray-500 text-xs">Follow-up Date</p>
                            <p class="text-gray-900 mt-0.5">{{ $appointment->session->follow_up_date->format('M d, Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @elseif($appointment->isCompleted() && auth()->user()->isCounselor())
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm">
                    <p class="text-yellow-800 font-medium">Case notes not yet written for this session.</p>
                    <a href="{{ route('sessions.create', ['appointment_id' => $appointment->id]) }}"
                       class="mt-2 inline-block bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-4 py-1.5 rounded-lg">
                        Write Case Notes
                    </a>
                </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
