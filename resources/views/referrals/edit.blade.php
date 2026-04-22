<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Referral</h2>
            <a href="{{ route('referrals.show', $referral) }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        </div>
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

                <form method="POST" action="{{ route('referrals.update', $referral) }}" class="space-y-5">
                    @csrf @method('PATCH')

                    @if(auth()->user()->isFaculty())
                    {{-- Faculty: urgency + description only --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urgency</label>
                        <select name="urgency" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            @foreach(['low','medium','high','critical'] as $u)
                                <option value="{{ $u }}" @selected(old('urgency',$referral->urgency) === $u)>{{ ucfirst($u) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="6" required minlength="20" maxlength="3000"
                                  class="w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('description', $referral->description) }}</textarea>
                    </div>

                    @else
                    {{-- Counselor / Admin --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign Counselor</label>
                        <select name="assigned_counselor_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">— Unassigned —</option>
                            @foreach($counselors as $c)
                                <option value="{{ $c->id }}" @selected(old('assigned_counselor_id',$referral->assigned_counselor_id) == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Feedback to Faculty</label>
                        <textarea name="faculty_feedback" rows="3" maxlength="1000"
                                  class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                                  placeholder="Brief update visible to the referring faculty...">{{ old('faculty_feedback', $referral->faculty_feedback) }}</textarea>
                    </div>
                    @endif

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                            Save Changes
                        </button>
                        <a href="{{ route('referrals.show', $referral) }}"
                           class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 border border-gray-300 rounded-lg">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
