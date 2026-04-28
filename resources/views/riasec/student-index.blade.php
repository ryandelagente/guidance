<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">🎯 Career Interest Test</h2>
            <a href="{{ route('riasec.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                {{ $responses->isEmpty() ? '✨ Take the Test' : '🔄 Retake Test' }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            @if($responses->isEmpty())
            {{-- ── Intro card ── --}}
            <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-xl text-white p-8 shadow-md">
                <h3 class="font-bold text-2xl mb-3">Discover careers that fit you 🌟</h3>
                <p class="text-blue-100 leading-relaxed mb-4">
                    The <strong>Holland Code (RIASEC)</strong> Career Interest Test helps you understand the kinds of work you naturally enjoy.
                    Answer 60 yes/no questions about activities you'd like or dislike — takes about 10 minutes — and we'll match you with career paths that align with your interests.
                </p>
                <a href="{{ route('riasec.create') }}" class="inline-block bg-white text-indigo-700 hover:bg-blue-50 font-semibold px-6 py-2.5 rounded-md text-sm">Start the Test →</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach(\App\Models\RiasecResponse::TYPE_LABELS as $code => $label)
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-9 h-9 rounded-full {{ \App\Models\RiasecResponse::TYPE_COLORS[$code] }} flex items-center justify-center font-bold text-base">{{ $code }}</span>
                        <h4 class="font-semibold text-gray-800">{{ $label }}</h4>
                    </div>
                    <p class="text-xs text-gray-500 leading-relaxed">{{ \App\Models\RiasecResponse::TYPE_DESCRIPTIONS[$code] }}</p>
                </div>
                @endforeach
            </div>
            @else
            {{-- ── Past results ── --}}
            <div class="bg-white shadow-sm rounded-lg p-5">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Your Past Results</h3>
                <div class="space-y-2">
                    @foreach($responses as $r)
                    <a href="{{ route('riasec.show', $r) }}" class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="flex gap-1">
                                @foreach(str_split($r->top_code) as $code)
                                    <span class="w-7 h-7 rounded-full {{ \App\Models\RiasecResponse::TYPE_COLORS[$code] ?? 'bg-gray-100' }} flex items-center justify-center font-bold text-xs">{{ $code }}</span>
                                @endforeach
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 text-sm">{{ $r->top_code }} — {{ \App\Models\RiasecResponse::TYPE_LABELS[$r->top_code[0]] }} dominant</p>
                                <p class="text-xs text-gray-400">{{ $r->completed_at->format('F d, Y') }} ({{ $r->completed_at->diffForHumans() }})</p>
                            </div>
                        </div>
                        <span class="text-xs text-blue-600 hover:underline">View →</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
