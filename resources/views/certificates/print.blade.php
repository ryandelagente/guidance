<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Good Moral Certificate – {{ $certificate->certificate_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }

        .certificate {
            background: #fff;
            width: 215mm;
            min-height: 280mm;
            padding: 25mm 22mm;
            border: 8px double #1a3a5c;
            position: relative;
            box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        }

        .certificate::before {
            content: '';
            position: absolute;
            top: 10px; left: 10px; right: 10px; bottom: 10px;
            border: 2px solid #c8a84b;
            pointer-events: none;
        }

        .header { text-align: center; margin-bottom: 24px; }

        .school-name {
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 1px;
            color: #1a3a5c;
            text-transform: uppercase;
        }

        .department {
            font-size: 11pt;
            color: #444;
            margin-top: 4px;
        }

        .address {
            font-size: 9pt;
            color: #666;
            margin-top: 2px;
        }

        .divider {
            border: none;
            border-top: 2px solid #c8a84b;
            margin: 16px 0;
        }

        .cert-title {
            text-align: center;
            font-size: 22pt;
            font-weight: bold;
            letter-spacing: 3px;
            color: #1a3a5c;
            text-transform: uppercase;
            margin: 20px 0 8px;
        }

        .cert-subtitle {
            text-align: center;
            font-size: 12pt;
            color: #555;
            letter-spacing: 1px;
            margin-bottom: 30px;
        }

        .cert-number {
            text-align: right;
            font-size: 9pt;
            color: #888;
            margin-bottom: 24px;
        }

        .body-text {
            font-size: 12pt;
            line-height: 1.9;
            color: #222;
            text-align: justify;
        }

        .student-name {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            color: #1a3a5c;
            margin: 10px 0;
            border-bottom: 1px solid #1a3a5c;
            display: inline-block;
            padding: 0 20px 4px;
        }

        .name-wrapper { text-align: center; margin: 8px 0 16px; }

        .details-table { width: 100%; margin: 16px 0; font-size: 11pt; }
        .details-table td { padding: 4px 0; }
        .details-table td:first-child { font-weight: bold; width: 160px; }

        .purpose-box {
            background: #f8f5ec;
            border-left: 4px solid #c8a84b;
            padding: 10px 14px;
            margin: 16px 0;
            font-size: 11pt;
            font-style: italic;
        }

        .validity {
            font-size: 10pt;
            color: #666;
            margin-top: 16px;
            text-align: center;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-block {
            text-align: center;
            width: 220px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 6px;
        }

        .signatory-name {
            font-size: 12pt;
            font-weight: bold;
            color: #1a3a5c;
        }

        .signatory-title {
            font-size: 10pt;
            color: #555;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            color: rgba(26,58,92,0.04);
            font-weight: bold;
            white-space: nowrap;
            pointer-events: none;
            user-select: none;
        }

        .footer-note {
            margin-top: 30px;
            font-size: 8pt;
            color: #999;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }

        .no-print button {
            background: #1a3a5c;
            color: #fff;
            border: none;
            padding: 10px 30px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 10px;
        }

        .no-print a {
            color: #555;
            font-size: 14px;
        }

        @media print {
            body { background: none; padding: 0; }
            .certificate { box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">🖨 Print / Save as PDF</button>
    <a href="{{ route('certificates.show', $certificate) }}">← Back to record</a>
</div>

<div class="certificate">
    <div class="watermark">CHMSU GC</div>

    <div class="header">
        <div class="school-name">Carlos Hilado Memorial State University</div>
        <div class="department">Office of Guidance and Counseling</div>
        <div class="address">Talisay City, Negros Occidental, Philippines</div>
    </div>

    <hr class="divider">

    <div class="cert-title">Certificate of Good Moral Character</div>
    <div class="cert-subtitle">This is to certify that</div>
    <div class="cert-number">Cert. No.: {{ $certificate->certificate_number }}</div>

    <div class="name-wrapper">
        <span class="student-name">{{ strtoupper($certificate->studentProfile->full_name ?? '—') }}</span>
    </div>

    <p class="body-text">
        is a bona fide student of Carlos Hilado Memorial State University and is known to be of good moral
        character. Based on the records on file with the Guidance and Counseling Office, this student has
        demonstrated honesty, integrity, and responsible behavior throughout their enrollment at this institution.
    </p>

    <table class="details-table" style="margin-top: 20px;">
        <tr>
            <td>Student ID No.:</td>
            <td>{{ $certificate->studentProfile->student_id_number ?? '—' }}</td>
        </tr>
        <tr>
            <td>Program:</td>
            <td>{{ $certificate->studentProfile->program ?? '—' }}</td>
        </tr>
        <tr>
            <td>College:</td>
            <td>{{ $certificate->studentProfile->college ?? '—' }}</td>
        </tr>
        <tr>
            <td>Year Level:</td>
            <td>{{ $certificate->studentProfile->year_level ?? '—' }}</td>
        </tr>
    </table>

    <div class="purpose-box">
        This certificate is issued upon the request of the student for the purpose of:
        <strong>{{ $certificate->purpose }}</strong>
    </div>

    <p class="body-text" style="margin-top: 12px;">
        This certification is issued this <strong>{{ $certificate->issued_at->format('jS') }}</strong> day of
        <strong>{{ $certificate->issued_at->format('F Y') }}</strong> at Talisay City, Negros Occidental,
        Philippines.
    </p>

    <p class="validity">
        Valid for <strong>{{ $certificate->validity_months }} month{{ $certificate->validity_months > 1 ? 's' : '' }}</strong>
        — Expires <strong>{{ $certificate->expiresAt()->format('F d, Y') }}</strong>
    </p>

    <div class="signature-section">
        <div class="signature-block">
            <div style="height: 50px;"></div>
            <div class="signature-line"></div>
            <div class="signatory-name">{{ $certificate->issuedBy->name ?? '—' }}</div>
            <div class="signatory-title">Guidance Counselor</div>
            <div class="signatory-title">CHMSU Guidance Office</div>
        </div>
    </div>

    <div class="footer-note">
        This document is computer-generated. To verify the authenticity of this certificate,
        contact the CHMSU Guidance and Counseling Office.
        Certificate No.: {{ $certificate->certificate_number }}
    </div>
</div>

</body>
</html>
