<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('announcements.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $announcement->title }}</h2>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center gap-2 mb-3 flex-wrap">
                    <span>{{ $announcement->getPriorityIcon() }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $announcement->getPriorityBadgeClass() }}">{{ ucfirst($announcement->priority) }}</span>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Audience: {{ ucwords($announcement->audience) }}</span>
                </div>
                <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $announcement->body }}</div>
                <div class="text-xs text-gray-400 mt-6 pt-4 border-t border-gray-100">
                    Posted by <span class="font-medium text-gray-600">{{ $announcement->author?->name }}</span> • {{ $announcement->published_at?->format('F d, Y h:i A') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
