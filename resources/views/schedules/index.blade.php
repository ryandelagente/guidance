<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Counselor Schedule</h2>
            @if(!auth()->user()->isCounselor())
            <form method="GET" class="flex items-center gap-2">
                <select name="counselor_id" onchange="this.form.submit()" class="border-gray-300 rounded-md text-sm">
                    @foreach($counselors as $c)
                        <option value="{{ $c->id }}" @selected($counselor->id === $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Add New Slot --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Add / Update Availability</h3>
                <form method="POST" action="{{ route('schedules.store') }}" class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
                    @csrf
                    <input type="hidden" name="counselor_id" value="{{ $counselor->id }}">

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Day</label>
                        <select name="day_of_week" class="w-full border-gray-300 rounded-md text-sm">
                            @foreach(['monday','tuesday','wednesday','thursday','friday','saturday'] as $d)
                                <option value="{{ $d }}">{{ ucfirst($d) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Start</label>
                        <input type="time" name="start_time" value="08:00" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">End</label>
                        <input type="time" name="end_time" value="17:00" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Slot Duration</label>
                        <select name="slot_duration" class="w-full border-gray-300 rounded-md text-sm">
                            <option value="30">30 min</option>
                            <option value="45">45 min</option>
                            <option value="60" selected>60 min</option>
                            <option value="90">90 min</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                        Save
                    </button>
                </form>
            </div>

            {{-- Current Schedule --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Day</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Slot</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Slots/Day</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Active</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($schedules as $sched)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ ucfirst($sched->day_of_week) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ substr($sched->start_time,0,5) }} – {{ substr($sched->end_time,0,5) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $sched->slot_duration }} min</td>
                            <td class="px-4 py-3 text-gray-600">{{ count($sched->generateSlots()) }} slots</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs {{ $sched->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $sched->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('schedules.destroy', $sched) }}"
                                      onsubmit="return confirm('Remove this schedule?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No schedule set up yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
