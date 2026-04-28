<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('action-plans.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Action Plan</h2>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('action-plans.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4"
                  x-data="{ milestones: [{description:'', target_date:''}] }">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student <span class="text-red-500">*</span></label>
                        <select name="student_profile_id" required class="w-full border-gray-300 rounded-md text-sm">
                            <option value="">— Select student —</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}" @selected(($selectedStudent?->id ?? old('student_profile_id')) == $s->id)>
                                    {{ $s->full_name }} ({{ $s->student_id_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required maxlength="200"
                               placeholder="e.g. Improve Math performance" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Focus Area</label>
                        <select name="focus_area" class="w-full border-gray-300 rounded-md text-sm">
                            @foreach(\App\Models\ActionPlan::FOCUS_AREAS as $v => $label)
                                <option value="{{ $v }}" @selected(old('focus_area') === $v)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                            @foreach(\App\Models\ActionPlan::STATUSES as $v => $label)
                                <option value="{{ $v }}" @selected(old('status', 'active') === $v)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" required value="{{ old('start_date', now()->toDateString()) }}"
                               class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Target Completion Date</label>
                        <input type="date" name="target_date" value="{{ old('target_date') }}"
                               class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description / Goal</label>
                        <textarea name="description" rows="3" maxlength="3000"
                                  placeholder="What is this plan about? What do we want the student to achieve?"
                                  class="w-full border-gray-300 rounded-md text-sm">{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- Milestones --}}
                <div class="border-t border-gray-100 pt-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-medium text-gray-700 text-sm">Milestones / Action Steps</h3>
                        <button type="button" @click="milestones.push({description:'', target_date:''})"
                                class="text-xs text-blue-600 hover:underline">+ Add Step</button>
                    </div>
                    <template x-for="(m, idx) in milestones" :key="idx">
                        <div class="flex gap-2 mb-2">
                            <input type="text" :name="`milestones[${idx}][description]`" x-model="m.description"
                                   placeholder="Step description" class="flex-1 border-gray-300 rounded-md text-sm">
                            <input type="date" :name="`milestones[${idx}][target_date]`" x-model="m.target_date"
                                   class="w-44 border-gray-300 rounded-md text-sm">
                            <button type="button" @click="milestones.splice(idx, 1)" class="text-red-400 hover:text-red-600 text-sm px-2">×</button>
                        </div>
                    </template>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <a href="{{ route('action-plans.index') }}" class="text-sm px-4 py-2 text-gray-600">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-md">Create Plan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
