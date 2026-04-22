<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h2 class="font-semibold text-xl text-gray-800">User Management</h2>
            <a href="{{ route('admin.users.create') }}"
               class="bg-red-800 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + New User
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="bg-white shadow-sm rounded-lg px-5 py-4 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Name or email…"
                           class="border-gray-300 rounded-md text-sm w-52">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
                    <select name="role" class="border-gray-300 rounded-md text-sm">
                        <option value="">All Roles</option>
                        @foreach(['super_admin','guidance_director','guidance_counselor','faculty','student'] as $r)
                            <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucwords(str_replace('_',' ',$r)) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-gray-700 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:text-gray-600 py-2">Clear</a>
            </form>

            {{-- Table --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-red-100 text-red-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @php
                                $roleColors = [
                                    'super_admin'        => 'bg-red-100 text-red-700',
                                    'guidance_director'  => 'bg-purple-100 text-purple-700',
                                    'guidance_counselor' => 'bg-teal-100 text-teal-700',
                                    'faculty'            => 'bg-slate-100 text-slate-700',
                                    'student'            => 'bg-blue-100 text-blue-700',
                                ];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $user->getRoleDisplayName() }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>

                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs {{ $user->is_active ? 'text-orange-500 hover:text-orange-700' : 'text-green-600 hover:text-green-800' }} font-medium">
                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    @if(!$user->isSuperAdmin())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                    </form>
                                    @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $users->withQueryString()->links() }}</div>

        </div>
    </div>
</x-app-layout>
