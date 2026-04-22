<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h2 class="font-semibold text-xl text-gray-800">Create User Account</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6 space-y-5">

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full border-gray-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               placeholder="user@chmsu.edu.ph"
                               class="w-full border-gray-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" required class="w-full border-gray-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500">
                            <option value="">Select role…</option>
                            @foreach([
                                'guidance_director'  => 'Guidance Director',
                                'guidance_counselor' => 'Guidance Counselor',
                                'faculty'            => 'Faculty / Staff',
                                'student'            => 'Student',
                                'super_admin'        => 'Super Administrator',
                            ] as $val => $label)
                                <option value="{{ $val }}" @selected(old('role') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full border-gray-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full border-gray-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500">
                    </div>

                    <div class="pt-2 flex gap-3">
                        <button type="submit"
                                class="bg-red-800 hover:bg-red-700 text-white font-medium px-5 py-2 rounded-lg text-sm">
                            Create Account
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
