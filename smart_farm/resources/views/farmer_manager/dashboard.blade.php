<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Farmer Manager Dashboard - {{ config('app.name', 'Smart Farm') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-5">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-4">Farmer Manager Dashboard</h1>
            <p class="mb-4">Welcome, {{ Auth::user()->name }}!</p>
            <p class="mb-4"><strong>Farm:</strong> {{ $farm->name }}</p>

            <!-- ملخص سريع -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-100 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold">Total Reports</h2>
                    <p class="text-2xl">{{ $reports }}</p>
                </div>
                <div class="bg-red-100 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold">Recent Alerts</h2>
                    <p class="text-2xl">{{ $recentAlerts->count() }}</p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold">Pending Tasks</h2>
                    <p class="text-2xl">{{ $pendingTasks }}</p>
                </div>
            </div>

            <!-- آخر التنبيهات -->
            <div class="bg-gray-100 p-4 rounded-lg">
                <h2 class="text-lg font-semibold">Recent Alerts</h2>
                <table class="w-full mt-2">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Message</th>
                            <th class="border px-4 py-2">Priority</th>
                            <th class="border px-4 py-2">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAlerts as $alert)
                            <tr>
                                <td class="border px-4 py-2">{{ $alert->message }}</td>
                                <td class="border px-4 py-2">{{ $alert->priority }}</td>
                                <td class="border px-4 py-2">{{ $alert->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- روابط سريعة -->
            <div class="flex justify-end mt-6">
                <a href="{{ route('reports.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 mr-2">
                    View Reports
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>