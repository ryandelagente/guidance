<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('riasec.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">🎯 Career Interest Profile</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Hero --}}
            <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 rounded-xl text-white p-8 shadow-md">
                @if(!auth()->user()->isStudent())
                    <p class="text-purple-200 text-sm mb-2">{{ $response->studentProfile?->full_name }}</p>
                @endif
                <p class="text-purple-200 text-xs uppercase tracking-wider mb-2">Your Holland Code</p>
                <div class="flex items-center gap-2 mb-3">
                    @foreach(str_split($response->top_code) as $code)
                        <span class="w-14 h-14 rounded-full bg-white/20 backdrop-blur flex items-center justify-center font-bold text-2xl">{{ $code }}</span>
                    @endforeach
                </div>
                <h3 class="font-bold text-xl">{{ $response->top_code }} — {{ \App\Models\RiasecResponse::TYPE_LABELS[$response->top_code[0]] }} Dominant</h3>
                <p class="text-purple-100 text-sm mt-2">Completed {{ $response->completed_at->format('F d, Y') }}</p>
            </div>

            {{-- Score breakdown --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">Your Score Breakdown</h3>
                <div class="space-y-2.5">
                    @php
                        $maxScore = max(array_values($response->scores_array)) ?: 1;
                        $sorted = collect($response->scores_array)->sortDesc();
                    @endphp
                    @foreach($sorted as $code => $score)
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full {{ \App\Models\RiasecResponse::TYPE_COLORS[$code] }} flex items-center justify-center font-bold text-sm flex-shrink-0">{{ $code }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between text-sm mb-0.5">
                                <span class="font-medium text-gray-800">{{ \App\Models\RiasecResponse::TYPE_LABELS[$code] }}</span>
                                <span class="text-gray-500 text-xs">{{ $score }} / 10</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all"
                                     style="width: {{ $score / 10 * 100 }}%; background: linear-gradient(to right, #6366f1, #a855f7);"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ \App\Models\RiasecResponse::TYPE_DESCRIPTIONS[$code] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Career matches --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-2">Suggested Career Paths</h3>
                <p class="text-xs text-gray-500 mb-4">Based on your top 3 interest codes — talk to your guidance counselor about which feel right for you.</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($response->career_matches as $career)
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-lg p-3 text-sm font-medium text-gray-800 text-center">
                        {{ $career }}
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Interpretation note --}}
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-900">
                <strong>💡 What does this mean?</strong>
                Your <strong>Holland Code</strong> shows the work environments and activities you naturally lean toward. It's not a prediction or a verdict —
                it's a starting point for a conversation with your guidance counselor about education paths, internships, and majors that match your interests.
            </div>

            <div class="flex justify-between gap-2">
                <a href="{{ route('riasec.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to test history</a>
                @if(auth()->user()->isStudent())
                <a href="{{ route('appointments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-md">
                    📅 Discuss with a Counselor
                </a>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
