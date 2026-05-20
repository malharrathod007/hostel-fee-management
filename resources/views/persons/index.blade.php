@extends('layouts.app')
@section('title', 'Persons - Hostel Fee Manager')
@section('page_title', 'Persons')

@section('top_actions')
    <button class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-file-earmark-arrow-up me-1"></i>
        <span class="d-none d-sm-inline">Import</span>
    </button>
    <a href="{{ route('persons.export') }}?{{ http_build_query(request()->only(['search','room_id','status'])) }}" class="btn btn-sm btn-outline-success me-1">
        <i class="bi bi-file-earmark-excel me-1"></i>
        <span class="d-none d-sm-inline">Export</span>
    </a>
    <a href="{{ route('persons.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-person-plus me-1"></i>
        <span class="d-none d-sm-inline">Add Person</span>
    </a>
@endsection

@section('content')

    {{-- ── Import Results Banner ───────────────────────────────────────── --}}
    @if(session('import_results'))
        @php $res = session('import_results'); @endphp
        @if(count($res['skipped']) > 0)
        <div class="card-custom p-3 mb-4" style="border-left:4px solid var(--warning);">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-triangle-fill" style="color:var(--warning);"></i>
                <strong style="font-size:0.9rem;">{{ count($res['skipped']) }} row(s) were skipped</strong>
            </div>
            <div style="max-height:180px;overflow-y:auto;">
                <table class="table table-sm mb-0" style="font-size:0.8rem;">
                    <thead><tr><th>Row</th><th>Name</th><th>Reason</th></tr></thead>
                    <tbody>
                        @foreach($res['skipped'] as $s)
                        <tr>
                            <td>{{ $s['row'] }}</td>
                            <td>{{ $s['name'] }}</td>
                            <td class="text-danger">{{ $s['reason'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endif

    {{-- ── Filters ─────────────────────────────────────────────────────── --}}
    <div class="card-custom p-3 mb-4 no-print">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-500">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, phone, Aadhar..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-500">Room</label>
                <select name="room_id" class="form-select form-select-sm">
                    <option value="">All Rooms</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                            Room {{ $room->room_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-500">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <button class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
            <div class="col-6 col-md-2">
                <a href="{{ route('persons.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>

    {{-- ── Persons Table ───────────────────────────────────────────────── --}}
    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Room</th>
                        <th>Phone</th>
                        <th>Join Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($persons as $index => $person)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('persons.show', $person) }}" class="text-decoration-none fw-500">
                                {{ $person->name }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('rooms.show', $person->room) }}" class="text-decoration-none">
                                Room {{ $person->room->room_number }}
                            </a>
                        </td>
                        <td>{{ $person->phone ?? '-' }}</td>
                        <td>{{ $person->join_date->format('d M Y') }}</td>
                        <td>
                            @if($person->is_active)
                                <span class="badge badge-paid">Active</span>
                            @else
                                <span class="badge badge-pending">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('persons.show', $person) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('persons.edit', $person) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('fees.create', ['person_id' => $person->id]) }}" class="btn btn-outline-success" title="Add Fee">
                                    <i class="bi bi-cash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No persons found. <a href="{{ route('persons.create') }}">Add your first person</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         Import Modal
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">

                {{-- Header --}}
                <div class="modal-header" style="background:linear-gradient(135deg,#1e1b4b,#312e81);border:none;padding:1.25rem 1.5rem;">
                    <div>
                        <h5 class="modal-title mb-0" id="importModalLabel" style="color:#fff;font-size:1rem;">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Import Persons from CSV
                        </h5>
                        <small style="color:#c7d2fe;font-size:0.78rem;">Upload a CSV file to create or update persons in bulk</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body p-0">

                    {{-- Step guide --}}
                    <div style="background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:1rem 1.5rem;">
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="step-badge">1</div>
                                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.3rem;">Download template</div>
                            </div>
                            <div class="col-4">
                                <div class="step-badge">2</div>
                                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.3rem;">Fill in Excel, save as CSV</div>
                            </div>
                            <div class="col-4">
                                <div class="step-badge">3</div>
                                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.3rem;">Upload &amp; import</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">

                        {{-- Template download --}}
                        <div class="d-flex align-items-center gap-3 p-3 mb-4"
                             style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;">
                            <div style="width:42px;height:42px;background:#dbeafe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-file-earmark-arrow-down" style="color:#2563eb;font-size:1.2rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div style="font-weight:600;font-size:0.875rem;color:var(--text);">Download CSV Template</div>
                                <div style="font-size:0.78rem;color:var(--text-muted);">
                                    Open in Excel → fill data → <strong>File → Save As → CSV</strong>
                                </div>
                            </div>
                            <a href="{{ route('persons.import.template') }}"
                               class="btn btn-sm btn-outline-primary flex-shrink-0"
                               style="white-space:nowrap;">
                                <i class="bi bi-download me-1"></i> Template
                            </a>
                        </div>

                        {{-- Upload form --}}
                        <form action="{{ route('persons.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                            @csrf

                            {{-- Drop zone --}}
                            <div id="dropZone"
                                 style="border:2px dashed #cbd5e1;border-radius:12px;padding:2rem 1rem;text-align:center;cursor:pointer;transition:border-color 0.2s,background 0.2s;margin-bottom:1rem;position:relative;">
                                <input type="file" name="csv_file" id="csvFileInput"
                                       accept=".csv,text/csv"
                                       style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;">
                                <div id="dropZoneContent">
                                    <i class="bi bi-cloud-arrow-up" style="font-size:2.5rem;color:#94a3b8;"></i>
                                    <div style="font-weight:600;color:var(--text);margin-top:0.5rem;font-size:0.9rem;">
                                        Drop your CSV file here
                                    </div>
                                    <div style="font-size:0.78rem;color:var(--text-muted);margin-top:0.25rem;">
                                        or click to browse &nbsp;·&nbsp; CSV only, max 2 MB
                                    </div>
                                </div>
                                <div id="dropZoneSelected" style="display:none;">
                                    <i class="bi bi-file-earmark-check" style="font-size:2rem;color:var(--success);"></i>
                                    <div style="font-weight:600;color:var(--success);margin-top:0.4rem;font-size:0.9rem;" id="selectedFileName">—</div>
                                    <div style="font-size:0.75rem;color:var(--text-muted);" id="selectedFileSize">—</div>
                                    <button type="button" id="clearFile"
                                            style="margin-top:0.5rem;background:none;border:1px solid #e2e8f0;border-radius:6px;padding:0.2rem 0.6rem;font-size:0.75rem;color:var(--text-muted);cursor:pointer;">
                                        <i class="bi bi-x me-1"></i>Change file
                                    </button>
                                </div>
                            </div>

                            {{-- Column reference --}}
                            <details style="margin-bottom:1rem;">
                                <summary style="font-size:0.8rem;font-weight:600;color:var(--primary);cursor:pointer;user-select:none;padding:0.4rem 0;">
                                    <i class="bi bi-info-circle me-1"></i>Column reference &amp; matching rules
                                </summary>
                                <div style="margin-top:0.75rem;background:#f8fafc;border-radius:10px;padding:0.875rem;font-size:0.78rem;">
                                    <div class="row g-2">
                                        <div class="col-12 col-md-6">
                                            <strong style="color:var(--danger);">Required columns</strong>
                                            <ul style="margin:0.35rem 0 0;padding-left:1.2rem;line-height:2;">
                                                <li><code>Name</code> — full name</li>
                                                <li><code>Phone</code> — 10-digit Indian mobile</li>
                                                <li><code>Aadhar Number</code> — 12 digits <em>(used to match existing)</em></li>
                                                <li><code>Room Number</code> — must match an existing room</li>
                                            </ul>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <strong style="color:var(--text-muted);">Optional columns</strong>
                                            <ul style="margin:0.35rem 0 0;padding-left:1.2rem;line-height:2;">
                                                <li><code>Type</code> — <em>student</em> or <em>employee</em></li>
                                                <li><code>Email</code>, <code>City</code>, <code>Address</code></li>
                                                <li><code>Guardian Name</code>, <code>Guardian Phone</code></li>
                                                <li><code>Join Date</code> — YYYY-MM-DD</li>
                                                <li><code>Deposit</code> — number</li>
                                                <li><code>Status</code> — <em>active</em> or <em>inactive</em></li>
                                                <li><code>Notes</code></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div style="margin-top:0.75rem;padding:0.5rem 0.75rem;background:#eff6ff;border-radius:8px;border-left:3px solid #3b82f6;">
                                        <strong>How matching works:</strong> If a row's <code>Aadhar Number</code> matches an existing person, that person is <strong>updated</strong>. Otherwise a new person is <strong>created</strong>.
                                    </div>
                                </div>
                            </details>

                            {{-- Submit --}}
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="importSubmitBtn" disabled>
                                    <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Persons
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<style>
    .step-badge {
        width: 32px; height: 32px;
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.85rem;
        margin: 0 auto;
    }
    #dropZone.drag-over {
        border-color: var(--primary);
        background: #eff6ff;
    }
    #dropZone:hover {
        border-color: var(--primary-light);
        background: #fafbff;
    }
