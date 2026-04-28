<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('session-feedback.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Feedback Details</h2>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center mb-6">
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Overall</div>
                        <div class="text-2xl font-bold text-yellow-500 mt-1">{{ str_repeat('★', $feedback->overall_rating) }}{{ str_repeat('☆', 5 - $feedback->overall_rating) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Helpful</div>
                        <div class="text-2xl font-bold text-blue-500 mt-1">{{ $feedback->helpful_score }}/5</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Listened</div>
                        <div class="text-2xl font-bold text-purple-500 mt-1">{{ $feedback->listened_score }}/5</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Comfort</div>
                        <div class="text-2xl font-bold text-pink-500 mt-1">{{ $feedback->comfort_score }}/5</div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded {{ $feedback->would_recommend ? 'bg-green-500' : 'bg-gray-200' }} flex items-center justify-center text-white text-xs">{{ $feedback->would_recommend ? '✓' : '✗' }}</span>
                        Would recommend counselor
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded {{ $feedback->issue_resolved ? 'bg-blue-500' : 'bg-gray-200' }} flex items-center justify-center text-white text-xs">{{ $feedback->issue_resolved ? '✓' : '✗' }}</span>
                        Concern was addressed
                    </div>
                </div>
            </div>

            @if($feedback->what_worked || $feedback->what_could_improve)
            <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @if($feedback->what_worked)
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase mb-2">What worked well</h4>
                        <p class="text-sm text-gray-700 bg-green-50 border border-green-100 rounded-lg p-3 italic">"{{ $feedback->what_worked }}"</p>
                    </div>
                @endif
                @if($feedback->what_could_improve)
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase mb-2">What could improve</h4>
                        <p class="text-sm text-gray-700 bg-yellow-50 border border-yellow-100 rounded-lg p-3 italic">"{{ $feedback->what_could_improve }}"</p>
                    </div>
                @endif
            </div>
            @endif

            <div class="bg-gray-50 rounded-lg p-4 text-xs text-gray-500">
                Session with <strong>{{ $feedback->session?->counselor?->name }}</strong> on {{ $feedback->session?->created_at?->format('F d, Y') }}<br>
                Feedback submitted {{ $feedback->created_at->format('F d, Y h:i A') }}
            </div>

        </div>
    </div>
</x-app-layout>
