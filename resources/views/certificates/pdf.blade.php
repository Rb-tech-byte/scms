<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; margin: 0; padding: 24px; }
        .frame { border: 8px double #013ba6; padding: 36px; text-align: center; }
        .org { color: #013ba6; font-size: 24px; font-weight: bold; margin-bottom: 6px; }
        .sub { color: #228ddd; font-size: 14px; margin-bottom: 24px; }
        .title { font-size: 32px; letter-spacing: 2px; margin: 16px 0; color: #013ba6; }
        .name { font-size: 28px; font-weight: bold; margin: 16px 0; border-bottom: 1px solid #9ca3af; display: inline-block; padding: 0 24px 6px; }
        .course { font-size: 20px; font-weight: bold; color: #111827; margin: 12px 0; }
        .meta { font-size: 13px; color: #4b5563; margin-top: 24px; }
        .qr { margin-top: 18px; }
    </style>
</head>
<body>
    <div class="frame">
        <div class="org">THE OPEN UNIVERSITY OF TANZANIA</div>
        <div class="sub">INSTITUTE OF EDUCATIONAL AND MANAGEMENT TECHNOLOGIES</div>
        <div class="title">CERTIFICATE OF COMPLETION</div>
        <p>This is to certify that</p>
        <div class="name">{{ $certificate->applicant?->full_name }}</div>
        <p>has successfully completed the short course programme in</p>
        <div class="course">{{ $certificate->course?->title }}</div>
        <p>Delivered at <strong>{{ $certificate->branch?->name ?? 'Main Centre' }}</strong>
           ({{ $certificate->course?->teachingMethodLabel() ?? 'Face to Face' }})</p>

        <div class="meta">
            Serial Number: <strong>{{ $certificate->serial_number }}</strong> |
            Issued Date: <strong>{{ ($certificate->issued_at ?? now())->format('d M Y') }}</strong>
        </div>
        <div class="qr">
            <img src="data:image/svg+xml;base64,{{ $qrSvg }}" width="90" height="90" alt="Verify QR"><br>
            <small>{{ $verifyUrl }}</small>
        </div>
    </div>
</body>
</html>

