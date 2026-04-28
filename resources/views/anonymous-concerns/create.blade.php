<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Submit a Concern Anonymously — CHMSU Guidance</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-indigo-50 min-h-screen">

<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6">

    {{-- Header --}}
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">🤝 Speak Up, Stay Safe</h1>
        <p class="text-sm text-gray-600 mt-2">CHMSU Anonymous Concern Form — Confidential & 100% Anonymous</p>
    </div>

    {{-- Reassurance --}}
    <div class="bg-white rounded-xl shadow-sm p-5 mb-5 border-l-4 border-blue-500">
        <h3 class="font-semibold text-gray-800 mb-2">What this form is for</h3>
        <p class="text-sm text-gray-600 leading-relaxed">
            If you're worried about someone — a friend, classmate, or yourself — but you're not ready to give your name, this is a safe way to let the Guidance Office know.
            Your submission is <strong>anonymous</strong>. We don't track who you are, and your IP is only used to prevent spam (not identification).
        </p>
        <p class="text-sm text-gray-600 leading-relaxed mt-2">
            Reports about <strong>bullying, mental health concerns, self-harm, abuse, or threats</strong> are reviewed by trained counselors within one working day.
        </p>
    </div>

    {{-- Crisis box --}}
    <div class="bg-red-50 border-2 border-red-200 rounded-xl p-5 mb-5">
        <h3 class="font-semibold text-red-900 mb-2">🚨 If someone is in immediate danger right now:</h3>
        <ul class="text-sm text-red-800 space-y-1">
            <li>• Call <strong>NCMH Crisis Hotline 1553</strong> (toll-free, 24/7)</li>
            <li>• Call <strong>In Touch 8893-7603</strong></li>
            <li>• Call <strong>Hopeline 0917-558-4673</strong></li>
            <li>• If life-threatening, dial <strong>911</strong> or go to the nearest hospital</li>
        </ul>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('anonymous-concerns.store') }}" class="bg-white rounded-xl shadow-sm p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">What's the concern about? <span class="text-red-500">*</span></label>
            <select name="concern_type" required class="w-full border-gray-300 rounded-md text-sm">
                <option value="">— Select —</option>
                @foreach(\App\Models\AnonymousConcern::TYPES as $v => $label)
                    <option value="{{ $v }}" @selected(old('concern_type') === $v)>{{ $label }}</option>
                @endforeach
            </select>
            @error('concern_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">How urgent is this? <span class="text-red-500">*</span></label>
            <div class="space-y-2">
                @foreach(\App\Models\AnonymousConcern::URGENCIES as $v => $label)
                <label class="flex items-start gap-3 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-300">
                    <input type="radio" name="urgency" value="{{ $v }}" required @checked(old('urgency') === $v) class="mt-0.5">
                    <span class="text-sm text-gray-700">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Tell us what's happening <span class="text-red-500">*</span></label>
            <textarea name="description" rows="6" required minlength="20" maxlength="5000"
                      placeholder="Describe what you've seen, heard, or experienced. Be as specific as you can with what, when, and where."
                      class="w-full border-gray-300 rounded-md text-sm">{{ old('description') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Min. 20 characters. Don't include your name unless you want to.</p>
            @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Who is this about? <span class="text-gray-400 font-normal">(optional)</span></label>
                <input type="text" name="about_who" maxlength="200" value="{{ old('about_who') }}"
                       placeholder="A name, description, or 'myself'"
                       class="w-full border-gray-300 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Where? <span class="text-gray-400 font-normal">(optional)</span></label>
                <input type="text" name="location" maxlength="200" value="{{ old('location') }}"
                       placeholder="e.g. CCS Building, Canteen, Online"
                       class="w-full border-gray-300 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">You are: <span class="text-gray-400 font-normal">(optional)</span></label>
                <select name="reporter_relationship" class="w-full border-gray-300 rounded-md text-sm">
                    <option value="">— Prefer not to say —</option>
                    <option value="student">A student</option>
                    <option value="faculty">Faculty / Staff</option>
                    <option value="parent">A parent or guardian</option>
                    <option value="friend">A friend or classmate</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email for follow-up <span class="text-gray-400 font-normal">(optional)</span></label>
                <input type="email" name="contact_email" maxlength="200" value="{{ old('contact_email') }}"
                       placeholder="Leave blank to stay fully anonymous"
                       class="w-full border-gray-300 rounded-md text-sm">
                <p class="text-xs text-gray-400 mt-1">Only if you want us to contact you back.</p>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <a href="{{ route('anonymous-concerns.track') }}" class="text-sm text-blue-600 hover:underline">Already submitted? Track your report →</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-md">
                Submit Anonymously
            </button>
        </div>
    </form>

    <div class="text-center text-xs text-gray-400 mt-6">
        <a href="{{ url('/') }}" class="hover:underline">← Back to CHMSU GMS</a>
    </div>
</div>

</body>
</html>
