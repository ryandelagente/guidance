<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clearance Request</h2>
            <a href="{{ route('clearance.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if(session('info'))
                <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-sm">{{ session('info') }}</div>
            @endif

            {{-- Request Details --}}
            <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $clearance->studentProfile->full_name ?? '—' }}</h3>
                        <p class="text-sm text-gray-500">{{ $clearance->studentProfile->student_id_number ?? '' }} &bull; {{ $clearance->studentProfile->college ?? '' }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $clearance->getStatusBadgeClass() }}">
                        {{ ucwords(str_replace('_',' ',$clearance->status)) }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div><span class="font-medium text-gray-500">Type</span><p class="mt-0.5 text-gray-800">{{ ucfirst($clearance->clearance_type) }}</p></div>
                    <div><span class="font-medium text-gray-500">Academic Year</span><p class="mt-0.5 text-gray-800">{{ $clearance->academic_year }}</p></div>
                    <div><span class="font-medium text-gray-500">Semester</span><p class="mt-0.5 text-gray-800">{{ $clearance->semester }}</p></div>
                    @if($clearance->purpose)
                    <div class="col-span-3"><span class="font-medium text-gray-500">Purpose</span><p class="mt-0.5 text-gray-800">{{ $clearance->purpose }}</p></div>
                    @endif
                    @if($clearance->processedBy)
                    <div><span class="font-medium text-gray-500">Processed By</span><p class="mt-0.5 text-gray-800">{{ $clearance->processedBy->name }}</p></div>
                    <div><span class="font-medium text-gray-500">Processed At</span><p class="mt-0.5 text-gray-800">{{ $clearance->processed_at?->format('M d, Y') ?? '—' }}</p></div>
                    @endif
                </div>

                @if($clearance->notes)
                <div class="text-sm bg-yellow-50 border border-yellow-100 p-3 rounded-md">
                    <span class="font-medium text-yellow-800">Notes from Counselor:</span>
                    <p class="text-yellow-900 mt-1">{{ $clearance->notes }}</p>
                </div>
                @endif

                {{-- Action buttons --}}
                <div class="flex flex-wrap gap-3 pt-2">
                    {{-- Student: go to exit survey --}}
                    @if($clearance->status === 'for_exit_survey' && (auth()->user()->isStudent() || auth()->user()->isStaff()))
                    <a href="{{ route('exit-survey.show', $clearance) }}"
                       class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                        Complete Exit Survey
                    </a>
                    @endif

                    {{-- Counselor: issue certificate when approved --}}
                    @if(auth()->user()->isStaff() && $clearance->status === 'approved' && !$clearance->certificate)
                    <a href="{{ route('certificates.create', ['clearance_id' => $clearance->id]) }}"
                       class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                        Issue Certificate
                    </a>
                    @endif

                    {{-- Already issued --}}
                    @if($clearance->certificate)
                    <a href="{{ route('certificates.show', $clearance->certificate) }}"
                       class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-md">
                        View Certificate
                    </a>
                    @endif
                </div>
            </div>

            {{-- Counselor status update --}}
            @if(auth()->user()->isStaff())
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Update Status</h3>
                <form method="POST" action="{{ route('clearance.update', $clearance) }}" class="flex flex-wrap gap-3 items-end">
                    @csrf @method('PATCH')
                    <div class="w-48">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                            @foreach(['pending','for_exit_survey','survey_done','approved','rejected','on_hold'] as $s)
                                <option value="{{ $s }}" @selected($clearance->status === $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Notes</label>
                        <input type="text" name="notes" value="{{ $clearance->notes }}" maxlength="1000"
                               class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Update</button>
                </form>
            </div>
            @endif

            {{-- Exit survey responses (visible to staff) --}}
            @if(auth()->user()->isStaff() && $clearance->surveyResponses->count())
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Exit Survey Responses</h3>
                <div class="space-y-4">
                    @foreach($clearance->surveyResponses as $resp)
                    <div class="border-b pb-3 last:border-0">
                        <p class="text-sm font-medium text-gray-700">{{ $resp->question->question_text ?? '—' }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $resp->response ?? '—' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
