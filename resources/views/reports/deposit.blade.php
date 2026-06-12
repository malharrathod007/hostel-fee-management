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
                    <div class="stat-icon tint-indigo">
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
                    <div class="stat-icon tint-green">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">Persons with Deposit</div>
                        <div class="stat-value text-success-c">{{ $paidCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon tint-amber">
                        <i class="bi bi-person-dash-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">No Deposit Paid</div>
                        <div class="stat-value text-warning-c">{{ $noneCount }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card-custom p-3 mb-4 no-print">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="micro-label">Search</label>
                <div class="input-icon">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Name, phone, city..."
                        value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <label class="micro-label">City</label>
                <select name="city" class="form-select form-select-sm">
                    <option value="">All Cities</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                            {{ $city }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="micro-label">Room</label>
                <select name="room_id" class="form-select form-select-sm">
                    <option value="">All Rooms</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                            Room {{ $room->room_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="micro-label">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-3 col-md-2">
                <button class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
            <div class="col-3 col-md-1">
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
                        <th>Person</th>
                        <th>City</th>
                        <th>Room</th>
                        <th>Type</th>
                        <th>Join Date</th>
                        <th>Status</th>
                        <th class="text-end">Deposit (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($persons as $i => $person)
                    @php
                        $initials = collect(explode(' ', trim($person->name)))
                            ->filter()
                            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                            ->take(2)
                            ->implode('');
                        $hue = abs(crc32($person->name)) % 360;
                    @endphp
                    <tr style="--i: {{ min($i, 15) }}">
                        <td class="cell-person">
                            <div class="cell-person-inner">
                                <span class="avatar" style="--av-h: {{ $hue }}">{{ $initials }}</span>
                                <div>
                                    <a href="{{ route('persons.show', $person) }}" class="person-name">
                                        {{ $person->name }}
                                    </a>
                                    <div class="person-meta">{{ $person->phone ?? 'No phone' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $person->city ?? '-' }}</td>
                        <td>
                            <a href="{{ route('rooms.show', $person->room) }}" class="room-tag">
                                <i class="bi bi-door-open"></i> {{ $person->room->room_number }}
                            </a>
                        </td>
                        <td>{{ ucfirst($person->person_type ?? 'student') }}</td>
                        <td>{{ $person->join_date->format('d M Y') }}</td>
                        <td>
                            @if($person->is_active)
                                <span class="pill pill-active"><span class="pill-dot"></span>Active</span>
                            @else
                                <span class="pill pill-inactive"><span class="pill-dot"></span>Inactive</span>
                            @endif
                        </td>
                        <td class="text-end fw-600">
                            @if(($person->deposit ?? 0) > 0)
                                <span class="text-success-c">₹{{ number_format($person->deposit) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="bi bi-cash-coin"></i></div>
                                <h6>No persons found</h6>
                                <p>Try adjusting the filters above.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($persons->count() > 0)
                <tfoot>
                    <tr class="fw-600">
                        <td colspan="6" class="text-end">Total Deposit</td>
                        <td class="text-end text-primary-c">₹{{ number_format($totalDeposit) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

@endsection
