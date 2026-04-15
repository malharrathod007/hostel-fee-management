@extends('layouts.app')
@section('title', isset($person) ? 'Edit Person' : 'Add Person')
@section('page_title', isset($person) ? 'Edit Person' : 'Add New Person')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom p-4">
                <form action="{{ isset($person) ? route('persons.update', $person) : route('persons.store') }}" method="POST">
                    @csrf
                    @if(isset($person)) @method('PUT') @endif

                    <h6 class="fw-600 mb-3 text-primary"><i class="bi bi-person me-1"></i> Personal Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-500">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $person->name ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-500">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $person->email ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-500">Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" maxlength="10"
                                pattern="[6-9][0-9]{9}" inputmode="numeric" required
                                value="{{ old('phone', $person->phone ?? '') }}" placeholder="10-digit mobile">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-500">Aadhar Number <span class="text-danger">*</span></label>
                            <input type="text" name="aadhar_number" class="form-control" maxlength="12"
                                pattern="[0-9]{12}" inputmode="numeric" required
                                value="{{ old('aadhar_number', $person->aadhar_number ?? '') }}" placeholder="12-digit Aadhar">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-500">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $person->address ?? '') }}</textarea>
                        </div>
                    </div>

                    <h6 class="fw-600 mb-3 text-primary"><i class="bi bi-shield me-1"></i> Guardian Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-500">Guardian Name</label>
                            <input type="text" name="guardian_name" class="form-control"
                                value="{{ old('guardian_name', $person->guardian_name ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-500">Guardian Phone</label>
                            <input type="tel" name="guardian_phone" class="form-control" maxlength="10"
                                pattern="[6-9][0-9]{9}" inputmode="numeric"
                                value="{{ old('guardian_phone', $person->guardian_phone ?? '') }}" placeholder="10-digit mobile">
                        </div>
                    </div>

                    <h6 class="fw-600 mb-3 text-primary"><i class="bi bi-door-open me-1"></i> Room Assignment</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-500">Room <span class="text-danger">*</span></label>
                            <select name="room_id" class="form-select" required>
                                <option value="">Select Room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}"
                                        {{ old('room_id', $person->room_id ?? $selectedRoom ?? '') == $room->id ? 'selected' : '' }}>
                                        Room {{ $room->room_number }} (Student ₹{{ number_format($room->student_rent ?? 0) }} / Employee ₹{{ number_format($room->employee_rent ?? 0) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-500">Person Type <span class="text-danger">*</span></label>
                            <select name="person_type" class="form-select" required>
                                <option value="student" {{ old('person_type', $person->person_type ?? 'student') == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="employee" {{ old('person_type', $person->person_type ?? '') == 'employee' ? 'selected' : '' }}>Employee</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-500">Join Date <span class="text-danger">*</span></label>
                            <input type="date" name="join_date" class="form-control"
                                value="{{ old('join_date', isset($person) ? $person->join_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    @if(isset($person))
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                id="is_active" {{ old('is_active', $person->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Person is currently active</label>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-500">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"
                            placeholder="Any additional notes...">{{ old('notes', $person->notes ?? '') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> {{ isset($person) ? 'Update Person' : 'Add Person' }}
                        </button>
                        <a href="{{ route('persons.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
