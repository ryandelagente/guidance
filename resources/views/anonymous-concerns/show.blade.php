<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('anonymous-concerns.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Concern {{ $concern->reference_code }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center gap-2 mb-4 flex-wrap">
                    <span class="text-xs px-2 py-0.5 rounded-full border {{ $concern->getUrgencyBadgeClass() }}">
                        {{ $concern->urgency === 'critical' ? '🚨 Critical' : ucfirst($concern->urgency) }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $concern->getStatusBadgeClass() }}">
                        {{ \App\Models\AnonymousConcern::STATUSES[$concern->status] }}
                    </span>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $concern->type_label }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-5">
                    <div>
                        <dt class="text-xs text-gray-500 uppercase">Submitted</dt>
                        <dd class="text-gray-900 mt-1">{{ $concern->created_at->format('F d, Y h:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 uppercase">From</dt>
                        <dd class="text-gray-900 mt-1">{{ $concern->reporter_relationship ? ucfirst($concern->reporter_relationship) : 'Anonymous' }}</dd>
                    </div>
                    @if($concern->about_who)
                    <div>
                        <dt class="text-xs text-gray-500 uppercase">About</dt>
                        <dd class="text-gray-900 mt-1">{{ $concern->about_who }}</dd>
                    </div>
                    @endif
                    @if($concern->location)
                    <div>
                        <dt class="text-xs text-gray-500 uppercase">Location</dt>
                        <dd class="text-gray-900 mt-1">{{ $concern->location }}</dd>
                    </div>
                    @endif
                    @if($concern->contact_email)
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-gray-500 uppercase">Reporter Email <span class="text-gray-400 normal-case">(opted-in)</span></dt>
                        <dd class="text-gray-900 mt-1"><a href="mailto:{{ $concern->contact_email }}" class="text-blue-600 hover:underline">{{ $concern->contact_email }}</a></dd>
                    </div>
                    @endif
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <dt class="text-xs text-gray-500 uppercase mb-2">Description</dt>
                    <dd class="text-sm text-gray-800 leading-relaxed whitespace-pre-line bg-gray-50 rounded-lg p-4 border border-gray-100">{{ $concern->description }}</dd>
                </div>
            </div>

            {{-- Action / Status update --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">Counselor Action</h4>
                <form method="POST" action="{{ route('anonymous-concerns.update', $concern) }}" class="space-y-4">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                            @foreach(\App\Models\AnonymousConcern::STATUSES as $v => $label)
                                <option value="{{ $v }}" @selected($concern->status === $v)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Internal Notes <span class="text-gray-400">(visible to staff only)</span></label>
                        <textarea name="staff_notes" rows="5" class="w-full border-gray-300 rounded-md text-sm">{{ $concern->staff_notes }}</textarea>
                    </div>
                    @if($concern->handler)
                        <p class="text-xs text-gray-500">Handled by <strong>{{ $concern->handler->name }}</strong></p>
                    @endif
                    @if($concern->resolved_at)
                        <p class="text-xs text-green-600">Resolved on {{ $concern->resolved_at->format('F d, Y h:i A') }}</p>
                    @endif
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-md">Save</button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
