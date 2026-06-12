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
            <div class="col-12 col-md-4">
                <label class="micro-label">Select Person</label>
                <select name="person_id" class="form-select form-select-sm" required>
                    <option value="">Choose a person...</option>
                    @foreach($persons as $person)
                        <option value="{{ $person->id }}" {{ $personId == $person->id ? 'selected' : '' }}>
                            {{ $person->name }} — Room {{ $person->room->room_number }}
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

    @if($selectedPerson)
        @php
            $initials = collect(explode(' ', trim($selectedPerson->name)))
                ->filter()
                ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                ->take(2)
                ->implode('');
            $hue = abs(crc32($selectedPerson->name)) % 360;
        @endphp

        <!-- Person Info -->
        <div class="card-custom p-3 p-md-4 mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                        <span class="avatar avatar-lg" style="--av-h: {{ $hue }}">{{ $initials }}</span>
                        <div>
                            <h6 class="fw-600 mb-1">{{ $selectedPerson->name }}</h6>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <a href="{{ route('rooms.show', $selectedPerson->room) }}" class="room-tag">
                                    <i class="bi bi-door-open"></i> {{ $selectedPerson->room->room_number }}
                                </a>
                                <span class="text-muted" style="font-size:0.78rem;">{{ $selectedPerson->phone ?? 'No phone' }}</span>
                                <span class="text-muted" style="font-size:0.78rem;">Joined {{ $selectedPerson->join_date->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-4 text-center">
                            <div class="stat-label">Total</div>
                            <div class="fw-700 fs-5">₹{{ number_format($totalAmount) }}</div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="stat-label">Paid</div>
                            <div class="fw-700 fs-5 text-success-c">₹{{ number_format($totalPaid) }}</div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="stat-label">Pending</div>
                            <div class="fw-700 fs-5 text-warning-c">₹{{ number_format($totalPending) }}</div>
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
                        <tr style="--i: {{ min($loop->index, 15) }}">
                            <td class="fw-500">{{ $fee->month_name }}</td>
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
                                    <div class="empty-icon"><i class="bi bi-calendar"></i></div>
                                    <h6>No fee records for {{ $year }}</h6>
                                    <p>Try a different year above.</p>
                                </div>
                            </td>
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
        <div class="card-custom">
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-person-badge"></i></div>
                <h6>Select a Person</h6>
                <p>Choose a person from the filter above to view their fee report.</p>
            </div>
        </div>
    @endif
@endsection
