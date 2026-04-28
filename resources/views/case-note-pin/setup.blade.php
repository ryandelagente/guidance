<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🔐 {{ $isResetting ? 'Change' : 'Set Up' }} Your Case-Note PIN</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 text-sm text-blue-900">
                <h3 class="font-semibold mb-2">Why a PIN?</h3>
                <p class="leading-relaxed">
                    Counseling case notes contain highly sensitive student information. Your account login alone isn't enough to access them — you must set a personal <strong>4–6 digit PIN</strong> that only you know.
                    Even system administrators cannot read case notes without this PIN. This is a CHMSU policy and Data Privacy Act requirement.
                </p>
                <p class="text-xs text-blue-700 mt-2">
                    The PIN is verified once per browsing session and lasts <strong>15 minutes</strong> before re-prompting.
                </p>
            </div>

            <form method="POST" action="{{ route('case-note-pin.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PIN <span class="text-red-500">*</span></label>
                    <input type="password" name="pin" required maxlength="6" minlength="4"
                           inputmode="numeric" pattern="[0-9]*" autocomplete="off" autofocus
                           placeholder="4 to 6 digits"
                           class="w-full border-gray-300 rounded-md text-lg tracking-[0.4em] text-center font-mono focus:border-red-500 focus:ring-red-500">
                    @error('pin')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm PIN <span class="text-red-500">*</span></label>
                    <input type="password" name="pin_confirmation" required maxlength="6" minlength="4"
                           inputmode="numeric" pattern="[0-9]*" autocomplete="off"
                           class="w-full border-gray-300 rounded-md text-lg tracking-[0.4em] text-center font-mono focus:border-red-500 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm with Account Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required autocomplete="current-password"
                           class="w-full border-gray-300 rounded-md text-sm focus:border-red-500 focus:ring-red-500">
                    <p class="text-xs text-gray-500 mt-1">Verifying your account password ensures only you can set this PIN.</p>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-800">
                    ⚠️ <strong>Choose carefully.</strong> If you forget your PIN, only a Super Admin can reset it (which itself is logged in the audit log). Don't write it down somewhere others can see, but make it memorable to you.
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <a href="{{ route('sessions.index') }}" class="text-sm px-4 py-2 text-gray-600">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-md">
                        {{ $isResetting ? 'Update PIN' : 'Set PIN' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
