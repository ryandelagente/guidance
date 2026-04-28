<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📋 Mental Health Screening</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 text-sm text-blue-900">
                <h3 class="font-semibold mb-2">About these screenings</h3>
                <p class="leading-relaxed">
                    These are standardized, validated mental-health questionnaires used in clinics worldwide. They take 1-2 minutes and help your counselor understand how you're doing right now.
                    <strong>Your responses are confidential</strong> and only seen by your assigned counselor and the Guidance Director. If your scores indicate immediate concern, your counselor will reach out.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('screening.start', 'phq9') }}" class="bg-white shadow-sm rounded-lg p-5 hover:shadow-md transition group">
                    <div class="text-3xl mb-2">😔</div>
                    <h3 class="font-bold text-gray-900 group-hover:text-blue-600">PHQ-9</h3>
                    <p class="text-xs text-gray-500 mt-1">Depression screening</p>
                    <p class="text-xs text-gray-400 mt-3">9 questions • 2 minutes</p>
                </a>
                <a href="{{ route('screening.start', 'gad7') }}" class="bg-white shadow-sm rounded-lg p-5 hover:shadow-md transition group">
                    <div class="text-3xl mb-2">😰</div>
                    <h3 class="font-bold text-gray-900 group-hover:text-blue-600">GAD-7</h3>
                    <p class="text-xs text-gray-500 mt-1">Anxiety screening</p>
                    <p class="text-xs text-gray-400 mt-3">7 questions • 1 minute</p>
                </a>
                <a href="{{ route('screening.start', 'k10') }}" class="bg-white shadow-sm rounded-lg p-5 hover:shadow-md transition group">
                    <div class="text-3xl mb-2">😟</div>
                    <h3 class="font-bold text-gray-900 group-hover:text-blue-600">K-10</h3>
                    <p class="text-xs text-gray-500 mt-1">Psychological distress</p>
                    <p class="text-xs text-gray-400 mt-3">10 questions • 2 minutes</p>
                </a>
            </div>

            @if($responses->isNotEmpty())
            <div class="bg-white shadow-sm rounded-lg p-5">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">My Past Screenings</h3>
                <div class="space-y-2">
                    @foreach($responses as $r)
                    <a href="{{ route('screening.show', $r) }}" class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">{{ $r->instrument_label }}</p>
                            <p class="text-xs text-gray-400">{{ $r->created_at->format('M d, Y') }} • Score {{ $r->total_score }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $r->getSeverityBadgeClass() }}">{{ ucwords(str_replace('_', ' ', $r->severity)) }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-900">
                <strong>🚨 If you're in crisis right now:</strong> NCMH 1553 (toll-free, 24/7), Hopeline 0917-558-4673, or call <a href="tel:911" class="font-semibold underline">911</a>. Don't wait.
            </div>
        </div>
    </div>
</x-app-layout>
