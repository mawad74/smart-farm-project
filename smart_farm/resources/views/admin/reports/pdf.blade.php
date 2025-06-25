<!DOCTYPE html>
<html>
<head>
    <title>Report #{{ $report->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .header {
            text-align: center;
            padding: 20px;
            background-color: #4a7c59;
            color: #fff;
            border-bottom: 2px solid #3a6147;
        }
        .header img {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
            line-height: 1.6;
        }
        .content h2 {
            color: #4a7c59;
            font-size: 20px;
            margin-top: 0;
        }
        .content p {
            margin: 5px 0;
        }
        .content ul {
            list-style-type: none;
            padding: 0;
        }
        .content ul li {
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            color: #666;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 12px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #4a7c59;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('img/0565efa05a6b7d16cb232d2d628c6e6c.png') }}" alt="Smart Farm Logo">
        <h1>Smart Farm Report</h1>
    </div>

    <div class="content">
        <h2>Report Details</h2>
        <p><strong>Report ID:</strong> {{ $report->id }}</p>
        <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $report->type)) }}</p>
        <p><strong>Farm:</strong> {{ $report->farm ? $report->farm->name : 'N/A' }}</p>
        <p><strong>User:</strong> {{ $report->user ? $report->user->name : 'N/A' }}</p>
        <p><strong>Date:</strong> {{ $report->created_at->format('d M, Y') }}</p>

        <h2>Data</h2>
        <table class="table">
            <tr>
                <th>Category</th>
                <th>Value</th>
                <th>Description</th>
            </tr>
            @foreach ($report->reportDetails as $detail)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $detail->category)) }}</td>
                    <td>{{ $detail->value }}</td>
                    <td>{{ $detail->description ?? '-' }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="footer">
        Generated on {{ date('d M, Y H:i') }} by Smart Farm System
    </div>
</body>
</html>