<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('resources.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $resource->title }}</h2>
            </div>
            @if(auth()->user()->isStaff())
                <div class="flex gap-2">
                    <a href="{{ route('resources.edit', $resource) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1.5 rounded-md">Edit</a>
                    <form method="POST" action="{{ route('resources.destroy', $resource) }}" onsubmit="return confirm('Delete this resource?')">
                        @csrf @method('DELETE')
                        <button class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1.5 rounded-md">Delete</button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @if($resource->is_emergency)
                <div class="bg-red-50 border-b border-red-200 px-6 py-3 text-sm text-red-800 font-medium">
                    🚨 This is an emergency resource — available 24/7
                </div>
                @endif

                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4 flex-wrap">
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $resource->category_label }}</span>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $resource->type_label }}</span>
                    </div>

                    @if($resource->description)
                        <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line mb-6">{{ $resource->description }}</div>
                    @endif

                    {{-- Action area depends on type --}}
                    @if(in_array($resource->type, ['hotline','contact']) && $resource->contact_number)
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-lg p-5 text-center">
                            <p class="text-xs uppercase tracking-wide text-blue-600 font-medium mb-2">Contact Number</p>
                            <a href="tel:{{ preg_replace('/\s+/', '', $resource->contact_number) }}"
                               class="font-mono font-bold text-blue-900 text-2xl hover:underline">
                                {{ $resource->contact_number }}
                            </a>
                            @if($resource->available_hours)
                                <p class="text-sm text-blue-700 mt-2">Available: {{ $resource->available_hours }}</p>
                            @endif
                        </div>
                    @endif

                    @if($resource->url)
                        <a href="{{ $resource->url }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-md mt-4">
                            Open External Link →
                        </a>
                    @endif

                    @if($resource->file_path)
                        <a href="{{ Storage::url($resource->file_path) }}" target="_blank"
                           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2.5 rounded-md mt-4">
                            ⬇ Download File
                        </a>
                    @endif
                </div>

                <div class="border-t border-gray-100 px-6 py-3 flex items-center justify-between text-xs text-gray-400">
                    <div>
                        @if($resource->author)
                            Added by <span class="font-medium text-gray-600">{{ $resource->author->name }}</span> • {{ $resource->created_at->format('M d, Y') }}
                        @endif
                    </div>
                    <div>{{ $resource->view_count }} views</div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
