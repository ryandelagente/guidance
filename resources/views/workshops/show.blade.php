<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('workshops.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $workshop->title }}</h2>
            </div>
            @if(auth()->user()->isStaff())
                <div class="flex gap-2">
                    <a href="{{ route('workshops.edit', $workshop) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1.5 rounded-md">Edit</a>
                    <form method="POST" action="{{ route('workshops.destroy', $workshop) }}" onsubmit="return confirm('Delete this workshop and all RSVPs?')">
                        @csrf @method('DELETE')
                        <button class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1.5 rounded-md">Delete</button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    @php
        $gradient = \App\Models\Workshop::COVER_COLORS[$workshop->cover_color] ?? \App\Models\Workshop::COVER_COLORS['blue'];
        $isRegistered = $workshop->userIsRegistered(auth()->user());
    @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Hero --}}
            <div class="bg-gradient-to-br {{ $gradient }} rounded-xl text-white p-8 shadow-md">
                <div class="text-xs opacity-90 uppercase tracking-wider mb-2">{{ $workshop->category_label }}</div>
                <h1 class="font-bold text-2xl">{{ $workshop->title }}</h1>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-5 text-sm">
                    <div>
                        <div class="opacity-75 text-xs uppercase">Date & Time</div>
                        <div class="font-medium mt-0.5">{{ $workshop->starts_at->format('l, F d, Y') }}</div>
                        <div class="opacity-90">{{ $workshop->starts_at->format('h:i A') }} – {{ $workshop->ends_at->format('h:i A') }}</div>
                    </div>
                    <div>
                        <div class="opacity-75 text-xs uppercase">Venue</div>
                        <div class="font-medium mt-0.5">{{ $workshop->venue }}</div>
                        <div class="opacity-90">
                            {{ $workshop->mode === 'virtual' ? '🌐 Virtual' : ($workshop->mode === 'hybrid' ? '🌐 Hybrid' : '🏢 In-Person') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- RSVP --}}
            <div class="bg-white shadow-sm rounded-lg p-5 flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <div class="font-semibold text-gray-800">
                        @if($workshop->capacity)
                            {{ $workshop->registered_count }} / {{ $workshop->capacity }} registered
                        @else
                            {{ $workshop->registered_count }} registered
                        @endif
                    </div>
                    @if($workshop->rsvp_deadline)
                        <div class="text-xs text-gray-500">RSVP by {{ $workshop->rsvp_deadline->format('M d, h:i A') }}</div>
                    @endif
                </div>

                <div>
                    @if($workshop->isPast())
                        <span class="text-sm text-gray-500">Event has ended</span>
                    @elseif($workshop->status === 'cancelled')
                        <span class="text-sm text-red-500 font-medium">Workshop cancelled</span>
                    @elseif($isRegistered)
                        <div class="flex items-center gap-3">
                            <span class="bg-green-50 text-green-700 px-3 py-1.5 rounded-md text-sm font-medium">✓ You're registered</span>
                            <form method="POST" action="{{ route('workshops.rsvp.cancel', $workshop) }}">
                                @csrf @method('DELETE')
                                <button class="text-xs text-gray-400 hover:text-red-500">Cancel RSVP</button>
                            </form>
                        </div>
                    @elseif($workshop->rsvpsClosed())
                        <span class="text-sm text-orange-500">RSVP closed</span>
                    @elseif($workshop->isFull())
                        <span class="text-sm text-gray-500">Workshop is full</span>
                    @else
                        <form method="POST" action="{{ route('workshops.rsvp', $workshop) }}">
                            @csrf
                            <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-md">RSVP</button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">About this workshop</h3>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $workshop->description }}</p>

                @if($workshop->meeting_link && ($isRegistered || auth()->user()->isStaff()))
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 uppercase mb-2">Meeting Link</p>
                    <a href="{{ $workshop->meeting_link }}" target="_blank" rel="noopener noreferrer"
                       class="text-blue-600 hover:underline text-sm break-all">{{ $workshop->meeting_link }}</a>
                </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400">
                    Organized by <strong class="text-gray-600">{{ $workshop->organizer->name }}</strong>
                </div>
            </div>

            {{-- Attendee List (staff only) --}}
            @if(auth()->user()->isStaff() && $workshop->rsvps->isNotEmpty())
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">
                    Registered Attendees ({{ $workshop->rsvps->whereIn('status', ['registered','attended'])->count() }})
                </h3>
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($workshop->rsvps->whereIn('status', ['registered','attended']) as $rsvp)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2.5">
                                <div class="font-medium text-gray-800">{{ $rsvp->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $rsvp->user->email }} • {{ $rsvp->user->getRoleDisplayName() }}</div>
                            </td>
                            <td class="py-2.5 text-xs text-gray-500">
                                Registered {{ $rsvp->created_at->diffForHumans() }}
                            </td>
                            <td class="py-2.5 text-right">
                                @if($rsvp->status === 'attended')
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">✓ Attended</span>
                                @else
                                    <form method="POST" action="{{ route('workshops.attended', [$workshop, $rsvp]) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-xs text-blue-600 hover:underline">Mark Attended</button>
                                    </form>
                                @endif
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
