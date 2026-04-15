@extends('layouts.app')
@section('title', 'Monthly Report')
@section('page_title', 'Monthly Fee Report')

@section('top_actions')
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-printer me-1"></i> Print
    </button>
@endsection

@section('content')
    <!-- Filter -->
    <div class="card-custom p-3 mb-4 no-print">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-500">Month</label>
                <select name="month" class="form-select form-select-sm">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
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

    <!-- Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="stat-label">Total Amount</div>
                <div class="stat-value">₹{{ number_format($totalAmount) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="stat-label">Collected</div>
                <div class="stat-value" style="color:var(--success)">₹{{ number_format($totalPaid) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="stat-label">Pending</div>
                <div class="stat-value" style="color:var(--warning)">₹{{ number_format($totalPending) }}</div>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-header">
            <i class="bi bi-calendar-month me-2"></i>{{ $months[$month] }} {{ $year }} — Fee Details
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Person</th>
                        <th>Room</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Paid Date</th>
                        <th>Mode</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees as $index => $fee)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-500">{{ $fee->person->name }}</td>
                        <td>Room {{ $fee->person->room->room_number }}</td>
                        <td>₹{{ number_format($fee->amount) }}</td>
                        <td><span class="badge badge-{{ $fee->status }}">{{ ucfirst($fee->status) }}</span></td>
                        <td>{{ $fee->paid_date ? $fee->paid_date->format('d M Y') : '-' }}</td>
                        <td>{{ $fee->payment_mode ? ucfirst($fee->payment_mode) : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No fee records for this month.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($fees->isNotEmpty())
                <tfoot>
                    <tr class="fw-600">
                        <td colspan="3" class="text-end">Total:</td>
                        <td>₹{{ number_format($totalAmount) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
