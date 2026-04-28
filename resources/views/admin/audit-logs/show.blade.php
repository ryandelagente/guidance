<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.audit-logs.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Audit Log Entry #{{ $log->id }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Timestamp</dt>
                        <dd class="mt-1 text-gray-900">{{ $log->created_at->format('F d, Y h:i:s A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Action</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $log->getActionBadgeClass() }}">
                                {{ ucwords(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">User</dt>
                        <dd class="mt-1 text-gray-900">
                            {{ $log->user?->name ?? 'System / Anonymous' }}
                            @if($log->user)
                                <span class="text-xs text-gray-400 block">{{ $log->user->email }} — {{ $log->user->getRoleDisplayName() }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Affected Record</dt>
                        <dd class="mt-1 text-gray-900">
                            @if($log->auditable_type)
                                {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase">Description</dt>
                        <dd class="mt-1 text-gray-900">{{ $log->description ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">IP Address</dt>
                        <dd class="mt-1 text-gray-700 font-mono text-xs">{{ $log->ip_address ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">User Agent</dt>
                        <dd class="mt-1 text-gray-700 text-xs break-all">{{ $log->user_agent ?? '—' }}</dd>
                    </div>
                </div>
            </div>

            @if($log->changes)
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Changes</h4>
                <table class="min-w-full text-sm border border-gray-200 rounded">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Old Value</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">New Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $old = $log->changes['old'] ?? [];
                            $new = $log->changes['new'] ?? [];
                            $fields = array_unique(array_merge(array_keys($old), array_keys($new)));
                        @endphp
                        @foreach($fields as $field)
                        <tr>
                            <td class="px-3 py-2 font-medium text-gray-700">{{ $field }}</td>
                            <td class="px-3 py-2 text-red-600 font-mono text-xs break-all">
                                {{ is_scalar($old[$field] ?? null) ? $old[$field] : json_encode($old[$field] ?? null) }}
                            </td>
                            <td class="px-3 py-2 text-green-600 font-mono text-xs break-all">
                                {{ is_scalar($new[$field] ?? null) ? $new[$field] : json_encode($new[$field] ?? null) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
