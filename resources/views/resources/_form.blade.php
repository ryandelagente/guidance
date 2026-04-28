@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" required value="{{ old('title', $resource->title ?? '') }}"
               class="w-full border-gray-300 rounded-md text-sm">
        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
        <select name="type" class="w-full border-gray-300 rounded-md text-sm">
            @foreach(\App\Models\Resource::TYPES as $v => $label)
                <option value="{{ $v }}" @selected(old('type', $resource->type ?? 'article') === $v)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
        <select name="category" class="w-full border-gray-300 rounded-md text-sm">
            @foreach(\App\Models\Resource::CATEGORIES as $v => $label)
                <option value="{{ $v }}" @selected(old('category', $resource->category ?? 'mental_health') === $v)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea name="description" rows="4" class="w-full border-gray-300 rounded-md text-sm">{{ old('description', $resource->description ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">URL <span class="text-gray-400">(for links/videos/articles)</span></label>
        <input type="url" name="url" value="{{ old('url', $resource->url ?? '') }}" placeholder="https://..."
               class="w-full border-gray-300 rounded-md text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number <span class="text-gray-400">(for hotlines)</span></label>
        <input type="text" name="contact_number" value="{{ old('contact_number', $resource->contact_number ?? '') }}"
               class="w-full border-gray-300 rounded-md text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Available Hours</label>
        <input type="text" name="available_hours" value="{{ old('available_hours', $resource->available_hours ?? '') }}"
               placeholder="e.g. 24/7 or Mon-Fri 8am–5pm" class="w-full border-gray-300 rounded-md text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $resource->sort_order ?? 100) }}" min="0" max="9999"
               class="w-full border-gray-300 rounded-md text-sm">
        <p class="text-xs text-gray-400 mt-0.5">Lower = appears first</p>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">File Upload <span class="text-gray-400">(PDF, image, doc — max 10MB)</span></label>
        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="text-sm">
        @if(isset($resource) && $resource->file_path)
            <p class="text-xs text-gray-500 mt-1">Current: <a href="{{ Storage::url($resource->file_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ basename($resource->file_path) }}</a></p>
        @endif
    </div>

    <div class="flex items-center gap-6 md:col-span-2">
        <label class="inline-flex items-center text-sm">
            <input type="checkbox" name="is_emergency" value="1"
                   @checked(old('is_emergency', $resource->is_emergency ?? false))
                   class="rounded border-gray-300 mr-2">
            🚨 Emergency resource (pin to top)
        </label>
        <label class="inline-flex items-center text-sm">
            <input type="checkbox" name="is_published" value="1"
                   @checked(old('is_published', $resource->is_published ?? true))
                   class="rounded border-gray-300 mr-2">
            Publish immediately
        </label>
    </div>
</div>
