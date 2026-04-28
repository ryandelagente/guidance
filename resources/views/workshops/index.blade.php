<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">🎓 Workshops & Events</h2>
            @if($isStaff)
                <a href="{{ route('workshops.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New Workshop</a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Tabs + Filters --}}
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-1 bg-white shadow-sm rounded-lg p-1">
                    <a href="{{ route('workshops.index') }}"
                       class="px-4 py-1.5 rounded-md text-sm font-medium transition {{ request('view') !== 'past' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                        Upcoming
                    </a>
                    <a href="{{ route('workshops.index', ['view' => 'past']) }}"
                       class="px-4 py-1.5 rounded-md text-sm font-medium transition {{ request('view') === 'past' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                        Past
                    </a>
                </div>
                <form method="GET" class="flex gap-2">
                    @if(request('view'))<input type="hidden" name="view" value="{{ request('view') }}">@endif
                    <select name="category" class="border-gray-300 rounded-md text-sm">
                        <option value="">All Categories</option>
                        @foreach(\App\Models\Workshop::CATEGORIES as $v => $label)
                            <option value="{{ $v }}" @selected(request('category') === $v)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                </form>
            </div>

            {{-- Workshop Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($workshops as $w)
                @php
                    $gradient = \App\Models\Workshop::COVER_COLORS[$w->cover_color] ?? \App\Models\Workshop::COVER_COLORS['blue'];
                    $userStatus = $userRsvps[$w->id] ?? null;
                @endphp
                <div class="bg-white shadow-sm rounded-xl overflow-hidden hover:shadow-md transition flex flex-col">
                    {{-- Cover --}}
                    <a href="{{ route('workshops.show', $w) }}" class="bg-gradient-to-br {{ $gradient }} p-5 text-white relative block">
                        <div class="text-xs opacity-90 uppercase tracking-wider mb-1">{{ $w->category_label }}</div>
                        <h3 class="font-bold text-lg leading-tight">{{ $w->title }}</h3>
                        <div class="text-sm opacity-90 mt-2">
                            📅 {{ $w->starts_at->format('D, M d') }} • {{ $w->starts_at->format('h:i A') }}
                        </div>
                        @if($w->status === 'cancelled')
                            <div class="absolute top-3 right-3 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-medium">CANCELLED</div>
                        @elseif($w->isOngoing())
                            <div class="absolute top-3 right-3 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full font-medium animate-pulse">LIVE</div>
                        @elseif($w->status === 'draft')
                            <div class="absolute top-3 right-3 bg-gray-700 text-white text-xs px-2 py-0.5 rounded-full font-medium">DRAFT</div>
                        @endif
                    </a>

                    {{-- Body --}}
                    <div class="p-4 flex-1 flex flex-col">
                        <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ $w->description }}</p>
                        <div class="text-xs text-gray-500 space-y-1 mb-3">
                            <div>📍 {{ $w->venue }} {!! $w->mode === 'virtual' ? '<span class="text-indigo-500">(Virtual)</span>' : ($w->mode === 'hybrid' ? '<span class="text-purple-500">(Hybrid)</span>' : '') !!}</div>
                            <div>👥 {{ $w->registered_count }} registered{{ $w->capacity ? ' / ' . $w->capacity . ' max' : '' }}</div>
                        </div>

                        <div class="mt-auto flex gap-2">
                            @if($w->isPast())
                                <span class="flex-1 text-xs text-center text-gray-400 py-2">Event ended</span>
                            @elseif($w->status === 'cancelled')
                                <span class="flex-1 text-xs text-center text-red-500 py-2">Cancelled</span>
                            @elseif($w->rsvpsClosed())
                                <span class="flex-1 text-xs text-center text-orange-500 py-2">RSVP closed</span>
                            @elseif(in_array($userStatus, ['registered','attended']))
                                <span class="flex-1 text-xs text-center bg-green-50 text-green-700 py-2 rounded-md font-medium">✓ You're registered</span>
                                <form method="POST" action="{{ route('workshops.rsvp.cancel', $w) }}" class="flex-shrink-0">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-gray-400 hover:text-red-500 px-2 py-2">Cancel</button>
                                </form>
                            @elseif($w->isFull())
                                <span class="flex-1 text-xs text-center bg-gray-100 text-gray-500 py-2 rounded-md">Full</span>
                            @else
                                <form method="POST" action="{{ route('workshops.rsvp', $w) }}" class="flex-1">
                                    @csrf
                                    <button class="w-full text-xs bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md font-medium">RSVP</button>
                                </form>
                            @endif
                            <a href="{{ route('workshops.show', $w) }}" class="text-xs text-gray-500 hover:text-blue-600 px-3 py-2">Details →</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="md:col-span-2 lg:col-span-3 bg-white shadow-sm rounded-lg p-12 text-center">
                    <div class="text-5xl mb-3">🎓</div>
                    <p class="text-gray-400 text-sm">{{ request('view') === 'past' ? 'No past workshops.' : 'No upcoming workshops.' }}</p>
                </div>
                @endforelse
            </div>

            @if($workshops->hasPages())
                <div class="bg-white px-4 py-3 rounded-lg shadow-sm">{{ $workshops->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