</style>
<script>
(function () {
    const input      = document.getElementById('csvFileInput');
    const dropZone   = document.getElementById('dropZone');
    const content    = document.getElementById('dropZoneContent');
    const selected   = document.getElementById('dropZoneSelected');
    const fileName   = document.getElementById('selectedFileName');
    const fileSize   = document.getElementById('selectedFileSize');
    const clearBtn   = document.getElementById('clearFile');
    const submitBtn  = document.getElementById('importSubmitBtn');

    function showFile(file) {
        if (!file) return;
        const ext = file.name.split('.').pop().toLowerCase();
        if (ext !== 'csv') {
            alert('Please select a CSV file. Open your Excel sheet and use File → Save As → CSV.');
            input.value = '';
            return;
        }
        fileName.textContent = file.name;
        fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
        content.style.display  = 'none';
        selected.style.display = 'block';
        submitBtn.disabled = false;
    }

    function clearFile() {
        input.value = '';
        content.style.display  = 'block';
        selected.style.display = 'none';
        submitBtn.disabled = true;
    }

    input.addEventListener('change', function () {
        showFile(this.files[0]);
    });

    clearBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        clearFile();
    });

    // Drag-and-drop
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('drag-over');
    });
    dropZone.addEventListener('dragleave', function () {
        dropZone.classList.remove('drag-over');
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file) {
            // Manually set file on input (DataTransfer approach)
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            showFile(file);
        }
    });

    // Show loading state on submit
    document.getElementById('importForm').addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Importing…';
    });

    // Re-open modal if there were validation errors
    @if($errors->has('csv_file'))
        var importModal = new bootstrap.Modal(document.getElementById('importModal'));
        importModal.show();
    @endif
})();
</script>
@endsection
