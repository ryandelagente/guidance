<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">👥 Group Counseling Sessions</h2>
            <a href="{{ route('group-sessions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New Group Session</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="w-48">
                    <label class="block text-xs text-gray-500 mb-1">Focus</label>
                    <select name="focus" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(\App\Models\GroupSession::FOCUSES as $v => $l)
                            <option value="{{ $v }}" @selected(request('focus') === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-44">
                    <label class="block text-xs text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(['scheduled','in_progress','completed','cancelled'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('group-sessions.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($sessions as $s)
                <a href="{{ route('group-sessions.show', $s) }}" class="bg-white shadow-sm rounded-lg p-5 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <h3 class="font-semibold text-gray-900">{{ $s->title }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ ['scheduled' => 'bg-blue-100 text-blue-700', 'in_progress' => 'bg-yellow-100 text-yellow-700', 'completed' => 'bg-green-100 text-green-700', 'cancelled' => 'bg-red-100 text-red-700'][$s->status] }}">{{ ucwords(str_replace('_', ' ', $s->status)) }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">{{ \App\Models\GroupSession::FOCUSES[$s->focus] }}</p>
                    <div class="text-xs text-gray-500 space-y-1">
                        <div>📅 {{ $s->session_date->format('D, M d, Y') }} • {{ substr($s->start_time, 0, 5) }} – {{ substr($s->end_time, 0, 5) }}</div>
                        <div>📍 {{ $s->venue }}</div>
                        <div>👥 {{ $s->registered_count }} / {{ $s->max_participants }} participants • Led by {{ $s->counselor?->name }}</div>
                    </div>
                </a>
                @empty
                <div class="md:col-span-2 bg-white shadow-sm rounded-lg p-12 text-center">
                    <div class="text-5xl mb-3">👥</div>
                    <p class="text-gray-400 text-sm">No group sessions yet.</p>
                </div>
                @endforelse
            </div>

            @if($sessions->hasPages())
                <div class="bg-white px-4 py-3 rounded-lg shadow-sm">{{ $sessions->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
