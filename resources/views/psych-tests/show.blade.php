<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $psychTest->name }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('psych-tests.edit', $psychTest) }}"
                   class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md">Edit</a>
                <a href="{{ route('psych-tests.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">{{ $psychTest->type_label }}</span>
                    @if($psychTest->is_active)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                    @else
                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-500">Inactive</span>
                    @endif
                </div>

                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div><span class="font-medium text-gray-500">Category</span><p class="mt-0.5 text-gray-800">{{ $psychTest->category ?? '—' }}</p></div>
                    <div><span class="font-medium text-gray-500">Total Items</span><p class="mt-0.5 text-gray-800">{{ $psychTest->total_items ?? '—' }}</p></div>
                    <div><span class="font-medium text-gray-500">Publisher</span><p class="mt-0.5 text-gray-800">{{ $psychTest->publisher ?? '—' }}</p></div>
                    <div><span class="font-medium text-gray-500">Edition Year</span><p class="mt-0.5 text-gray-800">{{ $psychTest->edition_year ?? '—' }}</p></div>
                </div>

                @if($psychTest->description)
                <div class="text-sm">
                    <span class="font-medium text-gray-500">Description</span>
                    <p class="mt-1 text-gray-700">{{ $psychTest->description }}</p>
                </div>
                @endif

                <div class="pt-3 flex gap-4">
                    <a href="{{ route('test-schedules.create', ['test_id' => $psychTest->id]) }}"
                       class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        + Schedule Session
                    </a>
                </div>
            </div>

            {{-- Schedules --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Scheduled Sessions ({{ $psychTest->schedules->count() }})</h3>
                @forelse($psychTest->schedules as $sched)
                <div class="flex items-center justify-between py-2 border-b last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $sched->scheduled_date->format('M d, Y') }} at {{ substr($sched->start_time,0,5) }}</p>
                        <p class="text-xs text-gray-400">{{ $sched->college ?? 'All colleges' }} &bull; {{ $sched->venue ?? 'Venue TBD' }} &bull; {{ $sched->administeredBy->name ?? '—' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $sched->getStatusBadgeClass() }}">
                            {{ ucfirst($sched->status) }}
                        </span>
                        <a href="{{ route('test-schedules.show', $sched) }}" class="text-blue-600 hover:underline text-xs">View</a>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">No sessions scheduled yet.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
