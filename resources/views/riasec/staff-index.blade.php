<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🎯 RIASEC Career Inventory Results</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Top Code Starts With</label>
                    <select name="top_code" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(\App\Models\RiasecResponse::TYPE_LABELS as $code => $label)
                            <option value="{{ $code }}" @selected(request('top_code') === $code)>{{ $code }} — {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('riasec.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Top Code</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">R</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">I</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">A</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">S</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">E</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">C</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($responses as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('students.show', $r->studentProfile) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $r->studentProfile?->full_name }}</a>
                                <div class="text-xs text-gray-400">{{ $r->studentProfile?->student_id_number ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    @foreach(str_split($r->top_code) as $code)
                                        <span class="w-6 h-6 rounded-full {{ \App\Models\RiasecResponse::TYPE_COLORS[$code] ?? 'bg-gray-100' }} flex items-center justify-center font-bold text-xs">{{ $code }}</span>
                                    @endforeach
                                </div>
                            </td>
                            @foreach(['r','i','a','s','e','c'] as $code)
                            <td class="px-4 py-3 text-center text-xs text-gray-600">{{ $r->{"score_$code"} }}</td>
                            @endforeach
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $r->completed_at->format('M d, Y') }}<br>
                                <span class="text-gray-400">{{ $r->completed_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('riasec.show', $r) }}" class="text-blue-600 hover:underline text-xs">View Profile</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="px-4 py-12 text-center text-gray-400">No completed assessments yet.</td></tr>
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
