@extends('layouts.app')
@section('title', 'Report by Person')
@section('page_title', 'Fee Report by Person')

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
                <label class="form-label small fw-500">Select Person</label>
                <select name="person_id" class="form-select form-select-sm" required>
                    <option value="">Choose a person...</option>
                    @foreach($persons as $person)
                        <option value="{{ $person->id }}" {{ $personId == $person->id ? 'selected' : '' }}>
                            {{ $person->name }} — Room {{ $person->room->room_number }}
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

    @if($selectedPerson)
        <!-- Person Info -->
        <div class="card-custom p-4 mb-4">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-600 text-primary mb-2">{{ $selectedPerson->name }}</h6>
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td class="text-muted" style="width:100px;">Room</td><td>Room {{ $selectedPerson->room->room_number }}</td></tr>
                        <tr><td class="text-muted">Phone</td><td>{{ $selectedPerson->phone ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Join Date</td><td>{{ $selectedPerson->join_date->format('d M Y') }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="text-center">
                                <div class="stat-label">Total</div>
                                <div class="fw-700 fs-5">₹{{ number_format($totalAmount) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <div class="stat-label">Paid</div>
                                <div class="fw-700 fs-5" style="color:var(--success)">₹{{ number_format($totalPaid) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <div class="stat-label">Pending</div>
                                <div class="fw-700 fs-5" style="color:var(--warning)">₹{{ number_format($totalPending) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Details -->
        <div class="card-custom">
            <div class="card-header">
                <i class="bi bi-calendar me-2"></i>Fee History — {{ $year }}
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Paid Date</th>
                            <th>Payment Mode</th>
                            <th>Receipt #</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fees as $fee)
                        <tr>
                            <td class="fw-500">{{ $fee->month_name }}</td>
                            <td>₹{{ number_format($fee->amount) }}</td>
                            <td><span class="badge badge-{{ $fee->status }}">{{ ucfirst($fee->status) }}</span></td>
                            <td>{{ $fee->paid_date ? $fee->paid_date->format('d M Y') : '-' }}</td>
                            <td>{{ $fee->payment_mode ? ucfirst($fee->payment_mode) : '-' }}</td>
                            <td>{{ $fee->receipt_number ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No fee records for {{ $year }}.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($fees->isNotEmpty())
                    <tfoot>
                        <tr class="fw-600">
                            <td class="text-end">Total:</td>
                            <td>₹{{ number_format($totalAmount) }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @else
        <div class="card-custom p-5 text-center">
            <i class="bi bi-person-badge text-muted" style="font-size:3rem;"></i>
            <h5 class="mt-3">Select a Person</h5>
            <p class="text-muted">Choose a person from the filter above to view their fee report.</p>
        </div>
    @endif
@endsection
