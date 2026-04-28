<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">⭐ Session Feedback</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                Your responses are <strong>anonymous</strong> and help us improve our counseling services. Takes 1 minute.
            </div>

            <form method="POST" action="{{ route('session-feedback.store', $session) }}" class="bg-white shadow-sm rounded-lg p-6 space-y-6">
                @csrf

                {{-- Session info --}}
                <div class="border-b border-gray-100 pb-4 mb-2">
                    <p class="text-xs text-gray-500 uppercase mb-1">Session with</p>
                    <p class="font-medium text-gray-900">{{ $session->counselor?->name ?? 'Counselor' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $session->created_at->format('F d, Y') }}</p>
                </div>

                {{-- Star rating component --}}
                @php
                    $questions = [
                        'overall_rating' => ['label' => 'Overall, how would you rate this session?', 'low' => 'Poor', 'high' => 'Excellent'],
                        'helpful_score'  => ['label' => 'How helpful was the counselor?', 'low' => 'Not helpful', 'high' => 'Very helpful'],
                        'listened_score' => ['label' => 'Did you feel heard and understood?', 'low' => 'Not at all', 'high' => 'Completely'],
                        'comfort_score'  => ['label' => 'How comfortable did you feel during the session?', 'low' => 'Uncomfortable', 'high' => 'Very comfortable'],
                    ];
                @endphp

                @foreach($questions as $field => $q)
                <div x-data="{ rating: 0 }">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ $q['label'] }}</label>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" @click="rating = {{ $i }}"
                                    class="text-3xl transition transform hover:scale-110">
                                <span :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">★</span>
                            </button>
                        @endfor
                        <input type="hidden" name="{{ $field }}" :value="rating" required>
                        <span class="ml-3 text-xs text-gray-400" x-text="rating === 0 ? '{{ $q['low'] }} → {{ $q['high'] }}' : ['','{{ $q['low'] }}','Below avg','Average','Good','{{ $q['high'] }}'][rating]"></span>
                    </div>
                </div>
                @endforeach

                {{-- Yes/No --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:bg-green-50 has-[:checked]:border-green-300">
                        <input type="checkbox" name="would_recommend" value="1" class="rounded border-gray-300">
                        <div>
                            <div class="text-sm font-medium text-gray-800">I would recommend this counselor</div>
                            <div class="text-xs text-gray-500">to other students who need help</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-300">
                        <input type="checkbox" name="issue_resolved" value="1" class="rounded border-gray-300">
                        <div>
                            <div class="text-sm font-medium text-gray-800">My concern was addressed</div>
                            <div class="text-xs text-gray-500">I feel I got what I came for</div>
                        </div>
                    </label>
                </div>

                {{-- Open feedback --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">What worked well? <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="what_worked" rows="2" maxlength="1500" placeholder="Anything the counselor did that helped you…"
                              class="w-full border-gray-300 rounded-md text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">What could be improved? <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="what_could_improve" rows="2" maxlength="1500" placeholder="Constructive feedback helps us serve you better…"
                              class="w-full border-gray-300 rounded-md text-sm"></textarea>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-md">
                        Submit Feedback
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
