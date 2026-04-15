@extends('layouts.app')
@section('title', 'Fees - Hostel Fee Manager')
@section('page_title', 'Fee Management')

@section('top_actions')
    <button class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal" data-bs-target="#generateModal">
        <i class="bi bi-lightning me-1"></i> Generate Monthly
    </button>
    <a href="{{ route('fees.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Fee
    </a>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card-custom p-3 mb-4 no-print">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-500">Month</label>
                <select name="month" class="form-select form-select-sm">
                    <option value="">All</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                            {{ date('F', mktime(0,0,0,$i,1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-500">Year</label>
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
            <div class="col-md-2">
                <label class="form-label small fw-500">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-500">Person</label>
                <select name="person_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($persons as $person)
                        <option value="{{ $person->id }}" {{ request('person_id') == $person->id ? 'selected' : '' }}>
                            {{ $person->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('fees.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>

    <!-- Summary -->
    @if($fees->isNotEmpty())
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="stat-label">Total Amount</div>
                <div class="stat-value">₹{{ number_format($fees->sum('amount')) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="stat-label">Paid</div>
                <div class="stat-value" style="color:var(--success)">₹{{ number_format($fees->where('status','paid')->sum('amount')) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="stat-label">Pending</div>
                <div class="stat-value" style="color:var(--warning)">₹{{ number_format($fees->where('status','pending')->sum('amount')) }}</div>
            </div>
        </div>
    </div>
    @endif

    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>#</th>
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
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('persons.show', $fee->person) }}" class="text-decoration-none fw-500">
                                {{ $fee->person->name }}
                            </a>
                        </td>
                        <td>Room {{ $fee->person->room->room_number }}</td>
                        <td>{{ $fee->month_name }} {{ $fee->fee_year }}</td>
                        <td class="fw-500">₹{{ number_format($fee->amount) }}</td>
                        <td><span class="badge badge-{{ $fee->status }}">{{ ucfirst($fee->status) }}</span></td>
                        <td>{{ $fee->paid_date ? $fee->paid_date->format('d M Y') : '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('fees.edit', $fee) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('fees.destroy', $fee) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this fee entry?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No fee records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Generate Monthly Modal -->
    <div class="modal fade" id="generateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Monthly Fees</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('fees.generate') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted small">This will create pending fee entries for all active persons based on their room rent.</p>
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
