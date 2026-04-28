<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('wellness.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Wellness Check-in</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="text-5xl">{{ \App\Models\WellnessCheckin::moodEmoji($checkin->mood) }}</div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">{{ \App\Models\WellnessCheckin::moodLabel($checkin->mood) }} mood</h3>
                        <p class="text-sm text-gray-500">{{ $checkin->created_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                    <div class="ml-auto">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $checkin->getRiskBadgeClass() }}">{{ ucfirst($checkin->risk_level) }} Risk</span>
                    </div>
                </div>

                @if(!auth()->user()->isStudent())
                <div class="border-t border-gray-100 pt-4 mb-4">
                    <p class="text-sm text-gray-500">Student</p>
                    <a href="{{ route('students.show', $checkin->studentProfile) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $checkin->studentProfile->full_name }}</a>
                    <p class="text-xs text-gray-400">{{ $checkin->studentProfile->student_id_number }} • {{ $checkin->studentProfile->program }}</p>
                    @if($checkin->studentProfile->assignedCounselor)
                        <p class="text-xs text-gray-500 mt-1">Assigned to: {{ $checkin->studentProfile->assignedCounselor->name }}</p>
                    @endif
                </div>
                @endif

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 border-t border-gray-100 pt-4">
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Stress</div>
                        <div class="font-medium text-gray-800 mt-1">{{ \App\Models\WellnessCheckin::intensityLabel($checkin->stress_level) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Sleep</div>
                        <div class="font-medium text-gray-800 mt-1">{{ ['Poor','Fair','Okay','Good','Excellent'][$checkin->sleep_quality - 1] }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Academic</div>
                        <div class="font-medium text-gray-800 mt-1">{{ \App\Models\WellnessCheckin::intensityLabel($checkin->academic_stress) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Risk Score</div>
                        <div class="font-medium text-gray-800 mt-1">{{ $checkin->risk_score }} / 5</div>
                    </div>
                </div>

                @if($checkin->wants_counselor)
                <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                    <strong>Student requested a counselor.</strong> Please reach out to schedule a session.
                </div>
                @endif

                @if($checkin->notes)
                <div class="mt-4 border-t border-gray-100 pt-4">
                    <div class="text-xs text-gray-500 uppercase mb-2">Student's Note</div>
                    <p class="text-sm text-gray-700 italic">"{{ $checkin->notes }}"</p>
                </div>
                @endif

                @if($checkin->reviewed)
                <div class="mt-4 border-t border-gray-100 pt-4 text-xs text-gray-500">
                    Reviewed by <span class="font-medium text-gray-700">{{ $checkin->reviewer?->name }}</span> on {{ $checkin->reviewed_at?->format('M d, Y h:i A') }}
                </div>
                @elseif(!auth()->user()->isStudent())
                <div class="mt-4 border-t border-gray-100 pt-4 flex justify-end">
                    <form method="POST" action="{{ route('wellness.review', $checkin) }}">
                        @csrf @method('PATCH')
                        <button class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md">Mark as Reviewed</button>
                    </form>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
