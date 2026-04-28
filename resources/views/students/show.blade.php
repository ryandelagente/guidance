<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('students.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
                <h2 class="font-semibold text-xl text-gray-800">{{ $student->full_name }}</h2>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('students.timeline', $student) }}"
                   class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2 rounded-lg">📋 Timeline</a>
                <a href="{{ route('students.cumulative-record', $student) }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">📄 Print Record</a>
                <a href="{{ route('students.edit', $student) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-4 py-2 rounded-lg">Edit</a>
                <form method="POST" action="{{ route('students.destroy', $student) }}"
                      onsubmit="return confirm('Delete this student record? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Delete</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Header Card --}}
            <div class="bg-white shadow-sm rounded-lg p-6 flex items-center gap-5">
                <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl font-bold overflow-hidden flex-shrink-0">
                    @if($student->profile_photo)
                        <img src="{{ Storage::url($student->profile_photo) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900">{{ $student->full_name }}</h3>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $student->program }} — {{ $student->college }}</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">{{ $student->student_id_number ?? 'No ID' }}</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $student->year_level }}</span>
                        @php
                            $badge = match($student->academic_status) {
                                'good_standing' => 'bg-green-100 text-green-700',
                                'probation'     => 'bg-yellow-100 text-yellow-700',
                                'at_risk'       => 'bg-orange-100 text-orange-700',
                                'dismissed'     => 'bg-red-100 text-red-700',
                                default         => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $badge }}">
                            {{ str_replace('_', ' ', ucfirst($student->academic_status)) }}
                        </span>
                    </div>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <p class="font-medium text-gray-700">Assigned Counselor</p>
                    <p>{{ $student->assignedCounselor?->name ?? 'Unassigned' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Personal Details --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h4 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">Personal Information</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">Date of Birth</dt><dd class="text-gray-900">{{ $student->date_of_birth?->format('F d, Y') ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Age</dt><dd class="text-gray-900">{{ $student->age ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Sex</dt><dd class="text-gray-900">{{ ucfirst($student->sex ?? '—') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Civil Status</dt><dd class="text-gray-900">{{ $student->civil_status ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Religion</dt><dd class="text-gray-900">{{ $student->religion ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Contact No.</dt><dd class="text-gray-900">{{ $student->contact_number ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">PWD</dt><dd class="text-gray-900">{{ $student->is_pwd ? 'Yes' : 'No' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Working Student</dt><dd class="text-gray-900">{{ $student->is_working_student ? 'Yes' : 'No' }}</dd></div>
                    </dl>
                </div>

                {{-- Family Background --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h4 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">Family Background</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">Father</dt><dd class="text-gray-900">{{ $student->father_name ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Father's Occupation</dt><dd class="text-gray-900">{{ $student->father_occupation ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Mother</dt><dd class="text-gray-900">{{ $student->mother_name ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Mother's Occupation</dt><dd class="text-gray-900">{{ $student->mother_occupation ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Parents' Status</dt><dd class="text-gray-900">{{ str_replace('_',' ', ucfirst($student->parents_status ?? '—')) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Guardian</dt><dd class="text-gray-900">{{ $student->guardian_name ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Monthly Income</dt><dd class="text-gray-900">{{ $student->monthly_family_income ?? '—' }}</dd></div>
                    </dl>
                </div>
            </div>

            {{-- Emergency Contacts --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h4 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wide">Emergency Contacts</h4>

                @if($student->emergencyContacts->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                    @foreach($student->emergencyContacts as $contact)
                    <div class="border border-gray-100 rounded-lg p-3 text-sm flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-gray-900">{{ $contact->name }}</p>
                                @if($contact->is_primary)
                                    <span class="text-xs bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded">Primary</span>
                                @endif
                            </div>
                            <p class="text-gray-500">{{ $contact->relationship }}</p>
                            <p class="text-gray-700 mt-1">{{ $contact->contact_number }}</p>
                        </div>
                        <form method="POST" action="{{ route('students.emergency-contacts.delete', [$student, $contact]) }}"
                              onsubmit="return confirm('Remove this contact?')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:text-red-600 text-xs mt-1">Remove</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-sm text-gray-400 mb-4">No emergency contacts on file.</p>
                @endif

                {{-- Add Contact Form --}}
                <details class="border border-gray-100 rounded-lg">
                    <summary class="px-4 py-2.5 text-sm font-medium text-gray-600 cursor-pointer hover:bg-gray-50 rounded-lg">
                        + Add Emergency Contact
                    </summary>
                    <form method="POST" action="{{ route('students.emergency-contacts.store', $student) }}"
                          class="px-4 pb-4 pt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Name</label>
                            <input type="text" name="name" required class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Relationship</label>
                            <input type="text" name="relationship" required placeholder="e.g. Parent, Sibling"
                                   class="w-full border-gray-300 rounded-md text-sm">
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
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md">
                                Save Contact
                            </button>
                        </div>
                    </form>
                </details>
            </div>

            {{-- Documents --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h4 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wide">Documents</h4>

                @if($student->documents->isNotEmpty())
                <ul class="space-y-2 mb-4">
                    @foreach($student->documents as $doc)
                    <li class="flex items-center justify-between text-sm border border-gray-100 rounded-lg px-3 py-2">
                        <div>
                            <span class="font-medium text-gray-800">{{ $doc->file_name }}</span>
                            <span class="ml-2 text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">{{ $doc->document_type }}</span>
                            <span class="ml-2 text-xs text-gray-400">{{ $doc->file_size ? round($doc->file_size / 1024) . ' KB' : '' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('students.documents.download', [$student, $doc]) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs font-medium">Download</a>
                            <form method="POST" action="{{ route('students.documents.delete', [$student, $doc]) }}"
                                  onsubmit="return confirm('Delete this document?')">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600 text-xs">Delete</button>
                            </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                    <p class="text-sm text-gray-400 mb-4">No documents uploaded yet.</p>
                @endif

                {{-- Upload Form --}}
                <details class="border border-gray-100 rounded-lg">
                    <summary class="px-4 py-2.5 text-sm font-medium text-gray-600 cursor-pointer hover:bg-gray-50 rounded-lg">
                        + Upload Document
                    </summary>
                    <form method="POST" action="{{ route('students.documents.upload', $student) }}"
                          enctype="multipart/form-data"
                          class="px-4 pb-4 pt-3 flex flex-wrap gap-3 items-end">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Document Type</label>
                            <select name="document_type" required class="border-gray-300 rounded-md text-sm">
                                @foreach(['Birth Certificate','Medical Certificate','School Records','Consent Form','Psychological Report','ID / Identification','Other'] as $dtype)
                                    <option value="{{ $dtype }}">{{ $dtype }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">File (PDF, image, doc — max 10 MB)</label>
                            <input type="file" name="document_file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   class="text-sm text-gray-600">
                        </div>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md">
                            Upload
                        </button>
                    </form>
                </details>
            </div>

        </div>
    </div>
</x-app-layout>
