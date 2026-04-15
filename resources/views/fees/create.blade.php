@extends('layouts.app')
@section('title', isset($fee) ? 'Edit Fee' : 'Add Fee')
@section('page_title', isset($fee) ? 'Edit Fee Entry' : 'Add Fee Entry')

@section('content')
    @if(!isset($fee) && isset($rooms))
    <div class="row justify-content-center mb-4">
        <div class="col-lg-6">
            <div class="card-custom p-4 border-primary">
                <h6 class="fw-600 mb-3 text-primary"><i class="bi bi-building me-1"></i> Bulk: Generate Fees for All Persons in a Room</h6>
                <form action="{{ route('fees.store_for_room') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-500">Room <span class="text-danger">*</span></label>
                        <select name="room_id" class="form-select" required>
                            <option value="">Select Room</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">
                                    Room {{ $room->room_number }} — Student ₹{{ number_format($room->student_rent ?? 0) }} / Employee ₹{{ number_format($room->employee_rent ?? 0) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-4">
                            <label class="form-label fw-500">Month <span class="text-danger">*</span></label>
                            <select name="fee_month" class="form-select" required>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ now()->month == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0,0,0,$i,1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-500">Year <span class="text-danger">*</span></label>
                            <input type="number" name="fee_year" class="form-control" value="{{ date('Y') }}" min="2000" max="2099" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-500">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="partial">Partial</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-collection me-1"></i> Generate Fees for Room
                    </button>
                    <small class="d-block text-muted mt-2">Creates one fee per active person in the room, using the rate matching their type (student/employee).</small>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card-custom p-4">
                <form action="{{ isset($fee) ? route('fees.update', $fee) : route('fees.store') }}" method="POST">
                    @csrf
                    @if(isset($fee)) @method('PUT') @endif

                    @if(!isset($fee) && isset($rooms))
                    <div class="mb-3">
                        <label class="form-label fw-500">Filter by Room</label>
                        <select class="form-select" id="roomFilter">
                            <option value="">All Rooms</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">Room {{ $room->room_number }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pick a room to narrow the person list below.</small>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-500">Person <span class="text-danger">*</span></label>
                        <select name="person_id" class="form-select" id="personSelect" required>
                            <option value="">Select Person</option>
                            @foreach($persons as $person)
                                @php
                                    $type = $person->person_type ?? 'student';
                                    $rate = $type === 'employee' ? ($person->room->employee_rent ?? 0) : ($person->room->student_rent ?? 0);
                                @endphp
                                <option value="{{ $person->id }}"
                                    data-amount="{{ $rate }}"
                                    data-type="{{ $type }}"
                                    data-room="{{ $person->room_id }}"
                                    {{ old('person_id', $fee->person_id ?? $selectedPerson ?? '') == $person->id ? 'selected' : '' }}>
                                    {{ $person->name }} — Room {{ $person->room->room_number }} ({{ ucfirst($type) }} ₹{{ number_format($rate) }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Fee type is auto-detected from the person (student / employee).</small>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-500">Month <span class="text-danger">*</span></label>
                            <select name="fee_month" class="form-select" required>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}"
                                        {{ old('fee_month', $fee->fee_month ?? now()->month) == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0,0,0,$i,1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-500">Year <span class="text-danger">*</span></label>
                            <input type="number" name="fee_year" class="form-control"
                                value="{{ old('fee_year', $fee->fee_year ?? date('Y')) }}" min="2000" max="2099" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-500">Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" min="0" step="0.01"
                            value="{{ old('amount', $fee->amount ?? '') }}" required placeholder="Enter amount">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-500">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required id="feeStatus">
                            <option value="pending" {{ old('status', $fee->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ old('status', $fee->status ?? '') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partial" {{ old('status', $fee->status ?? '') == 'partial' ? 'selected' : '' }}>Partial</option>
                        </select>
                    </div>

                    <div id="paidFields" style="display:none;">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-500">Paid Date</label>
                                <input type="date" name="paid_date" class="form-control"
                                    value="{{ old('paid_date', isset($fee) && $fee->paid_date ? $fee->paid_date->format('Y-m-d') : date('Y-m-d')) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-500">Payment Mode</label>
                                <select name="payment_mode" class="form-select">
                                    <option value="">Select</option>
                                    <option value="cash" {{ old('payment_mode', $fee->payment_mode ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="upi" {{ old('payment_mode', $fee->payment_mode ?? '') == 'upi' ? 'selected' : '' }}>UPI</option>
                                    <option value="bank" {{ old('payment_mode', $fee->payment_mode ?? '') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="cheque" {{ old('payment_mode', $fee->payment_mode ?? '') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-500">Receipt Number</label>
                            <input type="text" name="receipt_number" class="form-control"
                                value="{{ old('receipt_number', $fee->receipt_number ?? '') }}" placeholder="Optional">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-500">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $fee->notes ?? '') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> {{ isset($fee) ? 'Update' : 'Save Fee' }}
                        </button>
                        <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const statusEl = document.getElementById('feeStatus');
    const paidFields = document.getElementById('paidFields');

    function togglePaidFields() {
        paidFields.style.display = (statusEl.value === 'paid' || statusEl.value === 'partial') ? 'block' : 'none';
    }

    statusEl.addEventListener('change', togglePaidFields);
    togglePaidFields();

    const personSelect = document.getElementById('personSelect');
    const amountInput = document.querySelector('input[name="amount"]');
    const roomFilter = document.getElementById('roomFilter');

    if (personSelect && amountInput) {
        personSelect.addEventListener('change', function () {
            const opt = personSelect.options[personSelect.selectedIndex];
            const amt = opt && opt.dataset.amount;
            if (amt) {
                amountInput.value = amt;
            }
        });
    }

    if (roomFilter && personSelect) {
        // Cache the original full list once
        const allOptions = Array.from(personSelect.options).map(o => ({
            value: o.value,
            text: o.text,
            amount: o.dataset.amount || '',
            type: o.dataset.type || '',
            room: o.dataset.room || '',
        }));

        function rebuildPersonOptions(roomId) {
            const current = personSelect.value;
            personSelect.innerHTML = '';
            allOptions.forEach(o => {
                if (o.value === '' || !roomId || o.room === roomId) {
                    const opt = document.createElement('option');
                    opt.value = o.value;
                    opt.text = o.text;
                    if (o.amount) opt.dataset.amount = o.amount;
                    if (o.type) opt.dataset.type = o.type;
                    if (o.room) opt.dataset.room = o.room;
                    if (o.value === current) opt.selected = true;
                    personSelect.appendChild(opt);
                }
            });
            // If previously selected person is no longer in the filtered list, reset
            if (personSelect.value !== current) {
                personSelect.value = '';
                amountInput.value = '';
            }
        }

        roomFilter.addEventListener('change', function () {
            rebuildPersonOptions(roomFilter.value);
        });
    }
</script>
@endsection
