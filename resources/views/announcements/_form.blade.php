@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $announcement->title ?? '') }}" required
               class="w-full border-gray-300 rounded-md text-sm">
        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Body <span class="text-red-500">*</span></label>
        <textarea name="body" rows="8" required class="w-full border-gray-300 rounded-md text-sm">{{ old('body', $announcement->body ?? '') }}</textarea>
        @error('body')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Audience</label>
        <select name="audience" class="w-full border-gray-300 rounded-md text-sm">
            @foreach(['all'=>'Everyone','students'=>'Students Only','staff'=>'Staff Only','counselors'=>'Counselors Only','faculty'=>'Faculty Only'] as $v=>$l)
                <option value="{{ $v }}" @selected(old('audience', $announcement->audience ?? 'all') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
        <select name="priority" class="w-full border-gray-300 rounded-md text-sm">
            @foreach(['info'=>'ℹ️ Info','warning'=>'⚠️ Warning','urgent'=>'🚨 Urgent'] as $v=>$l)
                <option value="{{ $v }}" @selected(old('priority', $announcement->priority ?? 'info') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Expires On (optional)</label>
        <input type="date" name="expires_at"
               value="{{ old('expires_at', isset($announcement->expires_at) ? $announcement->expires_at->format('Y-m-d') : '') }}"
               class="w-full border-gray-300 rounded-md text-sm">
    </div>
    <div class="flex items-end gap-4">
        <label class="inline-flex items-center text-sm">
            <input type="checkbox" name="is_pinned" value="1"
                   @checked(old('is_pinned', $announcement->is_pinned ?? false))
                   class="rounded border-gray-300 mr-2"> Pin to top
        </label>
        <label class="inline-flex items-center text-sm">
            <input type="checkbox" name="is_published" value="1"
                   @checked(old('is_published', $announcement->is_published ?? true))
                   class="rounded border-gray-300 mr-2"> Publish immediately
        </label>
    </div>
</div>
