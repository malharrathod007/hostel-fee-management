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
        <div class="col-md-4 col-sm-6">
            <div class="card-custom p-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-0 fw-600">Room {{ $room->room_number }}</h5>
                        @if($room->floor)
                            <small class="text-muted">Floor: {{ $room->floor }}</small>
                        @endif
                    </div>
                    <!-- Direct action buttons — no dropdown, avoids overflow:hidden clipping -->
                    <div class="d-flex gap-1">
                        <a href="{{ route('rooms.show', $room) }}" class="btn btn-sm btn-outline-secondary" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('rooms.destroy', $room) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this room?')"
                              style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Occupancy</small>
                        <small class="fw-500">{{ $room->persons_count }} / {{ $room->capacity }}</small>
                    </div>
                    <div class="progress" style="height:6px;">
                        @php $pct = $room->capacity > 0 ? ($room->persons_count / $room->capacity) * 100 : 0; @endphp
                        <div class="progress-bar {{ $pct >= 100 ? 'bg-warning' : 'bg-primary' }}" style="width:{{ $pct }}%"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-600 text-primary" style="font-size:0.85rem;">S: ₹{{ number_format($room->student_rent) }} / E: ₹{{ number_format($room->employee_rent) }}</span>
                    <a href="{{ route('persons.create', ['room_id' => $room->id]) }}"
                       class="btn btn-sm btn-outline-success {{ $room->persons_count >= $room->capacity ? 'disabled' : '' }}">
                        <i class="bi bi-person-plus"></i> Add
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card-custom p-5 text-center">
                <i class="bi bi-door-open text-muted" style="font-size:3rem;"></i>
                <h5 class="mt-3">No Rooms Yet</h5>
                <p class="text-muted">Start by adding your first room.</p>
                <a href="{{ route('rooms.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Add Room
                </a>
            </div>
        </div>
        @endforelse
    </div>
@endsection
