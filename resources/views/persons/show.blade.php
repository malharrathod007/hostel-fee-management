@extends('layouts.app')
@section('title', $person->name . ' - Person Details')
@section('page_title', $person->name)

@section('top_actions')
    <a href="{{ route('persons.edit', $person) }}" class="btn btn-sm btn-outline-primary me-1">
        <i class="bi bi-pencil me-1"></i> Edit
    </a>
    <a href="{{ route('fees.create', ['person_id' => $person->id]) }}" class="btn btn-sm btn-primary">
        <i class="bi bi-cash me-1"></i> Add Fee
    </a>
@endsection

@section('content')
    <!-- Person Info Card -->
    <div class="card-custom p-3 p-md-4 mb-4">
        <div class="row g-0 g-md-3">
            <div class="col-12 col-md-6 mb-3 mb-md-0">
                <h6 class="fw-600 text-primary mb-2 mb-md-3" style="font-size:0.85rem;">
                    <i class="bi bi-person me-1"></i> Personal Info
                </h6>
                <div class="info-list">
                    <div class="info-row"><span class="info-label">Name</span><span class="fw-500">{{ $person->name }}</span></div>
                    <div class="info-row"><span class="info-label">Email</span><span>{{ $person->email ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Phone</span><span>{{ $person->phone ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Aadhar</span><span>{{ $person->aadhar_number ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">City</span><span>{{ $person->city ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Address</span><span>{{ $person->address ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Deposit</span><span class="fw-500" style="color:var(--success);">₹{{ number_format($person->deposit ?? 0) }}</span></div>
                    <div class="info-row"><span class="info-label">Status</span>
                        <span>@if($person->is_active)<span class="badge badge-paid">Active</span>@else<span class="badge badge-pending">Inactive</span>@endif</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <h6 class="fw-600 text-primary mb-2 mb-md-3" style="font-size:0.85rem;">
                    <i class="bi bi-shield me-1"></i> Guardian &amp; Room
                </h6>
                <div class="info-list">
                    <div class="info-row"><span class="info-label">Guardian</span><span>{{ $person->guardian_name ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">G. Phone</span><span>{{ $person->guardian_phone ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Room</span>
                        <span><a href="{{ route('rooms.show', $person->room) }}">Room {{ $person->room->room_number }}</a></span>
                    </div>
                    <div class="info-row"><span class="info-label">Type</span><span class="fw-500">{{ ucfirst($person->person_type ?? 'student') }}</span></div>
                    <div class="info-row"><span class="info-label">Monthly Fee</span><span class="fw-500">₹{{ number_format(($person->person_type ?? 'student') === 'employee' ? $person->room->employee_rent : $person->room->student_rent) }}</span></div>
                    <div class="info-row"><span class="info-label">Join Date</span><span>{{ $person->join_date->format('d M Y') }}</span></div>
                    <div class="info-row"><span class="info-label">Notes</span><span>{{ $person->notes ?? '-' }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .info-list { display: flex; flex-direction: column; gap: 0; }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.45rem 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.83rem;
            gap: 0.5rem;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.78rem;
            flex-shrink: 0;
            min-width: 80px;
        }
        @media (max-width: 768px) {
            .info-label { min-width: 72px; }
        }
    </style>

    <!-- Fee Summary -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="stat-card text-center">
                <div class="stat-label">Paid</div>
                <div class="stat-value" style="color:var(--success);font-size:1.15rem;">₹{{ number_format($person->total_paid) }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card text-center">
                <div class="stat-label">Pending</div>
                <div class="stat-value" style="color:var(--warning);font-size:1.15rem;">₹{{ number_format($person->total_pending) }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card text-center">
                <div class="stat-label">Entries</div>
                <div class="stat-value" style="font-size:1.15rem;">{{ $person->fees->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Fee History -->
    <div class="card-custom">
        <div class="card-header">
            <i class="bi bi-clock-history me-2"></i>Fee History
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Month/Year</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Paid Date</th>
                        <th>Payment Mode</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($person->fees as $fee)
                    <tr>
                        <td>{{ $fee->month_name }} {{ $fee->fee_year }}</td>
                        <td class="fw-500">₹{{ number_format($fee->amount) }}</td>
                        <td><span class="badge badge-{{ $fee->status }}">{{ ucfirst($fee->status) }}</span></td>
                        <td>{{ $fee->paid_date ? $fee->paid_date->format('d M Y') : '-' }}</td>
                        <td>{{ $fee->payment_mode ? ucfirst($fee->payment_mode) : '-' }}</td>
                        <td>{{ $fee->receipt_number ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No fee records yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
