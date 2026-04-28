<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🎯 Career Interest Inventory</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4"
             x-data="{
                 answers: {},
                 total: {{ $questions->count() }},
                 progress() { return Object.keys(this.answers).length; },
                 isComplete() { return this.progress() === this.total; }
             }">

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                For each statement below, choose <strong>Like</strong> if it sounds appealing to you or <strong>Dislike</strong> if it doesn't. There are no right or wrong answers — go with your gut. Takes ~10 minutes.
            </div>

            {{-- Progress --}}
            <div class="bg-white shadow-sm rounded-lg p-4 sticky top-2 z-10">
                <div class="flex items-center justify-between mb-2 text-sm">
                    <span class="text-gray-600">Progress</span>
                    <span class="font-bold text-blue-600"><span x-text="progress()"></span> / <span x-text="total"></span></span>
                </div>
                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-purple-600 rounded-full transition-all"
                         :style="`width: ${(progress() / total) * 100}%`"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('riasec.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf

                {{-- Hidden inputs for answers --}}
                <template x-for="(value, qid) in answers" :key="qid">
                    <input type="hidden" :name="`answers[${qid}]`" :value="value">
                </template>

                @foreach($questions as $i => $q)
                <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0"
                     :class="{ 'opacity-60': answers[{{ $q->id }}] === undefined }">
                    <span class="text-xs font-medium text-gray-400 w-8 flex-shrink-0">{{ $i + 1 }}.</span>
                    <p class="flex-1 text-sm text-gray-800">{{ $q->text }}</p>
                    <div class="flex gap-2 flex-shrink-0">
                        <button type="button" @click="answers[{{ $q->id }}] = 1"
                                :class="answers[{{ $q->id }}] === 1 ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-green-50'"
                                class="px-3 py-1.5 rounded-md text-xs font-medium transition">
                            👍 Like
                        </button>
                        <button type="button" @click="answers[{{ $q->id }}] = 0"
                                :class="answers[{{ $q->id }}] === 0 ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-red-50'"
                                class="px-3 py-1.5 rounded-md text-xs font-medium transition">
                            👎 Dislike
                        </button>
                    </div>
                </div>
                @endforeach

                {{-- Submit --}}
                <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500" x-show="!isComplete()">
                        Please answer all questions before submitting (<span x-text="total - progress()"></span> remaining).
                    </p>
                    <p class="text-xs text-green-600 font-medium" x-show="isComplete()">
                        All questions answered — ready to see your results!
                    </p>
                    <button type="submit" :disabled="!isComplete()"
                            :class="isComplete() ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                            class="text-white text-sm font-medium px-6 py-2.5 rounded-md transition">
                        See My Results →
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
