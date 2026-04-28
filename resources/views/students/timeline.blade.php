<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('students.show', $student) }}" class="text-gray-400 hover:text-gray-600">← Back to Profile</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $student->full_name }} — Activity Timeline</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Summary --}}
            <div class="bg-white shadow-sm rounded-lg p-5 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $events->where('type','appointment')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Appointments</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-purple-600">{{ $events->where('type','session')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Sessions</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-orange-600">{{ $events->where('type','referral')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Referrals</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-red-600">{{ $events->where('type','disciplinary')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Disciplinary</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-indigo-600">{{ $events->where('type','test_result')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Tests</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ $events->where('type','clearance')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Clearance</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $events->where('type','certificate')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Certificates</div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white shadow-sm rounded-lg p-6">

                @forelse($grouped as $date => $dayEvents)
                @php
                    $dateObj = \Carbon\Carbon::parse($date);
                    $isToday = $dateObj->isToday();
                    $relative = $dateObj->diffForHumans(['parts' => 1, 'short' => true]);
                @endphp

                <div class="mb-6 last:mb-0">
                    {{-- Date heading --}}
                    <div class="flex items-center gap-3 mb-3 sticky top-0 bg-white z-10 py-1">
                        <h3 class="font-semibold text-gray-800 text-sm">
                            {{ $dateObj->format('F d, Y') }}
                            <span class="text-xs text-gray-400 font-normal ml-1">({{ $dateObj->format('l') }})</span>
                        </h3>
                        @if($isToday)
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Today</span>
                        @else
                            <span class="text-xs text-gray-400">{{ $relative }}</span>
                        @endif
                        <div class="flex-1 border-t border-gray-100"></div>
                    </div>

                    {{-- Events for this day --}}
                    <div class="space-y-2 pl-2">
                        @foreach($dayEvents as $event)
                        @php
                            $colorMap = [
                                'blue'    => 'bg-blue-50 border-blue-200 text-blue-700',
                                'purple'  => 'bg-purple-50 border-purple-200 text-purple-700',
                                'orange'  => 'bg-orange-50 border-orange-200 text-orange-700',
                                'red'     => 'bg-red-50 border-red-200 text-red-700',
                                'indigo'  => 'bg-indigo-50 border-indigo-200 text-indigo-700',
                                'green'   => 'bg-green-50 border-green-200 text-green-700',
                                'emerald' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                                'gray'    => 'bg-gray-50 border-gray-200 text-gray-600',
                            ];
                            $cls = $colorMap[$event['color']] ?? $colorMap['gray'];
                        @endphp
                        <div class="flex gap-3 items-start group">
                            {{-- Time --}}
                            <div class="w-12 text-xs text-gray-400 text-right pt-2 flex-shrink-0">
                                {{ $event['time'] ?? '—' }}
                            </div>

                            {{-- Icon --}}
                            <div class="w-8 h-8 rounded-full {{ $cls }} border flex items-center justify-center text-sm flex-shrink-0">
                                {{ $event['icon'] }}
                            </div>

                            {{-- Body --}}
                            <div class="flex-1 min-w-0 border-l-2 {{ str_replace(['bg-', '50'], ['border-', '300'], explode(' ', $cls)[0]) }} pl-3 pb-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $event['title'] }}</p>
                                        @if(!empty($event['subtitle']))
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $event['subtitle'] }}</p>
                                        @endif
                                    </div>
                                    @if(!empty($event['status']))
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $cls }} flex-shrink-0">
                                            {{ $event['status'] }}
                                        </span>
                                    @endif
                                </div>
                                @if(!empty($event['url']))
                                    <a href="{{ $event['url'] }}" class="inline-block mt-1 text-xs text-blue-600 hover:underline">View details →</a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @empty
                <div class="text-center py-12">
                    <div class="text-gray-300 text-5xl mb-3">📋</div>
                    <p class="text-gray-400 text-sm">No activity recorded for this student yet.</p>
                </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
