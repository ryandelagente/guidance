<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GMS Accomplishment Report {{ $year }}</title>
    <style>
        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #111;
            background: #f0f2f5;
        }

        /* ── Screen wrapper ── */
        .page-wrapper {
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 2px 16px rgba(0,0,0,.15);
        }

        /* ── Print controls (screen-only) ── */
        .controls {
            background: #1a3a5c;
            padding: 12px 30px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .controls a {
            color: #a0c4e8;
            font-size: 12pt;
            text-decoration: none;
            font-family: Arial, sans-serif;
        }
        .controls a:hover { color: #fff; }
        .controls .spacer { flex: 1; }
        .btn-print {
            background: #fff;
            color: #1a3a5c;
            border: none;
            padding: 8px 24px;
            font-size: 12pt;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            font-family: Arial, sans-serif;
        }
        .btn-print:hover { background: #e8f0fe; }
        .filter-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .filter-form select, .filter-form input[type=number] {
            border: 1px solid rgba(255,255,255,.4);
            background: rgba(255,255,255,.15);
            color: #fff;
            padding: 5px 8px;
            border-radius: 4px;
            font-size: 11pt;
            font-family: Arial, sans-serif;
        }
        .filter-form select option { color: #111; background: #fff; }
        .filter-form button {
            background: rgba(255,255,255,.25);
            border: 1px solid rgba(255,255,255,.4);
            color: #fff;
            padding: 5px 14px;
            border-radius: 4px;
            cursor: pointer;
            font-family: Arial, sans-serif;
        }

        /* ── Report body ── */
        .report-body {
            padding: 36px 48px;
        }

        /* ── Letterhead ── */
        .letterhead {
            text-align: center;
            border-bottom: 3px double #1a3a5c;
            padding-bottom: 18px;
            margin-bottom: 24px;
        }
        .letterhead .republic {
            font-size: 9.5pt;
            color: #555;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .letterhead .university {
            font-size: 16pt;
            font-weight: bold;
            color: #1a3a5c;
            letter-spacing: 1px;
        }
        .letterhead .address {
            font-size: 9.5pt;
            color: #666;
            margin-top: 3px;
        }
        .letterhead .office {
            font-size: 12pt;
            font-weight: bold;
            color: #1a3a5c;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }
        .letterhead .report-title {
            font-size: 13pt;
            font-weight: bold;
            color: #333;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .letterhead .period {
            font-size: 10pt;
            color: #555;
            margin-top: 5px;
        }
        .letterhead .generated {
            font-size: 9pt;
            color: #999;
            margin-top: 4px;
        }

        /* ── Section headings ── */
        h2 {
            font-size: 11.5pt;
            font-weight: bold;
            color: #fff;
            background: #1a3a5c;
            padding: 5px 10px;
            margin: 28px 0 10px;
            letter-spacing: 0.5px;
        }
        h3 {
            font-size: 10.5pt;
            font-weight: bold;
            color: #1a3a5c;
            margin: 16px 0 6px;
            border-bottom: 1px solid #c8d6e5;
            padding-bottom: 3px;
        }

        /* ── KPI grid ── */
        .kpi-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin: 10px 0 4px;
        }
        .kpi-row { display: table-row; }
        .kpi-card {
            display: table-cell;
            border: 1.5px solid #c8d6e5;
            border-radius: 5px;
            padding: 10px 14px;
            text-align: center;
            background: #f8fafc;
            width: 33.33%;
        }
        .kpi-val {
            font-size: 22pt;
            font-weight: bold;
            color: #1a3a5c;
            line-height: 1;
        }
        .kpi-lbl {
            font-size: 8.5pt;
            color: #666;
            margin-top: 3px;
            font-family: Arial, sans-serif;
        }

        /* ── Tables ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 6px;
        }
        thead tr { background: #1a3a5c; }
        th {
            color: #fff;
            padding: 5px 10px;
            text-align: left;
            font-size: 9.5pt;
            font-family: Arial, sans-serif;
            font-weight: bold;
        }
        th.num, td.num { text-align: right; }
        td {
            padding: 4px 10px;
            border-bottom: 1px solid #e8ecf0;
            vertical-align: top;
        }
        tr:nth-child(even) td { background: #f5f8fb; }
        .total-row td {
            font-weight: bold;
            border-top: 2px solid #1a3a5c;
            background: #eef2f7;
        }
        .no-data { color: #bbb; font-style: italic; font-size: 9.5pt; }

        /* ── Signature block ── */
        .sig-section {
            margin-top: 48px;
            padding-top: 20px;
            border-top: 1.5px solid #ccc;
        }
        .sig-intro {
            font-size: 10pt;
            color: #444;
            margin-bottom: 24px;
        }
        .sig-grid {
            display: table;
            width: 100%;
            margin-top: 16px;
        }
        .sig-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 12px;
        }
        .sig-line {
            border-top: 1.5px solid #333;
            margin-top: 36px;
            padding-top: 4px;
        }
        .sig-name {
            font-size: 10.5pt;
            font-weight: bold;
            color: #111;
            text-transform: uppercase;
        }
        .sig-role {
            font-size: 9.5pt;
            color: #555;
            margin-top: 2px;
        }

        /* ── Footer ── */
        .report-footer {
            margin-top: 32px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 8.5pt;
            color: #aaa;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        /* ── Page break hints ── */
        .page-break { page-break-before: always; }
        .avoid-break { page-break-inside: avoid; }

        /* ── Print overrides ── */
        @page {
            size: A4 portrait;
            margin: 18mm 20mm 18mm 20mm;
        }
        @media print {
            body { background: #fff; font-size: 10.5pt; }
            .page-wrapper { box-shadow: none; max-width: none; }
            .controls { display: none; }
            .report-body { padding: 0; }
            h2 { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            thead tr { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .kpi-card { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
<div class="page-wrapper">

    {{-- ── Screen-only controls ── --}}
    <div class="controls">
        <a href="{{ route('analytics.index') }}">← Dashboard</a>
        <div class="spacer"></div>
        <form class="filter-form" method="GET" action="{{ route('analytics.report') }}">
            <select name="year">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                @endfor
            </select>
            <select name="month">
                <option value="0" @selected($month == 0)>All Months</option>
                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $mi => $mn)
                    <option value="{{ $mi + 1 }}" @selected($month == $mi + 1)>{{ $mn }}</option>
                @endforeach
            </select>
            <button type="submit">Refresh</button>
        </form>
        <button class="btn-print" onclick="window.print()">🖨 Print / PDF</button>
    </div>

    <div class="report-body">

        {{-- ── Letterhead ── --}}
        <div class="letterhead">
            <div class="republic">Republic of the Philippines</div>
            <div class="university">Carlos Hilado Memorial State University</div>
            <div class="address">Talisay City, Negros Occidental</div>
            <div class="office">Guidance and Counseling Office</div>
            <div class="report-title">Accomplishment Report</div>
            <div class="period">
                @php
                    $monthNames = ['','January','February','March','April','May','June',
                                   'July','August','September','October','November','December'];
                @endphp
                Period: {{ $month ? $monthNames[$month] . ' ' . $year : 'Calendar Year ' . $year }}
            </div>
            <div class="generated">Generated: {{ now()->format('F d, Y \a\t h:i A') }}</div>
        </div>

        {{-- ── Executive Summary ── --}}
        <h2>I. EXECUTIVE SUMMARY</h2>
        <div class="kpi-grid">
            <div class="kpi-row">
                <div class="kpi-card avoid-break">
                    <div class="kpi-val">{{ number_format($totalStudents) }}</div>
                    <div class="kpi-lbl">Students on Record</div>
                </div>
                <div class="kpi-card avoid-break">
                    <div class="kpi-val">{{ number_format($totalAppointments) }}</div>
                    <div class="kpi-lbl">Appointments</div>
                </div>
                <div class="kpi-card avoid-break">
                    <div class="kpi-val">{{ number_format($totalSessions) }}</div>
                    <div class="kpi-lbl">Counseling Sessions</div>
                </div>
            </div>
            <div class="kpi-row" style="height:8px"></div>
            <div class="kpi-row">
                <div class="kpi-card avoid-break">
                    <div class="kpi-val">{{ number_format($totalReferrals) }}</div>
                    <div class="kpi-lbl">Faculty Referrals</div>
                </div>
                <div class="kpi-card avoid-break">
                    <div class="kpi-val">{{ number_format($totalDisciplinary) }}</div>
                    <div class="kpi-lbl">Disciplinary Records</div>
                </div>
                <div class="kpi-card avoid-break">
                    <div class="kpi-val">{{ number_format($totalCertificates) }}</div>
                    <div class="kpi-lbl">Certificates Issued</div>
                </div>
            </div>
        </div>

        {{-- ── Student Demographics ── --}}
        <h2>II. STUDENT DEMOGRAPHICS</h2>

        <h3>A. Distribution by College / Department</h3>
        @php $collegeTotal = $byCollege->sum('total'); @endphp
        <table class="avoid-break">
            <thead><tr>
                <th style="width:60%">College / Department</th>
                <th class="num" style="width:20%">No. of Students</th>
                <th class="num" style="width:20%">Percentage</th>
            </tr></thead>
            <tbody>
                @forelse($byCollege as $row)
                <tr>
                    <td>{{ $row->college }}</td>
                    <td class="num">{{ number_format($row->total) }}</td>
                    <td class="num">{{ $collegeTotal ? number_format($row->total / $collegeTotal * 100, 1) . '%' : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="no-data">No data available.</td></tr>
                @endforelse
                @if($byCollege->isNotEmpty())
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td class="num">{{ number_format($collegeTotal) }}</td>
                    <td class="num">100.0%</td>
                </tr>
                @endif
            </tbody>
        </table>

        <h3>B. Distribution by Sex</h3>
        @php $sexTotal = $bySex->sum('total'); @endphp
        <table class="avoid-break">
            <thead><tr>
                <th>Sex</th>
                <th class="num">Count</th>
                <th class="num">Percentage</th>
            </tr></thead>
            <tbody>
                @foreach($bySex as $row)
                <tr>
                    <td>{{ ucfirst($row->sex ?? 'Not Specified') }}</td>
                    <td class="num">{{ number_format($row->total) }}</td>
                    <td class="num">{{ $sexTotal ? number_format($row->total / $sexTotal * 100, 1) . '%' : '—' }}</td>
                </tr>
                @endforeach
                @if($bySex->isNotEmpty())
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td class="num">{{ number_format($sexTotal) }}</td>
                    <td class="num">100.0%</td>
                </tr>
                @endif
            </tbody>
        </table>

        <h3>C. Distribution by Year Level</h3>
        @php $ylTotal = $byYearLevel->sum('total'); @endphp
        <table class="avoid-break">
            <thead><tr>
                <th>Year Level</th>
                <th class="num">Count</th>
                <th class="num">Percentage</th>
            </tr></thead>
            <tbody>
                @forelse($byYearLevel as $row)
                <tr>
                    <td>{{ $row->year_level ?? 'Not Specified' }}</td>
                    <td class="num">{{ number_format($row->total) }}</td>
                    <td class="num">{{ $ylTotal ? number_format($row->total / $ylTotal * 100, 1) . '%' : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="no-data">No data available.</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3>D. Academic Status</h3>
        @php $asTotal = $byAcademicStatus->sum('total'); @endphp
        <table class="avoid-break">
            <thead><tr>
                <th>Status</th>
                <th class="num">Count</th>
                <th class="num">Percentage</th>
            </tr></thead>
            <tbody>
                @foreach($byAcademicStatus as $row)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $row->academic_status ?? 'Unknown')) }}</td>
                    <td class="num">{{ number_format($row->total) }}</td>
                    <td class="num">{{ $asTotal ? number_format($row->total / $asTotal * 100, 1) . '%' : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ── Appointments ── --}}
        <div class="page-break"></div>
        <h2>III. APPOINTMENTS ({{ $month ? $monthNames[$month] . ' ' : '' }}{{ $year }})</h2>

        <h3>A. By Status</h3>
        @php $apptTotal = $apptByStatus->sum('total'); @endphp
        <table class="avoid-break">
            <thead><tr>
                <th>Status</th>
                <th class="num">Count</th>
                <th class="num">Percentage</th>
            </tr></thead>
            <tbody>
                @forelse($apptByStatus as $row)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $row->status)) }}</td>
                    <td class="num">{{ number_format($row->total) }}</td>
                    <td class="num">{{ $apptTotal ? number_format($row->total / $apptTotal * 100, 1) . '%' : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="no-data">No appointments recorded.</td></tr>
                @endforelse
                @if($apptByStatus->isNotEmpty())
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td class="num">{{ number_format($apptTotal) }}</td>
                    <td class="num">100.0%</td>
                </tr>
                @endif
            </tbody>
        </table>

        <h3>B. By Type of Counseling</h3>
        <table class="avoid-break">
            <thead><tr>
                <th>Appointment Type</th>
                <th class="num">Count</th>
                <th class="num">Percentage</th>
            </tr></thead>
            <tbody>
                @forelse($apptByType as $row)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $row->appointment_type)) }}</td>
                    <td class="num">{{ number_format($row->total) }}</td>
                    <td class="num">{{ $apptTotal ? number_format($row->total / $apptTotal * 100, 1) . '%' : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="no-data">No data available.</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3>C. Monthly Trend</h3>
        @php
            $mNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        @endphp
        <table class="avoid-break">
            <thead><tr>
                @foreach($mNames as $mn)<th class="num" style="width:8%">{{ $mn }}</th>@endforeach
                <th class="num">Total</th>
            </tr></thead>
            <tbody>
                <tr>
                    @for($m = 1; $m <= 12; $m++)
                        <td class="num">{{ $apptByMonth[$m] ?? 0 }}</td>
                    @endfor
                    <td class="num" style="font-weight:bold">{{ number_format($apptByMonth->sum()) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ── Counseling Sessions ── --}}
        <h2>IV. COUNSELING SESSIONS ({{ $year }})</h2>
        <p style="font-size:10pt;margin-bottom:10px">Total recorded sessions: <strong>{{ number_format($totalSessions) }}</strong></p>

        <h3>Presenting Concerns</h3>
        @php $concTotal = $sessionsByConcern->sum('total'); @endphp
        <table class="avoid-break">
            <thead><tr>
                <th>Presenting Concern</th>
                <th class="num">Frequency</th>
                <th class="num">Percentage</th>
            </tr></thead>
            <tbody>
                @forelse($sessionsByConcern as $row)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $row->presenting_concern ?? 'Not Specified')) }}</td>
                    <td class="num">{{ number_format($row->total) }}</td>
                    <td class="num">{{ $concTotal ? number_format($row->total / $concTotal * 100, 1) . '%' : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="no-data">No sessions recorded.</td></tr>
                @endforelse
                @if($sessionsByConcern->isNotEmpty())
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td class="num">{{ number_format($concTotal) }}</td>
                    <td class="num">100.0%</td>
                </tr>
                @endif
            </tbody>
        </table>

        {{-- ── Referrals ── --}}
        <div class="page-break"></div>
        <h2>V. FACULTY REFERRALS ({{ $year }})</h2>

        <h3>A. By Category</h3>
        @php $refCatTotal = $refByCategory->sum('total'); @endphp
        <table class="avoid-break">
            <thead><tr>
                <th>Reason Category</th>
                <th class="num">Count</th>
                <th class="num">Percentage</th>
            </tr></thead>
            <tbody>
                @forelse($refByCategory as $row)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $row->reason_category)) }}</td>
                    <td class="num">{{ number_format($row->total) }}</td>
                    <td class="num">{{ $refCatTotal ? number_format($row->total / $refCatTotal * 100, 1) . '%' : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="no-data">No referrals recorded.</td></tr>
                @endforelse
                @if($refByCategory->isNotEmpty())
                <tr class="total-row"><td>TOTAL</td><td class="num">{{ number_format($refCatTotal) }}</td><td class="num">100.0%</td></tr>
                @endif
            </tbody>
        </table>

        <h3>B. By Urgency Level</h3>
        @php $refUrgTotal = $refByUrgency->sum('total'); @endphp
        <table class="avoid-break">
            <thead><tr>
                <th>Urgency</th>
                <th class="num">Count</th>
                <th class="num">Percentage</th>
            </tr></thead>
            <tbody>
                @foreach($refByUrgency as $row)
                <tr>
                    <td>{{ ucfirst($row->urgency) }}</td>
                    <td class="num">{{ number_format($row->total) }}</td>
                    <td class="num">{{ $refUrgTotal ? number_format($row->total / $refUrgTotal * 100, 1) . '%' : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h3>C. By Status</h3>
        <table class="avoid-break">
            <thead><tr>
                <th>Status</th>
                <th class="num">Count</th>
                <th class="num">Percentage</th>
            </tr></thead>
            <tbody>
                @foreach($refByStatus as $row)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $row->status)) }}</td>
                    <td class="num">{{ number_format($row->total) }}</td>
                    <td class="num">{{ $refUrgTotal ? '—' : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ── Disciplinary ── --}}
        <h2>VI. DISCIPLINARY RECORDS ({{ $year }})</h2>
        <p style="font-size:10pt;margin-bottom:10px">Total cases: <strong>{{ number_format($totalDisciplinary) }}</strong></p>

        <h3>By Type of Offense</h3>
        @php $discTotal = $discByType->sum('total'); @endphp
        <table class="avoid-break">
            <thead><tr>
                <th>Offense Type</th>
                <th class="num">Count</th>
                <th class="num">Percentage</th>
            </tr></thead>
            <tbody>
                @forelse($discByType as $row)
                <tr>
                    <td>{{ ucfirst($row->offense_type) }}</td>
                    <td class="num">{{ number_format($row->total) }}</td>
                    <td class="num">{{ $discTotal ? number_format($row->total / $discTotal * 100, 1) . '%' : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="no-data">No disciplinary records.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- ── Clearance & Certificates ── --}}
        <h2>VII. CLEARANCE & CERTIFICATES ({{ $year }})</h2>
        <table class="avoid-break">
            <thead><tr>
                <th style="width:70%">Metric</th>
                <th class="num" style="width:30%">Value</th>
            </tr></thead>
            <tbody>
                <tr><td>Clearance Requests Filed</td><td class="num">{{ number_format($totalClearance) }}</td></tr>
                <tr><td>Clearance Approved</td><td class="num">{{ number_format($approvedClearance) }}</td></tr>
                <tr>
                    <td>Clearance Approval Rate</td>
                    <td class="num">{{ $totalClearance ? number_format($approvedClearance / $totalClearance * 100, 1) . '%' : '—' }}</td>
                </tr>
                <tr><td>Good Moral Certificates Issued</td><td class="num">{{ number_format($totalCertificates) }}</td></tr>
                <tr><td>Psychological Test Results Recorded</td><td class="num">{{ number_format($totalResults) }}</td></tr>
            </tbody>
        </table>

        @if($clearanceByType->isNotEmpty())
        <h3>Clearance by Type</h3>
        <table class="avoid-break">
            <thead><tr>
                <th>Type</th>
                <th class="num">Count</th>
            </tr></thead>
            <tbody>
                @foreach($clearanceByType as $row)
                <tr>
                    <td>{{ ucfirst($row->clearance_type) }}</td>
                    <td class="num">{{ number_format($row->total) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- ── Signature Block ── --}}
        <div class="sig-section avoid-break">
            <p class="sig-intro">Prepared and submitted in compliance with CHED reporting requirements and CHMSU institutional policy on student welfare services.</p>
            <div class="sig-grid">
                <div class="sig-col">
                    <div class="sig-line">
                        <div class="sig-name">Guidance Counselor</div>
                        <div class="sig-role">Prepared by</div>
                    </div>
                </div>
                <div class="sig-col">
                    <div class="sig-line">
                        <div class="sig-name">Guidance Director</div>
                        <div class="sig-role">Reviewed by</div>
                    </div>
                </div>
                <div class="sig-col">
                    <div class="sig-line">
                        <div class="sig-name">VP for Academic Affairs</div>
                        <div class="sig-role">Noted by</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="report-footer">
            CHMSU Guidance Management System &bull; Generated {{ now()->format('F d, Y') }} &bull; For official use only
        </div>

    </div>{{-- /report-body --}}
</div>{{-- /page-wrapper --}}

<script>
    // Auto-sync controls with URL params on load for the filter form
    (function () {
        const params = new URLSearchParams(window.location.search);
        const y = params.get('year');
        const m = params.get('month');
        if (y) document.querySelector('select[name=year]').value = y;
        if (m) document.querySelector('select[name=month]').value = m;
    })();
</script>
</body>
</html>
