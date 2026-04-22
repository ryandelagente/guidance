<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Graduation Exit Survey</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm text-blue-800">
                <p class="font-semibold">Graduation Clearance Exit Survey</p>
                <p class="mt-1">Student: <strong>{{ $clearance->studentProfile->full_name ?? '—' }}</strong></p>
                <p>Your feedback helps CHMSU improve guidance services for future students. Please answer honestly.</p>
            </div>

            @if($questions->count() === 0)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm">
                    No exit survey questions have been configured yet. Please contact the guidance office.
                </div>
            @else
            <form method="POST" action="{{ route('exit-survey.store', $clearance) }}" class="space-y-6">
                @csrf

                @foreach($questions as $i => $q)
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <label class="block text-sm font-semibold text-gray-800 mb-3">
                        {{ $i + 1 }}. {{ $q->question_text }}
                        @if($q->is_required)<span class="text-red-500"> *</span>@endif
                    </label>

                    @if($q->question_type === 'text')
                        <textarea name="responses[{{ $q->id }}]"
                                  rows="3" maxlength="1000"
                                  {{ $q->is_required ? 'required' : '' }}
                                  class="w-full border-gray-300 rounded-md text-sm">{{ $existing[$q->id] ?? old("responses.{$q->id}") }}</textarea>

                    @elseif($q->question_type === 'rating_1_5')
                        <div class="flex gap-4 mt-1">
                            @foreach([1,2,3,4,5] as $val)
                            <label class="flex flex-col items-center gap-1 cursor-pointer">
                                <input type="radio" name="responses[{{ $q->id }}]" value="{{ $val }}"
                                       {{ ($existing[$q->id] ?? old("responses.{$q->id}")) == $val ? 'checked' : '' }}
                                       {{ $q->is_required ? 'required' : '' }}
                                       class="text-blue-600">
                                <span class="text-sm text-gray-600">{{ $val }}</span>
                            </label>
                            @endforeach
                            <span class="text-xs text-gray-400 self-end ml-2">1 = Poor &nbsp; 5 = Excellent</span>
                        </div>

                    @elseif($q->question_type === 'yes_no')
                        <div class="flex gap-6 mt-1">
                            @foreach(['Yes','No'] as $opt)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="responses[{{ $q->id }}]" value="{{ $opt }}"
                                       {{ ($existing[$q->id] ?? old("responses.{$q->id}")) === $opt ? 'checked' : '' }}
                                       {{ $q->is_required ? 'required' : '' }}
                                       class="text-blue-600">
                                <span class="text-sm text-gray-700">{{ $opt }}</span>
                            </label>
                            @endforeach
                        </div>

                    @elseif($q->question_type === 'multiple_choice' && is_array($q->options))
                        <div class="space-y-2 mt-1">
                            @foreach($q->options as $opt)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="responses[{{ $q->id }}]" value="{{ $opt }}"
                                       {{ ($existing[$q->id] ?? old("responses.{$q->id}")) === $opt ? 'checked' : '' }}
                                       {{ $q->is_required ? 'required' : '' }}
                                       class="text-blue-600">
                                <span class="text-sm text-gray-700">{{ $opt }}</span>
                            </label>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endforeach

                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-lg">
                        Submit Survey
                    </button>
                    <a href="{{ route('clearance.show', $clearance) }}"
                       class="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">
                        Save for Later
                    </a>
                </div>
            </form>
            @endif
        </div>
    </div>
</x-app-layout>
