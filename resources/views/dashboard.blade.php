@extends('layouts.app')
@section('title', 'Dashboard - Hostel Fee Manager')
@section('page_title', 'Dashboard')

@section('content')
    <!-- Greeting hero -->
    <div class="hero-strip mb-4">
        <div>
            <h4>Hello, {{ Auth::user()->name }} 👋</h4>
            <p>{{ now()->format('l, d F Y') }} — here's how your hostel is doing.</p>
        </div>
        <div class="hero-actions no-print">
            <a href="{{ route('fees.create') }}" class="btn-hero"><i class="bi bi-cash"></i> Add Fee</a>
            <a href="{{ route('persons.create') }}" class="btn-hero"><i class="bi bi-person-plus"></i> Add Person</a>
        </div>
    </div>

    <!-- Stats Cards — 2×2 on mobile, 4 across on desktop -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('rooms.index') }}" class="stat-card">
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <div class="stat-icon tint-violet">
                        <i class="bi bi-door-open-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $totalRooms }}</div>
                        <div class="stat-label">Total Rooms</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('persons.index') }}" class="stat-card">
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <div class="stat-icon tint-blue">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $totalPersons }}</div>
                        <div class="stat-label">Active Persons</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('fees.index') }}" class="stat-card">
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <div class="stat-icon tint-green">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div class="stat-value">₹{{ number_format($totalCollected) }}</div>
                        <div class="stat-label">Collected (Month)</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('fees.index', ['status' => 'pending']) }}" class="stat-card">
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <div class="stat-icon tint-amber">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value">₹{{ number_format($totalPending) }}</div>
                        <div class="stat-label">Pending (Year)</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Room Occupancy -->
        <div class="col-lg-5">
            <div class="card-custom">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-door-open me-2"></i>Room Occupancy</span>
                    <a href="{{ route('rooms.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Add Room
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>Occupancy</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $index => $room)
                            <tr style="--i: {{ min($index, 15) }}">
                                <td>
                                    <a href="{{ route('rooms.show', $room) }}" class="room-tag">
                                        <i class="bi bi-door-open"></i> {{ $room->room_number }}
                                    </a>
                                </td>
                                <td>{{ $room->persons_count }} / {{ $room->capacity }}</td>
                                <td>
                                    @if($room->persons_count >= $room->capacity)
                                        <span class="pill pill-full"><span class="pill-dot"></span>Full</span>
                                    @else
                                        <span class="pill pill-available"><span class="pill-dot"></span>Available</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="bi bi-door-open"></i></div>
                                        <h6>No rooms added yet</h6>
                                        <p>Rooms you create will appear here.</p>
                                        <a href="{{ route('rooms.create') }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-lg me-1"></i> Add your first room
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Fee Transactions -->
        <div class="col-lg-7">
            <div class="card-custom">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2"></i>Recent Fee Transactions</span>
                    <a href="{{ route('fees.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Add Fee
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Person</th>
                                <th>Room</th>
                                <th>Month</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentFees as $index => $fee)
                            <tr style="--i: {{ min($index, 15) }}">
                                <td class="fw-500">{{ $fee->person->name }}</td>
                                <td>{{ $fee->person->room->room_number }}</td>
                                <td>{{ $fee->month_name }} {{ $fee->fee_year }}</td>
                                <td class="fw-600">₹{{ number_format($fee->amount) }}</td>
                                <td>
                                    <span class="pill pill-{{ $fee->status }}">
                                        <span class="pill-dot"></span>{{ ucfirst($fee->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="bi bi-cash-stack"></i></div>
                                        <h6>No fee records yet</h6>
                                        <p>Fee entries you add will show up here.</p>
                                        <a href="{{ route('fees.create') }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-lg me-1"></i> Create first entry
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
