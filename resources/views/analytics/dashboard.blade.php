<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Analytics & Reports</h2>
            <form method="GET" class="flex items-center gap-2">
                <select name="year" class="border-gray-300 rounded-md text-sm">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                    @endfor
                </select>
                <select name="month" class="border-gray-300 rounded-md text-sm">
                    <option value="0" @selected($month == 0)>Full Year</option>
                    @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $i => $m)
                        <option value="{{ $i + 1 }}" @selected($month == $i + 1)>{{ $m }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gray-800 text-white text-sm px-3 py-1.5 rounded-md">Apply</button>
            </form>
        </div>
    </x-slot>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- ── KPI Cards ── --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @php
                $cards = [
                    ['Students',      $totalStudents,      'bg-blue-600'],
                    ['Appointments',  $totalAppointments,  'bg-indigo-600'],
                    ['Sessions',      $totalSessions,      'bg-violet-600'],
                    ['Referrals',     $totalReferrals,     'bg-orange-500'],
                    ['Disciplinary',  $totalDisciplinary,  'bg-red-600'],
                    ['Certificates',  $totalCertificates,  'bg-green-600'],
                ];
                @endphp
                @foreach($cards as [$label, $value, $bg])
                <div class="{{ $bg }} text-white rounded-xl p-4 shadow-sm">
                    <p class="text-3xl font-bold">{{ number_format($value) }}</p>
                    <p class="text-sm opacity-90 mt-1">{{ $label }}</p>
                </div>
                @endforeach
            </div>

            {{-- ── Row 1: Student charts ── --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- By College --}}
                <div class="bg-white shadow-sm rounded-xl p-5 md:col-span-2">
                    <h3 class="font-semibold text-gray-700 mb-4">Students by College</h3>
                    <canvas id="chartCollege" height="160"></canvas>
                </div>

                {{-- By Sex --}}
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">By Sex</h3>
                    <canvas id="chartSex" height="160"></canvas>
                </div>
            </div>

            {{-- By Year Level + Academic Status --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">Students by Year Level</h3>
                    <canvas id="chartYear" height="140"></canvas>
                </div>
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">Academic Status</h3>
                    <canvas id="chartAcademic" height="140"></canvas>
                </div>
            </div>

            {{-- ── Row 2: Appointment charts ── --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white shadow-sm rounded-xl p-5 md:col-span-2">
                    <h3 class="font-semibold text-gray-700 mb-4">Appointments by Month ({{ $year }})</h3>
                    <canvas id="chartApptMonth" height="120"></canvas>
                </div>
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">Appointment Type</h3>
                    <canvas id="chartApptType" height="160"></canvas>
                </div>
            </div>

            {{-- ── Row 3: Counseling + Referrals ── --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">Presenting Concerns (Sessions)</h3>
                    <canvas id="chartConcern" height="200"></canvas>
                </div>
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">Referral Categories</h3>
                    <canvas id="chartRefCat" height="200"></canvas>
                </div>
            </div>

            {{-- Referral urgency + disciplinary ── --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">Referral Urgency</h3>
                    <canvas id="chartRefUrgency" height="160"></canvas>
                </div>
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">Disciplinary by Type</h3>
                    <canvas id="chartDiscType" height="160"></canvas>
                </div>
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">Test Result Levels</h3>
                    <canvas id="chartTestLevel" height="160"></canvas>
                </div>
            </div>

            {{-- ── Clearance summary table ── --}}
            <div class="bg-white shadow-sm rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-700">Clearance Summary ({{ $year }})</h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="border rounded-lg p-4">
                        <p class="text-2xl font-bold text-gray-800">{{ $totalClearance }}</p>
                        <p class="text-sm text-gray-500 mt-1">Total Requests</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <p class="text-2xl font-bold text-green-600">{{ $approvedClearance }}</p>
                        <p class="text-sm text-gray-500 mt-1">Approved</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <p class="text-2xl font-bold text-blue-600">{{ $totalCertificates }}</p>
                        <p class="text-sm text-gray-500 mt-1">Certificates Issued</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <p class="text-2xl font-bold text-purple-600">{{ $totalResults }}</p>
                        <p class="text-sm text-gray-500 mt-1">Test Results</p>
                    </div>
                </div>
                @if($clearanceByType->count())
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Clearance Type</th>
                                <th class="px-4 py-2 text-right font-medium text-gray-500">Count</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($clearanceByType as $row)
                            <tr>
                                <td class="px-4 py-2 text-gray-700">{{ ucfirst($row->clearance_type) }}</td>
                                <td class="px-4 py-2 text-right font-medium text-gray-900">{{ $row->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- ── CSV Export Buttons ── --}}
            <div class="bg-white shadow-sm rounded-xl p-5">
                <h3 class="font-semibold text-gray-700 mb-4">Export Data (CSV)</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('analytics.export.students') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md">
                        ⬇ Student Roster
                    </a>
                    <a href="{{ route('analytics.export.appointments', ['year' => $year]) }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-md">
                        ⬇ Appointments {{ $year }}
                    </a>
                    <a href="{{ route('analytics.export.referrals', ['year' => $year]) }}"
                       class="bg-orange-500 hover:bg-orange-600 text-white text-sm px-4 py-2 rounded-md">
                        ⬇ Referrals {{ $year }}
                    </a>
                    <a href="{{ route('analytics.export.disciplinary', ['year' => $year]) }}"
                       class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-md">
                        ⬇ Disciplinary {{ $year }}
                    </a>
                    <a href="{{ route('analytics.report', ['year' => $year]) }}" target="_blank"
                       class="bg-gray-800 hover:bg-gray-900 text-white text-sm px-4 py-2 rounded-md">
                        🖨 Print Report
                    </a>
                </div>
            </div>

        </div>
    </div>

<script>
const COLORS = {
    blue:   '#3b82f6', indigo: '#6366f1', violet: '#8b5cf6', purple: '#a855f7',
    green:  '#22c55e', emerald:'#10b981', teal:   '#14b8a6',
    orange: '#f97316', amber:  '#f59e0b', yellow: '#eab308',
    red:    '#ef4444', rose:   '#f43f5e', pink:   '#ec4899',
    gray:   '#6b7280', slate:  '#64748b', sky:    '#0ea5e9',
};
const PALETTE = Object.values(COLORS);

function makeChart(id, type, labels, data, opts = {}) {
    const ctx = document.getElementById(id);
    if (!ctx) return;
    new Chart(ctx, {
        type,
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: PALETTE.slice(0, data.length),
                borderColor: type === 'bar' ? PALETTE.slice(0, data.length) : '#fff',
                borderWidth: type === 'bar' ? 0 : 2,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: type !== 'bar', position: 'right' } },
            scales: type === 'bar' ? { y: { beginAtZero: true, ticks: { precision: 0 } } } : {},
            ...opts
        }
    });
}

// Students by College
makeChart('chartCollege', 'bar',
    {!! $byCollege->pluck('college')->map(fn($c) => '"' . addslashes($c) . '"')->implode(',') !!},
    [{{ $byCollege->pluck('total')->implode(',') }}]
);

// By Sex
makeChart('chartSex', 'doughnut',
    {!! $bySex->pluck('sex')->map(fn($s) => '"' . ucfirst($s ?? 'Unknown') . '"')->implode(',') !!},
    [{{ $bySex->pluck('total')->implode(',') }}]
);

// By Year Level
makeChart('chartYear', 'bar',
    {!! $byYearLevel->pluck('year_level')->map(fn($y) => '"' . ($y ?? 'Unknown') . '"')->implode(',') !!},
    [{{ $byYearLevel->pluck('total')->implode(',') }}]
);

// Academic Status
makeChart('chartAcademic', 'doughnut',
    {!! $byAcademicStatus->pluck('academic_status')->map(fn($s) => '"' . ucwords(str_replace('_',' ',$s ?? 'unknown')) . '"')->implode(',') !!},
    [{{ $byAcademicStatus->pluck('total')->implode(',') }}]
);

// Appointments by Month
@php
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$monthData = array_map(fn($m) => $apptByMonth->get($m, 0), range(1, 12));
@endphp
makeChart('chartApptMonth', 'bar',
    ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
    [{{ implode(',', $monthData) }}]
);

// Appointment Type
makeChart('chartApptType', 'doughnut',
    {!! $apptByType->pluck('appointment_type')->map(fn($t) => '"' . ucwords(str_replace('_',' ',$t)) . '"')->implode(',') !!},
    [{{ $apptByType->pluck('total')->implode(',') }}]
);

// Presenting Concerns
makeChart('chartConcern', 'bar',
    {!! $sessionsByConcern->pluck('presenting_concern')->map(fn($c) => '"' . ucwords(str_replace('_',' ',$c ?? 'Unknown')) . '"')->implode(',') !!},
    [{{ $sessionsByConcern->pluck('total')->implode(',') }}]
);

// Referral Categories
makeChart('chartRefCat', 'bar',
    {!! $refByCategory->pluck('reason_category')->map(fn($c) => '"' . ucwords(str_replace('_',' ',$c)) . '"')->implode(',') !!},
    [{{ $refByCategory->pluck('total')->implode(',') }}]
);

// Referral Urgency
makeChart('chartRefUrgency', 'doughnut',
    {!! $refByUrgency->pluck('urgency')->map(fn($u) => '"' . ucfirst($u) . '"')->implode(',') !!},
    [{{ $refByUrgency->pluck('total')->implode(',') }}]
);

// Disciplinary by Type
makeChart('chartDiscType', 'doughnut',
    {!! $discByType->pluck('offense_type')->map(fn($t) => '"' . ucfirst($t) . '"')->implode(',') !!},
    [{{ $discByType->pluck('total')->implode(',') }}]
);

// Test Levels
makeChart('chartTestLevel', 'doughnut',
    {!! $resultsByLevel->pluck('interpretation_level')->map(fn($l) => '"' . ucwords(str_replace('_',' ',$l ?? 'Unknown')) . '"')->implode(',') !!},
    [{{ $resultsByLevel->pluck('total')->implode(',') }}]
);
</script>
</x-app-layout>
