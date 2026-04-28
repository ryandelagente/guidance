<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('action-plans.show', $plan) }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Plan: {{ $plan->title }}</h2>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('action-plans.update', $plan) }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" required value="{{ old('title', $plan->title) }}"
                               class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Focus Area</label>
                        <select name="focus_area" class="w-full border-gray-300 rounded-md text-sm">
                            @foreach(\App\Models\ActionPlan::FOCUS_AREAS as $v => $label)
                                <option value="{{ $v }}" @selected(old('focus_area', $plan->focus_area) === $v)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                            @foreach(\App\Models\ActionPlan::STATUSES as $v => $label)
                                <option value="{{ $v }}" @selected(old('status', $plan->status) === $v)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" required value="{{ old('start_date', $plan->start_date->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Target Date</label>
                        <input type="date" name="target_date" value="{{ old('target_date', $plan->target_date?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description / Goal</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 rounded-md text-sm">{{ old('description', $plan->description) }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Outcome Notes <span class="text-gray-400">(once completed)</span></label>
                        <textarea name="outcome_notes" rows="3" class="w-full border-gray-300 rounded-md text-sm">{{ old('outcome_notes', $plan->outcome_notes) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <a href="{{ route('action-plans.show', $plan) }}" class="text-sm px-4 py-2 text-gray-600">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-md">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
