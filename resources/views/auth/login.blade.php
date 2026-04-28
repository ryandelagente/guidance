<x-guest-layout>
    <div class="mb-6">
        <h2 class="font-bold text-2xl text-gray-900">Welcome back</h2>
        <p class="text-sm text-gray-500 mt-1">Sign in to your CHMSU Guidance account.</p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   autocomplete="username"
                   placeholder="firstname.lastname@chmsu.edu.ph"
                   class="block w-full border-gray-300 rounded-md text-sm focus:border-red-500 focus:ring-red-500">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="block w-full border-gray-300 rounded-md text-sm focus:border-red-500 focus:ring-red-500">
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Remember + Forgot --}}
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                       class="rounded border-gray-300 text-red-700 focus:ring-red-500">
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-red-700 hover:text-red-900 hover:underline">
                    Forgot password?
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full bg-gradient-to-r from-red-800 to-red-900 hover:from-red-700 hover:to-red-800 text-white font-medium py-2.5 rounded-md transition shadow-sm">
            Sign In
        </button>
    </form>

    @if(env('GOOGLE_CLIENT_ID') || env('MS_CLIENT_ID'))
    <div class="mt-6 pt-6 border-t border-gray-100">
        <p class="text-center text-xs text-gray-500 mb-3">Or sign in with your CHMSU institutional account</p>
        <div class="flex gap-2">
            @if(env('GOOGLE_CLIENT_ID'))
            <a href="{{ route('sso.redirect', 'google') }}"
               class="flex-1 flex items-center justify-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 rounded-md text-sm">
                <svg class="w-4 h-4" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4C12.955 4 4 12.955 4 24s8.955 20 20 20s20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/><path fill="#FF3D00" d="m6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4C16.318 4 9.656 8.337 6.306 14.691z"/><path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/><path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/></svg>
                Google
            </a>
            @endif
            @if(env('MS_CLIENT_ID'))
            <a href="{{ route('sso.redirect', 'microsoft') }}"
               class="flex-1 flex items-center justify-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 rounded-md text-sm">
                <svg class="w-4 h-4" viewBox="0 0 23 23"><path fill="#f3f3f3" d="M0 0h23v23H0z"/><path fill="#f35325" d="M1 1h10v10H1z"/><path fill="#81bc06" d="M12 1h10v10H12z"/><path fill="#05a6f0" d="M1 12h10v10H1z"/><path fill="#ffba08" d="M12 12h10v10H12z"/></svg>
                Microsoft 365
            </a>
            @endif
        </div>
        <p class="text-xs text-gray-400 text-center mt-3">Only &#64;chmsu.edu.ph accounts accepted</p>
    </div>
    @endif

    <div class="mt-6 pt-6 border-t border-gray-100 text-center text-sm text-gray-500">
        Need an account?
        @if(Route::has('register'))
            <a href="{{ route('register') }}" class="font-medium text-red-700 hover:underline">Register here</a>
        @else
            Contact the Guidance Office to request access.
        @endif
    </div>
</x-guest-layout>
