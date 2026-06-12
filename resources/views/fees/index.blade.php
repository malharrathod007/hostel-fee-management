@extends('layouts.app')
@section('title', 'Fees - Hostel Fee Manager')
@section('page_title', 'Fee Management')

@section('top_actions')
    <button class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal" data-bs-target="#generateModal">
        <i class="bi bi-lightning me-1"></i> Generate Monthly
    </button>
    <a href="{{ route('fees.export') }}?{{ http_build_query(request()->only(['month','year','status','person_id'])) }}" class="btn btn-sm btn-outline-success me-1">
        <i class="bi bi-file-earmark-excel me-1"></i>
        <span class="d-none d-sm-inline">Export</span>
    </a>
    <a href="{{ route('fees.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Fee
    </a>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card-custom p-3 mb-4 no-print">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="micro-label">Month</label>
                <select name="month" class="form-select form-select-sm">
                    <option value="">All</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                            {{ date('F', mktime(0,0,0,$i,1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="micro-label">Year</label>
                <select name="year" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                    @if($years->isEmpty())
                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                    @endif
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="micro-label">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="micro-label">Person</label>
                <select name="person_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($persons as $person)
                        <option value="{{ $person->id }}" {{ request('person_id') == $person->id ? 'selected' : '' }}>
                            {{ $person->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <button class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
            <div class="col-6 col-md-2">
                <a href="{{ route('fees.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>

    <!-- Summary -->
    @if($fees->isNotEmpty())
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="stat-card text-center">
                <div class="stat-label">Total</div>
                <div class="stat-value">₹{{ number_format($fees->sum('amount')) }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card text-center">
                <div class="stat-label">Paid</div>
                <div class="stat-value text-success-c">₹{{ number_format($fees->where('status','paid')->sum('amount')) }}</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card text-center">
                <div class="stat-label">Pending</div>
                <div class="stat-value text-warning-c">₹{{ number_format($fees->where('status','pending')->sum('amount')) }}</div>
            </div>
        </div>
    </div>
    @endif

    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Person</th>
                        <th>Room</th>
                        <th>Month/Year</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Paid Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees as $index => $fee)
                    @php
                        $initials = collect(explode(' ', trim($fee->person->name)))
                            ->filter()
                            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                            ->take(2)
                            ->implode('');
                        $hue = abs(crc32($fee->person->name)) % 360;
                    @endphp
                    <tr style="--i: {{ min($index, 15) }}">
                        <td class="cell-person">
                            <div class="cell-person-inner">
                                <span class="avatar" style="--av-h: {{ $hue }}">{{ $initials }}</span>
                                <a href="{{ route('persons.show', $fee->person) }}" class="person-name">
                                    {{ $fee->person->name }}
                                </a>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('rooms.show', $fee->person->room) }}" class="room-tag">
                                <i class="bi bi-door-open"></i> {{ $fee->person->room->room_number }}
                            </a>
                        </td>
                        <td>{{ $fee->month_name }} {{ $fee->fee_year }}</td>
                        <td class="fw-600">₹{{ number_format($fee->amount) }}</td>
                        <td>
                            <span class="pill pill-{{ $fee->status }}">
                                <span class="pill-dot"></span>{{ ucfirst($fee->status) }}
                            </span>
                        </td>
                        <td>{{ $fee->paid_date ? $fee->paid_date->format('d M Y') : '-' }}</td>
                        <td>
                            <div class="action-row">
                                <a href="{{ route('fees.edit', $fee) }}" class="icon-btn" title="Edit" aria-label="Edit fee entry">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('fees.destroy', $fee) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this fee entry?')">
                                    @csrf @method('DELETE')
                                    <button class="icon-btn icon-btn-danger" title="Delete" aria-label="Delete fee entry">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="bi bi-cash-stack"></i></div>
                                <h6>No fee records found</h6>
                                <p>Try different filters, or add a fee entry.</p>
                                <a href="{{ route('fees.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i> Add Fee
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Generate Monthly Modal -->
    <div class="modal fade" id="generateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-ledger">
                <div class="modal-header ml-head">
                    <div>
                        <h5 class="modal-title mb-0"><i class="bi bi-lightning me-2"></i>Generate Monthly Fees</h5>
                        <small>One pending entry per active person, based on room rent</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('fees.generate') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-500">Month</label>
                                <select name="fee_month" class="form-select" required>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $i == now()->month ? 'selected' : '' }}>
                                            {{ date('F', mktime(0,0,0,$i,1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-500">Year</label>
                                <input type="number" name="fee_year" class="form-control"
                                    value="{{ date('Y') }}" min="2000" max="2099" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-lightning me-1"></i> Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
