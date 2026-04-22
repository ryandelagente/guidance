{{-- Left Sidebar Navigation --}}
<div class="h-full flex flex-col bg-red-900 text-white overflow-hidden">

    {{-- ── Brand ─────────────────────────────────────────────────── --}}
    <a href="{{ route('dashboard') }}"
       class="flex items-center gap-3 px-4 py-4 border-b border-red-800 hover:bg-red-800 transition flex-shrink-0">
        <img src="https://chmsualumni.org/images/chmsu-logo.png"
             alt="CHMSU Logo"
             class="h-10 w-10 object-contain rounded bg-white/10 p-0.5 flex-shrink-0"
             onerror="this.style.display='none'">
        <div class="min-w-0">
            <p class="font-bold text-sm leading-tight text-white tracking-wide">CHMSU</p>
            <p class="text-xs text-red-300 leading-tight truncate">Guidance Management System</p>
        </div>
    </a>

    {{-- ── Scrollable Nav ───────────────────────────────────────── --}}
    <nav class="flex-1 overflow-y-auto px-2 py-3 space-y-0.5 text-sm scrollbar-thin">
        @php
            $role    = auth()->user()?->role;
            $isStaff = in_array($role, ['guidance_counselor','guidance_director','super_admin']);

            // Helper: sidebar link classes
            $lc = fn(bool $active) => 'flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors duration-150 '
                . ($active ? 'bg-white/20 text-white font-semibold' : 'text-red-200 hover:bg-white/10 hover:text-white');

            $sc = fn(bool $active) => 'flex items-center gap-2.5 px-3 py-1.5 rounded-lg transition-colors duration-150 '
                . ($active ? 'bg-white/20 text-white font-semibold' : 'text-red-300 hover:bg-white/10 hover:text-white');
        @endphp

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" class="{{ $lc(request()->routeIs('*.dashboard')) }}">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        {{-- ── ADMIN ─────────────────────────────────────────────── --}}
        @if($role === 'super_admin')
            <p class="pt-4 pb-1 px-3 text-xs font-semibold text-red-400 uppercase tracking-wider">Administration</p>
            <a href="{{ route('admin.users.index') }}" class="{{ $lc(request()->routeIs('admin.users.*')) }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                User Accounts
            </a>
        @endif

        {{-- ── STAFF ─────────────────────────────────────────────── --}}
        @if($isStaff)

            <p class="pt-4 pb-1 px-3 text-xs font-semibold text-red-400 uppercase tracking-wider">Management</p>

            {{-- Students --}}
            <a href="{{ route('students.index') }}" class="{{ $lc(request()->routeIs('students.*')) }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Student Profiles
            </a>

            {{-- Appointments --}}
            <a href="{{ route('appointments.index') }}" class="{{ $lc(request()->routeIs('appointments.*')) }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Appointments
            </a>

            {{-- Case Notes --}}
            <a href="{{ route('sessions.index') }}" class="{{ $lc(request()->routeIs('sessions.*')) }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Case Notes
            </a>

            {{-- Referrals --}}
            <a href="{{ route('referrals.index') }}" class="{{ $lc(request()->routeIs('referrals.*')) }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Referrals
            </a>

            {{-- ── Testing (collapsible) ─── --}}
            <div x-data="{ testOpen: {{ request()->routeIs('psych-tests.*','test-schedules.*','test-results.*') ? 'true' : 'false' }} }">
                <button @click="testOpen = !testOpen"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors duration-150 text-red-200 hover:bg-white/10 hover:text-white">
                    <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <span class="flex-1 text-left">Psychological Testing</span>
                    <svg class="h-3.5 w-3.5 transition-transform duration-150" :class="testOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="testOpen" class="mt-0.5 ml-4 space-y-0.5 border-l border-red-800 pl-2">
                    <a href="{{ route('psych-tests.index') }}" class="{{ $sc(request()->routeIs('psych-tests.*')) }}">
                        <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Test Inventory
                    </a>
                    <a href="{{ route('test-schedules.index') }}" class="{{ $sc(request()->routeIs('test-schedules.*')) }}">
                        <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Testing Sessions
                    </a>
                    <a href="{{ route('test-results.index') }}" class="{{ $sc(request()->routeIs('test-results.*')) }}">
                        <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Test Results
                    </a>
                </div>
            </div>

            {{-- ── Records (collapsible) ─── --}}
            <div x-data="{ recOpen: {{ request()->routeIs('disciplinary.*','clearance.*','certificates.*','schedules.*') ? 'true' : 'false' }} }">
                <button @click="recOpen = !recOpen"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors duration-150 text-red-200 hover:bg-white/10 hover:text-white">
                    <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <span class="flex-1 text-left">Records</span>
                    <svg class="h-3.5 w-3.5 transition-transform duration-150" :class="recOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="recOpen" class="mt-0.5 ml-4 space-y-0.5 border-l border-red-800 pl-2">
                    <a href="{{ route('disciplinary.index') }}" class="{{ $sc(request()->routeIs('disciplinary.*')) }}">
                        <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Disciplinary
                    </a>
                    <a href="{{ route('clearance.index') }}" class="{{ $sc(request()->routeIs('clearance.*')) }}">
                        <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
                        </svg>
                        Clearance
                    </a>
                    <a href="{{ route('certificates.index') }}" class="{{ $sc(request()->routeIs('certificates.*')) }}">
                        <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        Certificates
                    </a>
                    <a href="{{ route('schedules.index') }}" class="{{ $sc(request()->routeIs('schedules.*')) }}">
                        <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/>
                        </svg>
                        My Schedule
                    </a>
                </div>
            </div>

            {{-- Analytics --}}
            <a href="{{ route('analytics.index') }}" class="{{ $lc(request()->routeIs('analytics.*')) }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Analytics & Reports
            </a>

        @endif

        {{-- ── FACULTY ──────────────────────────────────────────── --}}
        @if($role === 'faculty')
            <p class="pt-4 pb-1 px-3 text-xs font-semibold text-red-400 uppercase tracking-wider">Referrals</p>

            <a href="{{ route('referrals.create') }}"
               class="{{ $lc(request()->routeIs('referrals.create')) }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Submit Referral
            </a>
            <a href="{{ route('referrals.index') }}"
               class="{{ $lc(request()->routeIs('referrals.index') || (request()->routeIs('referrals.*') && !request()->routeIs('referrals.create'))) }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                My Referrals
            </a>
        @endif

        {{-- ── STUDENT ──────────────────────────────────────────── --}}
        @if($role === 'student')
            <p class="pt-4 pb-1 px-3 text-xs font-semibold text-red-400 uppercase tracking-wider">My Services</p>

            <a href="{{ route('appointments.index') }}" class="{{ $lc(request()->routeIs('appointments.*')) }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Appointments
            </a>
            <a href="{{ route('test-results.index') }}" class="{{ $lc(request()->routeIs('test-results.*')) }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                My Test Results
            </a>
            <a href="{{ route('clearance.index') }}" class="{{ $lc(request()->routeIs('clearance.*')) }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
                </svg>
                Clearance
            </a>
        @endif

    </nav>

    {{-- ── User Footer ─────────────────────────────────────────── --}}
    <div class="flex-shrink-0 border-t border-red-800 p-3">
        <div class="flex items-center gap-2.5 mb-2.5">
            <div class="w-8 h-8 rounded-full bg-red-700 border border-red-600 flex items-center justify-center text-sm font-bold flex-shrink-0 text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-white truncate leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-xs text-red-300 truncate leading-tight">{{ auth()->user()->getRoleDisplayName() }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('profile.edit') }}"
               class="flex-1 text-center text-xs bg-red-800 hover:bg-red-700 text-red-100 rounded-md px-2 py-1.5 transition">
                Profile
            </a>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full text-xs bg-red-800 hover:bg-red-700 text-red-100 rounded-md px-2 py-1.5 transition">
                    Log Out
                </button>
            </form>
        </div>
    </div>

</div>
