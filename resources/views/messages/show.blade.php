<x-app-layout>
    <x-slot name="header">
        @php $other = $conversation->otherParty(auth()->user()); @endphp
        <div class="flex items-center gap-3">
            <a href="{{ route('messages.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                {{ strtoupper(substr($other->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="font-semibold text-gray-800 leading-tight">{{ $other->name }}</h2>
                <p class="text-xs text-gray-500">{{ $other->getRoleDisplayName() }}{{ $conversation->subject ? ' • ' . $conversation->subject : '' }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg flex flex-col" style="height: calc(100vh - 200px); min-height: 480px;">

                {{-- Messages --}}
                <div class="flex-1 overflow-y-auto p-5 space-y-3" id="msg-scroll">
                    @forelse($messages as $i => $m)
                    @php
                        $isMe = $m->sender_id === auth()->id();
                        $prevSenderId = $i > 0 ? $messages[$i - 1]->sender_id : null;
                        $showSender   = $prevSenderId !== $m->sender_id;
                        $prev = $i > 0 ? $messages[$i - 1] : null;
                        $isNewDay = !$prev || !$prev->created_at->isSameDay($m->created_at);
                    @endphp

                    @if($isNewDay)
                    <div class="text-center py-2">
                        <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                            {{ $m->created_at->isToday() ? 'Today' : ($m->created_at->isYesterday() ? 'Yesterday' : $m->created_at->format('F d, Y')) }}
                        </span>
                    </div>
                    @endif

                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] {{ $isMe ? 'order-2' : '' }}">
                            @if($showSender && !$isMe)
                                <p class="text-xs text-gray-400 mb-1 ml-1">{{ $m->sender->name }}</p>
                            @endif
                            <div class="rounded-2xl px-4 py-2 {{ $isMe ? 'bg-blue-600 text-white rounded-br-md' : 'bg-gray-100 text-gray-800 rounded-bl-md' }}">
                                <p class="text-sm whitespace-pre-wrap break-words">{{ $m->body }}</p>
                            </div>
                            <div class="flex items-center gap-1.5 mt-1 px-1 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                <span class="text-[10px] text-gray-400">{{ $m->created_at->format('h:i A') }}</span>
                                @if($isMe && $m->read_at)
                                    <span class="text-[10px] text-blue-500" title="Read {{ $m->read_at->format('M d, h:i A') }}">✓✓ Read</span>
                                @elseif($isMe)
                                    <span class="text-[10px] text-gray-400">✓ Sent</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 text-sm py-12">
                        <div class="text-4xl mb-2">💬</div>
                        No messages yet. Start the conversation below.
                    </div>
                    @endforelse
                </div>

                {{-- Reply form --}}
                <form method="POST" action="{{ route('messages.reply', $conversation) }}" class="border-t border-gray-100 p-3">
                    @csrf
                    <div class="flex items-end gap-2">
                        <textarea name="body" required rows="2" maxlength="5000" placeholder="Type your message…"
                                  class="flex-1 border-gray-200 rounded-lg text-sm resize-none focus:border-blue-500 focus:ring-blue-500"
                                  onkeydown="if(event.key==='Enter' && (event.metaKey||event.ctrlKey)) this.form.submit();"></textarea>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg flex-shrink-0">
                            Send
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Press <kbd class="bg-gray-100 px-1 rounded">Ctrl/⌘</kbd> + <kbd class="bg-gray-100 px-1 rounded">Enter</kbd> to send</p>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom on load
        const scroller = document.getElementById('msg-scroll');
        if (scroller) scroller.scrollTop = scroller.scrollHeight;
    </script>
</x-app-layout>
