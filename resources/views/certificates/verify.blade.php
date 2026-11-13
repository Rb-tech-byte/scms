<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Verify Certificate - {{ config('app.name', 'SCMIS') }}</title>
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/custom-brand.css') }}" rel="stylesheet" />
    <link rel="shortcut icon" href="{{ asset('mini.png') }}">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ url('/') }}">
                <img src="{{ asset('mini.png') }}" alt="Logo" style="height:28px;background:#fff;border-radius:4px;padding:2px;">
                <span>OUT Short Courses MIS &mdash; Certificate Verification</span>
            </a>
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light">
                @auth Dashboard @else Sign In @endauth
            </a>
        </div>
    </nav>

    <main class="container" style="max-width: 780px;">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Verify Certificate Authenticity</div>
            <div class="card-body">
                <form method="GET" action="{{ route('certificates.verify') }}" class="row g-2">
                    <div class="col-md-8">
                        <input type="text" name="serial" class="form-control" value="{{ $serial }}" placeholder="Enter Certificate Serial Number (e.g. OUT-CERT-2026-...)" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Verify Authenticity</button>
                    </div>
                </form>
            </div>
        </div>

        @if($serial !== '')
            @if($certificate)
                <div class="alert alert-success shadow-sm">
                    <h5 class="mb-2">Verified Authentic Certificate</h5>
                    <p class="mb-1"><strong>Serial #:</strong> <code>{{ $certificate->serial_number }}</code></p>
                    <p class="mb-1"><strong>Student Name:</strong> {{ $certificate->applicant?->full_name ?? '-' }}</p>
                    <p class="mb-1"><strong>Course Title:</strong> {{ $certificate->course?->title ?? '-' }}</p>
                    <p class="mb-1"><strong>Centre:</strong> {{ $certificate->branch?->name ?? '-' }}</p>
                    <p class="mb-0"><strong>Status:</strong> {{ ucfirst($certificate->status) }}
                        @if($certificate->issued_at) &mdash; Issued {{ $certificate->issued_at->format('d M Y') }} @endif
                    </p>
                </div>
            @else
                <div class="alert alert-danger shadow-sm">
                    No matching certificate found for serial <code>{{ $serial }}</code>.
                </div>
            @endif
        @endif
    </main>
</body>
</html>
