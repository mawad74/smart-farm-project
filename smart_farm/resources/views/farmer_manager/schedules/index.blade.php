@extends('layouts.farmer_manager_dashboard')
@section('title', 'My Schedules')
@section('dashboard-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div>
                            <h5 class="card-title">My Schedules</h5>
                            <p class="text-success">Active Schedules</p>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                            <form method="GET" action="{{ route('farmer_manager.schedules.index') }}">
                                <input type="text" name="search" class="form-control" placeholder="Search schedule..." value="{{ request('search') }}">
                            </form>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="typeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filter by Type: {{ request('type') ? ucfirst(request('type')) : 'All' }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="typeDropdown">
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.schedules.index', ['sort' => request('sort'), 'search' => request('search')]) }}">All</a></li>
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.schedules.index', ['type' => 'irrigation', 'sort' => request('sort'), 'search' => request('search')]) }}">Irrigation</a></li>
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.schedules.index', ['type' => 'fertilization', 'sort' => request('sort'), 'search' => request('search')]) }}">Fertilization</a></li>
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.schedules.index', ['type' => 'harvest', 'sort' => request('sort'), 'search' => request('search')]) }}">Harvest</a></li>
                                </ul>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.schedules.index', ['sort' => 'newest', 'search' => request('search'), 'type' => request('type')]) }}">Newest</a></li>
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.schedules.index', ['sort' => 'oldest', 'search' => request('search'), 'type' => request('type')]) }}">Oldest</a></li>
                                </ul>
                            </div>
                            <a href="{{ route('farmer_manager.schedules.create') }}" class="btn btn-success">+ Create Schedule</a>
                        </div>
                    </div>
                    @if ($schedules->isEmpty())
                        <p class="text-muted">No schedules available.</p>
                    @else
                        <div class="calendar-container">
                            <div class="calendar-header">
                                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                                    <div class="calendar-day-header">{{ $day }}</div>
                                @endforeach
                            </div>
                            <div class="calendar-grid">
                                @php
                                    $days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                                    $cardColors = [
                                        'rgba(52, 152, 219, 0.13)', // blue
                                        'rgba(39, 174, 96, 0.13)',  // green
                                        'rgba(241, 196, 15, 0.13)', // yellow
                                        'rgba(231, 76, 60, 0.13)',  // red
                                        'rgba(155, 89, 182, 0.13)', // purple
                                        'rgba(230, 126, 34, 0.13)', // orange
                                        'rgba(26, 188, 156, 0.13)'  // teal
                                    ];
                                @endphp
                                @foreach(range(0,6) as $d)
                                    <div class="calendar-cell">
                                        @foreach($schedules as $schedule)
                                            @php
                                                $scheduleTime = $schedule->schedule_time ? \Carbon\Carbon::parse($schedule->schedule_time) : null;
                                                $scheduleDay = $scheduleTime ? $scheduleTime->dayOfWeek : null;
                                                $bgColor = $cardColors[random_int(0, count($cardColors)-1)];
                                            @endphp
                                            @if($scheduleDay === $d)
                                                <div class="event-card event-{{ $schedule->type }}" style="background: {{ $bgColor }};">
                                                    <div class="event-actions">
                                                        <a href="{{ route('farmer_manager.schedules.edit', $schedule->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                                        <form action="{{ route('farmer_manager.schedules.destroy', $schedule->id) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash-can"></i></button>
                                                        </form>
                                                    </div>
                                                    <div class="event-title">
                                                        <i class="@if($schedule->type==='irrigation') fas fa-water @elseif($schedule->type==='fertilization') fas fa-flask @elseif($schedule->type==='harvest') fas fa-tractor @else fas fa-calendar-alt @endif"></i>
                                                        {{ $schedule->title }}
                                                    </div>
                                                    <div class="event-date">
                                                        <i class="fa-regular fa-calendar"></i>
                                                        {{ $scheduleTime ? $scheduleTime->format('M d') : '' }}
                                                    </div>
                                                    <div class="event-details">
                                                        <span><b>Farm:</b> {{ isset($schedule->farm) && $schedule->farm ? $schedule->farm->name : 'N/A' }}</span>
                                                        <span><b>Plant:</b> {{ isset($schedule->plant) && $schedule->plant ? $schedule->plant->name : 'N/A' }}</span>
                                                        <span><b>Actuator:</b> 
                                                            @if(isset($schedule->actuator) && $schedule->actuator && isset($schedule->actuator->name))
                                                                {{ $schedule->actuator->name }}
                                                            @elseif(isset($schedule->actuator_name) && $schedule->actuator_name)
                                                                {{ $schedule->actuator_name }}
                                                            @elseif(isset($schedule->actuator_id) && $schedule->actuator_id)
                                                                #{{ $schedule->actuator_id }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </span>
                                                        <span><b>Time:</b> {{ $scheduleTime ? $scheduleTime->format('H:i') : 'N/A' }}</span>
                                                        <span><b>Status:</b> <span class="status-badge status-{{ $schedule->status }}">{{ ucfirst($schedule->status) }}</span></span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
.calendar-container {
    background: #f8fafc;
    border-radius: 18px;
    box-shadow: 0 4px 24px 0 rgba(0,0,0,0.07);
    padding: 0 0 20px 0;
    margin-top: 10px;
    overflow-x: auto;
    min-width: 1200px;
}
.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: #f3f4f6;
    border-radius: 18px 18px 0 0;
    font-weight: 600;
    color: #222;
    font-size: 1.1rem;
    padding: 12px 0 8px 0;
    border-bottom: 1px solid #e5e7eb;
}
.calendar-day-header {
    text-align: center;
}
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: #fff;
}
.calendar-cell {
    border: 1px solid #f1f1f1;
    min-height: 340px;
    padding: 18px 10px 12px 10px;
    position: relative;
    vertical-align: top;
}
.event-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 2px 12px 0 rgba(0,0,0,0.10);
    padding: 12px 12px 10px 12px;
    margin-bottom: 18px;
    font-size: 1.13rem;
    position: relative;
    border-left: 8px solid #b3b3b3;
    transition: box-shadow 0.2s, transform 0.2s;
    margin-top: 6px;
    min-width: 240px;
    max-width: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 4px;
    position: relative;
    padding-bottom: 12px; /* تقليل المساحة السفلية */
}
.event-irrigation { border-left-color: #3498db; background: #eaf6fb; }
.event-fertilization { border-left-color: #27ae60; background: #eafbf1; }
.event-harvest { border-left-color: #b7950b; background: #fdf7e3; }
.event-card .event-title {
    font-weight: 700;
    font-size: 1.13rem;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.event-card .event-date {
    font-size: 0.98rem;
    color: #888;
    margin-bottom: 2px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}
.event-card .event-details {
    font-size: 1.01rem;
    color: #444;
    margin-bottom: 4px;
    line-height: 1.5;
    word-break: break-word;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 10px;
}
.event-card .event-details span {
    display: inline-flex;
    align-items: center;
    background: #f7f7fa;
    border-radius: 7px;
    padding: 2px 7px;
    font-size: 1.01rem;
    margin-bottom: 0;
}
.event-card .event-details span b {
    min-width: 0;
    color: #222;
    font-weight: 600;
    margin-right: 3px;
}
.event-card .event-actions {
    position: absolute;
    top: 10px;
    right: 14px;
    display: flex;
    gap: 10px;
    z-index: 2;
}
.status-badge {
    border-radius: 8px;
    padding: 1px 7px;
    font-size: 0.85em;
    font-weight: 600;
}
.status-pending { background: #ffe082; color: #7a5c00; }
.status-completed { background: #a5d6a7; color: #1b5e20; }
.status-cancelled, .status-failed { background: #ffcdd2; color: #b71c1c; }
.d-flex.align-items-center.flex-wrap.gap-2.mb-2 {
    gap: 10px !important;
}
.d-flex.align-items-center.flex-wrap.gap-2.mb-2 > * {
    margin-right: 0 !important;
    margin-bottom: 0 !important;
}
@media (max-width: 900px) {
    .calendar-header, .calendar-grid { font-size: 0.93rem; }
    .calendar-grid { grid-auto-rows: 70px; }
}
</style>
@endsection