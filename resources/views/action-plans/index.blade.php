<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">🎯 Action Plans</h2>
            @if(auth()->user()->isStaff())
                <a href="{{ route('action-plans.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New Plan</a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(\App\Models\ActionPlan::STATUSES as $v => $label)
                            <option value="{{ $v }}" @selected(request('status') === $v)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-48">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Focus Area</label>
                    <select name="focus_area" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(\App\Models\ActionPlan::FOCUS_AREAS as $v => $label)
                            <option value="{{ $v }}" @selected(request('focus_area') === $v)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('action-plans.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            {{-- Plans --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($plans as $plan)
                <a href="{{ route('action-plans.show', $plan) }}"
                   class="bg-white shadow-sm rounded-lg p-5 hover:shadow-md transition group">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 group-hover:text-blue-600">{{ $plan->title }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $plan->studentProfile?->full_name }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $plan->getStatusBadgeClass() }} flex-shrink-0">
                            {{ \App\Models\ActionPlan::STATUSES[$plan->status] }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                        <span>{{ $plan->focus_label }}</span>
                        @if($plan->target_date)
                            <span>•</span>
                            <span class="{{ $plan->isOverdue() ? 'text-red-500 font-medium' : '' }}">
                                Due {{ $plan->target_date->format('M d, Y') }}
                                @if($plan->isOverdue()) (overdue) @endif
                            </span>
                        @endif
                    </div>

                    {{-- Progress bar --}}
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-gray-500">Progress</span>
                            <span class="font-medium text-gray-700">{{ $plan->progress_percent }}%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full transition-all" style="width: {{ $plan->progress_percent }}%"></div>
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            {{ $plan->milestones->whereNotNull('completed_at')->count() }} / {{ $plan->milestones->count() }} milestones complete
                        </div>
                    </div>
                </a>
                @empty
                <div class="md:col-span-2 bg-white shadow-sm rounded-lg p-12 text-center">
                    <div class="text-5xl mb-3">🎯</div>
                    <p class="text-gray-400 text-sm">No action plans yet.</p>
                </div>
                @endforelse
            </div>

            @if($plans->hasPages())
                <div class="bg-white px-4 py-3 rounded-lg shadow-sm">{{ $plans->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
