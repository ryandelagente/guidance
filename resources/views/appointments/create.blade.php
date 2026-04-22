<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('appointments.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800">Book an Appointment</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('appointments.store') }}" class="space-y-5">
                @csrf

                <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                    <h3 class="text-base font-semibold text-gray-900 pb-2 border-b">Appointment Details</h3>

                    @if(!auth()->user()->isStudent())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student <span class="text-red-500">*</span></label>
                        <select name="student_profile_id" class="w-full border-gray-300 rounded-md text-sm">
                            <option value="">Select student...</option>
                            @foreach(\App\Models\StudentProfile::orderBy('last_name')->get() as $sp)
                                <option value="{{ $sp->id }}" @selected(old('student_profile_id') == $sp->id)>
                                    {{ $sp->full_name }} ({{ $sp->student_id_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_profile_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Counselor <span class="text-red-500">*</span></label>
                        <select name="counselor_id" id="counselor_id"
                                class="w-full border-gray-300 rounded-md text-sm @error('counselor_id') border-red-400 @enderror">
                            <option value="">Select counselor...</option>
                            @foreach($counselors as $c)
                                <option value="{{ $c->id }}" @selected(old('counselor_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('counselor_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="appointment_date" id="appointment_date"
                               value="{{ old('appointment_date') }}" min="{{ date('Y-m-d') }}"
                               class="w-full border-gray-300 rounded-md text-sm @error('appointment_date') border-red-400 @enderror">
                        @error('appointment_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Available Time Slots <span class="text-red-500">*</span></label>
                        <select name="start_time" id="start_time"
                                class="w-full border-gray-300 rounded-md text-sm @error('start_time') border-red-400 @enderror">
                            <option value="">— Select counselor and date first —</option>
                        </select>
                        @error('start_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Type <span class="text-red-500">*</span></label>
                        <select name="appointment_type" class="w-full border-gray-300 rounded-md text-sm">
                            @foreach(['academic'=>'Academic','personal_social'=>'Personal / Social','career'=>'Career','crisis'=>'Crisis Intervention'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('appointment_type') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
                        <div class="flex gap-4 mt-1">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="meeting_type" value="in_person" @checked(old('meeting_type','in_person') === 'in_person')
                                       class="text-blue-600"> In-Person
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="meeting_type" value="virtual" @checked(old('meeting_type') === 'virtual')
                                       class="text-blue-600"> Virtual (Online)
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason / Concern</label>
                        <textarea name="student_concern" rows="3" placeholder="Briefly describe why you want to speak with a counselor..."
                                  class="w-full border-gray-300 rounded-md text-sm">{{ old('student_concern') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('appointments.index') }}" class="px-4 py-2 text-sm text-gray-600">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-lg">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const counselorEl = document.getElementById('counselor_id');
    const dateEl      = document.getElementById('appointment_date');
    const slotEl      = document.getElementById('start_time');

    async function loadSlots() {
        const cid  = counselorEl.value;
        const date = dateEl.value;
        if (!cid || !date) return;

        slotEl.innerHTML = '<option>Loading...</option>';
        try {
            const res   = await fetch(`/appointments/slots?counselor_id=${cid}&date=${date}`);
            const slots = await res.json();
            if (slots.length === 0) {
                slotEl.innerHTML = '<option value="">No available slots for this day</option>';
            } else {
                slotEl.innerHTML = slots.map(s => `<option value="${s}">${s}</option>`).join('');
            }
        } catch {
            slotEl.innerHTML = '<option value="">Error loading slots</option>';
        }
    }

    counselorEl.addEventListener('change', loadSlots);
    dateEl.addEventListener('change', loadSlots);
    </script>
</x-app-layout>
