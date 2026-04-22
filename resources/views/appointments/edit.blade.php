<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('appointments.show', $appointment) }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800">Update Appointment</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="space-y-5">
                @csrf @method('PATCH')

                <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                    <div class="p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
                        <strong>{{ $appointment->studentProfile->full_name }}</strong> —
                        {{ $appointment->appointment_date->format('M d, Y') }}
                        {{ substr($appointment->start_time,0,5) }}
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status_select" class="w-full border-gray-300 rounded-md text-sm">
                            @foreach(['pending','confirmed','in_progress','completed','cancelled','no_show'] as $s)
                                <option value="{{ $s }}" @selected($appointment->status === $s)>
                                    {{ ucwords(str_replace('_',' ',$s)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meeting Link (Virtual)</label>
                        <input type="url" name="meeting_link" value="{{ old('meeting_link', $appointment->meeting_link) }}"
                               placeholder="https://meet.google.com/..."
                               class="w-full border-gray-300 rounded-md text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note for Student</label>
                        <textarea name="notes_for_student" rows="3"
                                  class="w-full border-gray-300 rounded-md text-sm">{{ old('notes_for_student', $appointment->notes_for_student) }}</textarea>
                    </div>

                    <div id="cancel_reason_wrap" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cancellation Reason <span class="text-red-500">*</span></label>
                        <textarea name="cancelled_reason" rows="2"
                                  class="w-full border-gray-300 rounded-md text-sm">{{ old('cancelled_reason', $appointment->cancelled_reason) }}</textarea>
                        @error('cancelled_reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('appointments.show', $appointment) }}" class="px-4 py-2 text-sm text-gray-600">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-lg">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const statusEl = document.getElementById('status_select');
    const reasonEl = document.getElementById('cancel_reason_wrap');
    statusEl.addEventListener('change', () => {
        reasonEl.classList.toggle('hidden', statusEl.value !== 'cancelled');
    });
    if (statusEl.value === 'cancelled') reasonEl.classList.remove('hidden');
    </script>
</x-app-layout>
