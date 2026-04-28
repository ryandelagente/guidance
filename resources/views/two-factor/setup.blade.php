<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('two-factor.show') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">🛡️ Set Up Two-Factor Authentication</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white shadow-sm rounded-lg p-6">

                {{-- Step 1 --}}
                <div class="mb-6">
                    <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm">1</span>
                        Install an authenticator app
                    </h3>
                    <p class="text-sm text-gray-600 ml-9">
                        On your phone, install one of: <strong>Google Authenticator</strong>, <strong>Microsoft Authenticator</strong>, <strong>Authy</strong>, or <strong>1Password</strong>.
                    </p>
                </div>

                {{-- Step 2: QR + manual --}}
                <div class="mb-6">
                    <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm">2</span>
                        Scan or manually enter the secret
                    </h3>

                    <div class="ml-9 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- QR --}}
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div id="qrcode" class="inline-block bg-white p-2 rounded"></div>
                            <p class="text-xs text-gray-500 mt-2">Scan with your authenticator app</p>
                        </div>

                        {{-- Manual --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Can't scan? Enter manually:</p>
                            <p class="text-xs text-gray-500 mb-2">Use these exact values when adding the account.</p>
                            <div class="space-y-2 text-xs">
                                <div>
                                    <span class="text-gray-500">Account:</span>
                                    <code class="block bg-gray-100 rounded px-2 py-1 mt-0.5 break-all">{{ auth()->user()->email }}</code>
                                </div>
                                <div>
                                    <span class="text-gray-500">Secret:</span>
                                    <code class="block bg-gray-100 rounded px-2 py-1 mt-0.5 break-all font-mono select-all">{{ chunk_split($secret, 4, ' ') }}</code>
                                </div>
                                <div>
                                    <span class="text-gray-500">Type:</span> Time-based (TOTP), 6 digits, 30s
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <form method="POST" action="{{ route('two-factor.enable') }}">
                    @csrf
                    <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm">3</span>
                        Verify with the 6-digit code
                    </h3>

                    <div class="ml-9">
                        <input type="text" name="code" required maxlength="6" minlength="6"
                               inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" autofocus
                               placeholder="• • • • • •"
                               class="w-48 border-gray-300 rounded-md text-2xl tracking-[0.4em] text-center font-mono focus:border-red-500 focus:ring-red-500">
                        @error('code')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror

                        <div class="flex gap-2 mt-4 pt-4 border-t border-gray-100">
                            <a href="{{ route('two-factor.show') }}" class="text-sm px-4 py-2 text-gray-600">Cancel</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-md">
                                Enable 2FA
                            </button>
                        </div>
                    </div>
                </form>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                var qr = qrcode(0, 'M');
                qr.addData(@json($uri));
                qr.make();
                document.getElementById('qrcode').innerHTML = qr.createImgTag(5, 8);
            } catch (e) {
                document.getElementById('qrcode').innerHTML = '<p class="text-xs text-red-500 p-4">Could not load QR — please use manual entry.</p>';
            }
        });
    </script>
</x-app-layout>
