<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GMS — CHMSU') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">

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

</body>
</html>
