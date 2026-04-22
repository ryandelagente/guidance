<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Testing Session</h2>
            <a href="{{ route('test-schedules.show', $testSchedule) }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <p class="text-sm text-gray-500 mb-5">
                    Test: <strong>{{ $testSchedule->test->name ?? '—' }}</strong>
                </p>

                <form method="POST" action="{{ route('test-schedules.update', $testSchedule) }}" class="space-y-5">
                    @csrf @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Administered By <span class="text-red-500">*</span></label>
                        <select name="administered_by" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            @foreach($counselors as $c)
                                <option value="{{ $c->id }}" @selected(old('administered_by',$testSchedule->administered_by) == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="scheduled_date"
                                   value="{{ old('scheduled_date', $testSchedule->scheduled_date->toDateString()) }}"
                                   required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time <span class="text-red-500">*</span></label>
                            <input type="time" name="start_time"
                                   value="{{ old('start_time', substr($testSchedule->start_time,0,5)) }}"
                                   required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            @foreach(['scheduled','ongoing','completed','cancelled'] as $s)
                                <option value="{{ $s }}" @selected(old('status',$testSchedule->status) === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                        <input type="text" name="venue" value="{{ old('venue', $testSchedule->venue) }}" maxlength="200"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">College</label>
                            <input type="text" name="college" value="{{ old('college', $testSchedule->college) }}" maxlength="200"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year Level</label>
                            <select name="year_level" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">All</option>
                                @foreach(['1st','2nd','3rd','4th','5th','Graduate'] as $y)
                                    <option value="{{ $y }}" @selected(old('year_level',$testSchedule->year_level) === $y)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expected No.</label>
                            <input type="number" name="expected_participants"
                                   value="{{ old('expected_participants', $testSchedule->expected_participants) }}"
                                   min="1" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" maxlength="1000"
                                  class="w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('notes', $testSchedule->notes) }}</textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                            Save Changes
                        </button>
                        <a href="{{ route('test-schedules.show', $testSchedule) }}"
                           class="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
