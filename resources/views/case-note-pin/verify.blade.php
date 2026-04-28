<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🔐 Verify Your Case-Note PIN</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-xl p-8 text-center">
                <div class="w-16 h-16 mx-auto bg-red-50 border-2 border-red-100 rounded-full flex items-center justify-center mb-4">
                    <span class="text-3xl">🔐</span>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Enter your case-note PIN</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Confidential case notes are protected by a personal PIN.<br>
                    Once verified, you'll have access for the next 15 minutes.
                </p>

                <form method="POST" action="{{ route('case-note-pin.check') }}" class="space-y-4">
                    @csrf
                    <input type="password" name="pin" required maxlength="6" minlength="4"
                           inputmode="numeric" pattern="[0-9]*" autocomplete="off" autofocus
                           placeholder="• • • •"
                           class="w-48 mx-auto block border-gray-300 rounded-md text-2xl tracking-[0.5em] text-center font-mono focus:border-red-500 focus:ring-red-500">

                    @error('pin')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-8 py-2.5 rounded-md">
                        Unlock
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <a href="{{ route('case-note-pin.setup') }}" class="text-xs text-gray-500 hover:text-gray-700">
                        Forgot or want to change your PIN?
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
