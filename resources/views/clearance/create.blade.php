<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Clearance Request</h2>
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

                <div class="mb-5 bg-blue-50 border border-blue-100 p-3 rounded-md text-sm text-blue-800">
                    <strong>Note:</strong> Graduation clearance requests require completion of an Exit Survey before processing.
                </div>

                <form method="POST" action="{{ route('clearance.store') }}" class="space-y-5">
                    @csrf

                    @if(auth()->user()->isStaff())
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
                    @else
                    <input type="hidden" name="student_profile_id" value="{{ $profile->id }}">
                    <div class="text-sm text-gray-600">
                        Requesting as: <strong>{{ $profile->full_name }}</strong> ({{ $profile->student_id_number }})
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Clearance Type <span class="text-red-500">*</span></label>
                        <select name="clearance_type" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">— Select —</option>
                            @foreach(['graduation','departmental','scholarship','employment','other'] as $t)
                                <option value="{{ $t }}" @selected(old('clearance_type') === $t)>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year <span class="text-red-500">*</span></label>
                            <input type="text" name="academic_year" value="{{ old('academic_year', date('Y') . '-' . (date('Y') + 1)) }}"
                                   required maxlength="20"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                                   placeholder="2025-2026">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-500">*</span></label>
                            <select name="semester" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                @foreach(['1st','2nd','Summer'] as $sem)
                                    <option value="{{ $sem }}" @selected(old('semester') === $sem)>{{ $sem }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose / Additional Notes</label>
                        <textarea name="purpose" rows="2" maxlength="500"
                                  class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                                  placeholder="e.g. For board exam application...">{{ old('purpose') }}</textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                            Submit Request
                        </button>
                        <a href="{{ route('clearance.index') }}"
                           class="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
