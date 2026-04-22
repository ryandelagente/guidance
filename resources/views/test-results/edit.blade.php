<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Test Result</h2>
            <a href="{{ route('test-results.show', $testResult) }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
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
                    Student: <strong>{{ $testResult->studentProfile->full_name ?? '—' }}</strong> &bull;
                    Test: <strong>{{ $testResult->test->name ?? '—' }}</strong>
                </p>

                <form method="POST" action="{{ route('test-results.update', $testResult) }}" class="space-y-5">
                    @csrf @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Test Date <span class="text-red-500">*</span></label>
                        <input type="date" name="test_date"
                               value="{{ old('test_date', $testResult->test_date->toDateString()) }}"
                               required max="{{ now()->toDateString() }}"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Raw Score</label>
                            <input type="number" name="raw_score" value="{{ old('raw_score', $testResult->raw_score) }}" min="0"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Percentile (%)</label>
                            <input type="number" name="percentile" value="{{ old('percentile', $testResult->percentile) }}"
                                   min="0" max="100" step="0.01"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grade Equivalent</label>
                            <input type="text" name="grade_equivalent" value="{{ old('grade_equivalent', $testResult->grade_equivalent) }}"
                                   maxlength="20" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Interpretation Level</label>
                        <select name="interpretation_level" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">— None —</option>
                            @foreach(['very_low','low','average','above_average','superior','very_superior'] as $lvl)
                                <option value="{{ $lvl }}" @selected(old('interpretation_level',$testResult->interpretation_level) === $lvl)>
                                    {{ ucwords(str_replace('_',' ',$lvl)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Interpretation / Narrative</label>
                        <textarea name="interpretation" rows="4" maxlength="3000"
                                  class="w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('interpretation', $testResult->interpretation) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Suggested Career Paths <span class="text-gray-400 text-xs font-normal">(comma-separated)</span></label>
                        <input type="text" name="career_matches"
                               value="{{ old('career_matches', implode(', ', $testResult->career_matches ?? [])) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_released" value="0">
                        <input type="checkbox" name="is_released" id="is_released" value="1"
                               {{ old('is_released', $testResult->is_released) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        <label for="is_released" class="text-sm font-medium text-gray-700">Release to student</label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                            Save Changes
                        </button>
                        <a href="{{ route('test-results.show', $testResult) }}"
                           class="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
