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
    <div class="card-custom p-4 mb-4">
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-600 text-primary mb-3"><i class="bi bi-person me-1"></i> Personal Info</h6>
                <table class="table table-borderless table-sm">
                    <tr><td class="text-muted" style="width:140px;">Name</td><td class="fw-500">{{ $person->name }}</td></tr>
                    <tr><td class="text-muted">Email</td><td>{{ $person->email ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Phone</td><td>{{ $person->phone ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Aadhar</td><td>{{ $person->aadhar_number ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Address</td><td>{{ $person->address ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Status</td><td>
                        @if($person->is_active)
                            <span class="badge badge-paid">Active</span>
                        @else
                            <span class="badge badge-pending">Inactive</span>
                        @endif
                    </td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-600 text-primary mb-3"><i class="bi bi-shield me-1"></i> Guardian & Room</h6>
                <table class="table table-borderless table-sm">
                    <tr><td class="text-muted" style="width:140px;">Guardian</td><td>{{ $person->guardian_name ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Guardian Phone</td><td>{{ $person->guardian_phone ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Room</td><td>
                        <a href="{{ route('rooms.show', $person->room) }}">Room {{ $person->room->room_number }}</a>
                    </td></tr>
                    <tr><td class="text-muted">Type</td><td class="fw-500">{{ ucfirst($person->person_type ?? 'student') }}</td></tr>
                    <tr><td class="text-muted">Fee</td><td class="fw-500">₹{{ number_format(($person->person_type ?? 'student') === 'employee' ? $person->room->employee_rent : $person->room->student_rent) }}/month</td></tr>
                    <tr><td class="text-muted">Join Date</td><td>{{ $person->join_date->format('d M Y') }}</td></tr>
                    <tr><td class="text-muted">Notes</td><td>{{ $person->notes ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Fee Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Total Paid</div>
                <div class="stat-value" style="color:var(--success);">₹{{ number_format($person->total_paid) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Total Pending</div>
                <div class="stat-value" style="color:var(--warning);">₹{{ number_format($person->total_pending) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Total Entries</div>
                <div class="stat-value">{{ $person->fees->count() }}</div>
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
