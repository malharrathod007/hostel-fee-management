@extends('layouts.app')
@section('title', 'Deposit Report')
@section('page_title', 'Deposit Report')

@section('top_actions')
    <a href="{{ route('reports.deposit.export') }}?{{ http_build_query(request()->only(['search','city','room_id','status'])) }}" class="btn btn-sm btn-outline-success me-1">
        <i class="bi bi-file-earmark-excel me-1"></i> Export
    </a>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-printer me-1"></i> Print
    </button>
@endsection

@section('content')

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#ede9fe;color:#4f46e5;">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <div class="stat-label">Total Deposit Collected</div>
                        <div class="stat-value">₹{{ number_format($totalDeposit) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#d1fae5;color:#059669;">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">Persons with Deposit</div>
                        <div class="stat-value" style="color:var(--success);">{{ $paidCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;">
                        <i class="bi bi-person-dash-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">No Deposit Paid</div>
                        <div class="stat-value" style="color:var(--warning);">{{ $noneCount }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card-custom p-3 mb-4 no-print">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-500">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Name, phone, city..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-500">City</label>
                <select name="city" class="form-select form-select-sm">
                    <option value="">All Cities</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                            {{ $city }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
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
            <div class="col-md-1">
                <a href="{{ route('reports.deposit') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card-custom">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-cash-coin me-2"></i>Deposit Details</span>
            <span class="text-muted" style="font-size:0.82rem;">{{ $persons->count() }} person(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>City</th>
                        <th>Room</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Join Date</th>
                        <th>Status</th>
                        <th class="text-end">Deposit (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($persons as $i => $person)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <a href="{{ route('persons.show', $person) }}" class="text-decoration-none fw-500">
                                {{ $person->name }}
                            </a>
                        </td>
                        <td>{{ $person->city ?? '-' }}</td>
                        <td>
                            <a href="{{ route('rooms.show', $person->room) }}" class="text-decoration-none">
                                Room {{ $person->room->room_number }}
                            </a>
                        </td>
                        <td>{{ $person->phone ?? '-' }}</td>
                        <td>{{ ucfirst($person->person_type ?? 'student') }}</td>
                        <td>{{ $person->join_date->format('d M Y') }}</td>
                        <td>
                            @if($person->is_active)
                                <span class="badge badge-paid">Active</span>
                            @else
                                <span class="badge badge-pending">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end fw-500">
                            @if(($person->deposit ?? 0) > 0)
                                <span style="color:var(--success);">₹{{ number_format($person->deposit) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No persons found.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($persons->count() > 0)
                <tfoot>
                    <tr class="fw-600" style="background:#f8fafc;">
                        <td colspan="8" class="text-end">Total Deposit</td>
                        <td class="text-end" style="color:var(--primary);">₹{{ number_format($totalDeposit) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

@endsection
