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
            <div class="col-md-4">
                <label class="form-label small fw-500">Select Room</label>
                <select name="room_id" class="form-select form-select-sm" required>
                    <option value="">Choose a room...</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ $roomId == $room->id ? 'selected' : '' }}>
                            Room {{ $room->room_number }} ({{ $room->persons_count }} persons)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-500">Year</label>
                <select name="year" class="form-select form-select-sm">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i> View</button>
            </div>
        </form>
    </div>

    @if($selectedRoom)
        <!-- Room Info -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Room</div>
                    <div class="stat-value" style="font-size:1.2rem;">{{ $selectedRoom->room_number }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Student / Employee Fee</div>
                    <div class="stat-value" style="font-size:1.2rem;">₹{{ number_format($selectedRoom->student_rent) }} / ₹{{ number_format($selectedRoom->employee_rent) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Total Collected</div>
                    <div class="stat-value" style="font-size:1.2rem;color:var(--success);">₹{{ number_format($fees->where('status','paid')->sum('amount')) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Total Pending</div>
                    <div class="stat-value" style="font-size:1.2rem;color:var(--warning);">₹{{ number_format($fees->where('status','pending')->sum('amount')) }}</div>
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
                        <tr>
                            <td class="fw-500">{{ $data['person']->name }}</td>
                            <td>₹{{ number_format($data['total']) }}</td>
                            <td style="color:var(--success)">₹{{ number_format($data['paid']) }}</td>
                            <td style="color:var(--warning)">₹{{ number_format($data['pending']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No fee data found.</td>
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
                        <tr>
                            <td>{{ $fee->person->name }}</td>
                            <td>{{ $fee->month_name }} {{ $fee->fee_year }}</td>
                            <td>₹{{ number_format($fee->amount) }}</td>
                            <td><span class="badge badge-{{ $fee->status }}">{{ ucfirst($fee->status) }}</span></td>
                            <td>{{ $fee->paid_date ? $fee->paid_date->format('d M Y') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card-custom p-5 text-center">
            <i class="bi bi-door-closed text-muted" style="font-size:3rem;"></i>
            <h5 class="mt-3">Select a Room</h5>
            <p class="text-muted">Choose a room from the filter above to view its fee report.</p>
        </div>
    @endif
@endsection
