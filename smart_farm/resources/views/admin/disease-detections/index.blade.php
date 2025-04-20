@extends('layouts.dashboard')

@section('title', 'All Disease Detections')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">All Disease Detections</h5>
                                <p class="text-success">Disease Reports</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <form class="me-3" method="GET" action="{{ route('admin.disease-detections.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="plantDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Plant: {{ request('plant_id') ? $plants->find(request('plant_id'))->name : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="plantDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.disease-detections.index', ['sort' => request('sort'), 'search' => request('search'), 'disease_name' => request('disease_name')]) }}">All</a></li>
                                        @foreach ($plants as $plant)
                                            <li><a class="dropdown-item" href="{{ route('admin.disease-detections.index', ['plant_id' => $plant->id, 'sort' => request('sort'), 'search' => request('search'), 'disease_name' => request('disease_name')]) }}">{{ $plant->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="diseaseDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Disease: {{ request('disease_name') ? ucfirst(request('disease_name')) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="diseaseDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.disease-detections.index', ['sort' => request('sort'), 'search' => request('search'), 'plant_id' => request('plant_id')]) }}">All</a></li>
                                        @foreach ($diseaseNames as $disease)
                                            <li><a class="dropdown-item" href="{{ route('admin.disease-detections.index', ['disease_name' => $disease, 'sort' => request('sort'), 'search' => request('search'), 'plant_id' => request('plant_id')]) }}">{{ ucfirst($disease) }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.disease-detections.index', ['sort' => 'newest', 'search' => request('search'), 'plant_id' => request('plant_id'), 'disease_name' => request('disease_name')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.disease-detections.index', ['sort' => 'oldest', 'search' => request('search'), 'plant_id' => request('plant_id'), 'disease_name' => request('disease_name')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.disease-detections.create') }}" class="btn btn-success">Add New Detection</a>
                            </div>
                        </div>
                        @if ($detections->isEmpty())
                            <p class="text-muted">No disease detections available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Plant</th>
                                        <th>Disease Name</th>
                                        <th>Confidence</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detections as $detection)
                                        <tr>
                                            <td>{{ $detection->id }}</td>
                                            <td>{{ $detection->plant ? $detection->plant->name : 'N/A' }}</td>
                                            <td>{{ ucfirst($detection->disease_name) }}</td>
                                            <td>{{ number_format($detection->confidence * 100, 2) }}%</td>
                                            <td>{{ $detection->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.disease-detections.edit', $detection->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.disease-detections.destroy', $detection->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this disease detection?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <p class="text-muted">Showing {{ $detections->firstItem() }} to {{ $detections->lastItem() }} of {{ $detections->total() }} data</p>
                                {{ $detections->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .text-success {
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }

        .btn-outline-secondary {
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .btn-success {
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-outline-primary, .btn-outline-danger {
            padding: 5px 10px;
        }

        .pagination .page-link {
            color: #4a7c59;
        }

        .pagination .page-item.active .page-link {
            background-color: #4a7c59;
            border-color: #4a7c59;
            color: #fff;
        }
    </style>
@endsection