<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📋 Screening Results</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-red-500">
                    <div class="text-xs text-gray-500 uppercase">Severe</div>
                    <div class="text-2xl font-bold text-red-600 mt-1">{{ $stats['severe'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-red-700">
                    <div class="text-xs text-gray-500 uppercase">Self-Harm Flag</div>
                    <div class="text-2xl font-bold text-red-700 mt-1">{{ $stats['self_harm_flag'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-yellow-500">
                    <div class="text-xs text-gray-500 uppercase">Unreviewed</div>
                    <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['unreviewed'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-xs text-gray-500 uppercase">Last 30 Days</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['last_30_days'] }}</div>
                </div>
            </div>

            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="w-44">
                    <label class="block text-xs text-gray-500 mb-1">Instrument</label>
                    <select name="instrument" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(\App\Models\ScreeningResponse::INSTRUMENTS as $v => $l)
                            <option value="{{ $v }}" @selected(request('instrument') === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-44">
                    <label class="block text-xs text-gray-500 mb-1">Severity</label>
                    <select name="severity" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(['severe','moderately_severe','moderate','mild','minimal'] as $s)
                            <option value="{{ $s }}" @selected(request('severity') === $s)>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="inline-flex items-center text-sm pb-2">
                    <input type="checkbox" name="unreviewed" value="1" @checked(request('unreviewed')) class="rounded border-gray-300 mr-2">
                    Unreviewed only
                </label>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('screening.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instrument</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Score</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Severity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($responses as $r)
                        <tr class="hover:bg-gray-50 {{ $r->positive_self_harm && !$r->reviewed ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-2.5 text-xs text-gray-600">{{ $r->created_at->format('M d') }}<br><span class="text-gray-400">{{ $r->created_at->diffForHumans() }}</span></td>
                            <td class="px-4 py-2.5">
                                <a href="{{ route('students.show', $r->studentProfile) }}" class="font-medium text-gray-800 hover:text-blue-600">{{ $r->studentProfile?->full_name ?? '—' }}</a>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-600">{{ $r->instrument_label }}</td>
                            <td class="px-4 py-2.5 text-center font-bold">{{ $r->total_score }}</td>
                            <td class="px-4 py-2.5">
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $r->getSeverityBadgeClass() }}">{{ ucwords(str_replace('_', ' ', $r->severity)) }}</span>
                                @if($r->positive_self_harm)<br><span class="text-xs text-red-700 font-bold mt-1">🚨 Self-harm flag</span>@endif
                            </td>
                            <td class="px-4 py-2.5 text-xs">
                                @if($r->reviewed)
                                    <span class="text-green-600">✓ Reviewed</span>
                                @else
                                    <span class="text-gray-400">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <a href="{{ route('screening.show', $r) }}" class="text-blue-600 hover:underline text-xs">Review →</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No screening results yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($responses->hasPages())
                <div class="px-4 py-3 border-t">{{ $responses->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
