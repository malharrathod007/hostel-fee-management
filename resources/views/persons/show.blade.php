@extends('layouts.app')
@section('title', $person->name . ' - Person Details')
@section('page_title', $person->name)

@section('top_actions')
    <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#transferModal">
        <i class="bi bi-arrow-left-right me-1"></i><span class="d-none d-sm-inline"> Transfer</span>
    </button>
    <a href="{{ route('persons.edit', $person) }}" class="btn btn-sm btn-outline-primary me-1">
        <i class="bi bi-pencil me-1"></i><span class="d-none d-sm-inline"> Edit</span>
    </a>
    <form action="{{ route('persons.destroy', $person) }}" method="POST" class="d-inline me-1"
          onsubmit="return confirm('Delete {{ $person->name }}?\n\nTheir fee records and reports will be KEPT, and you can restore them anytime from the \'Deleted\' filter on the Persons page.')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger">
            <i class="bi bi-trash me-1"></i><span class="d-none d-sm-inline"> Delete</span>
        </button>
    </form>
    <a href="{{ route('fees.create', ['person_id' => $person->id]) }}" class="btn btn-sm btn-primary">
        <i class="bi bi-cash me-1"></i><span class="d-none d-sm-inline"> Add Fee</span>
    </a>
@endsection

@section('content')
    @php
        $initials = collect(explode(' ', trim($person->name)))
            ->filter()
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->take(2)
            ->implode('');
        $hue = abs(crc32($person->name)) % 360;
    @endphp

    <!-- Profile header -->
    <div class="card-custom p-3 p-md-4 mb-4">
        <div class="d-flex align-items-center gap-3 flex-wrap mb-3">
            <span class="avatar avatar-lg" style="--av-h: {{ $hue }}">{{ $initials }}</span>
            <div class="flex-grow-1">
                <h5 class="mb-1 fw-600" style="font-size:1.05rem;">{{ $person->name }}</h5>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($person->is_active)
                        <span class="pill pill-active"><span class="pill-dot"></span>Active</span>
                    @else
                        <span class="pill pill-inactive"><span class="pill-dot"></span>Inactive</span>
                    @endif
                    <span class="pill {{ ($person->person_type ?? 'student') === 'employee' ? 'pill-partial' : 'pill-paid' }}">
                        {{ ucfirst($person->person_type ?? 'student') }}
                    </span>
                    <a href="{{ route('rooms.show', $person->room) }}" class="room-tag">
                        <i class="bi bi-door-open"></i> {{ $person->room->room_number }}
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-0 g-md-3">
            <div class="col-12 col-md-6 mb-3 mb-md-0">
                <div class="form-section-title"><i class="bi bi-person"></i> Personal Info</div>
                <div class="info-list">
                    <div class="info-row"><span class="info-label">Email</span><span>{{ $person->email ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Phone</span><span>{{ $person->phone ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Aadhar</span><span>{{ $person->aadhar_number ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">City</span><span>{{ $person->city ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Address</span><span>{{ $person->address ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Deposit</span><span class="fw-600 text-success-c">₹{{ number_format($person->deposit ?? 0) }}</span></div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-section-title"><i class="bi bi-shield"></i> Guardian &amp; Room</div>
                <div class="info-list">
                    <div class="info-row"><span class="info-label">Guardian</span><span>{{ $person->guardian_name ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">G. Phone</span><span>{{ $person->guardian_phone ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Monthly Fee</span><span class="fw-600">₹{{ number_format(($person->person_type ?? 'student') === 'employee' ? $person->room->employee_rent : $person->room->student_rent) }}</span></div>
                    <div class="info-row"><span class="info-label">Join Date</span><span>{{ $person->join_date->format('d M Y') }}</span></div>
                    <div class="info-row"><span class="info-label">Notes</span><span>{{ $person->notes ?? '-' }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Summary -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="stat-card text-center">
                <div class="stat-label">Paid</div>
                <div class="stat-value text-success-c" style="font-size:1.15rem;">₹{{ number_format($person->total_paid) }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card text-center">
                <div class="stat-label">Pending</div>
                <div class="stat-value text-warning-c" style="font-size:1.15rem;">₹{{ number_format($person->total_pending) }}</div>
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
                    @forelse($person->fees as $index => $fee)
                    <tr style="--i: {{ min($index, 15) }}">
                        <td class="fw-500">{{ $fee->month_name }} {{ $fee->fee_year }}</td>
                        <td class="fw-600">₹{{ number_format($fee->amount) }}</td>
                        <td>
                            <span class="pill pill-{{ $fee->status }}">
                                <span class="pill-dot"></span>{{ ucfirst($fee->status) }}
                            </span>
                        </td>
                        <td>{{ $fee->paid_date ? $fee->paid_date->format('d M Y') : '-' }}</td>
                        <td>{{ $fee->payment_mode ? ucfirst($fee->payment_mode) : '-' }}</td>
                        <td>{{ $fee->receipt_number ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="bi bi-cash-stack"></i></div>
                                <h6>No fee records yet</h6>
                                <p>Fees recorded for {{ $person->name }} will appear here.</p>
                                <a href="{{ route('fees.create', ['person_id' => $person->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-cash me-1"></i> Add Fee
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Transfer Room Modal ── --}}
    <div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-ledger">
                <div class="modal-header ml-head">
                    <div>
                        <h5 class="modal-title mb-0" id="transferModalLabel">
                            <i class="bi bi-arrow-left-right me-2"></i>Transfer Room
                        </h5>
                        <small>Move <strong>{{ $person->name }}</strong> from Room {{ $person->room->room_number }} to another room</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('persons.transfer', $person) }}">
                    @csrf
                    <div class="modal-body">
                        <label class="form-label fw-500">New Room <span class="text-danger">*</span></label>
                        <select name="room_id" class="form-select" required>
                            <option value="">Select Room</option>
                            @foreach($rooms as $room)
                                @if($room->id !== $person->room_id)
                                    <option value="{{ $room->id }}" {{ $room->persons_count >= $room->capacity ? 'disabled' : '' }}>
                                        Room {{ $room->room_number }} — {{ $room->persons_count }}/{{ $room->capacity }} occupied{{ $room->persons_count >= $room->capacity ? ' (Full)' : '' }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>The transfer is recorded in the person's notes. Full rooms are disabled.
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-left-right me-1"></i> Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
