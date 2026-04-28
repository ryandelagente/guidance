<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Announcements</h2>
            @if($isStaff)
                <div class="flex gap-2">
                    @if(request('manage'))
                        <a href="{{ route('announcements.index') }}" class="text-sm text-gray-500 py-2 px-3">View as Audience</a>
                    @else
                        <a href="{{ route('announcements.index', ['manage' => 1]) }}" class="text-sm text-gray-500 py-2 px-3">Manage All</a>
                    @endif
                    <a href="{{ route('announcements.create') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New Announcement</a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            @forelse($announcements as $a)
            @php
                $borderColor = match($a->priority) {
                    'urgent'  => 'border-l-red-500',
                    'warning' => 'border-l-yellow-500',
                    default   => 'border-l-blue-500',
                };
            @endphp
            <div class="bg-white shadow-sm rounded-lg border-l-4 {{ $borderColor }} {{ $a->is_pinned ? 'ring-1 ring-blue-100' : '' }} p-5">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-lg">{{ $a->getPriorityIcon() }}</span>
                        <h3 class="font-semibold text-gray-900 text-lg">{{ $a->title }}</h3>
                        @if($a->is_pinned)
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">📌 Pinned</span>
                        @endif
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $a->getPriorityBadgeClass() }}">{{ ucfirst($a->priority) }}</span>
                        @if(!$a->is_published)
                            <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">Draft</span>
                        @endif
                        @if($a->isExpired())
                            <span class="text-xs bg-red-50 text-red-600 px-2 py-0.5 rounded-full">Expired</span>
                        @endif
                    </div>
                    @if($isStaff)
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('announcements.edit', $a) }}" class="text-yellow-600 hover:text-yellow-700 text-xs">Edit</a>
                        <form method="POST" action="{{ route('announcements.destroy', $a) }}"
                              onsubmit="return confirm('Delete this announcement?')" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                        </form>
                    </div>
                    @endif
                </div>

                <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $a->body }}</div>

                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 text-xs text-gray-400">
                    <div>
                        Posted by <span class="font-medium text-gray-600">{{ $a->author?->name ?? 'System' }}</span>
                        @if($a->published_at)
                            • {{ $a->published_at->diffForHumans() }}
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <span class="bg-gray-100 px-2 py-0.5 rounded">Audience: {{ ucwords($a->audience) }}</span>
                        @if($a->expires_at)
                            <span class="bg-gray-100 px-2 py-0.5 rounded">Expires: {{ $a->expires_at->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white shadow-sm rounded-lg p-12 text-center">
                <div class="text-gray-300 text-5xl mb-3">📢</div>
                <p class="text-gray-400 text-sm">No announcements at this time.</p>
            </div>
            @endforelse

            @if($announcements->hasPages())
                <div class="bg-white px-4 py-3 rounded-lg shadow-sm">{{ $announcements->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
