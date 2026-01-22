<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Applicant Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .header img {
            height: 60px;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 5px 0;
            color: #667eea;
            font-size: 18px;
        }

        .filter-info {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 8px;
        }

        .filter-info strong {
            color: #667eea;
        }

        .summary-stats {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 5px;
            margin-right: 5px;
        }

        .stat-box .value {
            font-size: 14px;
            font-weight: bold;
        }

        .stat-box .label {
            font-size: 8px;
            opacity: 0.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 5px 3px;
            text-align: left;
            font-size: 8px;
        }

        th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 600;
            text-transform: uppercase;
            color: #495057;
            font-size: 7px;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .footer {
            margin-top: 30px;
            font-size: 9px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            width: 200px;
            display: inline-block;
            margin: 0 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('mini.png') }}" alt="Logo">
        <h1>APPLICANTS REPORT</h1>
        <div style="font-size: 10px; color: #666;">Generated on {{ $generated_at ?? now()->format('d/m/Y H:i:s') }}</div>
    </div>

    @if(isset($filters) && !empty(array_filter($filters)))
    <div class="filter-info">
        <strong>Applied Filters:</strong>
        @if(!empty($filters['course']))
            Course ID: {{ $filters['course'] }} |
        @endif
        @if(!empty($filters['branch']))
            Branch ID: {{ $filters['branch'] }} |
        @endif
        @if(!empty($filters['venue']))
            Venue ID: {{ $filters['venue'] }} |
        @endif
        @if(!empty($filters['payment_status']))
            Payment Status: {{ $filters['payment_status'] == 1 ? 'Paid' : ($filters['payment_status'] == 2 ? 'Partial' : 'Unpaid') }} |
        @endif
        @if(!empty($filters['date_from']))
            From: {{ $filters['date_from'] }} |
        @endif
        @if(!empty($filters['date_to']))
            To: {{ $filters['date_to'] }}
        @endif
    </div>
    @endif

    @php
        $totalStudents = collect($reports)->unique('applicant_id')->count();
        $totalRevenue = collect($reports)->sum('paid_amount');
        $totalBalance = collect($reports)->sum('balance');
        $totalExpected = collect($reports)->sum('total_fee');
    @endphp

    <div class="summary-stats">
        <div class="stat-box" style="margin-right: 5px;">
            <div class="value">{{ number_format($totalStudents) }}</div>
            <div class="label">TOTAL STUDENTS</div>
        </div>
        <div class="stat-box" style="margin-right: 5px;">
            <div class="value">{{ number_format($totalRevenue, 0) }}</div>
            <div class="label">TOTAL REVENUE (TZS)</div>
        </div>
        <div class="stat-box" style="margin-right: 5px;">
            <div class="value">{{ number_format($totalBalance, 0) }}</div>
            <div class="label">PENDING BALANCE (TZS)</div>
        </div>
        <div class="stat-box">
            <div class="value">{{ $totalExpected > 0 ? number_format(($totalRevenue / $totalExpected) * 100, 1) : 0 }}%</div>
            <div class="label">COLLECTION RATE</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 12%;">Name</th>
                <th style="width: 10%;">Course</th>
                <th style="width: 8%;">Branch</th>
                <th style="width: 8%;">Venue</th>
                <th style="width: 7%;" class="text-right">Total Fee</th>
                <th style="width: 7%;" class="text-right">Paid</th>
                <th style="width: 7%;" class="text-right">Balance</th>
                <th style="width: 7%;">Status</th>
                <th style="width: 8%;">Invoice #</th>
                <th style="width: 8%;">Receipt #</th>
                <th style="width: 7%;">Discount</th>
                <th style="width: 8%;">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $index => $report)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $report['full_name'] ?? $report['name'] ?? '-' }}</td>
                    <td>{{ $report['course_name'] ?? $report['course_enrolled'] ?? '-' }}</td>
                    <td>{{ $report['branch_name'] ?? (is_array($report['branch'] ?? null) ? ($report['branch']['name'] ?? '-') : '-') }}</td>
                    <td>{{ $report['venue_name'] ?? $report['venue'] ?? '-' }}</td>
                    <td class="text-right">{{ number_format($report['total_fee'] ?? $report['payment'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($report['paid_amount'] ?? $report['paid'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($report['balance'] ?? 0, 2) }}</td>
                    <td class="text-center">
                        @php
                            $status = $report['invoice_status'] ?? 'Unpaid';
                        @endphp
                        @if($status == 'Paid')
                            <span class="badge badge-success">PAID</span>
                        @elseif($status == 'Partially Paid')
                            <span class="badge badge-warning">PARTIAL</span>
                        @else
                            <span class="badge badge-danger">UNPAID</span>
                        @endif
                    </td>
                    <td>{{ $report['invoice_number'] ?? $report['invoice_no'] ?? '-' }}</td>
                    <td style="font-size: 7px;">{{ $report['receipt_numbers'] ?? '-' }}</td>
                    <td class="text-right">
                        @if(($report['has_discount'] ?? false) && ($report['discount_amount'] ?? 0) > 0)
                            {{ number_format($report['discount_amount'], 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $report['invoice_date'] ?? $report['date_issued'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center" style="padding: 20px;">No records found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f8f9fa; font-weight: bold;">
                <td colspan="5" class="text-right">TOTALS:</td>
                <td class="text-right">{{ number_format($totalExpected, 2) }}</td>
                <td class="text-right">{{ number_format($totalRevenue, 2) }}</td>
                <td class="text-right">{{ number_format($totalBalance, 2) }}</td>
                <td colspan="5"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="signature-section">
            <div class="signature-box">
                <strong>Instructor Name:</strong> <span class="signature-line"></span>
                <br><br>
                <strong>Signature:</strong> <span class="signature-line"></span>
                <strong style="margin-left: 20px;">Date:</strong> <span class="signature-line"></span>
            </div>
            <div class="signature-box">
                <strong>Coordinator ICT Training:</strong> <span class="signature-line"></span>
                <br><br>
                <strong>Signature:</strong> <span class="signature-line"></span>
                <strong style="margin-left: 20px;">Date:</strong> <span class="signature-line"></span>
            </div>
        </div>
        <div style="margin-top: 20px; font-size: 8px; color: #666;">
            This is a computer-generated report. Total Records: {{ count($reports) }}
        </div>
    </div>
</body>

</html>