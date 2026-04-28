<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">💭 Daily Wellness Check-in</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if($today)
                <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-sm">
                    You already checked in today. Come back tomorrow for your next check-in.
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                    <p class="text-gray-700 mb-3">Today's mood: <span class="text-3xl">{{ \App\Models\WellnessCheckin::moodEmoji($today->mood) }}</span></p>
                    <a href="{{ route('wellness.index') }}" class="inline-block text-blue-600 hover:underline text-sm">View my check-in history</a>
                </div>
            @else

            <div class="bg-white shadow-sm rounded-lg p-6">
                <p class="text-gray-700 text-sm mb-1">Hi <strong>{{ $profile->first_name }}</strong> 👋</p>
                <p class="text-gray-500 text-sm">How are you doing today? Your responses are confidential and only seen by Guidance Counselors. Takes 30 seconds.</p>
            </div>

            <form method="POST" action="{{ route('wellness.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-6"
                  x-data="{ mood: 3, stress: 3, sleep: 3, academic: 3 }">
                @csrf

                {{-- Mood --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">How is your mood today?</label>
                    <div class="grid grid-cols-5 gap-2">
                        @foreach([1=>'😢',2=>'😟',3=>'😐',4=>'🙂',5=>'😄'] as $v=>$emoji)
                            <label class="cursor-pointer">
                                <input type="radio" name="mood" value="{{ $v }}" x-model.number="mood" class="sr-only" {{ $v == 3 ? 'checked' : '' }}>
                                <div :class="mood == {{ $v }} ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-200' : 'border-gray-200 hover:bg-gray-50'"
                                     class="border-2 rounded-lg py-3 text-center transition">
                                    <div class="text-3xl">{{ $emoji }}</div>
                                    <div class="text-xs text-gray-600 mt-1">{{ \App\Models\WellnessCheckin::moodLabel($v) }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Stress --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Overall stress level</label>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">None</span>
                        <input type="range" name="stress_level" min="1" max="5" step="1" x-model.number="stress" class="flex-1">
                        <span class="text-xs text-gray-400">Severe</span>
                        <span class="w-24 text-center text-sm font-medium text-gray-700" x-text="['None','Mild','Moderate','High','Severe'][stress-1]"></span>
                    </div>
                </div>

                {{-- Sleep --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Last night's sleep quality</label>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">Poor</span>
                        <input type="range" name="sleep_quality" min="1" max="5" step="1" x-model.number="sleep" class="flex-1">
                        <span class="text-xs text-gray-400">Excellent</span>
                        <span class="w-24 text-center text-sm font-medium text-gray-700" x-text="['Poor','Fair','Okay','Good','Excellent'][sleep-1]"></span>
                    </div>
                </div>

                {{-- Academic --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">How stressed are you about academics?</label>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">None</span>
                        <input type="range" name="academic_stress" min="1" max="5" step="1" x-model.number="academic" class="flex-1">
                        <span class="text-xs text-gray-400">Severe</span>
                        <span class="w-24 text-center text-sm font-medium text-gray-700" x-text="['None','Mild','Moderate','High','Severe'][academic-1]"></span>
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Anything else on your mind? <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="notes" rows="3" maxlength="1000" placeholder="It's okay to share — your counselor is here to help."
                              class="w-full border-gray-300 rounded-md text-sm"></textarea>
                </div>

                {{-- Want a counselor? --}}
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="wants_counselor" value="1" class="mt-0.5 rounded border-gray-300">
                        <div>
                            <span class="text-sm font-medium text-amber-900">I'd like to speak with a counselor</span>
                            <p class="text-xs text-amber-700 mt-1">A guidance counselor will reach out to schedule a session within 1–2 working days.</p>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-md">
                        Submit Check-in
                    </button>
                </div>
            </form>
            @endif

        </div>
    </div>
</x-app-layout>
