<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Faculty / Staff Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome --}}
            <div class="bg-white shadow-sm rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-lg">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-lg">Welcome back, {{ auth()->user()->name }}</p>
                    <p class="text-sm text-gray-400">Faculty / Staff &bull; {{ now()->format('l, F d, Y') }}</p>
                </div>
            </div>

            {{-- KPI Row --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                $kpis = [
                    ['label' => 'Total Referrals',   'value' => $myTotal,    'color' => 'bg-slate-600',  'href' => route('referrals.index')],
                    ['label' => 'Pending',           'value' => $myPending,  'color' => 'bg-orange-500', 'href' => route('referrals.index', ['status' => 'pending'])],
                    ['label' => 'In Progress',       'value' => $myActive,   'color' => 'bg-blue-600',   'href' => route('referrals.index', ['status' => 'acknowledged'])],
                    ['label' => 'Resolved',          'value' => $myResolved, 'color' => 'bg-green-600',  'href' => route('referrals.index', ['status' => 'resolved'])],
                ];
                @endphp
                @foreach($kpis as $k)
                <a href="{{ $k['href'] }}" class="{{ $k['color'] }} hover:opacity-90 text-white rounded-xl p-5 shadow-sm block transition">
                    <p class="text-3xl font-bold">{{ number_format($k['value']) }}</p>
                    <p class="text-sm opacity-90 mt-1">{{ $k['label'] }}</p>
                </a>
                @endforeach
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('referrals.create') }}"
                   class="bg-indigo-600 hover:opacity-90 text-white rounded-xl p-6 shadow-sm block transition text-center">
                    <p class="text-3xl mb-2">🔔</p>
                    <p class="font-semibold text-lg">Submit a Referral</p>
                    <p class="text-sm opacity-80 mt-1">Flag a student for guidance counseling</p>
                </a>
                <a href="{{ route('referrals.index') }}"
                   class="bg-white border-2 border-indigo-200 hover:border-indigo-400 text-indigo-700 rounded-xl p-6 shadow-sm block transition text-center">
                    <p class="text-3xl mb-2">📋</p>
                    <p class="font-semibold text-lg">Track My Referrals</p>
                    <p class="text-sm text-indigo-500 mt-1">Check status of all submitted referrals</p>
                </a>
            </div>

            {{-- Recent Referrals --}}
            @if($recentReferrals->isNotEmpty())
            <div>
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Recent Referrals</h3>
                <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Student</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Reason</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentReferrals as $ref)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-800">
                                    {{ $ref->studentProfile?->full_name ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-gray-600 max-w-xs truncate">{{ ucwords(str_replace('_', ' ', $ref->reason_category)) }}</td>
                                <td class="px-5 py-3">
                                    @php
                                    $colors = [
                                        'pending'      => 'bg-orange-100 text-orange-700',
                                        'acknowledged' => 'bg-blue-100 text-blue-700',
                                        'in_progress'  => 'bg-indigo-100 text-indigo-700',
                                        'resolved'     => 'bg-green-100 text-green-700',
                                        'closed'       => 'bg-gray-100 text-gray-600',
                                    ];
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$ref->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst(str_replace('_', ' ', $ref->status)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-400">{{ $ref->created_at->format('M d, Y') }}</td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('referrals.show', $ref) }}" class="text-indigo-600 hover:underline text-xs">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="bg-white shadow-sm rounded-xl p-10 text-center text-gray-400">
                <p class="text-4xl mb-3">🔔</p>
                <p class="font-medium">No referrals submitted yet</p>
                <p class="text-sm mt-1">Use the button above to flag a student for guidance.</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
