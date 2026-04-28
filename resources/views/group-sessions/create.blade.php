<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('group-sessions.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Group Session</h2>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('group-sessions.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required maxlength="200" placeholder="e.g. Anxiety Support Group — Session 1"
                               class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Focus</label>
                        <select name="focus" class="w-full border-gray-300 rounded-md text-sm">
                            @foreach(\App\Models\GroupSession::FOCUSES as $v => $l)
                                <option value="{{ $v }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                            <option value="scheduled">Scheduled</option>
                            <option value="in_progress">In Progress</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="session_date" required class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Participants</label>
                        <input type="number" name="max_participants" value="15" min="2" max="100" required class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                        <input type="time" name="start_time" required class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                        <input type="time" name="end_time" required class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue <span class="text-red-500">*</span></label>
                        <input type="text" name="venue" required maxlength="200" class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description / Group Goals</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 rounded-md text-sm"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Initial Participants <span class="text-gray-400">(can add more later)</span></label>
                        <select name="participants[]" multiple size="6" class="w-full border-gray-300 rounded-md text-sm">
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->last_name }}, {{ $s->first_name }} ({{ $s->student_id_number ?? 'No ID' }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Hold Ctrl/Cmd to select multiple.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <a href="{{ route('group-sessions.index') }}" class="text-sm px-4 py-2 text-gray-600">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-md">Create Group Session</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
