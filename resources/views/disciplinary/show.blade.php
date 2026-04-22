<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Disciplinary Record</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('disciplinary.edit', $disciplinary) }}"
                   class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md">Edit</a>
                <a href="{{ route('disciplinary.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6 space-y-5">

                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $disciplinary->studentProfile->full_name ?? '—' }}</h3>
                        <p class="text-sm text-gray-500">{{ $disciplinary->studentProfile->student_id_number ?? '' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $disciplinary->getOffenseTypeBadgeClass() }}">
                            {{ ucfirst($disciplinary->offense_type) }} offense
                        </span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $disciplinary->getStatusBadgeClass() }}">
                            {{ ucwords(str_replace('_',' ',$disciplinary->status)) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-500">Category</span>
                        <p class="text-gray-800 mt-0.5">{{ ucwords(str_replace('_',' ',$disciplinary->offense_category)) }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-500">Incident Date</span>
                        <p class="text-gray-800 mt-0.5">{{ $disciplinary->incident_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-500">Reported By</span>
                        <p class="text-gray-800 mt-0.5">{{ $disciplinary->reportedBy->name ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-500">Handled By</span>
                        <p class="text-gray-800 mt-0.5">{{ $disciplinary->handledBy->name ?? 'Unassigned' }}</p>
                    </div>
                    @if($disciplinary->sanction)
                    <div>
                        <span class="font-medium text-gray-500">Sanction</span>
                        <p class="text-gray-800 mt-0.5">{{ $disciplinary->sanction }}</p>
                    </div>
                    @endif
                    @if($disciplinary->sanction_end_date)
                    <div>
                        <span class="font-medium text-gray-500">Sanction End Date</span>
                        <p class="text-gray-800 mt-0.5">{{ $disciplinary->sanction_end_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>

                <div class="text-sm">
                    <span class="font-medium text-gray-500">Description</span>
                    <p class="text-gray-800 mt-1 whitespace-pre-wrap">{{ $disciplinary->description }}</p>
                </div>

                @if($disciplinary->action_taken)
                <div class="text-sm">
                    <span class="font-medium text-gray-500">Action Taken</span>
                    <p class="text-gray-800 mt-1 whitespace-pre-wrap">{{ $disciplinary->action_taken }}</p>
                </div>
                @endif

                @if(auth()->user()->isSuperAdmin())
                <div class="pt-4 border-t">
                    <form method="POST" action="{{ route('disciplinary.destroy', $disciplinary) }}"
                          onsubmit="return confirm('Permanently delete this record?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 hover:underline">Delete Record</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
