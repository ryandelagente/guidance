<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Concern Submitted — CHMSU Guidance</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-green-50 via-white to-blue-50 min-h-screen flex items-center">

<div class="max-w-xl mx-auto py-8 px-4 w-full">
    <div class="bg-white rounded-xl shadow-lg p-8 text-center">
        <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
            <span class="text-4xl">✓</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Thank you for speaking up</h1>
        <p class="text-sm text-gray-600 mb-6">Your concern has been received and a counselor will review it within 1 working day.</p>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-xs uppercase font-medium text-blue-700 tracking-wider">Your Reference Code</p>
            <p class="font-mono font-bold text-2xl text-blue-900 mt-1 select-all">{{ $code }}</p>
            <p class="text-xs text-blue-600 mt-2">Save this code if you'd like to follow up later.</p>
        </div>

        <p class="text-xs text-gray-500 mb-4">If this is a crisis right now, please call <strong>NCMH 1553</strong> or <strong>911</strong> immediately. Don't wait for our response.</p>

        <div class="flex flex-col sm:flex-row gap-2 justify-center">
            <a href="{{ route('anonymous-concerns.create') }}" class="text-sm text-gray-600 hover:text-gray-800">Submit Another</a>
            <span class="hidden sm:inline text-gray-300">•</span>
            <a href="{{ route('anonymous-concerns.track') }}" class="text-sm text-blue-600 hover:underline">Track This Report</a>
            <span class="hidden sm:inline text-gray-300">•</span>
            <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-gray-800">Home</a>
        </div>
    </div>
</div>

</body>
</html>
