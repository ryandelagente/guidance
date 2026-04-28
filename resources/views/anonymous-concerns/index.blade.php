<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🤝 Anonymous Concerns</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-blue-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">New</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['new'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-yellow-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Reviewing</div>
                    <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['reviewing'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-red-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Critical Open</div>
                    <div class="text-2xl font-bold text-red-600 mt-1">{{ $stats['critical'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Total</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</div>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(\App\Models\AnonymousConcern::STATUSES as $v => $label)
                            <option value="{{ $v }}" @selected(request('status') === $v)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Urgency</label>
                    <select name="urgency" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(\App\Models\AnonymousConcern::URGENCIES as $v => $label)
                            <option value="{{ $v }}" @selected(request('urgency') === $v)>{{ ucfirst($v) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-48">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <select name="concern_type" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(\App\Models\AnonymousConcern::TYPES as $v => $label)
                            <option value="{{ $v }}" @selected(request('concern_type') === $v)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('anonymous-concerns.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            {{-- Concerns Table --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urgency</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($concerns as $c)
                        <tr class="hover:bg-gray-50 {{ $c->urgency === 'critical' ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $c->reference_code }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                {{ $c->created_at->format('M d, Y') }}<br>
                                <span class="text-gray-400">{{ $c->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-700">{{ $c->type_label }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full border {{ $c->getUrgencyBadgeClass() }}">
                                    {{ $c->urgency === 'critical' ? '🚨 Critical' : ucfirst($c->urgency) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 max-w-md">
                                <p class="line-clamp-2 text-xs">{{ $c->description }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $c->getStatusBadgeClass() }}">{{ \App\Models\AnonymousConcern::STATUSES[$c->status] }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('anonymous-concerns.show', $c) }}" class="text-blue-600 hover:underline text-xs">Review</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No concerns match your filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($concerns->hasPages())
                    <div class="px-4 py-3 border-t">{{ $concerns->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
