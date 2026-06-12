@extends('layouts.app')
@section('title', 'Rooms - Hostel Fee Manager')
@section('page_title', 'Rooms')

@section('top_actions')
    <a href="{{ route('rooms.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Room
    </a>
@endsection

@section('content')
    <div class="row g-3">
        @forelse($rooms as $room)
        @php $pct = $room->capacity > 0 ? min(($room->persons_count / $room->capacity) * 100, 100) : 0; @endphp
        <div class="col-md-4 col-sm-6">
            <div class="card-custom p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon tint-violet" style="width:40px;height:40px;font-size:1.1rem;">
                            <i class="bi bi-door-open-fill"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-600" style="font-size:1rem;">Room {{ $room->room_number }}</h5>
                            @if($room->floor)
                                <small class="text-muted">Floor: {{ $room->floor }}</small>
                            @endif
                        </div>
                    </div>
                    <!-- Direct action buttons — no dropdown, avoids overflow:hidden clipping -->
                    <div class="action-row">
                        <a href="{{ route('rooms.show', $room) }}" class="icon-btn" title="View" aria-label="View room {{ $room->room_number }}">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('rooms.edit', $room) }}" class="icon-btn" title="Edit" aria-label="Edit room {{ $room->room_number }}">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('rooms.destroy', $room) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this room?')"
                              style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="icon-btn icon-btn-danger" title="Delete" aria-label="Delete room {{ $room->room_number }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Occupancy</small>
                        <small class="fw-600">{{ $room->persons_count }} / {{ $room->capacity }}</small>
                    </div>
                    <div class="occupancy-bar {{ $pct >= 100 ? 'is-full' : '' }}">
                        <span style="width:{{ $pct }}%"></span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div style="font-size:0.78rem;">
                        <span class="text-muted">Student</span> <span class="fw-600 text-primary-c">₹{{ number_format($room->student_rent) }}</span>
                        <span class="text-muted mx-1">·</span>
                        <span class="text-muted">Employee</span> <span class="fw-600 text-primary-c">₹{{ number_format($room->employee_rent) }}</span>
                    </div>
                    <a href="{{ route('persons.create', ['room_id' => $room->id]) }}"
                       class="btn btn-sm btn-outline-success {{ $room->persons_count >= $room->capacity ? 'disabled' : '' }}">
                        <i class="bi bi-person-plus"></i> Add
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card-custom">
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-door-open"></i></div>
                    <h6>No Rooms Yet</h6>
                    <p>Start by adding your first room.</p>
                    <a href="{{ route('rooms.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Add Room
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
@endsection
