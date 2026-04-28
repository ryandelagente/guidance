<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">💭 My Wellness Check-ins</h2>
            <a href="{{ route('wellness.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New Check-in</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-3">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            @forelse($checkins as $c)
            <div class="bg-white shadow-sm rounded-lg p-5">
                <div class="flex items-start gap-4">
                    <div class="text-4xl">{{ \App\Models\WellnessCheckin::moodEmoji($c->mood) }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="font-medium text-gray-900">{{ \App\Models\WellnessCheckin::moodLabel($c->mood) }}</span>
                            <span class="text-xs text-gray-400">{{ $c->created_at->format('F d, Y') }} • {{ $c->created_at->diffForHumans() }}</span>
                            @if($c->wants_counselor)
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Counselor requested</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-xs text-gray-500 mt-2">
                            <div>Stress: <span class="font-medium text-gray-700">{{ \App\Models\WellnessCheckin::intensityLabel($c->stress_level) }}</span></div>
                            <div>Sleep: <span class="font-medium text-gray-700">{{ ['Poor','Fair','Okay','Good','Excellent'][$c->sleep_quality - 1] }}</span></div>
                            <div>Academic: <span class="font-medium text-gray-700">{{ \App\Models\WellnessCheckin::intensityLabel($c->academic_stress) }}</span></div>
                        </div>
                        @if($c->notes)
                            <p class="text-sm text-gray-600 mt-2 italic">"{{ $c->notes }}"</p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white shadow-sm rounded-lg p-12 text-center">
                <div class="text-5xl mb-3">💭</div>
                <p class="text-gray-500 text-sm mb-4">You haven't done a wellness check-in yet.</p>
                <a href="{{ route('wellness.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">Start Your First Check-in</a>
            </div>
            @endforelse

            @if($checkins->hasPages())
                <div class="bg-white px-4 py-3 rounded-lg shadow-sm">{{ $checkins->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
