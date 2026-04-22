<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Submit Referral</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('referrals.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student <span class="text-red-500">*</span></label>
                        <select name="student_profile_id" required
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason Category <span class="text-red-500">*</span></label>
                            <select name="reason_category" required
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">— Select —</option>
                                @foreach(['academic','behavioral','attendance','personal','mental_health','financial','other'] as $cat)
                                    <option value="{{ $cat }}" @selected(old('reason_category') === $cat)>
                                        {{ ucwords(str_replace('_',' ',$cat)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Urgency <span class="text-red-500">*</span></label>
                            <select name="urgency" required
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                @foreach(['low','medium','high','critical'] as $u)
                                    <option value="{{ $u }}" @selected(old('urgency') === $u)>{{ ucfirst($u) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if(auth()->user()->isStaff())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign Counselor <span class="text-gray-400">(optional)</span></label>
                        <select name="assigned_counselor_id"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">— Auto-assign —</option>
                            @foreach($counselors as $c)
                                <option value="{{ $c->id }}" @selected(old('assigned_counselor_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description <span class="text-red-500">*</span>
                            <span class="text-gray-400 font-normal text-xs">(min 20 characters)</span>
                        </label>
                        <textarea name="description" rows="5" required minlength="20" maxlength="3000"
                                  class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Describe the concern in detail...">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                            Submit Referral
                        </button>
                        <a href="{{ route('referrals.index') }}"
                           class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 border border-gray-300 rounded-lg">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
