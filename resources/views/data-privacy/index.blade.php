<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🔏 My Data & Privacy</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Hero --}}
            <div class="bg-gradient-to-br from-indigo-700 to-purple-700 rounded-xl text-white p-7 shadow-md">
                <h1 class="text-xl font-bold mb-2">Your data, your rights 🔒</h1>
                <p class="text-indigo-100 text-sm leading-relaxed">
                    Under the <strong>Philippine Data Privacy Act (RA 10173)</strong>, you have the right to know what personal data CHMSU holds about you,
                    to see who has accessed it, to request a copy in machine-readable format, and to ask for corrections to incorrect data.
                </p>
            </div>

            {{-- Data summary --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">What we hold about you</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $summary['appointments'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Appointments</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $summary['sessions'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Counseling Notes</div>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $summary['referrals'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Referrals</div>
                    </div>
                    <div class="bg-pink-50 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-pink-600">{{ $summary['wellness_checkins'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Check-ins</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-gray-700">{{ $summary['documents'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Documents</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-gray-700">{{ $summary['emergency_contacts'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Contacts</div>
                    </div>
                    <div class="bg-emerald-50 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-emerald-600">{{ $summary['certificates'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Certificates</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-yellow-600">1</div>
                        <div class="text-xs text-gray-600 mt-1">Profile Record</div>
                    </div>
                </div>
            </div>

            {{-- Download my data --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-2">📥 Download a copy of my data</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    Under the right to <strong>data portability</strong>, you can download all your personal information held in this system as a single JSON file.
                    Note that the body of confidential counseling case notes is excluded — those require an in-person request to the Guidance Office.
                </p>
                <a href="{{ route('data-privacy.download') }}"
                   class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm px-5 py-2.5 rounded-md">
                    Download My Data (JSON)
                </a>
            </div>

            {{-- Request correction --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-2">✏️ Request a correction</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    If any of your information is incorrect (name spelling, date of birth, contact info, etc.), submit a correction request below.
                    Your counselor will review and respond within 7 working days. The request is also recorded in your access trail.
                </p>
                <details class="border border-gray-100 rounded-lg">
                    <summary class="px-4 py-2.5 text-sm font-medium text-gray-600 cursor-pointer hover:bg-gray-50 rounded-lg">
                        Submit a correction request
                    </summary>
                    <form method="POST" action="{{ route('data-privacy.correction') }}" class="px-4 pb-4 pt-3 space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Field that needs correcting <span class="text-red-500">*</span></label>
                            <input type="text" name="field" required maxlength="100"
                                   placeholder="e.g. Date of birth, Last name, Contact number"
                                   class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Current value (what's wrong)</label>
                            <input type="text" name="current" maxlength="500"
                                   class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Correct value <span class="text-red-500">*</span></label>
                            <input type="text" name="desired" required maxlength="500"
                                   class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Reason / supporting evidence (optional)</label>
                            <textarea name="reason" rows="3" maxlength="1000"
                                      placeholder="Anything that helps your counselor verify the correction (e.g. 'My birth certificate shows…')."
                                      class="w-full border-gray-300 rounded-md text-sm"></textarea>
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-5 py-2 rounded-md">Submit Request</button>
                    </form>
                </details>
            </div>

            {{-- Access log --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-2">👁️ Who has accessed my record</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    Last 30 actions involving your data — recorded automatically.
                    Each entry shows who, what, and when.
                </p>

                <div class="space-y-1 max-h-96 overflow-y-auto">
                    @forelse($recentAccesses as $log)
                    <div class="flex items-start gap-3 p-2.5 border-b border-gray-50 hover:bg-gray-50 rounded">
                        <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $log->getActionBadgeClass() }} flex-shrink-0">
                            {{ ucwords(str_replace('_', ' ', $log->action)) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800">{{ $log->description ?? '—' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $log->user?->name ?? 'System' }} • {{ $log->created_at->format('M d, Y h:i A') }}
                                ({{ $log->created_at->diffForHumans() }})
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">No recorded access yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Legal notice --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-xs text-gray-600 leading-relaxed">
                <h4 class="font-semibold text-gray-800 text-sm mb-2">📜 Your Data Privacy Rights (RA 10173)</h4>
                <ul class="space-y-1 list-disc list-inside">
                    <li><strong>Right to be informed</strong> — what data we collect and why</li>
                    <li><strong>Right to access</strong> — see what we have about you (this page)</li>
                    <li><strong>Right to data portability</strong> — download a machine-readable copy (above)</li>
                    <li><strong>Right to rectification</strong> — request corrections to incorrect data (above)</li>
                    <li><strong>Right to erasure</strong> — request deletion (subject to legal retention requirements; contact CHMSU's Data Protection Officer)</li>
                    <li><strong>Right to object</strong> — to specific data processing</li>
                    <li><strong>Right to file a complaint</strong> — with the National Privacy Commission (NPC) at <a href="https://privacy.gov.ph" target="_blank" class="text-blue-600 hover:underline">privacy.gov.ph</a></li>
                </ul>
                <p class="mt-3 text-xs text-gray-500">
                    Questions about your data? Contact CHMSU's Data Protection Officer via the Guidance Office at
                    <a href="tel:0344600511" class="text-blue-600 hover:underline">(034) 460-0511</a>.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
