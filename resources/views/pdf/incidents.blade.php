<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { font-weight: bold; }
        .company-info { margin-bottom: 10px; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin: 20px 0 10px 0; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .meta-table td { padding: 4px 8px; border: 1px solid #ddd; background: #f6f6f6; }
        .meta-table td.label { font-weight: bold; background: #eaeaea; width: 120px; }
        .events-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .events-table th { background: #222; color: #fff; font-weight: bold; padding: 6px 4px; border: 1px solid #222; font-size: 12px; }
        .events-table td { border: 1px solid #ddd; padding: 4px 4px; font-size: 12px; }
        .footer { font-size: 10px; color: #888; margin-top: 20px; text-align: left; }
        hr { border: none; border-top: 2px solid #222; margin: 10px 0 15px 0; }
    </style>
</head>
<body>
    <div class="company-info header">
        Patrol Sync<br>
        https://qrpatrol.co.uk/<br>
    </div>
    <hr>
    <div class="title">Incident Reports</div>
    <table class="meta-table">
        <tr><td class="label">Client</td><td>{{ $filters['client'] ?? 'All' }}</td></tr>
        <tr><td class="label">Branch</td><td>{{ $filters['branch'] ?? 'All' }}</td></tr>
        <tr><td class="label">Guard</td><td>{{ $filters['guard'] ?? 'All' }}</td></tr>
        <tr><td class="label">Date Range</td><td>{{ $filters['date_range'] ?? (isset($from) && isset($to) ? $from.' - '.$to : 'All') }}</td></tr>
    </table>
    <table class="events-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Message</th>
                <th>Checkpoint Name</th>
                <th>Guard Name</th>
            </tr>
        </thead>
        <tbody>
        @foreach($incidents as $incident)
            <tr>
                <td>{{ \Carbon\Carbon::parse($incident->created_at)->format('d-m-Y H:i:s') }}</td>
                <td>{{ $incident->type ?? '-' }}</td>
                <td>{{ $incident->message ?? '-' }}</td>
                <td>{{ $incident->checkpoint->name ?? '-' }}</td>
                <td>{{ $incident->user->name ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="footer">
        Page: {PAGE_NUM} / {PAGE_COUNT}<br>
        Generated date: {{ now()->format('d-m-Y H:i:s') }}
    </div>
</body>
</html>
