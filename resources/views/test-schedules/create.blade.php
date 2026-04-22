<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Schedule Testing Session</h2>
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

                <form method="POST" action="{{ route('test-schedules.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Test <span class="text-red-500">*</span></label>
                        <select name="psychological_test_id" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">— Select test —</option>
                            @foreach($tests as $t)
                                <option value="{{ $t->id }}" @selected(old('psychological_test_id', request('test_id')) == $t->id)>
                                    {{ $t->name }} ({{ $t->type_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Administered By <span class="text-red-500">*</span></label>
                        <select name="administered_by" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">— Select counselor —</option>
                            @foreach($counselors as $c)
                                <option value="{{ $c->id }}" @selected(old('administered_by') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="scheduled_date" value="{{ old('scheduled_date') }}" required
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time <span class="text-red-500">*</span></label>
                            <input type="time" name="start_time" value="{{ old('start_time') }}" required
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                        <input type="text" name="venue" value="{{ old('venue') }}" maxlength="200"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                               placeholder="e.g. AVR, GC Room 101">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">College</label>
                            <input type="text" name="college" value="{{ old('college') }}" maxlength="200"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                                   placeholder="e.g. CICT">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year Level</label>
                            <select name="year_level" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">All</option>
                                @foreach(['1st','2nd','3rd','4th','5th','Graduate'] as $y)
                                    <option value="{{ $y }}" @selected(old('year_level') === $y)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expected No.</label>
                            <input type="number" name="expected_participants" value="{{ old('expected_participants') }}"
                                   min="1" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" maxlength="1000"
                                  class="w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                            Schedule Session
                        </button>
                        <a href="{{ route('test-schedules.index') }}"
                           class="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
