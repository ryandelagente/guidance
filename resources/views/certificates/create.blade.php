<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Issue Good Moral Certificate</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                @if($preselect)
                <div class="mb-5 bg-green-50 border border-green-100 p-3 rounded-md text-sm text-green-800">
                    Issuing for approved clearance request of <strong>{{ $preselect->studentProfile->full_name ?? '—' }}</strong>
                </div>
                @endif

                <form method="POST" action="{{ route('certificates.store') }}" class="space-y-5">
                    @csrf

                    @if($preselect)
                        <input type="hidden" name="student_profile_id" value="{{ $preselect->student_profile_id }}">
                        <input type="hidden" name="clearance_request_id" value="{{ $preselect->id }}">
                    @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student <span class="text-red-500">*</span></label>
                        <select name="student_profile_id" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">— Select student —</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}" @selected(old('student_profile_id') == $s->id)>
                                    {{ $s->full_name }} ({{ $s->student_id_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose <span class="text-red-500">*</span></label>
                        <input type="text" name="purpose" value="{{ old('purpose', $preselect?->purpose) }}"
                               required maxlength="500"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                               placeholder="e.g. Employment, Board Exam Application, Transfer...">
                    </div>

                    <div class="w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Validity (months) <span class="text-red-500">*</span></label>
                        <input type="number" name="validity_months" value="{{ old('validity_months', 6) }}"
                               required min="1" max="24"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                            Issue Certificate
                        </button>
                        <a href="{{ route('certificates.index') }}"
                           class="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
