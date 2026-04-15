@extends('layouts.app')
@section('title', isset($room) ? 'Edit Room' : 'Add Room')
@section('page_title', isset($room) ? 'Edit Room' : 'Add New Room')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card-custom p-4">
                <form action="{{ isset($room) ? route('rooms.update', $room) : route('rooms.store') }}" method="POST">
                    @csrf
                    @if(isset($room)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-500">Room Number <span class="text-danger">*</span></label>
                        <input type="text" name="room_number" class="form-control"
                            value="{{ old('room_number', $room->room_number ?? '') }}" required placeholder="e.g., 101">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-500">Floor</label>
                        <input type="text" name="floor" class="form-control"
                            value="{{ old('floor', $room->floor ?? '') }}" placeholder="e.g., Ground, 1st, 2nd">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-500">Capacity (persons) <span class="text-danger">*</span></label>
                        <input type="number" name="capacity" class="form-control" min="1"
                            value="{{ old('capacity', $room->capacity ?? 1) }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-500">Student Fee (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="student_rent" class="form-control" min="0" step="0.01"
                                value="{{ old('student_rent', $room->student_rent ?? '') }}" required placeholder="e.g., 4000">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-500">Employee Fee (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="employee_rent" class="form-control" min="0" step="0.01"
                                value="{{ old('employee_rent', $room->employee_rent ?? '') }}" required placeholder="e.g., 6000">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-500">Description</label>
                        <textarea name="description" class="form-control" rows="2"
                            placeholder="Optional notes about the room">{{ old('description', $room->description ?? '') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> {{ isset($room) ? 'Update Room' : 'Create Room' }}
                        </button>
                        <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
