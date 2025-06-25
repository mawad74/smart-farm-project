<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name', 'Smart Farm') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm fixed-top">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('welcome') }}">
                <img src="{{ asset('img/0565efa05a6b7d16cb232d2d628c6e6c.png') }}" 
                     alt="Smart Farm Logo" style="width: 40px; height: auto; margin-right: 10px;">
                <span style="color: #fff; font-weight: bold; font-size: 1.5rem;">Smart Farm</span>
            </a>

            <!-- Notifications and User Info -->
            <div class="d-flex align-items-center ms-auto">
                <!-- Notifications -->
                @if (Auth::check() && Auth::user()->role === 'farmer_manager')
                    @php
                        $recentAlerts = \App\Models\Alert::where('user_id', Auth::user()->id)->latest()->take(5)->count();
                        $totalNotifications = $recentAlerts;

                        $subscription = \App\Models\Subscription::where('user_id', Auth::user()->id)->first();
                        $expiringSoonCount = $subscription && $subscription->status === 'active' && $subscription->end_date && Carbon\Carbon::parse($subscription->end_date)->between(Carbon\Carbon::today(), Carbon\Carbon::today()->addDays(3)) ? 1 : 0;

                        $pendingNotifications = 0;
                        if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                            $pendingNotifications = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count() ?? 0;
                        }
                        $totalNotifications += $expiringSoonCount + $pendingNotifications;
                    @endphp
                    <div class="dropdown me-3">
                        <a class="nav-link notification-icon" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            @if ($totalNotifications > 0)
                                <span class="badge rounded-pill">{{ $totalNotifications }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notifications-dropdown" aria-labelledby="notificationsDropdown">
                            @if ($totalNotifications === 0)
                                <li class="dropdown-item text-muted">No new notifications.</li>
                            @else
                                @if ($expiringSoonCount > 0)
                                    <li>
                                        <a class="dropdown-item text-warning" href="{{ route('farmer_manager.subscriptions.show') }}">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Your subscription will expire on {{ $subscription->end_date->format('d M, Y') }} (within 3 days).
                                        </a>
                                    </li>
                                @endif
                                @if (\Illuminate\Support\Facades\Schema::hasTable('notifications'))
                                    @foreach (\App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->with('user')->get() as $notification)
                                        <li>
                                            <a class="dropdown-item {{ $notification->type === 'report_request_approved' ? 'text-success' : '' }}" href="{{ route('farmer_manager.reports.index') }}" onclick="markAsRead({{ $notification->id }})">
                                                <i class="fas fa-check-circle me-2"></i>
                                                {{ $notification->message }}
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                                @foreach (\App\Models\Alert::where('user_id', Auth::user()->id)->latest()->take(5)->get() as $alert)
                                    <li>
                                        <a class="dropdown-item {{ $alert->severity == 'high' ? 'text-danger' : ($alert->severity == 'medium' ? 'text-warning' : 'text-info') }}" href="{{ route('farmer_manager.alerts.index') }}">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            {{ $alert->message }} - {{ $alert->created_at->format('d M, Y H:i') }}
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                @endif

                <!-- User Info -->
                <div class="dropdown user-profile">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="user-info ms-2 d-flex flex-column">
                            <span class="user-name">{{ Auth::user()->name }}</span>
                            <small class="user-email">{{ Auth::user()->email }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userDropdown">
                        <li class="dropdown-header">
                            <span class="d-block">{{ Auth::user()->name }}</span>
                            <small class="text-muted">Farmer Manager</small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('farmer_manager.profile.edit') }}"><i class="fas fa-user-edit me-2"></i> Edit Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('farmer_manager.subscriptions.show') }}"><i class="fas fa-star me-2"></i> My Subscription</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar shadow-sm">
            <div class="sidebar-header text-center py-4">
                <a href="{{ route('welcome') }}" class="d-flex align-items-center justify-content-center text-decoration-none">
                    <img src="{{ asset('img/0565efa05a6b7d16cb232d2d628c6e6c.png') }}" 
                         alt="Smart Farm Logo" style="width: 40px; height: auto; margin-right: 10px;">
                    <span style="color: #4a7c59; font-weight: bold; font-size: 1.5rem;">Smart Farm</span>
                </a>
            </div>
            <!-- Search Bar -->
            <div class="px-3 mb-3">
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Search..." aria-label="Search">
                    <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmer_manager.dashboard') ? 'active' : '' }}" href="{{ route('farmer_manager.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmer_manager.farms.index') ? 'active' : '' }}" href="{{ route('farmer_manager.farms.index') }}">
                        <i class="fas fa-tractor me-2"></i> Farms
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmer_manager.plants.index') ? 'active' : '' }}" href="{{ route('farmer_manager.plants.index') }}">
                        <i class="fas fa-leaf me-2"></i> Plants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmer_manager.sensors.index') ? 'active' : '' }}" href="{{ route('farmer_manager.sensors.index') }}">
                        <i class="fas fa-microchip me-2"></i> Sensors
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmer_manager.actuators.index') ? 'active' : '' }}" href="{{ route('farmer_manager.actuators.index') }}">
                        <i class="fas fa-cog me-2"></i> Actuators
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmer_manager.weather-data.index') ? 'active' : '' }}" href="{{ route('farmer_manager.weather-data.index') }}">
                        <i class="fas fa-cloud me-2"></i> Weather Data
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmer_manager.alerts.index') ? 'active' : '' }}" href="{{ route('farmer_manager.alerts.index') }}">
                        <i class="fas fa-bell me-2"></i> Alerts
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmer_manager.control-commands.index') ? 'active' : '' }}" href="{{ route('farmer_manager.control-commands.index') }}">
                        <i class="fas fa-cogs me-2"></i> Control Commands
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmer_manager.disease-detections.index') ? 'active' : '' }}" href="{{ route('farmer_manager.disease-detections.index') }}">
                        <i class="fas fa-virus me-2"></i> Disease Detections
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmer_manager.schedules.index') ? 'active' : '' }}" href="{{ route('farmer_manager.schedules.index') }}">
                        <i class="fas fa-calendar-alt me-2"></i> Schedules
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmer_manager.reports.index') ? 'active' : '' }}" href="{{ route('farmer_manager.reports.index') }}">
                        <i class="fas fa-file-alt me-2"></i> Reports
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="content flex-grow-1 p-4">
            @yield('dashboard-content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')

    <!-- JavaScript for Dropdown Margin -->
    <script>
        
        document.addEventListener('DOMContentLoaded', function () {
            const dropdowns = document.querySelectorAll('.sidebar .sidebar-dropdown');
            dropdowns.forEach(dropdown => {
                const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                dropdown.addEventListener('show.bs.dropdown', function () {
                    dropdown.style.marginBottom = `${dropdownMenu.scrollHeight}px`;
                });
                dropdown.addEventListener('hide.bs.dropdown', function () {
                    dropdown.style.marginBottom = '0';
                });
            });
        });

        function markAsRead(notificationId) {
        fetch('/mark-notification-as-read/' + notificationId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notificationItem = document.querySelector(`a[onclick="markAsRead(${notificationId})"]`).closest('li');
                if (notificationItem) {
                    notificationItem.remove();
                }
                // تحديث العداد إذا كان موجود
                const badge = document.querySelector('.notification-icon .badge');
                if (badge) {
                    let count = parseInt(badge.textContent) - 1;
                    if (count <= 0) {
                        badge.remove();
                    } else {
                        badge.textContent = count;
                    }
                }
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }
    </script>

</body>
</html>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar {
        background: #4a7c59;
        border-bottom: 2px solid #3a6147;
        padding: 10px 20px;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
    }

    .navbar-brand span {
        font-size: 1.5rem;
        color: #fff;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .user-profile .nav-link {
        padding: 8px 15px;
        border-radius: 10px;
        transition: background-color 0.3s ease;
    }

    .user-profile .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #fff 0%, #e0e0e0 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .user-avatar i {
        font-size: 1.5rem;
        color: #4a7c59;
    }

    .user-info {
        color: #fff;
        text-align: left;
    }

    .user-info .user-name {
        font-size: 1rem;
        font-weight: 600;
        display: block;
    }

    .user-info .user-email {
        font-size: 0.8rem;
        color: #ddd;
        display: block;
    }

    .user-dropdown {
        background-color: #fff;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
        min-width: 200px;
    }

    .user-dropdown .dropdown-header {
        padding: 10px 15px;
        text-align: center;
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
    }

    .user-dropdown .dropdown-item {
        color: #333;
        font-size: 0.9rem;
        padding: 8px 15px;
        transition: background-color 0.3s ease;
    }

    .user-dropdown .dropdown-item:hover {
        background-color: #4a7c59;
        color: #fff;
    }

    .user-dropdown .dropdown-item i {
        color: #4a7c59;
    }

    .user-dropdown .dropdown-item:hover i {
        color: #fff;
    }

    .notification-icon {
        position: relative;
        color: #fff;
        font-size: 1.5rem;
        padding: 8px 15px;
        border-radius: 10px;
        transition: background-color 0.3s ease;
    }

    .notification-icon:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .notification-icon .badge {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: #a3c585;
        color: #fff;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .notifications-dropdown {
        background-color: #fff;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
        min-width: 300px;
        max-height: 400px;
        overflow-y: auto;
    }

    .notifications-dropdown .dropdown-item {
        color: #333;
        font-size: 0.9rem;
        padding: 10px 15px;
        transition: background-color 0.3s ease;
        border-bottom: 1px solid #e0e0e0;
    }

    .notifications-dropdown .dropdown-item:last-child {
        border-bottom: none;
    }

    .notifications-dropdown .dropdown-item:hover {
        background-color: #4a7c59;
        color: #fff;
    }

    .notifications-dropdown .dropdown-item.text-danger {
        color: #dc3545 !important;
    }

    .notifications-dropdown .dropdown-item.text-danger:hover {
        color: #fff !important;
    }

    .notifications-dropdown .dropdown-item.text-warning {
        color: #ffc107 !important;
    }

    .notifications-dropdown .dropdown-item.text-warning:hover {
        color: #fff !important;
    }

    .notifications-dropdown .dropdown-item.text-success {
        color: #28a745 !important;
    }

    .notifications-dropdown .dropdown-item.text-success:hover {
        color: #fff !important;
    }

    .notifications-dropdown .dropdown-item i {
        color: inherit;
    }

    .sidebar {
        width: 250px;
        height: 100vh;
        background-color: #f8f9fa;
        border-right: 1px solid #e0e0e0;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 70px;
        overflow-y: auto;
        transition: margin-bottom 0.3s ease;
    }

    .sidebar .sidebar-header a {
        text-decoration: none;
    }

    .sidebar .form-control {
        border-radius: 20px;
        border: 1px solid #e0e0e0;
        font-size: 0.9rem;
    }

    .sidebar .btn-outline-success {
        border-radius: 20px;
        border-color: #4a7c59;
        color: #4a7c59;
    }

    .sidebar .btn-outline-success:hover {
        background-color: #4a7c59;
        color: #fff;
    }

    .sidebar .nav-link {
        color: #555;
        padding: 12px 20px;
        font-size: 1rem;
        transition: background-color 0.3s ease;
        border-left: 3px solid transparent;
    }

    .sidebar .nav-link:hover {
        background-color: #e9ecef;
        border-left: 3px solid #4a7c59;
    }

    .sidebar .nav-link.active {
        background-color: #4a7c59;
        color: white;
        border-left: 3px solid #3a6147;
    }

    .sidebar .nav-link i {
        width: 20px;
        text-align: center;
    }

    .content {
        margin-left: 250px;
        margin-top: 70px;
        background-color: #fff;
        min-height: calc(100vh - 70px);
    }

    @media (max-width: 768px) {
        .sidebar { width: 200px; }
        .content { margin-left: 200px; margin-top: 60px; }
        .navbar-brand span { font-size: 1.2rem; }
        .navbar { padding: 8px 15px; }
        .user-avatar { width: 35px; height: 35px; }
        .user-avatar i { font-size: 1.2rem; }
        .user-info .user-name { font-size: 0.9rem; }
        .user-info .user-email { font-size: 0.7rem; }
        .notification-icon { font-size: 1.2rem; }
        .notification-icon .badge { font-size: 0.6rem; padding: 1px 4px; }
        .notifications-dropdown { min-width: 250px; }
        .sidebar .nav-link { padding: 10px 15px; font-size: 0.95rem; }
    }
</style>