@extends('layouts.app')
@section('title', 'Persons - Hostel Fee Manager')
@section('page_title', 'Persons')

@section('top_actions')
    <a href="{{ route('persons.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-person-plus me-1"></i> Add Person
    </a>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card-custom p-3 mb-4 no-print">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-500">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, phone, Aadhar..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-500">Room</label>
                <select name="room_id" class="form-select form-select-sm">
                    <option value="">All Rooms</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                            Room {{ $room->room_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-500">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('persons.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>

    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Room</th>
                        <th>Phone</th>
                        <th>Join Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($persons as $index => $person)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('persons.show', $person) }}" class="text-decoration-none fw-500">
                                {{ $person->name }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('rooms.show', $person->room) }}" class="text-decoration-none">
                                Room {{ $person->room->room_number }}
                            </a>
                        </td>
                        <td>{{ $person->phone ?? '-' }}</td>
                        <td>{{ $person->join_date->format('d M Y') }}</td>
                        <td>
                            @if($person->is_active)
                                <span class="badge badge-paid">Active</span>
                            @else
                                <span class="badge badge-pending">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('persons.show', $person) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('persons.edit', $person) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('fees.create', ['person_id' => $person->id]) }}" class="btn btn-outline-success" title="Add Fee">
                                    <i class="bi bi-cash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No persons found. <a href="{{ route('persons.create') }}">Add your first person</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
