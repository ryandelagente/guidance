<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('action-plans.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $plan->title }}</h2>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $plan->getStatusBadgeClass() }}">
                    {{ \App\Models\ActionPlan::STATUSES[$plan->status] }}
                </span>
            </div>
            @if(auth()->user()->isStaff())
                <div class="flex gap-2">
                    <a href="{{ route('action-plans.edit', $plan) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1.5 rounded-md">Edit</a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Overview --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Student</div>
                        <a href="{{ route('students.show', $plan->studentProfile) }}" class="font-medium text-gray-900 hover:text-blue-600">
                            {{ $plan->studentProfile->full_name }}
                        </a>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Counselor</div>
                        <div class="font-medium text-gray-900">{{ $plan->counselor->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Focus Area</div>
                        <div class="font-medium text-gray-900">{{ $plan->focus_label }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Timeline</div>
                        <div class="text-gray-900">
                            {{ $plan->start_date->format('M d, Y') }}
                            @if($plan->target_date)
                                → <span class="{{ $plan->isOverdue() ? 'text-red-500 font-medium' : '' }}">{{ $plan->target_date->format('M d, Y') }}</span>
                                @if($plan->isOverdue())<span class="text-xs text-red-500">(overdue)</span>@endif
                            @endif
                        </div>
                    </div>
                </div>

                @if($plan->description)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="text-xs text-gray-500 uppercase mb-2">Goal / Description</div>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $plan->description }}</p>
                </div>
                @endif

                {{-- Progress --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="font-medium text-gray-700">Overall Progress</span>
                        <span class="font-bold text-blue-600">{{ $plan->progress_percent }}%</span>
                    </div>
                    <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all" style="width: {{ $plan->progress_percent }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Milestones --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">Milestones</h3>

                @if($plan->milestones->isEmpty())
                    <p class="text-gray-400 text-sm text-center py-4">No milestones yet.</p>
                @else
                <div class="space-y-2">
                    @foreach($plan->milestones as $m)
                    <div class="flex items-start gap-3 p-3 rounded-lg border {{ $m->isCompleted() ? 'bg-green-50 border-green-100' : ($m->isOverdue() ? 'bg-red-50 border-red-100' : 'bg-gray-50 border-gray-100') }}">
                        @if(auth()->user()->isStaff())
                        <form method="POST" action="{{ route('action-plans.milestones.toggle', [$plan, $m]) }}" class="flex-shrink-0 pt-0.5">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-5 h-5 rounded border-2 {{ $m->isCompleted() ? 'bg-green-500 border-green-500' : 'border-gray-300 hover:border-blue-500' }} flex items-center justify-center transition">
                                @if($m->isCompleted())
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @endif
                            </button>
                        </form>
                        @else
                        <div class="w-5 h-5 rounded border-2 {{ $m->isCompleted() ? 'bg-green-500 border-green-500' : 'border-gray-300' }} flex items-center justify-center pt-0.5 flex-shrink-0">
                            @if($m->isCompleted())
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @endif
                        </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <p class="text-sm {{ $m->isCompleted() ? 'text-gray-500 line-through' : 'text-gray-800' }}">{{ $m->description }}</p>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                                @if($m->target_date)
                                    <span class="{{ $m->isOverdue() ? 'text-red-500 font-medium' : '' }}">📅 {{ $m->target_date->format('M d, Y') }}</span>
                                @endif
                                @if($m->isCompleted())
                                    <span class="text-green-600">✓ Completed {{ $m->completed_at->format('M d') }}</span>
                                @endif
                            </div>
                        </div>

                        @if(auth()->user()->isStaff())
                        <form method="POST" action="{{ route('action-plans.milestones.delete', [$plan, $m]) }}" onsubmit="return confirm('Remove this milestone?')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:text-red-600 text-xs">×</button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                @if(auth()->user()->isStaff())
                <details class="mt-4 border border-gray-100 rounded-lg">
                    <summary class="px-4 py-2.5 text-sm font-medium text-gray-600 cursor-pointer hover:bg-gray-50 rounded-lg">+ Add Milestone</summary>
                    <form method="POST" action="{{ route('action-plans.milestones.store', $plan) }}" class="px-4 pb-4 pt-3 flex gap-2">
                        @csrf
                        <input type="text" name="description" required maxlength="300" placeholder="What needs to happen?" class="flex-1 border-gray-300 rounded-md text-sm">
                        <input type="date" name="target_date" class="w-40 border-gray-300 rounded-md text-sm">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 rounded-md">Add</button>
                    </form>
                </details>
                @endif
            </div>

            @if($plan->outcome_notes)
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Outcome Notes</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $plan->outcome_notes }}</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
