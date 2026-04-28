<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">💬 Messages</h2>
            @if(auth()->user()->isStaff())
                <button onclick="document.getElementById('new-msg').showModal()"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New Message</button>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-xs text-blue-800">
                🔒 Messages are <strong>AES-256 encrypted at rest</strong> and stay within the GMS — they never leave the system. For confidential matters, this is the recommended channel.
            </div>

            {{-- Conversation list --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @forelse($conversations as $c)
                @php
                    $other = $c->otherParty(auth()->user());
                    $last  = $c->lastMessage;
                @endphp
                <a href="{{ route('messages.show', $c) }}"
                   class="block px-5 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition {{ $c->unread_count > 0 ? 'bg-blue-50/50' : '' }}">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                            {{ strtoupper(substr($other->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-0.5">
                                <p class="font-semibold text-gray-900 truncate">{{ $other->name }}</p>
                                <span class="text-xs text-gray-400 flex-shrink-0">{{ $c->last_message_at?->diffForHumans() ?? '' }}</span>
                            </div>
                            <p class="text-xs text-gray-400 mb-1">{{ $other->getRoleDisplayName() }}{{ $c->subject ? ' • ' . $c->subject : '' }}</p>
                            @if($last)
                                <p class="text-sm text-gray-600 truncate">
                                    @if($last->sender_id === auth()->id())
                                        <span class="text-gray-400">You:</span>
                                    @endif
                                    {{ $last->body }}
                                </p>
                            @endif
                        </div>
                        @if($c->unread_count > 0)
                            <span class="bg-blue-600 text-white text-xs font-bold rounded-full min-w-5 h-5 px-1.5 flex items-center justify-center flex-shrink-0">{{ $c->unread_count }}</span>
                        @endif
                    </div>
                </a>
                @empty
                <div class="p-12 text-center">
                    <div class="text-5xl mb-3">💬</div>
                    <p class="text-gray-500 text-sm">No conversations yet.</p>
                    @if(auth()->user()->isStaff())
                        <p class="text-xs text-gray-400 mt-2">Click "+ New Message" to start a conversation with a student.</p>
                    @else
                        <p class="text-xs text-gray-400 mt-2">A counselor will reach out via this channel when needed.</p>
                    @endif
                </div>
                @endforelse

                @if($conversations->hasPages())
                    <div class="px-4 py-3 border-t">{{ $conversations->links() }}</div>
                @endif
            </div>

        </div>
    </div>

    {{-- New conversation modal (staff only) --}}
    @if(auth()->user()->isStaff())
    <dialog id="new-msg" class="rounded-xl shadow-2xl backdrop:bg-black/50 p-0 max-w-xl w-full">
        <form method="POST" action="{{ route('messages.store') }}" class="bg-white">
            @csrf
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 text-lg">New Message</h3>
                <button type="button" onclick="document.getElementById('new-msg').close()" class="text-gray-400 hover:text-gray-600 text-xl">×</button>
            </div>
            <div class="p-6 space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Student <span class="text-red-500">*</span></label>
                    <select name="student_user_id" required class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">— Select student —</option>
                        @foreach($startableStudents as $s)
                            @if($s->user_id)
                                <option value="{{ $s->user_id }}">{{ $s->last_name }}, {{ $s->first_name }} ({{ $s->student_id_number ?? 'No ID' }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject <span class="text-gray-400">(optional)</span></label>
                    <input type="text" name="subject" maxlength="200" placeholder="e.g. Follow-up on our last session"
                           class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                    <textarea name="body" rows="6" required maxlength="5000" placeholder="Type your message…"
                              class="w-full border-gray-300 rounded-md text-sm"></textarea>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('new-msg').close()" class="text-sm px-4 py-2 text-gray-600">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-md">Send</button>
            </div>
        </form>
    </dialog>
    @endif
</x-app-layout>
