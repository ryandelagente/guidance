<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Caseload</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Summary --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Total Students</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-red-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">High Risk</div>
                    <div class="text-2xl font-bold text-red-600 mt-1">{{ $stats['high_risk'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-yellow-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Medium Risk</div>
                    <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['medium_risk'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-orange-400">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">No Contact 30+ Days</div>
                    <div class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['no_contact_30'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-blue-500">
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Pending Follow-ups</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['pending_followups'] }}</div>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                @if($counselors->isNotEmpty())
                <div class="w-56">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Counselor</label>
                    <select name="counselor_id" class="w-full border-gray-300 rounded-md text-sm">
                        @foreach($counselors as $c)
                            <option value="{{ $c->id }}" @selected($counselorId == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="w-40">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Risk Level</label>
                    <select name="risk" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach(['high','medium','low'] as $r)
                            <option value="{{ $r }}" @selected(request('risk') === $r)>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sort By</label>
                    <select name="sort" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="name" @selected(request('sort','name')==='name')>Name (A–Z)</option>
                        <option value="risk" @selected(request('sort')==='risk')>Risk Level</option>
                        <option value="last_contact" @selected(request('sort')==='last_contact')>Last Contact</option>
                        <option value="referrals" @selected(request('sort')==='referrals')>Active Referrals</option>
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded-md">Apply</button>
                <a href="{{ route('caseload.index') }}" class="text-sm text-gray-500 py-2">Reset</a>
            </form>

            {{-- Caseload table --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Appt.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Refs</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Follow-up</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($rows as $row)
                        @php
                            $s = $row['student'];
                            $lastDays = $row['last_contact'] ? \Carbon\Carbon::parse($row['last_contact'])->diffInDays(now()) : null;
                            $riskBadge = match($row['risk']) {
                                'high'   => 'bg-red-100 text-red-700',
                                'medium' => 'bg-yellow-100 text-yellow-700',
                                default  => 'bg-green-100 text-green-700',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('students.show', $s) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $s->full_name }}</a>
                                <div class="text-xs text-gray-400">{{ $s->student_id_number ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <div class="text-xs">{{ $s->program ?? '—' }}</div>
                                <div class="text-xs text-gray-400">{{ $s->year_level ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusBadge = match($s->academic_status) {
                                        'good_standing' => 'bg-green-100 text-green-700',
                                        'probation'     => 'bg-yellow-100 text-yellow-700',
                                        'at_risk'       => 'bg-orange-100 text-orange-700',
                                        'dismissed'     => 'bg-red-100 text-red-700',
                                        default         => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $statusBadge }}">{{ ucwords(str_replace('_', ' ', $s->academic_status)) }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                @if($row['last_contact'])
                                    <div class="text-xs">{{ \Carbon\Carbon::parse($row['last_contact'])->format('M d, Y') }}</div>
                                    <div class="text-xs {{ $lastDays > 30 ? 'text-red-500 font-medium' : 'text-gray-400' }}">{{ $lastDays }}d ago</div>
                                @else
                                    <span class="text-xs text-red-500 italic">Never</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                @if($row['next_appt'])
                                    <a href="{{ route('appointments.show', $row['next_appt']) }}" class="text-xs text-blue-600 hover:underline">
                                        {{ $row['next_appt']->appointment_date->format('M d') }}
                                        <span class="text-gray-400">{{ substr($row['next_appt']->start_time, 0, 5) }}</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($row['active_refs'] > 0)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-700 text-xs font-bold">{{ $row['active_refs'] }}</span>
                                @else
                                    <span class="text-xs text-gray-300">0</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($row['follow_up'])
                                    <div class="text-xs text-gray-700">{{ $row['follow_up']->follow_up_date->format('M d, Y') }}</div>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $riskBadge }}">{{ ucfirst($row['risk']) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('students.timeline', $s) }}" class="text-xs text-purple-600 hover:underline mr-2">Timeline</a>
                                <a href="{{ route('students.show', $s) }}" class="text-xs text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                                <div class="text-3xl mb-2">📂</div>
                                No students assigned to this caseload yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
