<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Certificate Details</h2>
            <div class="flex gap-3">
                @if(!$certificate->is_revoked)
                <a href="{{ route('certificates.download', $certificate) }}"
                   class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                    ⬇ Download PDF
                </a>
                <a href="{{ route('certificates.print', $certificate) }}" target="_blank"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                    🖨 Print
                </a>
                @endif
                <a href="{{ route('certificates.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            @if($certificate->is_revoked)
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                <strong>This certificate has been revoked.</strong> Reason: {{ $certificate->revoked_reason }}
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6 space-y-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-mono font-medium text-gray-400">{{ $certificate->certificate_number }}</p>
                        <h3 class="text-lg font-semibold text-gray-900 mt-1">{{ $certificate->studentProfile->full_name ?? '—' }}</h3>
                        <p class="text-sm text-gray-500">
                            {{ $certificate->studentProfile->student_id_number ?? '' }} &bull;
                            {{ $certificate->studentProfile->program ?? '' }} &bull;
                            {{ $certificate->studentProfile->college ?? '' }}
                        </p>
                    </div>
                    @if(!$certificate->is_revoked)
                        @if($certificate->isExpired())
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500 font-medium">Expired</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-medium">Valid</span>
                        @endif
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-medium">Revoked</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium text-gray-500">Purpose</span><p class="mt-0.5 text-gray-800">{{ $certificate->purpose }}</p></div>
                    <div><span class="font-medium text-gray-500">Issued By</span><p class="mt-0.5 text-gray-800">{{ $certificate->issuedBy->name ?? '—' }}</p></div>
                    <div><span class="font-medium text-gray-500">Issued Date</span><p class="mt-0.5 text-gray-800">{{ $certificate->issued_at->format('F d, Y') }}</p></div>
                    <div><span class="font-medium text-gray-500">Valid Until</span>
                        <p class="mt-0.5 {{ $certificate->isExpired() ? 'text-red-600 font-medium' : 'text-gray-800' }}">
                            {{ $certificate->expiresAt()->format('F d, Y') }}
                        </p>
                    </div>
                </div>

                @if(auth()->user()->isStaff() && !$certificate->is_revoked)
                <div class="pt-4 border-t">
                    <details class="text-sm">
                        <summary class="cursor-pointer text-red-600 hover:text-red-800 font-medium">Revoke Certificate</summary>
                        <form method="POST" action="{{ route('certificates.revoke', $certificate) }}" class="mt-3 flex gap-3 items-end">
                            @csrf @method('PATCH')
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Reason <span class="text-red-500">*</span></label>
                                <input type="text" name="revoked_reason" required maxlength="500"
                                       class="w-full border-gray-300 rounded-md text-sm"
                                       placeholder="State reason for revocation...">
                            </div>
                            <button type="submit" onclick="return confirm('Revoke this certificate?')"
                                    class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-md">
                                Revoke
                            </button>
                        </form>
                    </details>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
