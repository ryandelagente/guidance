<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('screening.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $screening->instrument_label }} Result</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                <p class="text-xs uppercase tracking-wider text-gray-500 mb-2">Total Score</p>
                <div class="text-5xl font-bold text-gray-800 mb-2">{{ $screening->total_score }}</div>
                <span class="inline-block text-sm px-3 py-1 rounded-full font-medium {{ $screening->getSeverityBadgeClass() }}">
                    {{ ucwords(str_replace('_', ' ', $screening->severity)) }}
                </span>
                @if($screening->positive_self_harm)
                <div class="mt-4 bg-red-50 border-2 border-red-200 rounded-lg p-3 text-sm text-red-800 text-left">
                    🚨 <strong>Important:</strong> Your answer to question 9 indicates you may be having difficult thoughts. Please reach out to your counselor or call NCMH <a href="tel:1553" class="underline">1553</a> right away. You're not alone.
                </div>
                @endif
            </div>

            @if(!auth()->user()->isStudent())
            <div class="bg-white shadow-sm rounded-lg p-5">
                <p class="text-xs text-gray-500 uppercase">Student</p>
                <a href="{{ route('students.show', $screening->studentProfile) }}" class="font-medium text-gray-800 hover:text-blue-600">{{ $screening->studentProfile->full_name }}</a>
                <p class="text-xs text-gray-400 mt-1">{{ $screening->studentProfile->student_id_number ?? '—' }}</p>
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">Answer Breakdown</h3>
                <div class="space-y-2">
                    @foreach($questions as $i => $q)
                    @php
                        $ans = $screening->answers[$i] ?? null;
                        $highlight = ($screening->instrument === 'phq9' && $i === 8 && (int)$ans > 0)
                                  || ((int)$ans >= ($screening->instrument === 'k10' ? 4 : 2));
                    @endphp
                    <div class="flex items-start gap-3 p-2 {{ $highlight ? 'bg-red-50 rounded' : '' }}">
                        <span class="text-xs text-gray-400 font-medium flex-shrink-0 w-6">{{ $i + 1 }}.</span>
                        <p class="text-sm text-gray-700 flex-1">{{ $q }}</p>
                        <span class="text-xs font-medium text-gray-600 flex-shrink-0">{{ $options[$ans] ?? '—' }} ({{ $ans }})</span>
                    </div>
                    @endforeach
                </div>
            </div>

            @if(!auth()->user()->isStudent())
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">Counselor Review</h3>
                @if($screening->reviewed)
                    <p class="text-xs text-green-600">✓ Reviewed by {{ $screening->reviewer?->name }} on {{ $screening->reviewed_at?->format('M d, Y') }}</p>
                    @if($screening->counselor_notes)
                        <p class="text-sm text-gray-700 bg-gray-50 rounded p-3 mt-2 whitespace-pre-line">{{ $screening->counselor_notes }}</p>
                    @endif
                @else
                <form method="POST" action="{{ route('screening.review', $screening) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Internal Notes</label>
                        <textarea name="counselor_notes" rows="3" maxlength="2000" class="w-full border-gray-300 rounded-md text-sm" placeholder="Action taken, follow-up plan, etc."></textarea>
                    </div>
                    <button class="bg-green-600 hover:bg-green-700 text-white text-sm px-5 py-2 rounded-md">Mark Reviewed</button>
                </form>
                @endif
            </div>
            @endif

            <p class="text-xs text-gray-400 italic text-center">
                Screening tools are not diagnostic. Talk to a qualified mental-health professional for clinical assessment.
            </p>
        </div>
    </div>
</x-app-layout>
