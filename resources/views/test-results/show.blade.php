<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Test Result</h2>
            <div class="flex gap-3">
                @if(auth()->user()->isStaff())
                <a href="{{ route('test-results.edit', $testResult) }}"
                   class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md">Edit</a>
                @endif
                <a href="{{ route('test-results.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6 space-y-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $testResult->studentProfile->full_name ?? '—' }}</h3>
                        <p class="text-sm text-gray-500">{{ $testResult->studentProfile->student_id_number ?? '' }}</p>
                    </div>
                    @if($testResult->interpretation_level)
                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $testResult->getInterpretationBadgeClass() }}">
                        {{ ucwords(str_replace('_',' ',$testResult->interpretation_level)) }}
                    </span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium text-gray-500">Test</span><p class="mt-0.5 text-gray-800">{{ $testResult->test->name ?? '—' }}</p></div>
                    <div><span class="font-medium text-gray-500">Test Date</span><p class="mt-0.5 text-gray-800">{{ $testResult->test_date->format('M d, Y') }}</p></div>
                    <div><span class="font-medium text-gray-500">Raw Score</span><p class="mt-0.5 text-gray-800">{{ $testResult->raw_score ?? '—' }}</p></div>
                    <div><span class="font-medium text-gray-500">Percentile</span><p class="mt-0.5 text-gray-800">{{ $testResult->percentile ? $testResult->percentile . '%' : '—' }}</p></div>
                    <div><span class="font-medium text-gray-500">Grade Equivalent</span><p class="mt-0.5 text-gray-800">{{ $testResult->grade_equivalent ?? '—' }}</p></div>
                    <div><span class="font-medium text-gray-500">Recorded By</span><p class="mt-0.5 text-gray-800">{{ $testResult->recordedBy->name ?? '—' }}</p></div>
                    @if($testResult->schedule)
                    <div><span class="font-medium text-gray-500">Session</span>
                        <p class="mt-0.5"><a href="{{ route('test-schedules.show', $testResult->schedule) }}" class="text-blue-600 hover:underline text-sm">{{ $testResult->schedule->scheduled_date->format('M d, Y') }}</a></p>
                    </div>
                    @endif
                </div>

                @if($testResult->interpretation)
                <div class="text-sm">
                    <span class="font-medium text-gray-500">Interpretation</span>
                    <p class="mt-1 text-gray-800 whitespace-pre-wrap">{{ $testResult->interpretation }}</p>
                </div>
                @endif

                @if(!empty($testResult->career_matches))
                <div class="text-sm">
                    <span class="font-medium text-gray-500">Suggested Career Paths</span>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($testResult->career_matches as $career)
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">{{ $career }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(auth()->user()->isStaff())
                <div class="pt-3 border-t flex items-center justify-between">
                    <div class="text-sm">
                        <span class="font-medium text-gray-500">Release to Student:</span>
                        <span class="ml-2 {{ $testResult->is_released ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                            {{ $testResult->is_released ? 'Yes — visible in student portal' : 'Not yet released' }}
                        </span>
                    </div>
                    @if(auth()->user()->isSuperAdmin())
                    <form method="POST" action="{{ route('test-results.destroy', $testResult) }}"
                          onsubmit="return confirm('Delete this result?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 hover:underline">Delete</button>
                    </form>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
