<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Record Test Result</h2>
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

                @if($selectedSchedule)
                <div class="mb-5 bg-blue-50 border border-blue-100 p-3 rounded-md text-sm text-blue-800">
                    Recording for: <strong>{{ $selectedSchedule->test->name ?? '' }}</strong>
                    on {{ $selectedSchedule->scheduled_date->format('M d, Y') }}
                </div>
                @endif

                <form method="POST" action="{{ route('test-results.store') }}" class="space-y-5">
                    @csrf

                    <input type="hidden" name="test_schedule_id" value="{{ $selectedSchedule?->id ?? old('test_schedule_id') }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student <span class="text-red-500">*</span></label>
                        <select name="student_profile_id" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">— Select student —</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}" @selected(old('student_profile_id') == $s->id)>
                                    {{ $s->full_name }} ({{ $s->student_id_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(!$selectedSchedule)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Test <span class="text-red-500">*</span></label>
                        <select name="psychological_test_id" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">— Select test —</option>
                            @foreach($tests as $t)
                                <option value="{{ $t->id }}" @selected(old('psychological_test_id') == $t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <input type="hidden" name="psychological_test_id" value="{{ $selectedSchedule->psychological_test_id }}">
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Test Date <span class="text-red-500">*</span></label>
                        <input type="date" name="test_date"
                               value="{{ old('test_date', $selectedSchedule?->scheduled_date?->toDateString() ?? now()->toDateString()) }}"
                               required max="{{ now()->toDateString() }}"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Raw Score</label>
                            <input type="number" name="raw_score" value="{{ old('raw_score') }}" min="0"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Percentile (%)</label>
                            <input type="number" name="percentile" value="{{ old('percentile') }}"
                                   min="0" max="100" step="0.01"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grade Equivalent</label>
                            <input type="text" name="grade_equivalent" value="{{ old('grade_equivalent') }}" maxlength="20"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="e.g. 7.5">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Interpretation Level</label>
                        <select name="interpretation_level" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">— Select —</option>
                            @foreach(['very_low','low','average','above_average','superior','very_superior'] as $lvl)
                                <option value="{{ $lvl }}" @selected(old('interpretation_level') === $lvl)>
                                    {{ ucwords(str_replace('_',' ',$lvl)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Interpretation / Narrative</label>
                        <textarea name="interpretation" rows="4" maxlength="3000"
                                  class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                                  placeholder="Counselor's interpretation of results...">{{ old('interpretation') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Suggested Career Paths
                            <span class="text-gray-400 font-normal text-xs">(comma-separated)</span>
                        </label>
                        <input type="text" name="career_matches" value="{{ old('career_matches') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                               placeholder="e.g. Software Engineer, Data Analyst, Teacher">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_released" value="0">
                        <input type="checkbox" name="is_released" id="is_released" value="1"
                               {{ old('is_released') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        <label for="is_released" class="text-sm font-medium text-gray-700">Release to student (visible in student portal)</label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                            Save Result
                        </button>
                        <a href="{{ route('test-results.index') }}"
                           class="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
