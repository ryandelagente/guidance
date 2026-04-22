<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Good Moral Certificate – {{ $certificate->certificate_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', serif;
            font-size: 12pt;
            color: #222;
            background: #fff;
        }

        .page {
            width: 190mm;
            margin: 0 auto;
            padding: 20mm 18mm;
            border: 6px double #1a3a5c;
            position: relative;
        }

        .inner-border {
            position: absolute;
            top: 8px; left: 8px; right: 8px; bottom: 8px;
            border: 2px solid #c8a84b;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
        }

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
            margin: 14px 0;
        }

        .cert-title {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            letter-spacing: 3px;
            color: #1a3a5c;
            text-transform: uppercase;
            margin: 16px 0 6px;
        }

        .cert-subtitle {
            text-align: center;
            font-size: 11pt;
            color: #555;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .cert-number {
            text-align: right;
            font-size: 9pt;
            color: #888;
            margin-bottom: 16px;
        }

        .name-wrapper {
            text-align: center;
            margin: 12px 0;
        }

        .student-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1a3a5c;
            border-bottom: 1px solid #1a3a5c;
            padding: 0 20px 4px;
        }

        .body-text {
            font-size: 11pt;
            line-height: 1.9;
            color: #222;
            text-align: justify;
        }

        .details-table {
            width: 100%;
            margin: 14px 0;
            font-size: 10pt;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 3px 4px;
            vertical-align: top;
        }

        .details-table .label {
            font-weight: bold;
            width: 160px;
            color: #333;
        }

        .purpose-box {
            background: #f8f5ec;
            border-left: 4px solid #c8a84b;
            padding: 8px 12px;
            margin: 14px 0;
            font-size: 10pt;
            font-style: italic;
        }

        .validity {
            font-size: 9pt;
            color: #666;
            margin-top: 12px;
            text-align: center;
        }

        .signature-section {
            margin-top: 40px;
            text-align: right;
        }

        .signature-block {
            display: inline-block;
            text-align: center;
            width: 200px;
        }

        .signature-space {
            height: 45px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
        }

        .signatory-name {
            font-size: 11pt;
            font-weight: bold;
            color: #1a3a5c;
        }

        .signatory-title {
            font-size: 9pt;
            color: #555;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            font-size: 70pt;
            color: rgba(26, 58, 92, 0.04);
            font-weight: bold;
            white-space: nowrap;
            transform: translate(-50%, -50%) rotate(-45deg);
        }

        .footer-note {
            margin-top: 24px;
            font-size: 7pt;
            color: #aaa;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="inner-border"></div>
    <div class="watermark">CHMSU</div>

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

    <table class="details-table">
        <tr>
            <td class="label">Student ID No.:</td>
            <td>{{ $certificate->studentProfile->student_id_number ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Program:</td>
            <td>{{ $certificate->studentProfile->program ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">College:</td>
            <td>{{ $certificate->studentProfile->college ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Year Level:</td>
            <td>{{ $certificate->studentProfile->year_level ?? '—' }}</td>
        </tr>
    </table>

    <div class="purpose-box">
        This certificate is issued upon the request of the student for the purpose of:
        <strong>{{ $certificate->purpose }}</strong>
    </div>

    <p class="body-text" style="margin-top: 10px;">
        This certification is issued this <strong>{{ $certificate->issued_at->format('jS') }}</strong> day of
        <strong>{{ $certificate->issued_at->format('F Y') }}</strong> at Talisay City, Negros Occidental,
        Philippines.
    </p>

    <p class="validity">
        Valid for <strong>{{ $certificate->validity_months }} month{{ $certificate->validity_months > 1 ? 's' : '' }}</strong>
        &mdash; Expires <strong>{{ $certificate->expiresAt()->format('F d, Y') }}</strong>
    </p>

    <div class="signature-section">
        <div class="signature-block">
            <div class="signature-space"></div>
            <div class="signature-line"></div>
            <div class="signatory-name">{{ $certificate->issuedBy->name ?? '—' }}</div>
            <div class="signatory-title">Guidance Counselor</div>
            <div class="signatory-title">CHMSU Guidance Office</div>
        </div>
    </div>

    <div class="footer-note">
        This document is computer-generated. To verify the authenticity of this certificate,
        contact the CHMSU Guidance and Counseling Office. Certificate No.: {{ $certificate->certificate_number }}
    </div>
</div>
</body>
</html>
