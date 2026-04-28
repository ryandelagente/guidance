<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📆 Calendar Subscription</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 text-lg mb-1">Sync your appointments to your calendar app</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Subscribe to your personal feed URL below and your CHMSU appointments + RSVPed workshops will appear in
                    Google Calendar, Outlook, Apple Calendar, or any calendar app — refreshing automatically every few hours.
                </p>
            </div>

            {{-- Feed URL --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Your Personal Feed URL</label>
                <div class="flex gap-2"
                     x-data="{ copied: false, copy() { navigator.clipboard.writeText($refs.url.value); this.copied = true; setTimeout(() => this.copied = false, 2000); } }">
                    <input type="text" readonly value="{{ $feedUrl }}" x-ref="url"
                           class="flex-1 border-gray-300 rounded-md text-xs font-mono bg-gray-50"
                           onclick="this.select()">
                    <button type="button" @click="copy()"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md flex-shrink-0 whitespace-nowrap">
                        <span x-show="!copied">📋 Copy</span>
                        <span x-show="copied" style="display:none">✓ Copied</span>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-2">⚠️ This URL is private. Anyone with it can read your calendar. Keep it secret.</p>

                <details class="mt-4 border border-gray-100 rounded-lg">
                    <summary class="px-4 py-2.5 text-sm font-medium text-gray-600 cursor-pointer hover:bg-gray-50 rounded-lg">
                        How to subscribe (instructions for each calendar app)
                    </summary>
                    <div class="px-4 pb-4 pt-2 space-y-4 text-sm text-gray-600">
                        <div>
                            <p class="font-semibold text-gray-800 mb-1">📅 Google Calendar</p>
                            <ol class="list-decimal list-inside space-y-0.5 text-xs text-gray-500">
                                <li>Open Google Calendar in a browser</li>
                                <li>On the left, click <strong>+ Other calendars</strong> → <strong>From URL</strong></li>
                                <li>Paste your feed URL above and click <strong>Add calendar</strong></li>
                                <li>It will appear under "Other calendars" — refreshes every few hours automatically</li>
                            </ol>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 mb-1">📧 Outlook (Web)</p>
                            <ol class="list-decimal list-inside space-y-0.5 text-xs text-gray-500">
                                <li>Open Outlook calendar</li>
                                <li>Click <strong>Add calendar</strong> → <strong>Subscribe from web</strong></li>
                                <li>Paste your feed URL, name it "CHMSU GMS", click <strong>Import</strong></li>
                            </ol>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 mb-1">🍎 Apple Calendar (Mac/iPhone)</p>
                            <ol class="list-decimal list-inside space-y-0.5 text-xs text-gray-500">
                                <li>Mac: <strong>File</strong> → <strong>New Calendar Subscription</strong></li>
                                <li>iPhone: <strong>Settings</strong> → <strong>Calendar</strong> → <strong>Accounts</strong> → <strong>Add Subscribed Calendar</strong></li>
                                <li>Paste your feed URL</li>
                            </ol>
                        </div>
                    </div>
                </details>
            </div>

            {{-- What's included --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">What's in the feed</h3>
                <ul class="text-sm text-gray-700 space-y-1.5">
                    <li>• <strong>Counseling appointments</strong> assigned to you (excluding cancelled / no-shows)</li>
                    <li>• <strong>Workshops</strong> you organized or RSVPed to attend</li>
                    <li>• Date range: past 30 days + next 90 days (rolling)</li>
                    <li>• Includes meeting links, locations, and student concern summaries</li>
                </ul>
                <p class="text-xs text-gray-400 mt-3">
                    Note: This is a <strong>read-only</strong> feed. Cancelling or rescheduling must still be done inside the GMS — your calendar app will reflect the change at the next refresh.
                </p>
            </div>

            {{-- Regenerate --}}
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-5">
                <h4 class="font-semibold text-amber-900 text-sm mb-2">⚠️ Suspect your feed URL leaked?</h4>
                <p class="text-sm text-amber-800 mb-3">
                    Regenerate your token to immediately invalidate the old URL. Anyone using the old subscription will get a 404 error and won't see new events. You'll need to re-add the new URL to your calendar apps.
                </p>
                <form method="POST" action="{{ route('calendar-feed.regenerate') }}" onsubmit="return confirm('Regenerate token? Existing calendar subscriptions will stop receiving updates and you will need to re-add the new URL.')">
                    @csrf
                    <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                        🔄 Regenerate Token
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
