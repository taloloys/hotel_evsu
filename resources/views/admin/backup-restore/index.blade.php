@extends('layouts.app')

@section('title', 'Backup & Restore')
@section('pageTitle', 'Backup & Restore')
@section('pageSubtitle', 'Save backups to your computer or restore from a file')

@push('styles')
    <style>
        .br-card {
            border: none;
            border-radius: 16px;
            box-shadow: var(--shadow-soft, 0 14px 34px rgba(78, 52, 46, 0.08));
        }

        .br-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        /* Windows-style Save As / Open File dialog box */
        .native-dialog {
            border: 1px solid #adb5bd;
            border-radius: 8px;
            background: #f0f0f0;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .native-dialog-titlebar {
            background: linear-gradient(180deg, #fff 0%, #e8e8e8 100%);
            border-bottom: 1px solid #adb5bd;
            padding: 0.45rem 0.75rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .native-dialog-body {
            background: #fff;
            padding: 1rem;
        }

        .native-field-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.65rem;
        }

        .native-field-row:last-child {
            margin-bottom: 0;
        }

        .native-field-label {
            font-size: 0.78rem;
            color: #444;
            width: 72px;
            flex-shrink: 0;
            text-align: right;
        }

        .native-field-input {
            flex: 1;
            border: 1px solid #adb5bd;
            border-radius: 4px;
            padding: 0.35rem 0.55rem;
            font-size: 0.82rem;
            background: #fff;
            color: #333;
            min-width: 0;
        }

        .native-field-input:read-only {
            background: #fafafa;
            color: #555;
        }

        .native-browse-btn {
            border: 1px solid #adb5bd;
            border-radius: 4px;
            background: linear-gradient(180deg, #fff 0%, #e8e8e8 100%);
            padding: 0.35rem 0.85rem;
            font-size: 0.78rem;
            color: #333;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.15s;
        }

        .native-browse-btn:hover {
            background: linear-gradient(180deg, #fff 0%, #d8d8d8 100%);
        }

        .native-dialog-footer {
            background: #f0f0f0;
            border-top: 1px solid #adb5bd;
            padding: 0.65rem 1rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .native-action-btn {
            border: 1px solid #adb5bd;
            border-radius: 4px;
            padding: 0.4rem 1.1rem;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .native-action-btn.primary {
            background: linear-gradient(180deg, #4a90d9 0%, #2563eb 100%);
            border-color: #1d4ed8;
            color: #fff;
        }

        .native-action-btn.primary:hover {
            background: linear-gradient(180deg, #5a9ae9 0%, #1d4ed8 100%);
        }

        .native-action-btn.warning {
            background: linear-gradient(180deg, #dc3545 0%, #b02a37 100%);
            border-color: #a52834;
            color: #fff;
        }

        .native-action-btn.warning:hover {
            background: linear-gradient(180deg, #bb2d3b 0%, #842029 100%);
        }

        .native-hint {
            font-size: 0.76rem;
            color: #888;
            margin-top: 0.5rem;
            padding-left: 78px;
        }

        .warning-badge {
            background: linear-gradient(135deg, #fff8e1 0%, #fff3cd 100%);
            color: #856404;
            border: 1px solid #ffe082;
            border-radius: 10px;
            padding: 0.65rem 1rem;
            font-size: 0.82rem;
        }

        .auto-toggle-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e8f4fd 100%);
            border: 1px solid #b3dff5;
            border-radius: 12px;
            padding: 1rem 1.15rem;
        }

        .auto-toggle-card.disabled-state {
            background: #f8f9fa;
            border-color: #dee2e6;
        }

        .form-check-input:checked {
            background-color: var(--caramel, #a97142);
            border-color: var(--caramel, #a97142);
        }

        .settings-panel.is-disabled {
            opacity: 0.45;
            pointer-events: none;
        }

        .file-list {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .file-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background: #faf8f5;
            border: 1px solid var(--border-soft, #e7dccf);
            border-radius: 12px;
            transition: background 0.15s, box-shadow 0.15s;
        }

        .file-row:hover {
            background: #fff;
            box-shadow: 0 3px 12px rgba(78, 52, 46, 0.06);
        }

        .file-row-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #388e3c;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .file-row-info { flex: 1; min-width: 0; }

        .file-row-name {
            font-family: ui-monospace, monospace;
            font-size: 0.82rem;
            font-weight: 600;
            color: #4e342e;
            word-break: break-all;
        }

        .file-row-meta {
            font-size: 0.75rem;
            color: #999;
            margin-top: 0.1rem;
        }

        .file-row-actions {
            display: flex;
            gap: 0.3rem;
            flex-shrink: 0;
        }

        .file-row-actions .btn {
            font-size: 0.72rem;
            padding: 0.3rem 0.55rem;
            border-radius: 6px;
            white-space: nowrap;
        }

        .server-note {
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 8px;
            padding: 0.55rem 0.75rem;
            font-size: 0.76rem;
            color: #666;
        }

        input[type="file"].visually-hidden-file {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }
    </style>
@endpush

@section('content')

    <div class="row g-4 mb-4">

        {{-- SAVE BACKUP AS (like saving image from Google) --}}
        <div class="col-lg-6">
            <div class="card br-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="br-icon-wrap bg-primary bg-opacity-10 me-3">
                            <i class="fa-solid fa-database text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Save Backup</div>
                            <div class="text-muted small">Export database — you pick where to save it on your PC</div>
                        </div>
                    </div>

                    <div class="native-dialog">
                        <div class="native-dialog-titlebar">
                            <i class="fa-solid fa-floppy-disk text-primary"></i>
                            Save Database Backup As...
                        </div>
                        <div class="native-dialog-body">
                            <div class="native-field-row">
                                <span class="native-field-label">File name:</span>
                                <input type="text" class="native-field-input" readonly
                                       value="{{ now()->format('F j Y g-i A') }}.sql">
                            </div>
                            <div class="native-field-row">
                                <span class="native-field-label">Save in:</span>
                                <input type="text" class="native-field-input" readonly
                                       value="Choose location when saving..." id="save-location-hint">
                            </div>
                            <div class="native-hint">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                Clicking Save opens your browser's <strong>Save As</strong> window — just like saving a picture.
                            </div>
                        </div>
                        <div class="native-dialog-footer">
                            <button type="button" class="native-action-btn primary" onclick="confirmSaveBackup()">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- OPEN FILE TO RESTORE (like opening a file on PC) --}}
        <div class="col-lg-6">
            <div class="card br-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="br-icon-wrap bg-warning bg-opacity-10 me-3">
                            <i class="fa-solid fa-rotate-left text-warning"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Restore Database</div>
                            <div class="text-muted small">Browse your computer and pick a .sql backup file</div>
                        </div>
                    </div>

                    <div class="warning-badge mb-3">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        <strong>Warning:</strong> This overwrites all current data. All users may be logged out.
                    </div>

                    <form id="restore-form" action="{{ route('admin.backup-restore.restore') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="native-dialog">
                            <div class="native-dialog-titlebar">
                                <i class="fa-solid fa-folder-open text-warning"></i>
                                Open Backup File
                            </div>
                            <div class="native-dialog-body">
                                <div class="native-field-row">
                                    <span class="native-field-label">File name:</span>
                                    <input type="text" class="native-field-input" readonly
                                           id="restore-file-display" placeholder="No file selected">
                                    <button type="button" class="native-browse-btn" onclick="document.getElementById('backup_file').click()">
                                        Browse...
                                    </button>
                                    <input type="file" id="backup_file" name="backup_file" accept=".sql,.txt" class="visually-hidden-file">
                                </div>
                                <div class="native-field-row">
                                    <span class="native-field-label">Look in:</span>
                                    <input type="text" class="native-field-input" readonly
                                           id="restore-folder-display" value="My Computer">
                                </div>
                                <div class="native-hint">
                                    <i class="fa-solid fa-circle-info me-1"></i>
                                    Click <strong>Browse...</strong> to open your file picker and select a .sql file.
                                </div>
                            </div>
                            <div class="native-dialog-footer">
                                <button id="restore-btn" type="button" class="native-action-btn warning" onclick="confirmRestore()">
                                    <i class="fa-solid fa-rotate-left me-1"></i> Restore
                                </button>
                            </div>
                        </div>

                        @error('backup_file')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Auto backup + server stored files --}}
    <div class="row g-4">

        <div class="col-lg-5">
            <div class="card br-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="br-icon-wrap bg-info bg-opacity-10 me-3">
                            <i class="fa-solid fa-clock text-info"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Automatic Daily Backup</div>
                            <div class="text-muted small">Runs on the server — no action needed from you</div>
                        </div>
                    </div>

                    <form id="settings-form" action="{{ route('admin.backup-restore.settings') }}" method="POST">
                        @csrf

                        <div class="auto-toggle-card mb-3" id="auto-toggle-card">
                            <div class="form-check form-switch m-0 d-flex align-items-center gap-3">
                                <input class="form-check-input flex-shrink-0" type="checkbox" role="switch"
                                       id="auto_backup_enabled" name="enabled" value="1"
                                       {{ ($settings['enabled'] ?? false) ? 'checked' : '' }}
                                       onchange="toggleAutoSettings()">
                                <div>
                                    <label class="form-check-label fw-bold text-dark mb-0" for="auto_backup_enabled">
                                        Enable Daily Backup
                                    </label>
                                    <div class="text-muted small">Backs up automatically every day</div>
                                </div>
                            </div>
                        </div>

                        <div class="settings-panel" id="settings-panel">
                            <div class="mb-3">
                                <label for="auto_backup_time" class="form-label fw-semibold small">Run at</label>
                                <input type="time" class="form-control" id="auto_backup_time" name="time"
                                       value="{{ $settings['time'] ?? '02:00' }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="auto_backup_folder" class="form-label fw-semibold small">Server save folder</label>
                                @php
                                    $currentFolder = $settings['folder'] ?? storage_path('backups');
                                    $isCustom = !collect($folderPresets)->pluck('path')->contains($currentFolder);
                                @endphp
                                <select class="form-select" id="folder_select" onchange="handleFolderSelect()">
                                    @foreach($folderPresets as $preset)
                                        <option value="{{ $preset['path'] }}" {{ !$isCustom && $currentFolder === $preset['path'] ? 'selected' : '' }}>
                                            {{ $preset['label'] }}
                                        </option>
                                    @endforeach
                                    <option value="custom" {{ $isCustom ? 'selected' : '' }}>Custom Directory...</option>
                                </select>
                                
                                <div id="custom_folder_container" class="mt-2" style="display: {{ $isCustom ? 'block' : 'none' }};">
                                    <input type="text" class="form-control" id="auto_backup_folder" name="folder" 
                                           value="{{ $currentFolder }}"
                                           placeholder="e.g. D:\Backups or C:\Hotel\Backups" required>
                                </div>
                                <div class="server-note mt-2">
                                    <i class="fa-solid fa-server me-1"></i>
                                    Auto backups save on the <strong>server</strong>, not your computer. Use "Save Backup" above to save to your PC.
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary w-100" onclick="confirmSaveSettings()">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card br-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="br-icon-wrap bg-success bg-opacity-10 me-3">
                                <i class="fa-solid fa-server text-success"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Server Backups</div>
                                <div class="text-muted small">Previously saved copies on the server</div>
                            </div>
                        </div>
                        <span class="badge bg-light text-dark border">{{ count($backups) }} file{{ count($backups) !== 1 ? 's' : '' }}</span>
                    </div>

                    @if(count($backups) > 0)
                        <div class="file-list">
                            @foreach($backups as $backup)
                                <div class="file-row">
                                    <div class="file-row-icon">
                                        <i class="fa-solid fa-file-code"></i>
                                    </div>
                                    <div class="file-row-info">
                                        <div class="file-row-name">{{ $backup['filename'] }}</div>
                                        <div class="file-row-meta">
                                            {{ $backup['created_at'] }} &middot; {{ $backup['size'] }}
                                        </div>
                                    </div>
                                    <div class="file-row-actions">
                                        <a href="{{ route('admin.backup-restore.download-local', $backup['filename']) }}"
                                           class="btn btn-sm btn-outline-primary" title="Save to your computer">
                                            <i class="fa-solid fa-floppy-disk me-1"></i> Save As...
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-warning text-dark"
                                                onclick="confirmServerRestore('{{ $backup['filename'] }}')">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <form id="delete-form-{{ md5($backup['filename']) }}"
                                              action="{{ route('admin.backup-restore.delete-local', $backup['filename']) }}"
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirmServerDelete('{{ $backup['filename'] }}', '{{ md5($backup['filename']) }}')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($hasOlderBackups)
                            <div class="server-note mt-3">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                Only the 5 most recent backups are shown here.
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4 text-muted small">
                            <i class="fa-solid fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                            No server backups yet. Click <strong>Save</strong> above to create one.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('turbo:load', function () {
                toggleAutoSettings();
                initFilePicker();
            });

            function initFilePicker() {
                const fileInput = document.getElementById('backup_file');
                const fileDisplay = document.getElementById('restore-file-display');
                const folderDisplay = document.getElementById('restore-folder-display');

                if (!fileInput) return;

                fileInput.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        const file = this.files[0];
                        fileDisplay.value = file.name;
                        // webkitRelativePath or fake folder hint
                        folderDisplay.value = file.webkitRelativePath
                            ? file.webkitRelativePath.split('/').slice(0, -1).join('/') || 'Selected folder'
                            : 'Selected from your computer';
                    } else {
                        fileDisplay.value = '';
                        fileDisplay.placeholder = 'No file selected';
                        folderDisplay.value = 'My Computer';
                    }
                });
            }

            function toggleAutoSettings() {
                const enabled = document.getElementById('auto_backup_enabled').checked;
                document.getElementById('settings-panel').classList.toggle('is-disabled', !enabled);
                document.getElementById('auto-toggle-card').classList.toggle('disabled-state', !enabled);
            }

            function handleFolderSelect() {
                const select = document.getElementById('folder_select');
                const customContainer = document.getElementById('custom_folder_container');
                const folderInput = document.getElementById('auto_backup_folder');
                
                if (select.value === 'custom') {
                    customContainer.style.display = 'block';
                    folderInput.value = ''; // clear or leave previous custom value
                    folderInput.focus();
                } else {
                    customContainer.style.display = 'none';
                    folderInput.value = select.value;
                }
            }

            function confirmSaveBackup() {
                Swal.fire({
                    title: 'Save Database Backup?',
                    html: 'Your browser will open a <strong>Save As</strong> window.<br>Choose any folder on your computer to save the file.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa-solid fa-floppy-disk me-1"></i> Save As...',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const downloadUrl = "{{ route('admin.backup-restore.backup') }}";
                        const iframe = document.createElement('iframe');
                        iframe.style.display = 'none';
                        iframe.src = downloadUrl;
                        document.body.appendChild(iframe);

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                });
            }

            function confirmRestore() {
                const fileInput = document.getElementById('backup_file');
                if (!fileInput || !fileInput.files.length) {
                    Swal.fire({
                        title: 'No File Selected',
                        text: 'Click Browse... and pick a .sql backup file first.',
                        icon: 'warning',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Restore Database?',
                    html: `Restore from: <strong>${fileInput.files[0].name}</strong><br><br><span class="text-danger fw-bold">This will OVERWRITE all current data!</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e53935',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Restore',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const btn = document.getElementById('restore-btn');
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Restoring...';

                        Swal.fire({
                            title: 'Restoring...',
                            text: 'Please wait, do not close this page.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading()
                        });

                        document.getElementById('restore-form').submit();
                    }
                });
            }

            function confirmServerRestore(filename) {
                Swal.fire({
                    title: 'Restore Server Backup?',
                    html: `Restore from: <strong>${filename}</strong><br><br><span class="text-danger fw-bold">This will OVERWRITE all current data!</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e53935',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Restore',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Restoring...',
                            text: 'Please wait, do not close this page.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading()
                        });

                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ route('admin.backup-restore.restore-local') }}";

                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = "{{ csrf_token() }}";
                        form.appendChild(csrfInput);

                        const fileInput = document.createElement('input');
                        fileInput.type = 'hidden';
                        fileInput.name = 'filename';
                        fileInput.value = filename;
                        form.appendChild(fileInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            function confirmServerDelete(filename, md5Id) {
                Swal.fire({
                    title: 'Delete Backup?',
                    html: `Delete <strong>${filename}</strong> from server?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + md5Id).submit();
                    }
                });
            }

            function confirmSaveSettings() {
                const isEnabled = document.getElementById('auto_backup_enabled').checked;
                const time = document.getElementById('auto_backup_time').value;
                const folderValue = document.getElementById('auto_backup_folder').value;

                Swal.fire({
                    title: 'Save Settings?',
                    html: isEnabled
                        ? `Enable daily backup at <strong>${time}</strong><br>Server folder: <strong>${folderValue}</strong>`
                        : 'Disable automatic daily backups?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('settings-form').submit();
                    }
                });
            }
        </script>
    @endpush

@endsection
