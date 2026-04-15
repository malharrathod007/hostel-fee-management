@extends('layouts.app')
@section('title', 'Dashboard - Hostel Fee Manager')
@section('page_title', 'Dashboard')

@section('content')
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#ede9fe;color:#7c3aed;">
                        <i class="bi bi-door-open-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $totalRooms }}</div>
                        <div class="stat-label">Total Rooms</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $totalPersons }}</div>
                        <div class="stat-label">Active Persons</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#d1fae5;color:#059669;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div class="stat-value">₹{{ number_format($totalCollected) }}</div>
                        <div class="stat-label">Collected (This Month)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value">₹{{ number_format($totalPending) }}</div>
                        <div class="stat-label">Pending (This Year)</div>
                    </div>
                </div>
            </div>
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
                            @forelse($rooms as $room)
                            <tr>
                                <td>
                                    <a href="{{ route('rooms.show', $room) }}" class="text-decoration-none fw-500">
                                        Room {{ $room->room_number }}
                                    </a>
                                </td>
                                <td>{{ $room->persons_count }} / {{ $room->capacity }}</td>
                                <td>
                                    @if($room->persons_count >= $room->capacity)
                                        <span class="badge badge-pending">Full</span>
                                    @else
                                        <span class="badge badge-paid">Available</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    No rooms added yet. <a href="{{ route('rooms.create') }}">Add your first room</a>
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
                            @forelse($recentFees as $fee)
                            <tr>
                                <td>{{ $fee->person->name }}</td>
                                <td>{{ $fee->person->room->room_number }}</td>
                                <td>{{ $fee->month_name }} {{ $fee->fee_year }}</td>
                                <td>₹{{ number_format($fee->amount) }}</td>
                                <td>
                                    <span class="badge badge-{{ $fee->status }}">
                                        {{ ucfirst($fee->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No fee records yet. <a href="{{ route('fees.create') }}">Create first entry</a>
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
