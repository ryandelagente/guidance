<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Student Profiles</h2>
            <a href="{{ route('students.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + Add Student
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Flash Message --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-48">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Name or Student ID..."
                           class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="w-48">
                    <label class="block text-xs font-medium text-gray-500 mb-1">College</label>
                    <select name="college" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Colleges</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college }}" @selected(request('college') === $college)>{{ $college }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="academic_status" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="good_standing" @selected(request('academic_status') === 'good_standing')>Good Standing</option>
                        <option value="probation"     @selected(request('academic_status') === 'probation')>Probation</option>
                        <option value="at_risk"       @selected(request('academic_status') === 'at_risk')>At Risk</option>
                        <option value="dismissed"     @selected(request('academic_status') === 'dismissed')>Dismissed</option>
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm px-4 py-2 rounded-md">Filter</button>
                <a href="{{ route('students.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">Reset</a>
            </form>

            {{-- Table --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">ID No.</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">College / Program</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Year</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Counselor</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($profiles as $profile)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $profile->full_name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $profile->student_id_number ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $profile->college ?? '—' }}<br>
                                <span class="text-xs text-gray-400">{{ $profile->program ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $profile->year_level ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $badge = match($profile->academic_status) {
                                        'good_standing' => 'bg-green-100 text-green-700',
                                        'probation'     => 'bg-yellow-100 text-yellow-700',
                                        'at_risk'       => 'bg-orange-100 text-orange-700',
                                        'dismissed'     => 'bg-red-100 text-red-700',
                                        default         => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                    {{ str_replace('_', ' ', ucfirst($profile->academic_status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $profile->assignedCounselor?->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('students.show', $profile) }}"
                                   class="text-blue-600 hover:underline text-xs font-medium">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">No student profiles found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($profiles->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $profiles->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
