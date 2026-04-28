<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">👤 My Profile</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm text-blue-900">
                You can update your contact info, address, family background, and emergency contacts here.
                Changes you make are visible to your assigned counselor.
                If you need to update your name, program, or year level, please <a href="{{ route('appointments.create') }}" class="font-semibold underline">visit the Guidance Office</a> or message your counselor.
            </div>

            {{-- Read-only summary --}}
            <div class="bg-white shadow-sm rounded-lg p-5 flex items-center gap-4">
                <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl font-bold overflow-hidden flex-shrink-0">
                    @if($profile->profile_photo)
                        <img src="{{ Storage::url($profile->profile_photo) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($profile->first_name, 0, 1) . substr($profile->last_name, 0, 1)) }}
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-900 text-lg">{{ $profile->full_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $profile->student_id_number ?? 'No ID' }} • {{ $profile->program ?? '—' }} • {{ $profile->year_level ?? '' }}</p>
                </div>
                <div class="text-right text-xs text-gray-400">
                    Assigned counselor:<br>
                    <span class="font-medium text-gray-600">{{ $profile->assignedCounselor?->name ?? 'Unassigned' }}</span>
                </div>
            </div>

            {{-- Edit form --}}
            <form method="POST" action="{{ route('my-profile.update') }}" enctype="multipart/form-data" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf @method('PUT')

                <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wide pb-2 border-b border-gray-100">Contact & Personal</h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <input type="text" name="contact_number" value="{{ old('contact_number', $profile->contact_number) }}"
                               maxlength="30" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Civil Status</label>
                        <select name="civil_status" class="w-full border-gray-300 rounded-md text-sm">
                            <option value="">—</option>
                            @foreach(['Single','Married','Separated','Widowed','Common-law'] as $opt)
                                <option value="{{ $opt }}" @selected(old('civil_status', $profile->civil_status) === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Religion</label>
                        <input type="text" name="religion" value="{{ old('religion', $profile->religion) }}"
                               maxlength="100" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo (≤ 5MB)</label>
                        <input type="file" name="profile_photo" accept="image/*"
                               class="w-full text-sm text-gray-600">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Home Address</label>
                        <textarea name="home_address" rows="2" maxlength="500"
                                  class="w-full border-gray-300 rounded-md text-sm">{{ old('home_address', $profile->home_address) }}</textarea>
                    </div>
                </div>

                <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wide pb-2 border-b border-gray-100 pt-4">Family Background</h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Father's Name</label>
                        <input type="text" name="father_name" value="{{ old('father_name', $profile->father_name) }}" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Father's Occupation</label>
                        <input type="text" name="father_occupation" value="{{ old('father_occupation', $profile->father_occupation) }}" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Father's Contact</label>
                        <input type="text" name="father_contact" value="{{ old('father_contact', $profile->father_contact) }}" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mother's Name</label>
                        <input type="text" name="mother_name" value="{{ old('mother_name', $profile->mother_name) }}" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mother's Occupation</label>
                        <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $profile->mother_occupation) }}" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mother's Contact</label>
                        <input type="text" name="mother_contact" value="{{ old('mother_contact', $profile->mother_contact) }}" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian's Name</label>
                        <input type="text" name="guardian_name" value="{{ old('guardian_name', $profile->guardian_name) }}" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian's Relationship</label>
                        <input type="text" name="guardian_relationship" value="{{ old('guardian_relationship', $profile->guardian_relationship) }}" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian's Contact</label>
                        <input type="text" name="guardian_contact" value="{{ old('guardian_contact', $profile->guardian_contact) }}" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-md">Save Changes</button>
                </div>
            </form>

            {{-- Emergency contacts --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">Emergency Contacts</h4>

                @if($profile->emergencyContacts->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                    @foreach($profile->emergencyContacts as $contact)
                    <div class="border border-gray-100 rounded-lg p-3 text-sm flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-gray-900">{{ $contact->name }}</p>
                                @if($contact->is_primary)
                                    <span class="text-xs bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded">Primary</span>
                                @endif
                            </div>
                            <p class="text-gray-500 text-xs">{{ $contact->relationship }}</p>
                            <p class="text-gray-700 mt-1 text-xs">{{ $contact->contact_number }}</p>
                        </div>
                        <form method="POST" action="{{ route('my-profile.contacts.delete', $contact) }}"
                              onsubmit="return confirm('Remove this contact?')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:text-red-600 text-xs">Remove</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-sm text-gray-400 mb-4">No emergency contacts on file.</p>
                @endif

                <details class="border border-gray-100 rounded-lg">
                    <summary class="px-4 py-2.5 text-sm font-medium text-gray-600 cursor-pointer hover:bg-gray-50 rounded-lg">+ Add Emergency Contact</summary>
                    <form method="POST" action="{{ route('my-profile.contacts.store') }}"
                          class="px-4 pb-4 pt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Name</label>
                            <input type="text" name="name" required class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Relationship</label>
                            <input type="text" name="relationship" required placeholder="e.g. Parent, Sibling, Guardian" class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Contact Number</label>
                            <input type="text" name="contact_number" required class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Address (optional)</label>
                            <input type="text" name="address" class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md">Save Contact</button>
                        </div>
                    </form>
                </details>
            </div>

        </div>
    </div>
</x-app-layout>
