<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('appointments.show', $appointment) }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800">
                {{ $session ? 'Edit Case Notes' : 'Write Case Notes' }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Student & Appointment Info --}}
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm text-blue-800">
                <strong>{{ $appointment->studentProfile->full_name }}</strong>
                · {{ $appointment->studentProfile->student_id_number }}
                · {{ $appointment->appointment_date->format('M d, Y') }}
                · {{ substr($appointment->start_time,0,5) }}
                <span class="ml-2 text-xs bg-blue-200 px-2 py-0.5 rounded-full">
                    {{ ucwords(str_replace('_',' ',$appointment->appointment_type)) }}
                </span>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2 text-xs text-yellow-700">
                🔒 Case notes are encrypted with AES-256 and can only be decrypted by authorised counselors.
            </div>

            <form method="POST" action="{{ $session ? route('sessions.update', $session) : route('sessions.store') }}" class="space-y-5">
                @csrf
                @if($session) @method('PATCH') @endif
                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

                <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Session Status <span class="text-red-500">*</span></label>
                            <select name="session_status" class="w-full border-gray-300 rounded-md text-sm">
                                @foreach(['initial'=>'Initial','ongoing'=>'Ongoing','terminated'=>'Terminated','referred'=>'Referred'] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('session_status', $session?->session_status) === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Presenting Concern</label>
                            <select name="presenting_concern" class="w-full border-gray-300 rounded-md text-sm">
                                <option value="">Select...</option>
                                @foreach(['academic','personal_social','career','financial','family','mental_health','behavioral','other'] as $c)
                                    <option value="{{ $c }}" @selected(old('presenting_concern', $session?->presenting_concern) === $c)>
                                        {{ ucwords(str_replace('_',' ',$c)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Case Notes
                            <span class="text-xs text-gray-400 font-normal ml-1">(Confidential — encrypted on save)</span>
                        </label>
                        <textarea name="case_notes" rows="10"
                                  class="w-full border-gray-300 rounded-md text-sm font-mono focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Record session observations, student disclosures, interventions used, and counselor assessment...">{{ old('case_notes', $session?->case_notes) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recommendations</label>
                        <textarea name="recommendations" rows="3"
                                  class="w-full border-gray-300 rounded-md text-sm">{{ old('recommendations', $session?->recommendations) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Follow-up Date</label>
                        <input type="date" name="follow_up_date"
                               value="{{ old('follow_up_date', $session?->follow_up_date?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('appointments.show', $appointment) }}" class="px-4 py-2 text-sm text-gray-600">Cancel</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-6 py-2 rounded-lg">
                        🔒 Save Encrypted Notes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
