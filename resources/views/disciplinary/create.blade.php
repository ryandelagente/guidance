<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">File Disciplinary Record</h2>
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

                <form method="POST" action="{{ route('disciplinary.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student <span class="text-red-500">*</span></label>
                        <select name="student_profile_id" required
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">— Select student —</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}" @selected(old('student_profile_id') == $s->id)>
                                    {{ $s->full_name }} ({{ $s->student_id_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Offense Type <span class="text-red-500">*</span></label>
                            <select name="offense_type" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">— Select —</option>
                                <option value="minor" @selected(old('offense_type') === 'minor')>Minor</option>
                                <option value="major" @selected(old('offense_type') === 'major')>Major</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                            <select name="offense_category" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">— Select —</option>
                                @foreach(['tardiness','absences','misconduct','cheating','property_damage','harassment','substance','other'] as $cat)
                                    <option value="{{ $cat }}" @selected(old('offense_category') === $cat)>
                                        {{ ucwords(str_replace('_',' ',$cat)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Incident Date <span class="text-red-500">*</span></label>
                            <input type="date" name="incident_date" value="{{ old('incident_date') }}" required
                                   max="{{ now()->toDateString() }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Handled By</label>
                            <select name="handled_by" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">— Unassigned —</option>
                                @foreach($counselors as $c)
                                    <option value="{{ $c->id }}" @selected(old('handled_by') == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="4" required minlength="20" maxlength="3000"
                                  class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                                  placeholder="Describe the incident in detail...">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Action Taken</label>
                        <textarea name="action_taken" rows="2" maxlength="2000"
                                  class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                                  placeholder="Initial action or sanction imposed...">{{ old('action_taken') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sanction</label>
                            <input type="text" name="sanction" value="{{ old('sanction') }}" maxlength="200"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                                   placeholder="e.g. Written Warning, Suspension">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sanction End Date</label>
                            <input type="date" name="sanction_end_date" value="{{ old('sanction_end_date') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                            File Record
                        </button>
                        <a href="{{ route('disciplinary.index') }}"
                           class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 border border-gray-300 rounded-lg">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
