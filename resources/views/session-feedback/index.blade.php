<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">⭐ Service Feedback</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- KPIs --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Total Surveys</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-yellow-400">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Overall</div>
                    <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['avg_overall'] }}<span class="text-sm text-gray-400">/5</span></div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Helpfulness</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['avg_helpful'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Listened</div>
                    <div class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['avg_listened'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Comfort</div>
                    <div class="text-2xl font-bold text-pink-600 mt-1">{{ $stats['avg_comfort'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-green-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Would Recommend</div>
                    <div class="text-2xl font-bold text-green-600 mt-1">{{ $stats['recommend_pct'] }}%</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-emerald-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Concern Resolved</div>
                    <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['resolved_pct'] }}%</div>
                </div>
            </div>

            {{-- Filter --}}
            @if(auth()->user()->isGuidanceDirector() || auth()->user()->isSuperAdmin())
            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="w-56">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Counselor</label>
                    <select name="counselor_id" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All Counselors</option>
                        @foreach($counselors as $c)
                            <option value="{{ $c->id }}" @selected(request('counselor_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('session-feedback.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>
            @endif

            {{-- Feedback Table --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Counselor</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Overall</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Helpful</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Listened</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Comfort</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flags</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($feedback as $f)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 text-xs text-gray-600 whitespace-nowrap">
                                {{ $f->created_at->format('M d, Y') }}<br>
                                <span class="text-gray-400">{{ $f->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-4 py-2.5 text-gray-700">{{ $f->session?->counselor?->name ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-center font-medium text-yellow-600">{{ $f->overall_rating }}/5</td>
                            <td class="px-4 py-2.5 text-center text-gray-700">{{ $f->helpful_score }}</td>
                            <td class="px-4 py-2.5 text-center text-gray-700">{{ $f->listened_score }}</td>
                            <td class="px-4 py-2.5 text-center text-gray-700">{{ $f->comfort_score }}</td>
                            <td class="px-4 py-2.5 text-xs">
                                @if($f->would_recommend)<span class="bg-green-100 text-green-700 px-1.5 py-0.5 rounded mr-1">👍 Recommend</span>@endif
                                @if($f->issue_resolved)<span class="bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">✓ Resolved</span>@endif
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <a href="{{ route('session-feedback.show', $f) }}" class="text-blue-600 hover:underline text-xs">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-12 text-center text-gray-400">No feedback submitted yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($feedback->hasPages())
                    <div class="px-4 py-3 border-t">{{ $feedback->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
