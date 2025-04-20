<!DOCTYPE html>
<html>
<head>
    <title>Report #{{ $report->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #4a7c59;
        }
        .report-details {
            margin-top: 20px;
        }
        .report-details p {
            margin: 5px 0;
        }
        .report-details strong {
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Report #{{ $report->id }}</h1>
    <div class="report-details">
        <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $report->type)) }}</p>
        <p><strong>Farm:</strong> {{ $report->farm ? $report->farm->name : 'N/A' }}</p>
        <p><strong>User:</strong> {{ $report->user ? $report->user->name : 'N/A' }}</p>
        <p><strong>Created At:</strong> {{ $report->created_at->format('d M, Y') }}</p>
        <p><strong>Data:</strong></p>
        <ul>
            @foreach ($report->reportDetails as $detail)
                <li><strong>{{ ucfirst(str_replace('_', ' ', $detail->category)) }}:</strong> {{ $detail->value }} @if($detail->description) - {{ $detail->description }} @endif</li>
            @endforeach
        </ul>
    </div>
</body>
</html>