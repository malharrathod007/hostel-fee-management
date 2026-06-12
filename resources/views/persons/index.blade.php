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
        <div class="banner banner-warn mb-4">
            <div class="banner-title">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ count($res['skipped']) }} row(s) were skipped
            </div>
            <div class="banner-scroll">
                <table class="table table-sm mb-0">
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
                <label class="micro-label">Search</label>
                <div class="input-icon">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, phone, Aadhar..."
                        value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <label class="micro-label">Room</label>
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
                <label class="micro-label">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
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

    {{-- ── Toolbar: result count + quick status chips ──────────────────── --}}
    @php
        $chipBase    = array_filter(request()->only(['search', 'room_id']));
        $activeCount = $persons->where('is_active', true)->count();
    @endphp
    <div class="page-toolbar no-print">
        <div class="result-count">
            <strong>{{ $persons->count() }}</strong>
            {{ $persons->count() === 1 ? 'person' : 'persons' }}
            @if($persons->count() > 0 && !request()->filled('status'))
                &middot; {{ $activeCount }} active
                &middot; {{ $persons->count() - $activeCount }} inactive
            @endif
        </div>
        <div class="chip-row">
            <a href="{{ route('persons.index', $chipBase) }}"
               class="chip {{ !request()->filled('status') ? 'active' : '' }}">All</a>
            <a href="{{ route('persons.index', array_merge($chipBase, ['status' => 'active'])) }}"
               class="chip {{ request('status') === 'active' ? 'active' : '' }}">Active</a>
            <a href="{{ route('persons.index', array_merge($chipBase, ['status' => 'inactive'])) }}"
               class="chip {{ request('status') === 'inactive' ? 'active' : '' }}">Inactive</a>
            <a href="{{ route('persons.index', array_merge($chipBase, ['status' => 'deleted'])) }}"
               class="chip {{ request('status') === 'deleted' ? 'active' : '' }}"><i class="bi bi-trash" style="font-size:0.7rem;"></i> Deleted</a>
        </div>
    </div>

    {{-- ── Persons Table ───────────────────────────────────────────────── --}}
    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Person</th>
                        <th>Room</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($persons as $index => $person)
                    @php
                        $initials = collect(explode(' ', trim($person->name)))
                            ->filter()
                            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                            ->take(2)
                            ->implode('');
                        $hue  = abs(crc32($person->name)) % 360;
                        $type = ucfirst($person->person_type ?? 'student');
                    @endphp
                    <tr style="--i: {{ min($index, 15) }}">
                        <td class="cell-person">
                            <div class="cell-person-inner">
                                <span class="avatar" style="--av-h: {{ $hue }}">{{ $initials }}</span>
                                <div>
                                    @if($person->trashed())
                                        <span class="person-name">{{ $person->name }}</span>
                                    @else
                                        <a href="{{ route('persons.show', $person) }}" class="person-name">
                                            {{ $person->name }}
                                        </a>
                                    @endif
                                    <div class="person-meta">
                                        {{ $person->phone ?? 'No phone' }} &middot; {{ $type }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('rooms.show', $person->room) }}" class="room-tag">
                                <i class="bi bi-door-open"></i> {{ $person->room->room_number }}
                            </a>
                        </td>
                        <td>{{ $person->join_date->format('d M Y') }}</td>
                        <td>
                            @if($person->trashed())
                                <span class="pill pill-inactive"><span class="pill-dot"></span>Deleted {{ $person->deleted_at->format('d M Y') }}</span>
                            @elseif($person->is_active)
                                <span class="pill pill-active"><span class="pill-dot"></span>Active</span>
                            @else
                                <span class="pill pill-inactive"><span class="pill-dot"></span>Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if($person->trashed())
                                <form action="{{ route('persons.restore', $person->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                    </button>
                                </form>
                            @else
                                <div class="action-row">
                                    <a href="{{ route('persons.show', $person) }}" class="icon-btn" title="View" aria-label="View {{ $person->name }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('persons.edit', $person) }}" class="icon-btn" title="Edit" aria-label="Edit {{ $person->name }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('fees.create', ['person_id' => $person->id]) }}" class="icon-btn icon-btn-success" title="Add Fee" aria-label="Add fee for {{ $person->name }}">
                                        <i class="bi bi-cash"></i>
                                    </a>
                                    <button type="button" class="icon-btn transfer-btn" title="Transfer Room"
                                            aria-label="Transfer {{ $person->name }} to another room"
                                            data-bs-toggle="modal" data-bs-target="#transferModal"
                                            data-action="{{ route('persons.transfer', $person) }}"
                                            data-name="{{ $person->name }}"
                                            data-room="{{ $person->room->room_number }}">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </button>
                                    <form action="{{ route('persons.destroy', $person) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete {{ $person->name }}?\n\nTheir fee records and reports will be KEPT, and you can restore them anytime from the \'Deleted\' filter.')">
                                        @csrf @method('DELETE')
                                        <button class="icon-btn icon-btn-danger" title="Delete" aria-label="Delete {{ $person->name }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="bi bi-people"></i></div>
                                <h6>No persons found</h6>
                                <p>Try adjusting your filters, or add someone new to get started.</p>
                                <a href="{{ route('persons.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-person-plus me-1"></i> Add Person
                                </a>
                            </div>
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
            <div class="modal-content modal-ledger">

                {{-- Header --}}
                <div class="modal-header ml-head">
                    <div>
                        <h5 class="modal-title mb-0" id="importModalLabel">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Import Persons from CSV
                        </h5>
                        <small>Upload a CSV file to create or update persons in bulk</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body p-0">

                    {{-- Step guide --}}
                    <div class="ml-steps">
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="step-badge">1</div>
                                <div class="step-caption">Download template</div>
                            </div>
                            <div class="col-4">
                                <div class="step-badge">2</div>
                                <div class="step-caption">Fill in Excel, save as CSV</div>
                            </div>
                            <div class="col-4">
                                <div class="step-badge">3</div>
                                <div class="step-caption">Upload &amp; import</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">

                        {{-- Template download --}}
                        <div class="tile-info mb-4">
                            <div class="tile-icon">
                                <i class="bi bi-file-earmark-arrow-down"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="tile-title">Download CSV Template</div>
                                <div class="tile-text">
                                    Open in Excel → fill data → <strong>File → Save As → CSV</strong>
                                </div>
                            </div>
                            <a href="{{ route('persons.import.template') }}"
                               class="btn btn-sm btn-outline-primary flex-shrink-0 text-nowrap">
                                <i class="bi bi-download me-1"></i> Template
                            </a>
                        </div>

                        {{-- Upload form --}}
                        <form action="{{ route('persons.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                            @csrf

                            {{-- Drop zone --}}
                            <div id="dropZone" class="drop-zone mb-3">
                                <input type="file" name="csv_file" id="csvFileInput"
                                       accept=".csv,text/csv" class="drop-input">
                                <div id="dropZoneContent">
                                    <i class="bi bi-cloud-arrow-up drop-icon"></i>
                                    <div class="drop-title">Drop your CSV file here</div>
                                    <div class="drop-hint">or click to browse &nbsp;·&nbsp; CSV only, max 2 MB</div>
                                </div>
                                <div id="dropZoneSelected" style="display:none;">
                                    <i class="bi bi-file-earmark-check drop-ok-icon"></i>
                                    <div class="drop-ok-name" id="selectedFileName">—</div>
                                    <div class="drop-ok-size" id="selectedFileSize">—</div>
                                    <button type="button" id="clearFile" class="drop-clear-btn">
                                        <i class="bi bi-x me-1"></i>Change file
                                    </button>
                                </div>
                            </div>

                            {{-- Column reference --}}
                            <details class="ml-ref mb-3">
                                <summary>
                                    <i class="bi bi-info-circle me-1"></i>Column reference &amp; matching rules
                                </summary>
                                <div class="ml-ref-body">
                                    <div class="row g-2">
                                        <div class="col-12 col-md-6">
                                            <strong class="text-danger">Required columns</strong>
                                            <ul>
                                                <li><code>Name</code> — full name</li>
                                                <li><code>Phone</code> — 10-digit Indian mobile</li>
                                                <li><code>Aadhar Number</code> — 12 digits <em>(used to match existing)</em></li>
                                                <li><code>Room Number</code> — must match an existing room</li>
                                            </ul>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <strong class="text-muted">Optional columns</strong>
                                            <ul>
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
                                    <div class="ml-ref-note">
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

    {{-- ══════════════════════════════════════════════════════════════════
         Transfer Room Modal (shared — action set per person via JS)
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-ledger">
                <div class="modal-header ml-head">
                    <div>
                        <h5 class="modal-title mb-0" id="transferModalLabel">
                            <i class="bi bi-arrow-left-right me-2"></i>Transfer Room
                        </h5>
                        <small>Move <strong id="transferPersonName">this person</strong> from Room <span id="transferCurrentRoom">—</span> to another room</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="transferForm" action="">
                    @csrf
                    <div class="modal-body">
                        <label class="form-label fw-500">New Room <span class="text-danger">*</span></label>
                        <select name="room_id" class="form-select" required>
                            <option value="">Select Room</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ $room->persons_count >= $room->capacity ? 'disabled' : '' }}>
                                    Room {{ $room->room_number }} — {{ $room->persons_count }}/{{ $room->capacity }} occupied{{ $room->persons_count >= $room->capacity ? ' (Full)' : '' }}
                                </option>
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

@section('scripts')
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

/* ── Transfer modal: point the shared form at the clicked person ── */
(function () {
    const form        = document.getElementById('transferForm');
    const nameEl      = document.getElementById('transferPersonName');
    const roomEl      = document.getElementById('transferCurrentRoom');
    if (!form) return;

    document.querySelectorAll('.transfer-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            form.action = btn.dataset.action;
            nameEl.textContent = btn.dataset.name;
            roomEl.textContent = btn.dataset.room;
            form.querySelector('select[name="room_id"]').value = '';
        });
    });
})();
</script>
@endsection
