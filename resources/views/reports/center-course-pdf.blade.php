<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2 { color: #013ba6; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        th { background-color: #013ba6; color: #ffffff; }
    </style>
</head>
<body>
    <h2>OUT SCMIS — Ongoing Regional Center Course Report ({{ ucfirst($period) }})</h2>
    <div>Generated: {{ $generatedAt }}</div>
    <table>
        <thead>
            <tr>
                <th>Centre</th>
                <th>Course</th>
                <th>Method</th>
                <th>Total Students</th>
                <th>Full Paid</th>
                <th>Half Paid</th>
                <th>Unpaid</th>
                <th>Collected (TZS)</th>
                <th>Outstanding (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['branch_name'] }}</td>
                    <td>{{ $row['course_title'] }}</td>
                    <td>{{ $row['teaching_method'] }}</td>
                    <td>{{ $row['total_students'] }}</td>
                    <td>{{ $row['full_paid'] }}</td>
                    <td>{{ $row['half_paid'] }}</td>
                    <td>{{ $row['unpaid'] }}</td>
                    <td>{{ number_format($row['collected'], 2) }}</td>
                    <td>{{ number_format($row['outstanding'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

