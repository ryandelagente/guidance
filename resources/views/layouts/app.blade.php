<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GMS — CHMSU') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#7f1d1d">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="CHMSU GMS">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Apply saved theme before paint to prevent flash
        try {
            const t = localStorage.getItem('theme');
            if (t === 'dark' || (t !== 'light' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        } catch (e) {}
    </script>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">

<div class="flex h-screen overflow-hidden" x-data="{ open: false }">

    {{-- ── Mobile overlay ─────────────────────────────────── --}}
    <div x-show="open"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 z-20 bg-black/50 lg:hidden"
         style="display:none"></div>

    {{-- ── Sidebar ─────────────────────────────────────────── --}}
    {{-- Desktop: always visible (lg:translate-x-0 overrides -translate-x-full)   --}}
    {{-- Mobile:  starts hidden, slides in when open = true                        --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-64 -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out"
           :class="{ 'translate-x-0': open }">
        @include('layouts.navigation')
    </aside>

    {{-- ── Main column ─────────────────────────────────────── --}}
    {{-- lg:ml-64 offsets the content on desktop where sidebar is always visible   --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden lg:ml-64">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 flex items-center gap-3 px-4 sm:px-6 py-3 flex-shrink-0 shadow-sm">

            {{-- Hamburger — mobile only --}}
            <button @click="open = !open"
                    class="text-gray-500 hover:text-gray-700 focus:outline-none lg:hidden"
                    aria-label="Open sidebar">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page title / header slot --}}
            @isset($header)
                <div class="flex-1 min-w-0">{{ $header }}</div>
            @endisset

            {{-- Global Search --}}
            @auth
            <div x-data="{
                    q: '',
                    open: false,
                    loading: false,
                    groups: [],
                    timer: null,
                    selectedIdx: -1,
                    flatItems() { return this.groups.flatMap(g => g.items); },
                    async search() {
                        clearTimeout(this.timer);
                        if (this.q.trim().length < 2) { this.groups = []; this.open = false; return; }
                        this.timer = setTimeout(async () => {
                            this.loading = true;
                            this.open = true;
                            try {
                                const r = await fetch('{{ route('search.quick') }}?q=' + encodeURIComponent(this.q));
                                const d = await r.json();
                                this.groups = d.groups || [];
                                this.selectedIdx = -1;
                            } catch(e) { this.groups = []; }
                            this.loading = false;
                        }, 200);
                    },
                    handleKey(e) {
                        if (!this.open) return;
                        const items = this.flatItems();
                        if (e.key === 'ArrowDown') { e.preventDefault(); this.selectedIdx = Math.min(this.selectedIdx + 1, items.length - 1); }
                        if (e.key === 'ArrowUp')   { e.preventDefault(); this.selectedIdx = Math.max(this.selectedIdx - 1, 0); }
                        if (e.key === 'Enter' && this.selectedIdx >= 0) {
                            e.preventDefault();
                            window.location = items[this.selectedIdx].url;
                        }
                        if (e.key === 'Escape') { this.open = false; this.q = ''; }
                    }
                 }"
                 @keydown.window.cmd-k.prevent="$refs.searchInput.focus()"
                 @keydown.window.ctrl-k.prevent="$refs.searchInput.focus()"
                 @click.outside="open = false"
                 class="relative hidden sm:block flex-shrink-0">
                <div class="relative">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="q" x-ref="searchInput"
                           @input="search()"
                           @focus="if (groups.length) open = true"
                           @keydown="handleKey($event)"
                           placeholder="Search students, appointments, referrals…"
                           class="w-64 lg:w-80 pl-8 pr-12 py-1.5 text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-500 bg-gray-50">
                    <kbd class="absolute right-2 top-1/2 -translate-y-1/2 hidden lg:block text-[10px] font-mono text-gray-400 bg-white border border-gray-200 px-1.5 py-0.5 rounded">⌘K</kbd>
                </div>

                {{-- Results dropdown --}}
                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-lg border border-gray-200 max-h-[70vh] overflow-y-auto z-50"
                     style="display:none">

                    <template x-if="loading && groups.length === 0">
                        <div class="px-4 py-6 text-center text-sm text-gray-400">Searching…</div>
                    </template>

                    <template x-if="!loading && groups.length === 0">
                        <div class="px-4 py-6 text-center text-sm text-gray-400">
                            <div class="text-3xl mb-1">🔎</div>
                            No results for "<span x-text="q"></span>"
                        </div>
                    </template>

                    <template x-for="(group, gi) in groups" :key="gi">
                        <div class="border-b border-gray-100 last:border-b-0">
                            <div class="px-4 py-1.5 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 flex items-center gap-2">
                                <span x-text="group.icon"></span>
                                <span x-text="group.label"></span>
                            </div>
                            <template x-for="(item, ii) in group.items" :key="ii">
                                <a :href="item.url"
                                   :class="{ 'bg-blue-50': flatItems().findIndex(x => x === item) === selectedIdx }"
                                   class="block px-4 py-2.5 hover:bg-gray-50 transition">
                                    <div class="text-sm font-medium text-gray-800" x-text="item.title"></div>
                                    <div class="text-xs text-gray-500 mt-0.5 truncate" x-text="item.subtitle"></div>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
            @endauth

            {{-- Dark mode toggle --}}
            @auth
            <button onclick="window.toggleTheme()" title="Toggle dark mode"
                    class="p-1.5 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition flex-shrink-0">
                <svg class="h-5 w-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
                <svg class="h-5 w-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>

            {{-- Help icon --}}
            <a href="{{ route('help.index') }}" title="Help & User Guide"
               class="p-1.5 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition flex-shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </a>
            @endauth

            {{-- Notification Bell --}}
            @auth
            <div x-data="{
                    count: 0,
                    details: {},
                    show: false,
                    async load() {
                        try {
                            const r = await fetch('{{ route('notifications.counts') }}');
                            const d = await r.json();
                            this.count = d.total ?? 0;
                            this.details = d;
                        } catch(e) {}
                    }
                }"
                 x-init="load()"
                 class="relative flex-shrink-0">

                <button @click="show = !show; if(show) load()"
                        class="relative p-1.5 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span x-show="count > 0"
                          x-text="count > 9 ? '9+' : count"
                          class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center leading-none"
                          style="display:none"></span>
                </button>

                <div x-show="show"
                     @click.outside="show = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute right-0 top-full mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-lg z-50 py-2"
                     style="display:none">

                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-700">Notifications</p>
                    </div>

                    <template x-if="count === 0">
                        <p class="px-4 py-4 text-sm text-gray-400 text-center">All caught up!</p>
                    </template>

                    <template x-if="details.pendingReferrals > 0">
                        <a href="{{ route('referrals.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                            <span class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-sm flex-shrink-0">🔔</span>
                            <div>
                                <p class="text-sm font-medium text-gray-800" x-text="details.pendingReferrals + ' pending referral' + (details.pendingReferrals > 1 ? 's' : '')"></p>
                                <p class="text-xs text-gray-400">Requires attention</p>
                            </div>
                        </a>
                    </template>

                    <template x-if="details.todayAppointments > 0">
                        <a href="{{ route('appointments.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                            <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm flex-shrink-0">📅</span>
                            <div>
                                <p class="text-sm font-medium text-gray-800" x-text="details.todayAppointments + ' appointment' + (details.todayAppointments > 1 ? 's' : '') + ' today'"></p>
                                <p class="text-xs text-gray-400">Scheduled for today</p>
                            </div>
                        </a>
                    </template>

                    <template x-if="details.pendingClearance > 0">
                        <a href="{{ route('clearance.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                            <span class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-sm flex-shrink-0">✅</span>
                            <div>
                                <p class="text-sm font-medium text-gray-800" x-text="details.pendingClearance + ' clearance request' + (details.pendingClearance > 1 ? 's' : '') + ' pending'"></p>
                                <p class="text-xs text-gray-400">Waiting for processing</p>
                            </div>
                        </a>
                    </template>

                    <template x-if="details.unreadMessages > 0">
                        <a href="{{ route('messages.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                            <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm flex-shrink-0">💬</span>
                            <div>
                                <p class="text-sm font-medium text-gray-800" x-text="details.unreadMessages + ' unread message' + (details.unreadMessages > 1 ? 's' : '')"></p>
                                <p class="text-xs text-gray-400">Click to read</p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
            @endauth
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>

    </div>
</div>

{{-- ── Cookie consent banner (PH-DPA notice) ── --}}
<div x-data="{ shown: !localStorage.getItem('chmsu_cookie_consent') }" x-show="shown" x-cloak
     class="fixed bottom-4 left-4 right-4 sm:left-auto sm:max-w-md z-50 bg-white border border-gray-200 rounded-xl shadow-2xl p-5">
    <h4 class="font-semibold text-gray-900 text-sm mb-1">🍪 Privacy notice</h4>
    <p class="text-xs text-gray-600 leading-relaxed mb-3">
        CHMSU GMS uses essential cookies to keep you signed in and secure your session. We don't use tracking cookies.
        By continuing, you acknowledge our handling of your data under the
        <strong>Philippine Data Privacy Act (RA 10173)</strong>.
        @auth
        Visit <a href="{{ route('data-privacy.index') }}" class="text-blue-600 hover:underline">My Data & Privacy</a> for details.
        @endauth
    </p>
    <div class="flex gap-2">
        <button @click="localStorage.setItem('chmsu_cookie_consent', '1'); shown = false"
                class="flex-1 bg-red-700 hover:bg-red-800 text-white text-xs font-medium py-1.5 rounded">
            Got it
        </button>
    </div>
</div>

{{-- ── Service worker registration for PWA ── --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('{{ asset('service-worker.js') }}').catch(() => {});
        });
    }

    // Dark mode toggle helper exposed globally
    window.toggleTheme = function() {
        const isDark = document.documentElement.classList.toggle('dark');
        try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch(e) {}
    };
</script>

</body>
</html>
