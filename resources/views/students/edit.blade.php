<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('students.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit: {{ $student->full_name }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PATCH')

                {{-- Personal Information --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                   class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('first_name') border-red-400 @enderror">
                            @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                   class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                   class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('last_name') border-red-400 @enderror">
                            @error('last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Suffix</label>
                            <input type="text" name="suffix" value="{{ old('suffix') }}" placeholder="Jr., Sr., III"
                                   class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                   class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
                            <select name="sex" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select...</option>
                                <option value="male"   @selected(old('sex') === 'male')>Male</option>
                                <option value="female" @selected(old('sex') === 'female')>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Civil Status</label>
                            <select name="civil_status" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select...</option>
                                @foreach(['Single','Married','Widowed','Separated'] as $cs)
                                    <option value="{{ $cs }}" @selected(old('civil_status') === $cs)>{{ $cs }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Religion</label>
                            <input type="text" name="religion" value="{{ old('religion') }}"
                                   class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                                   class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Home Address</label>
                            <textarea name="home_address" rows="2"
                                      class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('home_address') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                            <input type="file" name="profile_photo" accept="image/*"
                                   class="w-full text-sm text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700">
                        </div>
                    </div>
                </div>

                {{-- Academic Information --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b">Academic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student ID Number</label>
                            <input type="text" name="student_id_number" value="{{ old('student_id_number') }}"
                                   class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 @error('student_id_number') border-red-400 @enderror">
                            @error('student_id_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">College</label>
                            <input type="text" name="college" value="{{ old('college') }}"
                                   class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Program / Course</label>
                            <input type="text" name="program" value="{{ old('program') }}"
                                   class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year Level</label>
                            <select name="year_level" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select...</option>
                                @foreach(['1st Year','2nd Year','3rd Year','4th Year','5th Year','Graduate'] as $yr)
                                    <option value="{{ $yr }}" @selected(old('year_level') === $yr)>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student Type <span class="text-red-500">*</span></label>
                            <select name="student_type" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="regular"    @selected(old('student_type','regular') === 'regular')>Regular</option>
                                <option value="irregular"  @selected(old('student_type') === 'irregular')>Irregular</option>
                                <option value="transferee" @selected(old('student_type') === 'transferee')>Transferee</option>
                                <option value="returnee"   @selected(old('student_type') === 'returnee')>Returnee</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Academic Status <span class="text-red-500">*</span></label>
                            <select name="academic_status" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="good_standing" @selected(old('academic_status','good_standing') === 'good_standing')>Good Standing</option>
                                <option value="probation"     @selected(old('academic_status') === 'probation')>Probation</option>
                                <option value="at_risk"       @selected(old('academic_status') === 'at_risk')>At Risk</option>
                                <option value="dismissed"     @selected(old('academic_status') === 'dismissed')>Dismissed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Scholarship</label>
                            <input type="text" name="scholarship" value="{{ old('scholarship') }}"
                                   class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Counselor</label>
                            <select name="assigned_counselor_id" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Unassigned</option>
                                @foreach($counselors as $counselor)
                                    <option value="{{ $counselor->id }}" @selected(old('assigned_counselor_id') == $counselor->id)>
                                        {{ $counselor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Family Background --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b">Family Background</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Father's Name</label>
                            <input type="text" name="father_name" value="{{ old('father_name') }}" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Father's Occupation</label>
                            <input type="text" name="father_occupation" value="{{ old('father_occupation') }}" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Father's Contact</label>
                            <input type="text" name="father_contact" value="{{ old('father_contact') }}" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Mother's Name</label>
                            <input type="text" name="mother_name" value="{{ old('mother_name') }}" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Mother's Occupation</label>
                            <input type="text" name="mother_occupation" value="{{ old('mother_occupation') }}" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Mother's Contact</label>
                            <input type="text" name="mother_contact" value="{{ old('mother_contact') }}" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Parents' Status</label>
                            <select name="parents_status" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select...</option>
                                @foreach(['married'=>'Married','separated'=>'Separated','widowed'=>'Widowed','single_parent'=>'Single Parent','deceased'=>'Deceased'] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('parents_status') === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Monthly Family Income</label>
                            <input type="text" name="monthly_family_income" value="{{ old('monthly_family_income') }}" placeholder="e.g. Below ₱10,000" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Guardian's Name</label>
                            <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                            <input type="text" name="guardian_relationship" value="{{ old('guardian_relationship') }}" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Guardian's Contact</label>
                            <input type="text" name="guardian_contact" value="{{ old('guardian_contact') }}" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                    </div>

                    <div class="mt-4 flex gap-6">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_pwd" value="1" @checked(old('is_pwd'))
                                   class="rounded border-gray-300 text-blue-600"> Person with Disability (PWD)
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_working_student" value="1" @checked(old('is_working_student'))
                                   class="rounded border-gray-300 text-blue-600"> Working Student
                        </label>
                    </div>
                </div>

                {{-- Emergency Contacts --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b">Emergency Contacts</h3>
                    <div id="emergency-contacts" class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-3 bg-gray-50 rounded-lg">
                            <div><label class="block text-xs font-medium text-gray-500 mb-1">Full Name</label>
                                <input type="text" name="emergency_contacts[0][name]" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                            <div><label class="block text-xs font-medium text-gray-500 mb-1">Relationship</label>
                                <input type="text" name="emergency_contacts[0][relationship]" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                            <div><label class="block text-xs font-medium text-gray-500 mb-1">Contact Number</label>
                                <input type="text" name="emergency_contacts[0][contact_number]" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        </div>
                    </div>
                    <button type="button" onclick="addEmergencyContact()"
                            class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium">+ Add Another Contact</button>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('students.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-lg">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    let ecIndex = 1;
    function addEmergencyContact() {
        const container = document.getElementById('emergency-contacts');
        const div = document.createElement('div');
        div.className = 'grid grid-cols-1 md:grid-cols-3 gap-3 p-3 bg-gray-50 rounded-lg';
        div.innerHTML = `
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Full Name</label>
                <input type="text" name="emergency_contacts[${ecIndex}][name]" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Relationship</label>
                <input type="text" name="emergency_contacts[${ecIndex}][relationship]" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Contact Number</label>
                <input type="text" name="emergency_contacts[${ecIndex}][contact_number]" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"></div>`;
        container.appendChild(div);
        ecIndex++;
    }
    </script>
</x-app-layout>
