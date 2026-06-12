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
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <div class="stat-icon tint-cyan"><i class="bi bi-layers"></i></div>
                    <div>
                        <div class="stat-value" style="font-size:1.2rem;">{{ $room->floor ?? 'N/A' }}</div>
                        <div class="stat-label">Floor</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <div class="stat-icon tint-blue"><i class="bi bi-people"></i></div>
                    <div>
                        <div class="stat-value" style="font-size:1.2rem;">{{ $room->occupancy }} / {{ $room->capacity }}</div>
                        <div class="stat-label">Capacity</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <div class="stat-icon tint-green"><i class="bi bi-cash"></i></div>
                    <div>
                        <div class="stat-value" style="font-size:0.95rem;">₹{{ number_format($room->student_rent) }} / ₹{{ number_format($room->employee_rent) }}</div>
                        <div class="stat-label">Student / Employee</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <div class="stat-icon {{ $room->is_full ? 'tint-rose' : 'tint-green' }}">
                        <i class="bi {{ $room->is_full ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="font-size:1.2rem;">
                            @if($room->is_full)
                                <span class="pill pill-full"><span class="pill-dot"></span>Full</span>
                            @else
                                <span class="pill pill-available"><span class="pill-dot"></span>Available</span>
                            @endif
                        </div>
                        <div class="stat-label">Status</div>
                    </div>
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
                        <th>Person</th>
                        <th>Join Date</th>
                        <th>Guardian</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($room->persons as $index => $person)
                    @php
                        $initials = collect(explode(' ', trim($person->name)))
                            ->filter()
                            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                            ->take(2)
                            ->implode('');
                        $hue = abs(crc32($person->name)) % 360;
                    @endphp
                    <tr style="--i: {{ min($index, 15) }}">
                        <td class="cell-person">
                            <div class="cell-person-inner">
                                <span class="avatar" style="--av-h: {{ $hue }}">{{ $initials }}</span>
                                <div>
                                    <a href="{{ route('persons.show', $person) }}" class="person-name">{{ $person->name }}</a>
                                    <div class="person-meta">{{ $person->phone ?? 'No phone' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $person->join_date->format('d M Y') }}</td>
                        <td>{{ $person->guardian_name ?? '-' }}</td>
                        <td>
                            <div class="action-row">
                                <a href="{{ route('persons.show', $person) }}" class="icon-btn" title="View" aria-label="View {{ $person->name }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('fees.create', ['person_id' => $person->id]) }}" class="icon-btn icon-btn-success" title="Add Fee" aria-label="Add fee for {{ $person->name }}">
                                    <i class="bi bi-cash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="bi bi-people"></i></div>
                                <h6>No persons in this room yet</h6>
                                <p>Assign someone to this room to see them here.</p>
                                <a href="{{ route('persons.create', ['room_id' => $room->id]) }}" class="btn btn-sm btn-primary {{ $room->is_full ? 'disabled' : '' }}">
                                    <i class="bi bi-person-plus me-1"></i> Add Person
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
