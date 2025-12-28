<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt Confirmation</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        .section { margin-bottom: 12px; }
        .label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; }
    </style>
</head>
<body>
    <h1>Daret Contribution Receipt</h1>

    <div class="section">
        <div><span class="label">Daret:</span> {{ $daret->name }}</div>
        <div><span class="label">Member:</span> {{ $user->name }} ({{ $user->email }})</div>
        <div><span class="label">Amount:</span> {{ number_format($contribution->amount, 2) }}</div>
    </div>

    <div class="section">
        <h3>Confirmation</h3>
        <table>
            <tr>
                <th>Confirmed by</th>
                <th>Confirmed at</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>{{ $confirmedBy?->name ?? 'N/A' }}</td>
                <td>{{ optional($contribution->confirmed_at)->format('Y-m-d H:i') }}</td>
                <td>{{ ucfirst($contribution->status) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <p>
            This PDF serves as a confirmation stamp for the original bank transfer receipt stored in the
            application. It reflects the status at the time of confirmation.
        </p>
    </div>
</body>
</html>
