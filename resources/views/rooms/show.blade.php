@extends('layouts.app')
@section('title', 'Room ' . $room->room_number)
@section('page_title', 'Room ' . $room->room_number)

@section('top_actions')
    <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-outline-primary me-1">
        <i class="bi bi-pencil me-1"></i> Edit
    </a>
    <a href="{{ route('persons.create', ['room_id' => $room->id]) }}" class="btn btn-sm btn-primary {{ $room->is_full ? 'disabled' : '' }}">
        <i class="bi bi-person-plus me-1"></i> Add Person
    </a>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Floor</div>
                <div class="stat-value" style="font-size:1.2rem;">{{ $room->floor ?? 'N/A' }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Capacity</div>
                <div class="stat-value" style="font-size:1.2rem;">{{ $room->occupancy }} / {{ $room->capacity }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Student / Employee Fee</div>
                <div class="stat-value" style="font-size:1.2rem;">₹{{ number_format($room->student_rent) }} / ₹{{ number_format($room->employee_rent) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Status</div>
                <div class="stat-value" style="font-size:1.2rem;">
                    @if($room->is_full)
                        <span class="badge badge-pending">Full</span>
                    @else
                        <span class="badge badge-paid">Available</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-header">
            <i class="bi bi-people me-2"></i>Persons in this Room
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Join Date</th>
                        <th>Guardian</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($room->persons as $person)
                    <tr>
                        <td><a href="{{ route('persons.show', $person) }}" class="text-decoration-none fw-500">{{ $person->name }}</a></td>
                        <td>{{ $person->phone ?? '-' }}</td>
                        <td>{{ $person->join_date->format('d M Y') }}</td>
                        <td>{{ $person->guardian_name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('persons.show', $person) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('fees.create', ['person_id' => $person->id]) }}" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-cash"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No persons in this room yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
