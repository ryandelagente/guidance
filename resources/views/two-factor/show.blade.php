<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🛡️ Two-Factor Authentication</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            {{-- Recovery codes (one-time display) --}}
            @if(session('recoveryCodes'))
            <div class="bg-yellow-50 border-2 border-yellow-300 rounded-xl p-5">
                <h3 class="font-bold text-yellow-900 mb-2">⚠️ Save these recovery codes</h3>
                <p class="text-sm text-yellow-800 mb-3">
                    Each code can be used once if you lose access to your authenticator app. <strong>This is the only time we'll show them</strong> — print or paste into a password manager.
                </p>
                <div class="bg-white rounded-lg p-4 grid grid-cols-2 gap-2 font-mono text-sm">
                    @foreach(session('recoveryCodes') as $code)
                        <div class="px-2 py-1 bg-gray-50 rounded select-all">{{ $code }}</div>
                    @endforeach
                </div>
                <button onclick="navigator.clipboard.writeText('{{ implode("\n", session('recoveryCodes')) }}'); this.textContent='✓ Copied'"
                        class="mt-3 text-xs text-yellow-700 hover:text-yellow-900 font-medium">📋 Copy all to clipboard</button>
            </div>
            @endif

            {{-- Status card --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Authenticator App</h3>
                        <p class="text-sm text-gray-500">Use Google Authenticator, Microsoft Authenticator, or Authy.</p>
                    </div>
                    @if($user->hasTwoFactorEnabled())
                        <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-medium">✓ Enabled</span>
                    @else
                        <span class="bg-gray-100 text-gray-500 text-xs px-3 py-1 rounded-full">Not enabled</span>
                    @endif
                </div>

                @if($user->hasTwoFactorEnabled())
                    <div class="text-sm text-gray-600 mb-4">
                        <p>Enabled on <strong>{{ $user->two_factor_enabled_at->format('F d, Y') }}</strong>.</p>
                        <p class="text-xs text-gray-400 mt-1">{{ count($user->two_factor_recovery_codes ?? []) }} recovery code(s) remaining.</p>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-100">
                        <form method="POST" action="{{ route('two-factor.regenerate-codes') }}" class="inline"
                              onsubmit="return confirm('Generate new recovery codes? Old codes will stop working immediately.')">
                            @csrf
                            <details>
                                <summary class="cursor-pointer text-sm text-blue-600 hover:underline list-none px-3 py-1.5">🔄 Regenerate recovery codes</summary>
                                <div class="mt-2 flex gap-2">
                                    <input type="password" name="password" required placeholder="Confirm with password"
                                           class="border-gray-300 rounded-md text-sm flex-1">
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 rounded-md">Confirm</button>
                                </div>
                            </details>
                        </form>
                    </div>

                    <details class="mt-4 border border-red-100 rounded-lg">
                        <summary class="px-4 py-2.5 text-sm font-medium text-red-600 cursor-pointer hover:bg-red-50 rounded-lg">⚠️ Disable 2FA</summary>
                        <form method="POST" action="{{ route('two-factor.disable') }}" class="px-4 pb-4 pt-3 space-y-3">
                            @csrf
                            <p class="text-xs text-gray-500">Disabling 2FA reduces your account security. Confirm with your account password.</p>
                            <div class="flex gap-2">
                                <input type="password" name="password" required placeholder="Account password"
                                       class="border-gray-300 rounded-md text-sm flex-1">
                                <button class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 rounded-md">Disable 2FA</button>
                            </div>
                            @error('password')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                        </form>
                    </details>
                @else
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-900 mb-4">
                        🔒 <strong>Why enable 2FA?</strong> Even if someone learns your password, they can't sign in without the rotating 6-digit code from your phone. Strongly recommended for any account with access to confidential student data.
                    </div>

                    <a href="{{ route('two-factor.setup') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-md text-sm">
                        Set Up 2FA →
                    </a>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
