<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Good Moral Certificates</h2>
            <a href="{{ route('certificates.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + Issue Certificate
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-52">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search Student</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full border-gray-300 rounded-md text-sm" placeholder="Name or student ID...">
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Search</button>
                <a href="{{ route('certificates.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Certificate No.</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Issued By</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Issued</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($certificates as $cert)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs text-gray-700 font-medium">{{ $cert->certificate_number }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $cert->studentProfile->full_name ?? '—' }}<br>
                                <span class="text-xs text-gray-400">{{ $cert->studentProfile->student_id_number ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $cert->purpose }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $cert->issuedBy->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $cert->issued_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-xs {{ $cert->isExpired() ? 'text-red-500' : 'text-gray-500' }}">
                                {{ $cert->expiresAt()->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($cert->is_revoked)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Revoked</span>
                                @elseif($cert->isExpired())
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Expired</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Valid</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('certificates.show', $cert) }}" class="text-blue-600 hover:underline text-xs">View</a>
                                <a href="{{ route('certificates.print', $cert) }}" target="_blank" class="text-gray-500 hover:underline text-xs">Print</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No certificates issued yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($certificates->hasPages())
                    <div class="px-4 py-3 border-t">{{ $certificates->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
