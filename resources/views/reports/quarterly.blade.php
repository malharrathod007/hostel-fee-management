@extends('layouts.app')
@section('title', 'Quarterly Report')
@section('page_title', 'Quarterly Fee Report')

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
                <label class="form-label small fw-500">Quarter</label>
                <select name="quarter" class="form-select form-select-sm">
                    <option value="1" {{ $quarter == 1 ? 'selected' : '' }}>Q1 (Jan-Mar)</option>
                    <option value="2" {{ $quarter == 2 ? 'selected' : '' }}>Q2 (Apr-Jun)</option>
                    <option value="3" {{ $quarter == 3 ? 'selected' : '' }}>Q3 (Jul-Sep)</option>
                    <option value="4" {{ $quarter == 4 ? 'selected' : '' }}>Q4 (Oct-Dec)</option>
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
                <div class="stat-label">Quarter Total</div>
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

    <!-- Person-wise Summary -->
    <div class="card-custom mb-4">
        <div class="card-header">
            <i class="bi bi-people me-2"></i>Person-wise Summary — Q{{ $quarter }} {{ $year }}
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Person</th>
                        <th>Room</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Pending</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($personSummary as $index => $data)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-500">{{ $data['person']->name }}</td>
                        <td>Room {{ $data['person']->room->room_number }}</td>
                        <td>₹{{ number_format($data['total']) }}</td>
                        <td style="color:var(--success)">₹{{ number_format($data['paid']) }}</td>
                        <td style="color:var(--warning)">₹{{ number_format($data['pending']) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No data for this quarter.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($personSummary->isNotEmpty())
                <tfoot>
                    <tr class="fw-600">
                        <td colspan="3" class="text-end">Total:</td>
                        <td>₹{{ number_format($totalAmount) }}</td>
                        <td style="color:var(--success)">₹{{ number_format($totalPaid) }}</td>
                        <td style="color:var(--warning)">₹{{ number_format($totalPending) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Detailed Breakdown -->
    <div class="card-custom">
        <div class="card-header">
            <i class="bi bi-list-ul me-2"></i>Detailed Breakdown
        </div>
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
                        <td>{{ $fee->month_name }}</td>
                        <td>₹{{ number_format($fee->amount) }}</td>
                        <td><span class="badge badge-{{ $fee->status }}">{{ ucfirst($fee->status) }}</span></td>
                        <td>{{ $fee->paid_date ? $fee->paid_date->format('d M Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
