<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="inline-block w-16 h-16 bg-red-50 border-2 border-red-100 rounded-full flex items-center justify-center text-3xl mb-3">
            🛡️
        </div>
        <h2 class="font-bold text-2xl text-gray-900">Two-Factor Authentication</h2>
        <p class="text-sm text-gray-500 mt-1">Enter the 6-digit code from your authenticator app.</p>
    </div>

    <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-4">
        @csrf

        <div>
            <input type="text" name="code" required autofocus autocomplete="one-time-code"
                   inputmode="numeric" pattern="[0-9 ]*"
                   placeholder="• • • • • •"
                   class="w-full block border-gray-300 rounded-md text-2xl tracking-[0.5em] text-center font-mono focus:border-red-500 focus:ring-red-500">
            @error('code')<p class="text-red-500 text-xs mt-2 text-center">{{ $message }}</p>@enderror
        </div>

        <button type="submit"
                class="w-full bg-gradient-to-r from-red-800 to-red-900 hover:from-red-700 hover:to-red-800 text-white font-medium py-2.5 rounded-md">
            Verify
        </button>
    </form>

    <details class="mt-6">
        <summary class="text-sm text-gray-500 hover:text-gray-700 cursor-pointer text-center">Lost your phone? Use a recovery code</summary>
        <p class="text-xs text-gray-400 mt-2 text-center leading-relaxed">
            Enter one of the recovery codes you saved when you set up 2FA. Each code can be used only once.
            Format looks like <code class="bg-gray-100 px-1.5 py-0.5 rounded">abc12-de34f</code>.
            Just paste it into the box above and click Verify.
        </p>
    </details>

    <div class="mt-6 pt-6 border-t border-gray-100 text-center">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-xs text-gray-500 hover:text-red-600">← Cancel and sign out</button>
        </form>
    </div>
</x-guest-layout>
