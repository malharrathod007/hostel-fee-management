@extends('layouts.app')
@section('title', isset($fee) ? 'Edit Fee' : 'Add Fee')
@section('page_title', isset($fee) ? 'Edit Fee Entry' : 'Add Fee Entry')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card-custom p-4">
                <form action="{{ isset($fee) ? route('fees.update', $fee) : route('fees.store') }}" method="POST">
                    @csrf
                    @if(isset($fee)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-500">Person <span class="text-danger">*</span></label>
                        <select name="person_id" class="form-select" required>
                            <option value="">Select Person</option>
                            @foreach($persons as $person)
                                <option value="{{ $person->id }}"
                                    {{ old('person_id', $fee->person_id ?? $selectedPerson ?? '') == $person->id ? 'selected' : '' }}>
                                    {{ $person->name }} — Room {{ $person->room->room_number }} ({{ ucfirst($person->person_type ?? 'student') }} ₹{{ number_format(($person->person_type ?? 'student') === 'employee' ? $person->room->employee_rent : $person->room->student_rent) }})
                                </option>
                            @endforeach
                        </select>
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
</script>
@endsection
