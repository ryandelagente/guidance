<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $label }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4"
             x-data="{ answers: {}, total: {{ count($questions) }}, progress() { return Object.keys(this.answers).length; }, complete() { return this.progress() === this.total; } }">

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-900">
                <strong>Over the last 2 weeks</strong>, how often have you been bothered by the following problems? Pick the option that best fits.
            </div>

            <div class="bg-white shadow-sm rounded-lg p-4 sticky top-2 z-10">
                <div class="flex items-center justify-between mb-2 text-sm">
                    <span class="text-gray-600">Progress</span>
                    <span class="font-bold text-blue-600"><span x-text="progress()"></span> / {{ count($questions) }}</span>
                </div>
                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full transition-all"
                         :style="`width: ${(progress() / total) * 100}%`"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('screening.store', $instrument) }}" class="bg-white shadow-sm rounded-lg p-6 space-y-5">
                @csrf
                <template x-for="(value, qid) in answers" :key="qid">
                    <input type="hidden" :name="`answers[${qid}]`" :value="value">
                </template>

                @foreach($questions as $i => $q)
                <div class="pb-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <p class="text-sm text-gray-800 mb-3">
                        <span class="text-xs text-gray-400 font-medium mr-1">{{ $i + 1 }}.</span>
                        {{ $q }}
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-{{ count($options) }} gap-2">
                        @foreach($options as $optValue => $optLabel)
                        <button type="button" @click="answers[{{ $i }}] = {{ $optValue }}"
                                :class="answers[{{ $i }}] === {{ $optValue }} ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-blue-50'"
                                class="border-2 rounded-md py-2 px-2 text-xs font-medium transition text-center">
                            <div class="font-bold">{{ $optValue }}</div>
                            <div>{{ $optLabel }}</div>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500" x-show="!complete()">Please answer all questions before submitting.</p>
                    <p class="text-xs text-green-600 font-medium" x-show="complete()">All answered — ready to see results.</p>
                    <button type="submit" :disabled="!complete()"
                            :class="complete() ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                            class="text-white text-sm font-medium px-6 py-2.5 rounded-md transition">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
