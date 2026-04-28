<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CHMSU Guidance Management System</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        body { font-family: 'Figtree', sans-serif; }
        .hero-bg {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 40%, #7c2d12 100%);
        }
        .feature-card:hover { transform: translateY(-3px); }
        .feature-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    </style>
</head>
<body class="antialiased bg-white text-gray-900">

{{-- ── Top Navigation ────────────────────────────────────────── --}}
<header class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <img src="https://chmsualumni.org/images/chmsu-logo.png"
                     alt="CHMSU"
                     class="h-10 w-10 object-contain"
                     onerror="this.style.display='none'">
                <div>
                    <p class="font-bold text-gray-900 text-sm leading-tight">CHMSU</p>
                    <p class="text-xs text-gray-500 leading-tight">Guidance Management System</p>
                </div>
            </div>

            {{-- Auth Buttons --}}
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="bg-red-800 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('anonymous-concerns.create') }}"
                       class="text-blue-600 hover:text-blue-700 text-sm font-medium px-3 py-2 rounded-lg transition">
                        🤝 Speak Up
                    </a>
                    <a href="{{ route('login') }}"
                       class="text-gray-600 hover:text-gray-900 text-sm font-medium px-3 py-2 rounded-lg transition">
                        Sign In
                    </a>
                    @if(Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="bg-red-800 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        Get Started
                    </a>
                    @endif
                @endauth
            </div>

        </div>
    </div>
</header>

{{-- ── Hero ──────────────────────────────────────────────────── --}}
<section class="hero-bg text-white py-20 sm:py-28 relative overflow-hidden">
    {{-- Decorative circles --}}
    <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="max-w-3xl">
            <div class="flex items-center gap-4 mb-8">
                <img src="https://chmsualumni.org/images/chmsu-logo.png"
                     alt="CHMSU"
                     class="h-20 w-20 object-contain bg-white/10 rounded-2xl p-2"
                     onerror="this.style.display='none'">
                <div>
                    <p class="text-red-200 text-sm font-medium tracking-widest uppercase">Carlos Hilado Memorial State University</p>
                    <p class="text-white/80 text-sm mt-0.5">Guidance &amp; Counseling Office</p>
                </div>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                Guidance<br>
                <span class="text-yellow-300">Management System</span>
            </h1>
            <p class="text-lg text-red-100 mb-8 max-w-xl leading-relaxed">
                A comprehensive digital platform for student counseling, referrals,
                psychological testing, and graduation clearance — all in one secure system.
            </p>
            <div class="flex flex-wrap gap-3">
                @auth
                <a href="{{ url('/dashboard') }}"
                   class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-semibold px-6 py-3 rounded-xl transition shadow-lg">
                    Open Dashboard →
                </a>
                @else
                <a href="{{ route('login') }}"
                   class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-semibold px-6 py-3 rounded-xl transition shadow-lg">
                    Sign In to GMS →
                </a>
                <a href="#features"
                   class="bg-white/10 hover:bg-white/20 text-white font-medium px-6 py-3 rounded-xl transition border border-white/20">
                    Learn More
                </a>
                @endauth
            </div>
        </div>
    </div>
</section>

{{-- ── Stats Strip ───────────────────────────────────────────── --}}
<section class="bg-red-900 text-white py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
            @foreach([
                ['icon' => '👥', 'label' => 'Student Profiles',    'desc' => 'Cumulative records'],
                ['icon' => '📅', 'label' => 'Online Booking',       'desc' => 'Self-service scheduling'],
                ['icon' => '🔒', 'label' => 'Encrypted Notes',      'desc' => 'AES-256 case notes'],
                ['icon' => '📈', 'label' => 'CHED-Ready Reports',   'desc' => 'One-click exports'],
            ] as $stat)
            <div>
                <p class="text-3xl mb-1">{{ $stat['icon'] }}</p>
                <p class="font-semibold text-sm text-white">{{ $stat['label'] }}</p>
                <p class="text-xs text-red-300 mt-0.5">{{ $stat['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Features ──────────────────────────────────────────────── --}}
<section id="features" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <p class="text-red-700 font-semibold text-sm uppercase tracking-wider mb-2">What's Inside</p>
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Everything the Guidance Office Needs</h2>
            <p class="text-gray-500 mt-3 max-w-2xl mx-auto">
                Built specifically for CHMSU's Guidance and Counseling Office, covering every workflow
                from student intake to graduation clearance.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $features = [
                [
                    'icon'  => '👤',
                    'title' => 'Student Profiling',
                    'desc'  => 'Digital cumulative records with family background, academic standing, and document vault. Links directly to the SIS to identify at-risk students.',
                    'color' => 'border-blue-500',
                    'bg'    => 'bg-blue-50',
                    'tc'    => 'text-blue-700',
                ],
                [
                    'icon'  => '📅',
                    'title' => 'Appointment & Scheduling',
                    'desc'  => 'Students self-book counseling sessions using a live availability calendar. Counselors manage their schedule slots and get notified of bookings.',
                    'color' => 'border-indigo-500',
                    'bg'    => 'bg-indigo-50',
                    'tc'    => 'text-indigo-700',
                ],
                [
                    'icon'  => '📝',
                    'title' => 'Encrypted Case Notes',
                    'desc'  => 'AES-256 encrypted session notes that only the assigned counselor can decrypt. Confidentiality is enforced at the database level.',
                    'color' => 'border-violet-500',
                    'bg'    => 'bg-violet-50',
                    'tc'    => 'text-violet-700',
                ],
                [
                    'icon'  => '🔔',
                    'title' => 'Faculty Referral System',
                    'desc'  => 'Faculty flag students via a standardized form. Counselors log interventions and update status while keeping case details confidential.',
                    'color' => 'border-orange-500',
                    'bg'    => 'bg-orange-50',
                    'tc'    => 'text-orange-700',
                ],
                [
                    'icon'  => '⚠️',
                    'title' => 'Disciplinary Records',
                    'desc'  => 'Track minor and major offenses, sanctions, and resolution status. Linked to the student\'s cumulative record for full history.',
                    'color' => 'border-red-500',
                    'bg'    => 'bg-red-50',
                    'tc'    => 'text-red-700',
                ],
                [
                    'icon'  => '🧪',
                    'title' => 'Psychological Testing',
                    'desc'  => 'Manage your test inventory, schedule batch testing sessions by college or year level, and securely record and release results to students.',
                    'color' => 'border-cyan-500',
                    'bg'    => 'bg-cyan-50',
                    'tc'    => 'text-cyan-700',
                ],
                [
                    'icon'  => '✅',
                    'title' => 'Graduation Clearance',
                    'desc'  => 'Students submit clearance requests online. Graduation clearances are gated behind an automated exit interview survey before approval.',
                    'color' => 'border-green-500',
                    'bg'    => 'bg-green-50',
                    'tc'    => 'text-green-700',
                ],
                [
                    'icon'  => '📜',
                    'title' => 'Good Moral Certificates',
                    'desc'  => 'Generate professional Good Moral Character certificates with auto-numbering, validity tracking, and browser-native PDF printing.',
                    'color' => 'border-emerald-500',
                    'bg'    => 'bg-emerald-50',
                    'tc'    => 'text-emerald-700',
                ],
                [
                    'icon'  => '📈',
                    'title' => 'Analytics & CHED Reports',
                    'desc'  => 'Real-time charts on demographics, appointment trends, and counseling concerns. One-click CSV exports for CHED accomplishment reports.',
                    'color' => 'border-purple-500',
                    'bg'    => 'bg-purple-50',
                    'tc'    => 'text-purple-700',
                ],
            ];
            @endphp

            @foreach($features as $f)
            <div class="feature-card bg-white rounded-2xl shadow-sm border-l-4 {{ $f['color'] }} p-6 hover:shadow-md">
                <div class="w-12 h-12 {{ $f['bg'] }} rounded-xl flex items-center justify-center text-2xl mb-4">
                    {{ $f['icon'] }}
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $f['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Role Overview ─────────────────────────────────────────── --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <p class="text-red-700 font-semibold text-sm uppercase tracking-wider mb-2">Who Uses It</p>
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Designed for Every Role</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            @php
            $roles = [
                [
                    'icon'  => '🛡',
                    'title' => 'Super Admin',
                    'color' => 'bg-red-600',
                    'items' => ['Manage user accounts', 'Assign roles', 'Full system access', 'Audit all modules'],
                ],
                [
                    'icon'  => '🎓',
                    'title' => 'Guidance Director',
                    'color' => 'bg-purple-700',
                    'items' => ['Department oversight', 'Aggregate analytics', 'All counselor reports', 'Critical referral alerts'],
                ],
                [
                    'icon'  => '💼',
                    'title' => 'Counselor',
                    'color' => 'bg-teal-700',
                    'items' => ['Assigned student records', 'Case note writing', 'Testing & results', 'Clearance processing'],
                ],
                [
                    'icon'  => '📚',
                    'title' => 'Faculty / Staff',
                    'color' => 'bg-slate-700',
                    'items' => ['Submit referrals', 'Track referral status', 'No confidential data access'],
                ],
                [
                    'icon'  => '🎒',
                    'title' => 'Student',
                    'color' => 'bg-blue-700',
                    'items' => ['Book appointments', 'View test results', 'Request clearance', 'Exit survey'],
                ],
            ];
            @endphp
            @foreach($roles as $r)
            <div class="rounded-2xl overflow-hidden shadow-sm border border-gray-100">
                <div class="{{ $r['color'] }} text-white px-4 py-5 text-center">
                    <p class="text-3xl mb-1">{{ $r['icon'] }}</p>
                    <p class="font-bold text-sm">{{ $r['title'] }}</p>
                </div>
                <div class="bg-white px-4 py-4">
                    <ul class="space-y-2">
                        @foreach($r['items'] as $item)
                        <li class="flex items-start gap-2 text-xs text-gray-600">
                            <span class="text-green-500 font-bold mt-0.5">✓</span>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── CTA ───────────────────────────────────────────────────── --}}
<section class="hero-bg text-white py-16 text-center">
    <div class="max-w-2xl mx-auto px-4">
        <img src="https://chmsualumni.org/images/chmsu-logo.png"
             alt="CHMSU"
             class="h-16 w-16 object-contain mx-auto mb-6 bg-white/10 rounded-2xl p-2"
             onerror="this.style.display='none'">
        <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
        <p class="text-red-200 mb-8">
            Log in with your CHMSU credentials to access the Guidance Management System.
        </p>
        @auth
        <a href="{{ url('/dashboard') }}"
           class="inline-block bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold px-8 py-3 rounded-xl transition shadow-lg">
            Go to Your Dashboard →
        </a>
        @else
        <a href="{{ route('login') }}"
           class="inline-block bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold px-8 py-3 rounded-xl transition shadow-lg">
            Sign In Now →
        </a>
        @endauth
    </div>
</section>

{{-- ── Footer ────────────────────────────────────────────────── --}}
<footer class="bg-gray-900 text-gray-400 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="https://chmsualumni.org/images/chmsu-logo.png"
                     alt="CHMSU"
                     class="h-8 w-8 object-contain opacity-70"
                     onerror="this.style.display='none'">
                <div>
                    <p class="text-white font-semibold text-sm">CHMSU Guidance Management System</p>
                    <p class="text-xs mt-0.5">Carlos Hilado Memorial State University &mdash; Guidance &amp; Counseling Office</p>
                </div>
            </div>
            <p class="text-xs text-center sm:text-right">
                &copy; {{ date('Y') }} CHMSU. Built with Laravel &amp; Tailwind CSS.
            </p>
        </div>
    </div>
</footer>

</body>
</html>
