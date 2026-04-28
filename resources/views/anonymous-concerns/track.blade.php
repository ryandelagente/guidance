<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Your Report — CHMSU Guidance</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-indigo-50 min-h-screen">

<div class="max-w-xl mx-auto py-8 px-4">
    <h1 class="text-xl font-bold text-gray-900 mb-4 text-center">Track Your Anonymous Report</h1>

    <form method="GET" class="bg-white rounded-xl shadow-sm p-5 mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Enter your reference code:</label>
        <div class="flex gap-2">
            <input type="text" name="code" value="{{ $code ?? '' }}" placeholder="TIP-XXXXXXXX"
                   class="flex-1 border-gray-300 rounded-md text-sm font-mono uppercase">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 rounded-md">Track</button>
        </div>
    </form>

    @if(isset($code) && $code !== '')
        @if($concern)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <p class="font-mono text-sm font-medium text-gray-700">{{ $concern->reference_code }}</p>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $concern->getStatusBadgeClass() }}">
                    {{ \App\Models\AnonymousConcern::STATUSES[$concern->status] }}
                </span>
            </div>

            <p class="text-xs text-gray-500 uppercase">Type</p>
            <p class="text-sm font-medium text-gray-800 mb-3">{{ $concern->type_label }}</p>

            <p class="text-xs text-gray-500 uppercase">Submitted</p>
            <p class="text-sm text-gray-700 mb-3">{{ $concern->created_at->format('F d, Y h:i A') }} ({{ $concern->created_at->diffForHumans() }})</p>

            @if($concern->resolved_at)
                <p class="text-xs text-gray-500 uppercase">Resolved</p>
                <p class="text-sm text-gray-700 mb-3">{{ $concern->resolved_at->format('F d, Y') }}</p>
            @endif

            <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500 leading-relaxed">
                Out of respect for everyone's privacy, we don't share specifics about how a concern is being handled. But know that <strong>your report has been seen</strong> and the counseling team is doing their part. Thank you.
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 text-sm text-yellow-800">
            We couldn't find a report with that reference code. Double-check the code (it should look like <span class="font-mono">TIP-XXXXXXXX</span>) and try again.
        </div>
        @endif
    @endif

    <div class="text-center mt-6">
        <a href="{{ route('anonymous-concerns.create') }}" class="text-sm text-blue-600 hover:underline">← Submit a new concern</a>
    </div>
</div>

</body>
</html>
