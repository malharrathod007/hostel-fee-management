@extends('layouts.app')
@section('title', 'Report by Room')
@section('page_title', 'Fee Report by Room')

@section('top_actions')
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-printer me-1"></i> Print
    </button>
@endsection

@section('content')
    <!-- Filter -->
    <div class="card-custom p-3 mb-4 no-print">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="micro-label">Select Room</label>
                <select name="room_id" class="form-select form-select-sm" required>
                    <option value="">Choose a room...</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ $roomId == $room->id ? 'selected' : '' }}>
                            Room {{ $room->room_number }} ({{ $room->persons_count }} persons)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="micro-label">Year</label>
                <select name="year" class="form-select form-select-sm">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <button class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i> View</button>
            </div>
        </form>
    </div>

    @if($selectedRoom)
        <!-- Room Info -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center gap-2 gap-md-3">
                        <div class="stat-icon tint-violet"><i class="bi bi-door-open-fill"></i></div>
                        <div>
                            <div class="stat-value" style="font-size:1.2rem;">{{ $selectedRoom->room_number }}</div>
                            <div class="stat-label">Room</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center gap-2 gap-md-3">
                        <div class="stat-icon tint-blue"><i class="bi bi-cash"></i></div>
                        <div>
                            <div class="stat-value" style="font-size:0.95rem;">₹{{ number_format($selectedRoom->student_rent) }} / ₹{{ number_format($selectedRoom->employee_rent) }}</div>
                            <div class="stat-label">Student / Employee Fee</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center gap-2 gap-md-3">
                        <div class="stat-icon tint-green"><i class="bi bi-check-circle"></i></div>
                        <div>
                            <div class="stat-value text-success-c" style="font-size:1.2rem;">₹{{ number_format($fees->where('status','paid')->sum('amount')) }}</div>
                            <div class="stat-label">Total Collected</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center gap-2 gap-md-3">
                        <div class="stat-icon tint-amber"><i class="bi bi-hourglass-split"></i></div>
                        <div>
                            <div class="stat-value text-warning-c" style="font-size:1.2rem;">₹{{ number_format($fees->where('status','pending')->sum('amount')) }}</div>
                            <div class="stat-label">Total Pending</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Person Summary -->
        <div class="card-custom mb-4">
            <div class="card-header">
                <i class="bi bi-people me-2"></i>Person Summary — Room {{ $selectedRoom->room_number }} ({{ $year }})
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Person</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Pending</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personSummary as $data)
                        <tr style="--i: {{ min($loop->index, 15) }}">
                            <td class="fw-500">{{ $data['person']->name }}</td>
                            <td class="fw-600">₹{{ number_format($data['total']) }}</td>
                            <td class="text-success-c">₹{{ number_format($data['paid']) }}</td>
                            <td class="text-warning-c">₹{{ number_format($data['pending']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="bi bi-cash-stack"></i></div>
                                    <h6>No fee data found</h6>
                                    <p>No fees recorded for this room in {{ $year }}.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detailed -->
        <div class="card-custom">
            <div class="card-header"><i class="bi bi-list-ul me-2"></i>All Fee Entries</div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Person</th>
                            <th>Month</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Paid Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fees as $fee)
                        <tr style="--i: {{ min($loop->index, 15) }}">
                            <td class="fw-500">{{ $fee->person->name }}</td>
                            <td>{{ $fee->month_name }} {{ $fee->fee_year }}</td>
                            <td class="fw-600">₹{{ number_format($fee->amount) }}</td>
                            <td>
                                <span class="pill pill-{{ $fee->status }}">
                                    <span class="pill-dot"></span>{{ ucfirst($fee->status) }}
                                </span>
                            </td>
                            <td>{{ $fee->paid_date ? $fee->paid_date->format('d M Y') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card-custom">
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-door-closed"></i></div>
                <h6>Select a Room</h6>
                <p>Choose a room from the filter above to view its fee report.</p>
            </div>
        </div>
    @endif
@endsection
