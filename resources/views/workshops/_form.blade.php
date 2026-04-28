@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" required maxlength="200" value="{{ old('title', $workshop->title ?? '') }}"
               class="w-full border-gray-300 rounded-md text-sm">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
        <textarea name="description" rows="5" required class="w-full border-gray-300 rounded-md text-sm">{{ old('description', $workshop->description ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
        <select name="category" class="w-full border-gray-300 rounded-md text-sm">
            @foreach(\App\Models\Workshop::CATEGORIES as $v => $label)
                <option value="{{ $v }}" @selected(old('category', $workshop->category ?? 'mental_health') === $v)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Audience</label>
        <select name="audience" class="w-full border-gray-300 rounded-md text-sm">
            @foreach(['all'=>'Everyone','students'=>'Students Only','staff'=>'Staff Only','faculty'=>'Faculty Only'] as $v=>$label)
                <option value="{{ $v }}" @selected(old('audience', $workshop->audience ?? 'all') === $v)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
        <select name="mode" class="w-full border-gray-300 rounded-md text-sm">
            <option value="in_person" @selected(old('mode', $workshop->mode ?? 'in_person') === 'in_person')>In-Person</option>
            <option value="virtual" @selected(old('mode', $workshop->mode ?? '') === 'virtual')>Virtual</option>
            <option value="hybrid" @selected(old('mode', $workshop->mode ?? '') === 'hybrid')>Hybrid</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" class="w-full border-gray-300 rounded-md text-sm">
            <option value="published" @selected(old('status', $workshop->status ?? 'published') === 'published')>Published</option>
            <option value="draft" @selected(old('status', $workshop->status ?? '') === 'draft')>Draft</option>
            <option value="cancelled" @selected(old('status', $workshop->status ?? '') === 'cancelled')>Cancelled</option>
            <option value="completed" @selected(old('status', $workshop->status ?? '') === 'completed')>Completed</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Venue <span class="text-red-500">*</span></label>
        <input type="text" name="venue" required maxlength="200" value="{{ old('venue', $workshop->venue ?? '') }}"
               placeholder="e.g. Guidance Conference Room, Online via Zoom" class="w-full border-gray-300 rounded-md text-sm">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Meeting Link <span class="text-gray-400">(for virtual/hybrid)</span></label>
        <input type="url" name="meeting_link" value="{{ old('meeting_link', $workshop->meeting_link ?? '') }}"
               placeholder="https://meet.google.com/..." class="w-full border-gray-300 rounded-md text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Starts <span class="text-red-500">*</span></label>
        <input type="datetime-local" name="starts_at" required
               value="{{ old('starts_at', isset($workshop) ? $workshop->starts_at->format('Y-m-d\TH:i') : '') }}"
               class="w-full border-gray-300 rounded-md text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Ends <span class="text-red-500">*</span></label>
        <input type="datetime-local" name="ends_at" required
               value="{{ old('ends_at', isset($workshop) ? $workshop->ends_at->format('Y-m-d\TH:i') : '') }}"
               class="w-full border-gray-300 rounded-md text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Capacity <span class="text-gray-400">(blank = unlimited)</span></label>
        <input type="number" name="capacity" min="1" max="9999" value="{{ old('capacity', $workshop->capacity ?? '') }}"
               class="w-full border-gray-300 rounded-md text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">RSVP Deadline <span class="text-gray-400">(optional)</span></label>
        <input type="datetime-local" name="rsvp_deadline"
               value="{{ old('rsvp_deadline', isset($workshop) && $workshop->rsvp_deadline ? $workshop->rsvp_deadline->format('Y-m-d\TH:i') : '') }}"
               class="w-full border-gray-300 rounded-md text-sm">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">Cover Color</label>
        <div class="flex gap-2 flex-wrap">
            @foreach(\App\Models\Workshop::COVER_COLORS as $color => $gradient)
                <label class="cursor-pointer">
                    <input type="radio" name="cover_color" value="{{ $color }}"
                           @checked(old('cover_color', $workshop->cover_color ?? 'blue') === $color)
                           class="sr-only peer">
                    <div class="w-12 h-10 rounded-lg bg-gradient-to-br {{ $gradient }} ring-offset-2 peer-checked:ring-2 peer-checked:ring-gray-700 transition"></div>
                </label>
            @endforeach
        </div>
    </div>
</div>
