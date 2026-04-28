<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Audit Log</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Filters --}}
            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">User</label>
                    <select name="user_id" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Action</label>
                    <select name="action" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach($actions as $a)
                            <option value="{{ $a }}" @selected(request('action') === $a)>{{ ucwords(str_replace('_', ' ', $a)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <input type="text" name="type" value="{{ request('type') }}" placeholder="e.g. Student"
                           class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md flex-1">Filter</button>
                    <a href="{{ route('admin.audit-logs.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
                </div>
                <div class="md:col-span-6">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search description…"
                           class="w-full border-gray-300 rounded-md text-sm">
                </div>
            </form>

            {{-- Logs Table --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Timestamp</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">User</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Action</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Description</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">IP</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 text-gray-700 text-xs whitespace-nowrap">
                                {{ $log->created_at->format('M d, Y') }}<br>
                                <span class="text-gray-400">{{ $log->created_at->format('h:i:s A') }}</span>
                            </td>
                            <td class="px-4 py-2.5">
                                @if($log->user)
                                    <div class="font-medium text-gray-800">{{ $log->user->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->user->getRoleDisplayName() }}</div>
                                @else
                                    <span class="text-gray-400 text-xs italic">System / Anonymous</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $log->getActionBadgeClass() }}">
                                    {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-gray-700 max-w-md">
                                <div class="truncate">{{ $log->description ?? '—' }}</div>
                                @if($log->auditable_type)
                                    <div class="text-xs text-gray-400">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-500 font-mono">{{ $log->ip_address ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <a href="{{ route('admin.audit-logs.show', $log) }}"
                                   class="text-blue-600 hover:underline text-xs">Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No audit log entries match your filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($logs->hasPages())
                    <div class="px-4 py-3 border-t">{{ $logs->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
