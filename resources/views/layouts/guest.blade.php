<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GMS — CHMSU') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-50">

<div class="min-h-screen flex flex-col lg:flex-row">

    {{-- ── Left: Brand panel (hidden on mobile) ── --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-red-900 via-red-800 to-red-950 text-white relative overflow-hidden">
        {{-- Decorative pattern --}}
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;"></div>

        {{-- Decorative shape --}}
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-red-700 rounded-full opacity-30"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-red-950 rounded-full opacity-50"></div>

        <div class="relative z-10 flex flex-col justify-between p-12 w-full">
            {{-- Logo header --}}
            <div class="flex items-center gap-3">
                <img src="https://chmsualumni.org/images/chmsu-logo.png"
                     alt="CHMSU"
                     class="w-14 h-14 object-contain rounded bg-white/10 p-1"
                     onerror="this.style.display='none'">
                <div>
                    <h2 class="font-bold text-lg leading-tight">CHMSU</h2>
                    <p class="text-xs text-red-200">Guidance Management System</p>
                </div>
            </div>

            {{-- Hero text --}}
            <div>
                <h1 class="font-bold text-4xl leading-tight mb-4">
                    Care that's<br>
                    <span class="text-red-200">always within reach.</span>
                </h1>
                <p class="text-red-100 leading-relaxed max-w-md">
                    A unified platform for student profiling, counseling, mental wellness, and clearance —
                    built for the Carlos Hilado Memorial State University Guidance Office.
                </p>

                {{-- Feature pills --}}
                <div class="grid grid-cols-2 gap-2 mt-8 max-w-md">
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur rounded-lg px-3 py-2 text-sm">
                        <span>📅</span> Online Appointments
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur rounded-lg px-3 py-2 text-sm">
                        <span>💭</span> Wellness Check-ins
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur rounded-lg px-3 py-2 text-sm">
                        <span>🎓</span> Career Workshops
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur rounded-lg px-3 py-2 text-sm">
                        <span>🔒</span> Confidential & Secure
                    </div>
                </div>
            </div>

            {{-- Anonymous concern callout --}}
            <div class="bg-white/10 backdrop-blur rounded-xl p-4 max-w-md">
                <p class="text-xs text-red-200 uppercase tracking-wider font-medium mb-1">Need help anonymously?</p>
                <p class="text-sm leading-relaxed">
                    You don't need an account to speak up.
                    <a href="{{ route('anonymous-concerns.create') }}" class="font-semibold underline hover:text-white">
                        Submit an anonymous concern →
                    </a>
                </p>
            </div>
        </div>
    </div>

    {{-- ── Right: Auth form ── --}}
    <div class="flex-1 flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16 xl:px-24">

        {{-- Mobile-only logo --}}
        <div class="flex lg:hidden justify-center mb-8">
            <a href="/" class="flex items-center gap-3">
                <x-application-logo class="w-12 h-12" />
                <div>
                    <h2 class="font-bold text-gray-900">CHMSU GMS</h2>
                    <p class="text-xs text-gray-500">Guidance Management System</p>
                </div>
            </a>
        </div>

        <div class="w-full max-w-md mx-auto">
            {{ $slot }}

            {{-- Mobile-only anonymous concern link --}}
            <div class="lg:hidden mt-8 text-center bg-blue-50 border border-blue-100 rounded-lg p-4">
                <p class="text-xs text-blue-800 mb-1">Need help anonymously?</p>
                <a href="{{ route('anonymous-concerns.create') }}" class="text-sm text-blue-700 hover:underline font-semibold">
                    Submit a concern without signing in →
                </a>
            </div>

            <p class="text-center text-xs text-gray-400 mt-8">
                © {{ date('Y') }} Carlos Hilado Memorial State University<br>
                Guidance and Counseling Office
            </p>
        </div>
    </div>

</div>

</body>
</html>
