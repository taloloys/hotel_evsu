@extends('layouts.app')

@section('title', 'Backup & Restore')
@section('pageTitle', 'Backup & Restore')
@section('pageSubtitle', 'Download a database backup or restore from a previous file')

@push('styles')
    <style>
        .br-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.07);
        }

        .br-icon-wrap {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .upload-area {
            border: 2px dashed #d0c4b0;
            border-radius: 10px;
            padding: 2.2rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, background-color 0.2s;
            background-color: #fdfaf6;
        }

        .upload-area:hover,
        .upload-area.drag-over {
            border-color: var(--caramel, #a97142);
            background-color: #fdf6ee;
        }

        .upload-area input[type="file"] {
            display: none;
        }

        .upload-icon {
            font-size: 2.4rem;
            color: #c8a97a;
            margin-bottom: 0.6rem;
        }

        .warning-badge {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-size: 0.82rem;
        }

        #selected-file-info {
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
    </style>
@endpush

@section('content')

    <div class="row g-4">

        {{-- CREATE BACKUP --}}
        <div class="col-lg-6">
            <div class="card br-card h-100">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-4">
                        <div class="br-icon-wrap bg-primary bg-opacity-10 me-3">
                            <i class="fa-solid fa-database text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-6">Create Backup</div>
                            <div class="text-muted small">Downloads a .sql file directly to your computer</div>
                        </div>
                    </div>

                    <p class="text-muted small mb-4">
                        Click the button below. Your browser will prompt you to choose where to save the file.
                        The filename will include today's date and time automatically.
                    </p>

                    <div class="bg-light rounded-3 px-3 py-2 mb-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-file-code text-muted"></i>
                        <span class="text-muted small font-monospace">
                            {{ now()->format('F j Y g-i A') }}.sql
                        </span>
                    </div>

                    <button id="backup-btn"
                            type="button"
                            class="btn btn-primary w-100"
                            onclick="confirmBackup()"
                    >
                        <i class="fa-solid fa-download me-2"></i>
                        Download Backup
                    </button>

                </div>
            </div>
        </div>

        {{-- RESTORE --}}
        <div class="col-lg-6">
            <div class="card br-card h-100">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-4">
                        <div class="br-icon-wrap bg-warning bg-opacity-10 me-3">
                            <i class="fa-solid fa-rotate-left text-warning"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-6">Restore Database</div>
                            <div class="text-muted small">Upload a .sql file from your computer to restore</div>
                        </div>
                    </div>

                    <div class="warning-badge mb-4">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        <strong>Warning:</strong> This will overwrite the current database. This cannot be undone.
                    </div>

                    <form
                        id="restore-form"
                        action="{{ route('admin.backup-restore.restore') }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >
                        @csrf

                        <div class="mb-3">
                            <div class="upload-area" id="upload-area" onclick="document.getElementById('backup_file').click()">
                                <div class="upload-icon">
                                    <i class="fa-solid fa-file-arrow-up"></i>
                                </div>
                                <div class="fw-semibold small">Click to select a .sql backup file</div>
                                <div class="text-muted" style="font-size:.78rem; margin-top:.25rem;">or drag and drop here</div>
                                <input type="file" id="backup_file" name="backup_file" accept=".sql,.txt">
                            </div>

                            <div id="selected-file-info" class="text-success d-none">
                                <i class="fa-solid fa-file-code me-1"></i>
                                <span id="selected-file-name"></span>
                            </div>

                            @error('backup_file')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button id="restore-btn" type="button" class="btn btn-warning w-100 text-dark" onclick="confirmRestore()">
                            <i class="fa-solid fa-rotate-left me-2"></i>
                            Restore Database
                        </button>

                    </form>

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('turbo:load', function () {
                const uploadArea = document.getElementById('upload-area');
                const fileInput  = document.getElementById('backup_file');
                const fileInfo   = document.getElementById('selected-file-info');
                const fileName   = document.getElementById('selected-file-name');

                // Show selected filename
                if (fileInput) {
                    fileInput.addEventListener('change', function () {
                        if (this.files.length > 0) {
                            fileName.textContent = this.files[0].name;
                            fileInfo.classList.remove('d-none');
                        } else {
                            fileInfo.classList.add('d-none');
                        }
                    });
                }

                // Drag & drop
                if (uploadArea) {
                    uploadArea.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        uploadArea.classList.add('drag-over');
                    });
                    uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
                    uploadArea.addEventListener('drop', (e) => {
                        e.preventDefault();
                        uploadArea.classList.remove('drag-over');
                        if (e.dataTransfer.files.length > 0) {
                            fileInput.files = e.dataTransfer.files;
                            fileName.textContent = e.dataTransfer.files[0].name;
                            fileInfo.classList.remove('d-none');
                        }
                    });
                }
            });

            function confirmBackup() {
                Swal.fire({
                    title: 'Download Database Backup?',
                    text: "You are about to export and download a full SQL dump of the current database.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Download',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const backupBtn = document.getElementById('backup-btn');
                        if (backupBtn) {
                            backupBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Preparing download...';
                            backupBtn.disabled = true;
                            
                            // Re-enable after download starts/timeout
                            setTimeout(() => {
                                backupBtn.innerHTML = '<i class="fa-solid fa-download me-2"></i>Download Backup';
                                backupBtn.disabled = false;
                            }, 5000);
                        }
                        
                        // Trigger actual download redirect
                        window.location.href = "{{ route('admin.backup-restore.backup') }}";
                    }
                });
            }

            function confirmRestore() {
                const fileInput = document.getElementById('backup_file');
                if (!fileInput || !fileInput.files.length) {
                    Swal.fire({
                        title: 'No File Selected',
                        text: 'Please select or drag a .sql backup file first.',
                        icon: 'warning',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to restore the database from: <strong>${fileInput.files[0].name}</strong>.<br><br><span class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> WARNING: This will completely OVERWRITE the current database!</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e53935',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Restore Database',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const btn = document.getElementById('restore-btn');
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Restoring database...';
                        
                        Swal.fire({
                            title: 'Restoring Database...',
                            text: 'Please wait, do not close or refresh this page.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        document.getElementById('restore-form').submit();
                    }
                });
            }
        </script>
    @endpush

@endsection
