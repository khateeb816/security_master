@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h4 class="fw-semibold mb-0">Checkpoints List</h4>

            <div class="d-flex align-items-center gap-3">
                <!-- Filter by Client -->
                <div class="position-relative" style="min-width: 220px;">
                    <select name="client_id" id="clientFilter" class="form-select form-select-lg border-2 border-primary"
                        style="height: 38px; border-radius: 8px;">
                        <option value="">Select Company</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" {{ $selectedClient == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="position-absolute top-50 end-0 translate-middle-y pe-3">
                        <i class="fas fa-building text-primary"></i>
                    </div>
                </div>


                <!-- Branch Filter -->
                <div class="position-relative" style="min-width: 220px;">
                    <select name="branch_id" id="branchFilter" class="form-select form-select-lg border-2 border-info"
                        style="height: 38px; border-radius: 8px;" {{ !$selectedClient ? 'disabled' : '' }}>
                        <option value="">Select Branch</option>
                        @if ($selectedClient && $branches->count())
                            @foreach ($branches as $branch)
                                @php
                                    $name = $branch->name;
                                    $id = $branch->id;
                                @endphp
                                <option value="{{ $id }}" {{ $selectedBranch == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div class="position-absolute top-50 end-0 translate-middle-y pe-3">
                        <i class="fas fa-code-branch text-info"></i>
                    </div>
                </div>

                <!-- Add Checkpoint Button -->
                <button class="btn btn-primary d-flex align-items-center gap-2" id="addCheckpointBtn"
                    style="height: 38px; border-radius: 8px;">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Checkpoint</span>
                </button>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Checkpoint Name</th>
                                @if (!request('client_id'))
                                    <th>Client</th>
                                    <th>Branch</th>
                                @endif
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="checkpointsTableBody">
                            @if (isset($checkpoints) && $checkpoints->count() > 0)
                                @foreach ($checkpoints as $index => $checkpoint)
                                    <tr data-id="{{ $checkpoint->id }}" data-branch-id="{{ $checkpoint->branch_id }}" data-client-id="{{ $checkpoint->client_id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $checkpoint->name }}</td>
                                        @if (!request('client_id'))
                                            <td>
                                                @if ($checkpoint->branch && $checkpoint->branch->client)
                                                    <span class="badge bg-primary">{{ $checkpoint->branch->client->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($checkpoint->branch)
                                                    <span class="badge bg-info">{{ $checkpoint->branch->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <span
                                                class="badge {{ $checkpoint->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $checkpoint->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $checkpoint->created_at->format('M d, Y') }}</td>
                                        <td class="text-nowrap">
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('clients.branches.checkpoints.edit', [
                                                    'client' => $checkpoint->client_id,
                                                    'branch' => $checkpoint->branch_id,
                                                    'checkpoint' => $checkpoint->id,
                                                ]) }}"
                                                    class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                    <span class="d-none d-md-inline">Edit</span>
                                                </a>
                                                <button
                                                    class="btn btn-sm btn-outline-danger delete-checkpoint d-flex align-items-center gap-1"
                                                    data-id="{{ $checkpoint->id }}" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                    <span class="d-none d-md-inline">Delete</span>
                                                </button>
                                                <a href="{{ route('clients.branches.checkpoints.qrcode', [
                                                    'client' => $checkpoint->client_id,
                                                    'branch' => $checkpoint->branch_id,
                                                    'checkpoint' => $checkpoint->id,
                                                ]) }}"
                                                    class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                                                    target="_blank" title="View QR Code">
                                                    <i class="fas fa-qrcode"></i>
                                                    <span class="d-none d-md-inline">QR Code</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="{{ !request('client_id') ? '7' : '5' }}" class="text-center py-4">
                                        No checkpoints found. Use the filters above to view specific checkpoints or add new ones.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- 🟩 Add Checkpoint Modal -->
    <div class="modal fade" id="addCheckpointModal" tabindex="-1" aria-labelledby="addCheckpointModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="checkpointForm" class="modal-content">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="checkpointId">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addCheckpointModalLabel">
                        <i class="fas fa-plus-circle me-1"></i> <span id="modalTitle">Add New Checkpoint</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="formErrors" class="alert alert-danger d-none"></div>

                    <div class="row g-3">
                        <!-- Client Selection -->
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                            <select class="form-select" id="client_id" name="client_id" required>
                                <option value="">Select Client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ $selectedClient == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Branch Selection -->
                        <div class="col-md-6">
                            <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                @if (isset($branches) && $branches->count() > 0)
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" data-lat="{{ $branch->latitude }}"
                                            data-lng="{{ $branch->longitude }}"
                                            {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Checkpoint Details -->
                        <div class="col-md-12">
                            <label for="name" class="form-label">Checkpoint Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-12">
                            <label for="description" class="form-label">Checkpoint Description </label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="number" step="any" class="form-control" id="latitude" name="latitude">
                        </div>

                        <div class="col-md-6">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="number" step="any" class="form-control" id="longitude" name="longitude">
                        </div>

                        <div class="col-md-12">
                            <label for="radius" class="form-label">Geofence Radius (meters) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="radius" name="radius" required>
                                <button class="btn btn-outline-secondary" type="button" id="setDefaultRadius">Default (50m)</button>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveCheckpointBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Save Checkpoint
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            .select2-container--bootstrap-5 .select2-selection {
                min-height: 38px;
                padding: 0.25rem 0.5rem;
            }

            .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
                padding-left: 0.5rem;
            }

            .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
                height: 36px;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }

            .badge {
                font-size: 0.85em;
                padding: 0.4em 0.65em;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Use jQuery in noConflict mode
            jQuery(document).ready(function($) {
                // Debug: Check if Bootstrap is loaded
                console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? bootstrap.Tooltip.VERSION :
                    'not loaded');
                console.log('jQuery version:', $.fn.jquery);

                // Initialize modal
                const modalElement = document.getElementById('addCheckpointModal');
                console.log('Modal element found:', !!modalElement);
                if (!modalElement) {
                    console.error('Modal element not found!');
                    return;
                }

                const checkpointModal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });

                // Debug: Log modal initialization
                console.log('Modal initialized:', !!checkpointModal);

                const branchId = '{{ request('branch_id') }}';
                const clientId = '{{ request('client_id') }}';


                // Debug: Log initial state
                console.log('Initial branchId:', branchId);
                console.log('Initial clientId:', clientId);
                console.log('Add Checkpoint Button disabled state:', $('#addCheckpointBtn').prop('disabled'));

                // Initialize Select2 for client filter
                $('#clientFilter').select2({
                    placeholder: 'Select Company',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#clientFilter').parent(),
                    theme: 'bootstrap-5'
                }).on('change', function() {
                    const clientId = $(this).val();
                    const branchFilter = $('#branchFilter');

                    // Reset and disable branch filter
                    branchFilter.empty().append('<option value="">Loading branches...</option>').prop(
                        'disabled', true).trigger('change');

                    if (!clientId) {
                        // Redirect to the checkpoints index with client_id parameter
                        const url = new URL(window.location.href);
                        url.searchParams.delete('branch_id');
                        window.location.href = url.toString();
                        return;
                    }

                    // Load branches for the selected client
                    $.ajax({
                        url: `/clients/${clientId}/branches`,
                        method: 'GET',
                        success: function(response) {
                            branchFilter.empty().append('<option value="">Select Branch</option>');

                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(branch) {
                                    branchFilter.append(
                                        `<option value="${branch.id}" data-lat="${branch.latitude || ''}" data-lng="${branch.longitude || ''}">${branch.name}</option>`
                                    );
                                });
                                branchFilter.prop('disabled', false);
                            } else {
                                branchFilter.append('<option value="">No branches found</option>');
                            }

                            // Update URL with client_id
                            updateUrl({
                                client_id: clientId,
                                branch_id: ''
                            });

                            // If there's only one branch, select it automatically
                            if (response.data && response.data.length === 1) {
                                console.log('Auto-selecting the only branch:', response.data[0].id);
                                branchFilter.val(response.data[0].id).trigger('change');
                            }
                        },
                        error: function(xhr) {
                            console.error('Failed to load branches', xhr);
                            showToast('error', 'Failed to load branches');
                            branchFilter.empty().append(
                                '<option value="">Error loading branches</option>');
                        }
                    });
                });

                // Initialize Select2 for branch filter
                $('#branchFilter').select2({
                    placeholder: 'Select Branch',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#branchFilter').parent(),
                    theme: 'bootstrap-5'
                });

                // Handle branch filter change with debounce
                let branchChangeTimer;
                $('#branchFilter').on('change', function() {
                    console.log('Branch filter changed');
                    clearTimeout(branchChangeTimer);
                    const branchId = $(this).val();
                    const clientId = $('#clientFilter').val();

                    console.log('Branch ID from select:', branchId, 'Client ID:', clientId);

                    branchChangeTimer = setTimeout(() => {
                        console.log('Processing branch change after debounce');
                        if (!branchId) {
                            updateUrl({
                                client_id: clientId,
                                branch_id: ''
                            });
                            // Load all checkpoints for the client or all checkpoints if no client
                            if (clientId) {
                                loadAllCheckpointsForClient(clientId);
                            } else {
                                loadAllCheckpoints();
                            }
                            return;
                        }

                        console.log('Loading checkpoints for branch:', branchId);
                        updateUrl({
                            client_id: clientId,
                            branch_id: branchId
                        });
                        loadCheckpoints(branchId);
                    }, 300); // 300ms debounce
                });

                // Initialize Select2 on page load if client is selected
                @if ($selectedClient)
                    $('#clientFilter').trigger('change');
                @endif

                // If branch is selected, trigger the change after a delay
                @if ($selectedBranch)
                    $(document).ready(function() {
                        // Small delay to ensure select2 is initialized
                        setTimeout(() => {
                            const branchId = '{{ $selectedBranch }}';
                            if ($('#branchFilter option[value="' + branchId + '"]').length > 0) {
                                $('#branchFilter').val(branchId).trigger('change');
                            }
                        }, 500);
                    });
                @endif

                // Set default geofence radius
                $('#setDefaultRadius').on('click', function() {
                    $('#radius').val('50');
                });

                // Update latitude/longitude when branch is selected
                $('#branch_id').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const lat = selectedOption.data('lat');
                    const lng = selectedOption.data('lng');

                    if (lat && lng) {
                        $('#latitude').val(lat);
                        $('#longitude').val(lng);
                    } else {
                        // If no coordinates in branch, allow manual entry
                        $('#latitude, #longitude').prop('readonly', false).attr('placeholder',
                            'Enter coordinates manually');
                    }
                });

                // Auto-select client when branch is selected
                $('#branch_id').on('change', function() {
                    const branchId = $(this).val();
                    if (branchId) {
                        // Find the branch in the branches list
                        const branchOption = $(`#branch_id option[value="${branchId}"]`);
                        const clientId = branchOption.closest('select').find(`option[value="${branchId}"]`)
                            .data('client-id');
                        if (clientId) {
                            $('#client_id').val(clientId);
                        }
                    }
                });

                // Load branches when client changes
                $('#client_id').on('change', function() {
                    const clientId = $(this).val();
                    const branchSelect = $('#branch_id');

                    if (!clientId) {
                        branchSelect.html('<option value="">Select Branch</option>');
                        return;
                    }

                    // Show loading state
                    branchSelect.html('<option value="">Loading branches...</option>').prop('disabled', true);

                    // Load branches via AJAX
                    $.get(`/clients/${clientId}/branches`, function(response) {
                        let options = '<option value="">Select Branch</option>';

                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(branch) {
                                options +=
                                    `<option value="${branch.id}" data-lat="${branch.latitude || ''}" data-lng="${branch.longitude || ''}">${branch.name}</option>`;
                            });
                            branchSelect.prop('disabled', false);
                        } else {
                            options = '<option value="">No branches found</option>';
                        }

                        branchSelect.html(options);
                    }).fail(function() {
                        branchSelect.html('<option value="">Error loading branches</option>');
                    });
                });

                // Handle add checkpoint button click
                $('#addCheckpointBtn').on('click', function(e) {
                    e.preventDefault();
                    console.log('Add Checkpoint button clicked');

                    // Check if button is disabled
                    if ($(this).prop('disabled')) {
                        console.log('Button is disabled');
                        return;
                    }

                    try {
                        // Reset form and prepare modal
                        resetForm();
                        $('#modalTitle').text('Add New Checkpoint');
                        $('#formMethod').val('POST');

                        // Show modal
                        checkpointModal.show();
                        console.log('Modal shown');
                    } catch (error) {
                        console.error('Error in click handler:', error);
                        alert('Error opening form: ' + error.message);
                    }
                });

                // Handle form submission
                $('#checkpointForm').on('submit', function(e) {
                    e.preventDefault();

                    // Get client and branch IDs from form
                    const clientId = $('#client_id').val();
                    const branchId = $('#branch_id').val();

                    if (!clientId || !branchId) {
                        showToast('error', 'Please select both client and branch');
                        return;
                    }

                    // Validate required fields
                    const name = $('#name').val().trim();
                    const latitude = $('#latitude').val().trim();
                    const longitude = $('#longitude').val().trim();

                    if (!name) {
                        showToast('error', 'Please enter a checkpoint name');
                        return;
                    }

                    if (!latitude || !longitude) {
                        showToast('error', 'Please select a branch with valid coordinates');
                        return;
                    }

                    const formData = $(this).serialize();
                    const method = $('#formMethod').val();
                    let url = `/clients/${clientId}/branches/${branchId}/checkpoints`;

                    // For update, append checkpoint ID to URL
                    const checkpointId = $('#checkpointId').val();
                    if (checkpointId) {
                        url += `/${checkpointId}`;
                    }

                    toggleLoading(true);

                    // Submit the form
                    $.ajax({
                        url: url,
                        method: method === 'PUT' ? 'POST' : 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            showToast('success', response.message || (method === 'PUT' ?
                                'Checkpoint updated successfully!' :
                                'Checkpoint added successfully!'));
                            checkpointModal.hide();

                            // Reload checkpoints for the current branch
                            loadCheckpoints(branchId);

                            // Update the URL to reflect the current branch
                            updateUrl({
                                client_id: clientId,
                                branch_id: branchId
                            });
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON?.errors;
                            if (errors) {
                                let errorHtml = '<ul class="mb-0">';
                                Object.values(errors).forEach(error => {
                                    errorHtml += `<li>${error[0]}</li>`;
                                });
                                errorHtml += '</ul>';
                                $('#formErrors').html(errorHtml).removeClass('d-none');

                                // Scroll to top of form to show errors
                                $('html, body').animate({
                                    scrollTop: $('#formErrors').offset().top - 20
                                }, 500);
                            } else {
                                showToast('error', xhr.responseJSON?.message ||
                                    'An error occurred. Please try again.');
                            }
                        },
                        complete: function() {
                            toggleLoading(false);
                        }
                    });
                });

                // Handle edit button click
                $(document).on('click', '.edit-checkpoint', function() {
                    const checkpointId = $(this).data('id');
                    const row = $(this).closest('tr');
                    let branchId = row.data('branch-id');
                    let clientId = row.data('client-id');
                    console.log('Edit checkpoint:', { checkpointId, branchId, clientId });

                    if (!clientId || !branchId) {
                        showToast('error', 'Could not determine client or branch for this checkpoint.');
                        return;
                    }

                    toggleLoading(true);

                    $.get(`/clients/${clientId}/branches/${branchId}/checkpoints/${checkpointId}`, function(response) {
                        console.log('Checkpoint data loaded:', response);
                        const checkpoint = response.data;
                        // Fallback: if branchId/clientId are missing, get from checkpoint object
                        branchId = branchId || checkpoint.branch_id;
                        clientId = clientId || (checkpoint.branch ? checkpoint.branch.user_id : checkpoint.client_id);
                        // ...populate modal as before...
                        $('#checkpointId').val(checkpoint.id);
                        $('#name').val(checkpoint.name || '');
                        $('#client_id').val(clientId).trigger('change');
                        setTimeout(() => {
                            $('#branch_id').val(branchId).trigger('change');
                            setTimeout(() => {
                                if (checkpoint.latitude && checkpoint.longitude) {
                                    $('#latitude').val(checkpoint.latitude);
                                    $('#longitude').val(checkpoint.longitude);
                                }
                            }, 200);
                        }, 200);
                        if (checkpoint.radius) {
                            $('#radius').val(checkpoint.radius);
                        }
                        $('#is_active').prop('checked', checkpoint.is_active);
                        $('#modalTitle').text('Edit Checkpoint');
                        $('#formMethod').val('PUT');
                        checkpointModal.show();
                    })
                    .fail(function(xhr) {
                        showToast('error', 'Failed to load checkpoint data');
                    })
                    .always(function() {
                        toggleLoading(false);
                    });
                });

                // Handle delete button click
                $(document).on('click', '.delete-checkpoint', function() {
                    const checkpointId = $(this).data('id');
                    const clientId = $('#clientFilter').val();
                    const branchId = $('#branchFilter').val();

                    if (confirm(
                            'Are you sure you want to delete this checkpoint? This action cannot be undone.')) {
                        toggleLoading(true);

                        $.ajax({
                            url: `/clients/${clientId}/branches/${branchId}/checkpoints/${checkpointId}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function() {
                                showToast('success', 'Checkpoint deleted successfully!');
                                loadCheckpoints(branchId);
                            },
                            error: function() {
                                showToast('error', 'Failed to delete checkpoint.');
                            },
                            complete: function() {
                                toggleLoading(false);
                            }
                        });
                    }
                });

                // Load checkpoints for the selected branch
                function loadCheckpoints(branchId) {
                    const clientId = $('#clientFilter').val();
                    if (!clientId || !branchId) {
                        console.error('Client ID or Branch ID is missing');
                        return;
                    }

                    // Update URL with current client and branch
                    updateUrl({
                        client_id: clientId,
                        branch_id: branchId
                    });

                    const url = `/clients/${clientId}/branches/${branchId}/checkpoints`;
                    console.log('Loading checkpoints from:', url);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log('Checkpoints loaded successfully:', response);
                            const tbody = $('#checkpointsTableBody');
                            tbody.empty();

                            if (response && response.length > 0) {
                                response.forEach((checkpoint, index) => {
                                    tbody.append(`
                            <tr data-id="${checkpoint.id}" data-branch-id="${checkpoint.branch_id}" data-client-id="${checkpoint.client_id}">
                                <td>${index + 1}</td>
                                <td>${checkpoint.name || '-'}</td>
                                <td>
                                    <span class="badge ${checkpoint.is_active ? 'bg-success' : 'bg-secondary'}">
                                        ${checkpoint.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </td>
                                <td>${checkpoint.created_at ? new Date(checkpoint.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-'}</td>
                                <td class="text-nowrap">
                                    <div class="d-flex gap-1">
                                        <a href="/clients/${checkpoint.client_id}/branches/${checkpoint.branch_id}/checkpoints/${checkpoint.id}/edit"
                                            class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                            <span class="d-none d-md-inline">Edit</span>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger delete-checkpoint d-flex align-items-center gap-1" data-id="${checkpoint.id}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                            <span class="d-none d-md-inline">Delete</span>
                                        </button>
                                        <a href="/clients/${checkpoint.client_id}/branches/${checkpoint.branch_id}/checkpoints/${checkpoint.id}/qrcode"
                                            class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                                            target="_blank" title="View QR Code">
                                            <i class="fas fa-qrcode"></i>
                                            <span class="d-none d-md-inline">QR Code</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `);
                                });
                            } else {
                                tbody.append(`
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No checkpoints found for this branch.
                            </td>
                        </tr>
                    `);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading checkpoints:', {
                                status: status,
                                error: error,
                                response: xhr.responseText
                            });
                            showToast('error',
                                'Failed to load checkpoints. Please check the console for details.');
                        }
                    });
                }

                // Reset form and show modal for adding new checkpoint
                function resetForm() {
                    // Reset form fields
                    $('#checkpointForm')[0].reset();
                    $('#checkpointId').val('');
                    $('#formMethod').val('POST');
                    $('#is_active').prop('checked', true);
                    $('.error-message').remove();
                    $('.is-invalid').removeClass('is-invalid');
                    $('#formErrors').addClass('d-none').html('');

                    // Get current client and branch from URL or form
                    const urlParams = new URLSearchParams(window.location.search);
                    const clientId = '{{ $selectedClient }}' || $('#clientFilter').val();
                    const branchId = '{{ $selectedBranch }}' || $('#branchFilter').val();

                    // Set client and branch in form if available
                    if (clientId) {
                        $('#client_id').val(clientId).trigger('change');
                    }

                    // Small delay to allow branch select to update
                    setTimeout(() => {
                        if (branchId) {
                            $('#branch_id').val(branchId).trigger('change');
                        }
                    }, 100);

                    // Update form action with current client and branch IDs
                    if (clientId && branchId) {
                        $('#checkpointForm').attr('action', `/clients/${clientId}/branches/${branchId}/checkpoints`);
                    }

                    // Enable geofence by default
                    $('#geofence_enabled').prop('checked', true);
                }

                // Toggle loading state
                function toggleLoading(show) {
                    const btn = $('#saveCheckpointBtn');
                    const spinner = btn.find('.spinner-border');

                    if (show) {
                        btn.prop('disabled', true);
                        spinner.removeClass('d-none');
                    } else {
                        btn.prop('disabled', false);
                        spinner.addClass('d-none');
                    }
                }

                // Show toast notification
                function showToast(type, message) {
                    const toast = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

                    const toastContainer = $('.toast-container');
                    if (toastContainer.length === 0) {
                        $('body').append('<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
                    }

                    const $toast = $(toast).appendTo('.toast-container');
                    const bsToast = new bootstrap.Toast($toast[0]);
                    bsToast.show();

                    $toast.on('hidden.bs.toast', function() {
                        $(this).remove();
                    });
                }

                // Update URL parameters
                function updateUrl(params) {
                    const url = new URL(window.location.href);
                    Object.keys(params).forEach(key => {
                        if (params[key]) {
                            url.searchParams.set(key, params[key]);
                        } else {
                            url.searchParams.delete(key);
                        }
                    });
                    window.history.pushState({}, '', url);
                }

                // Initialize the page
                if (branchId) {
                    loadCheckpoints(branchId);
                }

                // Load all checkpoints
                function loadAllCheckpoints() {
                    const url = '/checkpoints';
                    console.log('Loading all checkpoints from:', url);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log('All checkpoints loaded successfully:', response);
                            const tbody = $('#checkpointsTableBody');
                            tbody.empty();

                            if (response && response.length > 0) {
                                response.forEach((checkpoint, index) => {
                                    tbody.append(`
                            <tr data-id="${checkpoint.id}" data-branch-id="${checkpoint.branch_id}" data-client-id="${checkpoint.client_id}">
                                <td>${index + 1}</td>
                                <td>${checkpoint.name || '-'}</td>
                                <td>
                                    <span class="badge bg-primary">${checkpoint.branch && checkpoint.branch.client ? checkpoint.branch.client.name : '-'}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">${checkpoint.branch ? checkpoint.branch.name : '-'}</span>
                                </td>
                                <td>
                                    <span class="badge ${checkpoint.is_active ? 'bg-success' : 'bg-secondary'}">
                                        ${checkpoint.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </td>
                                <td>${checkpoint.created_at ? new Date(checkpoint.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-'}</td>
                                <td class="text-nowrap">
                                    <div class="d-flex gap-1">
                                        <a href="/clients/${checkpoint.client_id}/branches/${checkpoint.branch_id}/checkpoints/${checkpoint.id}/edit"
                                            class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                            <span class="d-none d-md-inline">Edit</span>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger delete-checkpoint d-flex align-items-center gap-1" data-id="${checkpoint.id}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                            <span class="d-none d-md-inline">Delete</span>
                                        </button>
                                        <a href="/clients/${checkpoint.client_id}/branches/${checkpoint.branch_id}/checkpoints/${checkpoint.id}/qrcode"
                                            class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                                            target="_blank" title="View QR Code">
                                            <i class="fas fa-qrcode"></i>
                                            <span class="d-none d-md-inline">QR Code</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `);
                                });
                            } else {
                                tbody.append(`
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No checkpoints found. Use the filters above to view specific checkpoints or add new ones.
                            </td>
                        </tr>
                    `);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading all checkpoints:', {
                                status: status,
                                error: error,
                                response: xhr.responseText
                            });
                            showToast('error',
                                'Failed to load checkpoints. Please check the console for details.');
                        }
                    });
                }

                // Load all checkpoints for a specific client
                function loadAllCheckpointsForClient(clientId) {
                    const url = `/clients/${clientId}/checkpoints`;
                    console.log('Loading all checkpoints for client from:', url);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log('Client checkpoints loaded successfully:', response);
                            const tbody = $('#checkpointsTableBody');
                            tbody.empty();

                            if (response && response.length > 0) {
                                response.forEach((checkpoint, index) => {
                                    tbody.append(`
                            <tr data-id="${checkpoint.id}" data-branch-id="${checkpoint.branch_id}" data-client-id="${checkpoint.client_id}">
                                <td>${index + 1}</td>
                                <td>${checkpoint.name || '-'}</td>
                                <td>
                                    <span class="badge bg-info">${checkpoint.branch ? checkpoint.branch.name : '-'}</span>
                                </td>
                                <td>
                                    <span class="badge ${checkpoint.is_active ? 'bg-success' : 'bg-secondary'}">
                                        ${checkpoint.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </td>
                                <td>${checkpoint.created_at ? new Date(checkpoint.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-'}</td>
                                <td class="text-nowrap">
                                    <div class="d-flex gap-1">
                                        <a href="/clients/${checkpoint.client_id}/branches/${checkpoint.branch_id}/checkpoints/${checkpoint.id}/edit"
                                            class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                            <span class="d-none d-md-inline">Edit</span>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger delete-checkpoint d-flex align-items-center gap-1" data-id="${checkpoint.id}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                            <span class="d-none d-md-inline">Delete</span>
                                        </button>
                                        <a href="/clients/${checkpoint.client_id}/branches/${checkpoint.branch_id}/checkpoints/${checkpoint.id}/qrcode"
                                            class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                                            target="_blank" title="View QR Code">
                                            <i class="fas fa-qrcode"></i>
                                            <span class="d-none d-md-inline">QR Code</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `);
                                });
                            } else {
                                tbody.append(`
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                No checkpoints found for this client. Please select a branch to view checkpoints.
                            </td>
                        </tr>
                    `);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading client checkpoints:', {
                                status: status,
                                error: error,
                                response: xhr.responseText
                            });
                            showToast('error',
                                'Failed to load checkpoints. Please check the console for details.');
                        }
                    });
                }
            });
        </script>
    @endpush

@endsection
