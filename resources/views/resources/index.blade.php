<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">📚 Resource Library</h2>
            @if($isStaff)
                <a href="{{ route('resources.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ Add Resource</a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- ── Crisis Hotlines (always pinned at top) ── --}}
            @if($emergency->isNotEmpty())
            <div class="bg-gradient-to-r from-red-50 to-orange-50 border-2 border-red-200 rounded-xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">🚨</span>
                    <h3 class="font-bold text-red-900 text-lg">Need Help Right Now?</h3>
                </div>
                <p class="text-sm text-red-800 mb-4">If you or someone you know is in crisis, reach out immediately. These hotlines are free, confidential, and available 24/7.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($emergency as $hotline)
                    <a href="{{ route('resources.show', $hotline) }}"
                       class="bg-white rounded-lg p-4 border border-red-100 hover:shadow-md hover:border-red-300 transition group">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl flex-shrink-0">{{ $hotline->type === 'hotline' ? '☎️' : '🆘' }}</span>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-900 text-sm group-hover:text-red-700">{{ $hotline->title }}</div>
                                @if($hotline->contact_number)
                                    <div class="font-mono text-red-600 font-bold text-base mt-1">{{ $hotline->contact_number }}</div>
                                @endif
                                @if($hotline->available_hours)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $hotline->available_hours }}</div>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── Filters ── --}}
            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-48">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search resources…"
                           class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                    <select name="category" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All Categories</option>
                        @foreach(\App\Models\Resource::CATEGORIES as $v => $label)
                            <option value="{{ $v }}" @selected(request('category') === $v)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <select name="type" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All Types</option>
                        @foreach(\App\Models\Resource::TYPES as $v => $label)
                            <option value="{{ $v }}" @selected(request('type') === $v)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Search</button>
                <a href="{{ route('resources.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            {{-- ── Grouped Resources ── --}}
            @forelse($grouped as $category => $items)
            @php $catLabel = \App\Models\Resource::CATEGORIES[$category] ?? ucfirst($category); @endphp
            <div>
                <h3 class="font-semibold text-gray-700 text-base mb-3 px-1">{{ $catLabel }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($items as $r)
                    <a href="{{ route('resources.show', $r) }}"
                       class="bg-white shadow-sm rounded-lg p-4 hover:shadow-md transition group flex items-start gap-3">
                        <div class="text-2xl flex-shrink-0">{{ explode(' ', $r->type_label)[0] }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-medium text-gray-900 group-hover:text-blue-600">{{ $r->title }}</h4>
                                @if(!$r->is_published)
                                    <span class="text-xs bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded">Draft</span>
                                @endif
                            </div>
                            @if($r->description)
                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $r->description }}</p>
                            @endif
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                                <span>{{ $r->type_label }}</span>
                                <span>•</span>
                                <span>{{ $r->view_count }} views</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="bg-white shadow-sm rounded-lg p-12 text-center">
                <div class="text-5xl mb-3">📚</div>
                <p class="text-gray-400 text-sm">No resources found.</p>
            </div>
            @endforelse

            @if($resources->hasPages())
                <div class="bg-white px-4 py-3 rounded-lg shadow-sm">{{ $resources->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
