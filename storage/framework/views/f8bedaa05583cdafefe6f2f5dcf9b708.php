<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h4 class="fw-semibold mb-0">Checkpoints List</h4>

            <div class="d-flex justify-content-end align-items-center mb-4 gap-2 flex-wrap">
                <form method="GET" class="d-flex align-items-center gap-2 flex-wrap mb-0" style="max-width: 700px;">
                    <div class="input-group">
                        <select name="client_id" id="clientFilter" class="form-select" style="min-width: 180px; border-radius: 8px 0 0 8px;">
                            <option value="">Select Client</option>
                            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($client->id); ?>" <?php echo e(request('client_id', $selectedClient) == $client->id ? 'selected' : ''); ?>><?php echo e($client->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <span class="input-group-text bg-white border-primary" style="border-radius: 0 8px 8px 0;"><i class="fas fa-building text-primary"></i></span>
                    </div>
                    <div class="input-group">
                        <select name="branch_id" id="branchFilter" class="form-select" style="min-width: 160px; border-radius: 8px 0 0 8px;" <?php echo e(!request('client_id', $selectedClient) ? 'disabled' : ''); ?>>
                            <option value="">Select Branch</option>
                            <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch_id', $selectedBranch) == $branch->id ? 'selected' : ''); ?>><?php echo e($branch->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <span class="input-group-text bg-white border-info" style="border-radius: 0 8px 8px 0;"><i class="fas fa-code-branch text-info"></i></span>
                    </div>
                    <button type="submit" class="btn btn-primary" style="height: 38px; border-radius: 8px; min-width: 90px;">Filter</button>
                </form>
                <a href="#" class="btn btn-primary d-flex align-items-center gap-2" id="addCheckpointBtn" style="height: 38px; border-radius: 8px;">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Checkpoint</span>
                </a>
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
                                <th>Client</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                            </tr>
                        </thead>
                        <tbody id="checkpointsTableBody">
                            <?php if(isset($checkpoints) && $checkpoints->count() > 0): ?>
                                <?php $__currentLoopData = $checkpoints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $checkpoint): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr data-id="<?php echo e($checkpoint->id); ?>" data-branch-id="<?php echo e($checkpoint->branch_id); ?>"
                                        data-client-id="<?php echo e($checkpoint->client_id); ?>">
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><?php echo e($checkpoint->name); ?></td>
                                        <td><?php echo e($checkpoint->branch && $checkpoint->branch->client ? $checkpoint->branch->client->name : '-'); ?>

                                        </td>
                                        <td><?php echo e($checkpoint->branch ? $checkpoint->branch->name : '-'); ?></td>
                                        <td>
                                            <span
                                                class="badge <?php echo e($checkpoint->is_active ? 'bg-success' : 'bg-secondary'); ?>">
                                                <?php echo e($checkpoint->is_active ? 'Active' : 'Inactive'); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($checkpoint->created_at->format('M d, Y')); ?></td>
                                        <td class="text-nowrap">
                                            <div class="d-flex gap-1">
                                                <a href="<?php echo e(route('clients.branches.checkpoints.edit', [
                                                    'client' => $checkpoint->client_id,
                                                    'branch' => $checkpoint->branch_id,
                                                    'checkpoint' => $checkpoint->id,
                                                ])); ?>"
                                                    class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                    <span class="d-none d-md-inline">Edit</span>
                                                </a>
                                                <form action="<?php echo e(route('clients.branches.checkpoints.destroy', [
                                                    'client' => $checkpoint->client_id,
                                                    'branch' => $checkpoint->branch_id,
                                                    'checkpoint' => $checkpoint->id,
                                                ])); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this checkpoint?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                        <span class="d-none d-md-inline">Delete</span>
                                                    </button>
                                                </form>
                                                <a href="<?php echo e(route('clients.branches.checkpoints.qrcode', [
                                                    'client' => $checkpoint->client_id,
                                                    'branch' => $checkpoint->branch_id,
                                                    'checkpoint' => $checkpoint->id,
                                                ])); ?>"
                                                    class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                                                    target="_blank" title="View QR Code">
                                                    <i class="fas fa-qrcode"></i>
                                                    <span class="d-none d-md-inline">QR Code</span>
                                                </a>
                                                <button
                                                    class="btn btn-sm btn-outline-info show-on-map d-flex align-items-center gap-1"
                                                    data-lat="<?php echo e($checkpoint->latitude); ?>"
                                                    data-lng="<?php echo e($checkpoint->longitude); ?>"
                                                    data-name="<?php echo e($checkpoint->name); ?>"
                                                    data-radius="<?php echo e($checkpoint->radius); ?>" title="Show on Map">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <span class="d-none d-md-inline">Map</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        No checkpoints found. Use the filters above to view specific checkpoints or add new
                                        ones.
                                    </td>
                                </tr>
                            <?php endif; ?>
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
                <?php echo csrf_field(); ?>
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
                                <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($client->id); ?>"
                                        <?php echo e($selectedClient == $client->id ? 'selected' : ''); ?>>
                                        <?php echo e($client->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Branch Selection -->
                        <div class="col-md-6">
                            <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                <?php if(isset($branches) && $branches->count() > 0): ?>
                                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($branch->id); ?>" data-lat="<?php echo e($branch->latitude); ?>"
                                            data-lng="<?php echo e($branch->longitude); ?>"
                                            <?php echo e($selectedBranch == $branch->id ? 'selected' : ''); ?>>
                                            <?php echo e($branch->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
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

                        <div class="col-12 mb-3">
                            <label class="form-label">Select Location on Map</label>
                            <div id="selectLocationMap"
                                style="height: 250px; width: 100%; border: 1px solid #ccc; border-radius: 8px;"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="radius" class="form-label">Geofence Radius (meters) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="radius" name="radius" required>
                                <button class="btn btn-outline-secondary" type="button" id="setDefaultRadius">Default
                                    (50m)</button>
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

    <!-- Map Modal -->
    <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="mapModalLabel">
                        <i class="fas fa-map-marker-alt me-1"></i> Checkpoint Location
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="checkpointMap" style="height: 400px; width: 100%;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            jQuery(document).ready(function($) {
                let map = null;
                let mapModal = null;

                // Toast notification function
                function showToast(type, message) {
                    // Create toast container if it doesn't exist
                    let toastContainer = document.getElementById('toastContainer');
                    if (!toastContainer) {
                        toastContainer = document.createElement('div');
                        toastContainer.id = 'toastContainer';
                        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                        toastContainer.style.zIndex = '9999';
                        document.body.appendChild(toastContainer);
                    }

                    // Create toast element
                    const toastId = 'toast-' + Date.now();
                    const toastHtml = `
                        <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body">
                                    ${message}
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    `;

                    toastContainer.insertAdjacentHTML('beforeend', toastHtml);

                    // Show toast
                    const toastElement = document.getElementById(toastId);
                    const toast = new bootstrap.Toast(toastElement, {
                        autohide: true,
                        delay: 5000
                    });
                    toast.show();

                    // Remove toast element after it's hidden
                    toastElement.addEventListener('hidden.bs.toast', function() {
                        toastElement.remove();
                    });
                }

                // Initialize map modal
                const mapModalElement = document.getElementById('mapModal');
                if (mapModalElement) {
                    mapModal = new bootstrap.Modal(mapModalElement);
                }

                // Function to load branches by client
                function loadBranchesByClient(clientId, targetSelect, includeEmptyOption = true) {
                    if (!clientId) {
                        // Clear branch options if no client selected
                        targetSelect.html('<option value="">Select Branch</option>');
                        targetSelect.prop('disabled', true);
                        return;
                    }

                    // Enable the branch select
                    targetSelect.prop('disabled', false);

                    // Show loading state
                    targetSelect.html('<option value="">Loading branches...</option>');

                    $.ajax({
                        url: `/clients/${clientId}/branches`,
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                let options = '';
                                if (includeEmptyOption) {
                                    options += '<option value="">Select Branch</option>';
                                }

                                response.data.forEach(function(branch) {
                                    options += `<option value="${branch.id}" data-lat="${branch.latitude}" data-lng="${branch.longitude}">${branch.name}</option>`;
                                });

                                targetSelect.html(options);
                            } else {
                                targetSelect.html('<option value="">Error loading branches</option>');
                            }
                        },
                        error: function() {
                            targetSelect.html('<option value="">Error loading branches</option>');
                        }
                    });
                }

                // Handle client selection change in filter form
                $('#clientFilter').on('change', function() {
                    const clientId = $(this).val();
                    loadBranchesByClient(clientId, $('#branchFilter'), true);
                });

                // Handle client selection change in add/edit modal
                $('#client_id').on('change', function() {
                    const clientId = $(this).val();
                    console.log('Client changed to:', clientId);

                    loadBranchesByClient(clientId, $('#branch_id'), true);

                    // Clear other form fields when client changes
                    $('#name').val('');
                    $('#description').val('');
                    $('#latitude').val('');
                    $('#longitude').val('');
                    $('#radius').val('50'); // Set default radius
                });

                // Handle branch selection change in add/edit modal to auto-fill coordinates
                $('#branch_id').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const lat = selectedOption.data('lat');
                    const lng = selectedOption.data('lng');

                    if (lat && lng) {
                        $('#latitude').val(lat);
                        $('#longitude').val(lng);
                    }
                });

                // Handle show on map button click
                $(document).on('click', '.show-on-map', function() {
                    const lat = parseFloat($(this).data('lat'));
                    const lng = parseFloat($(this).data('lng'));
                    const name = $(this).data('name');
                    const radius = parseInt($(this).data('radius'));

                    if (!lat || !lng) {
                        showToast('error', 'No coordinates available for this checkpoint');
                        return;
                    }

                    // Show modal
                    mapModal.show();

                    // Initialize map after modal is shown
                    mapModalElement.addEventListener('shown.bs.modal', function() {
                        if (map) {
                            map.remove();
                        }

                        // Create map
                        map = L.map('checkpointMap').setView([lat, lng], 16);

                        // Add tile layer
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(map);

                        // Add marker for checkpoint
                        const marker = L.marker([lat, lng]).addTo(map);
                        marker.bindPopup(`<b>${name}</b><br>Checkpoint Location`);

                        // Add circle for geofence radius
                        if (radius) {
                            const circle = L.circle([lat, lng], {
                                color: 'red',
                                fillColor: '#f03',
                                fillOpacity: 0.2,
                                radius: radius
                            }).addTo(map);
                            circle.bindPopup(`Geofence Radius: ${radius}m`);
                        }
                    }, {
                        once: true
                    });
                });

                // Clean up map when modal is hidden
                mapModalElement.addEventListener('hidden.bs.modal', function() {
                    if (map) {
                        map.remove();
                        map = null;
                    }
                });

                // Initialize Add Checkpoint Modal
                const addCheckpointModal = new bootstrap.Modal(document.getElementById('addCheckpointModal'));

                // Handle Add Checkpoint button click
                $('#addCheckpointBtn').on('click', function(e) {
                    e.preventDefault();

                    // Reset form
                    $('#checkpointForm')[0].reset();
                    $('#formErrors').addClass('d-none');
                    $('#modalTitle').text('Add New Checkpoint');
                    $('#formMethod').val('POST');
                    $('#checkpointId').val('');

                    // Reset branch dropdown
                    $('#branch_id').html('<option value="">Select Branch</option>').prop('disabled', true);

                    // Clear coordinate fields
                    $('#latitude').val('');
                    $('#longitude').val('');

                    // Set default radius
                    $('#radius').val('50');

                    // Show modal
                    addCheckpointModal.show();
                });

                // Handle form submission
                $('#checkpointForm').on('submit', function(e) {
                    e.preventDefault();

                    console.log('Form submission started');

                    const formData = new FormData(this);
                    const submitBtn = $('#saveCheckpointBtn');
                    const spinner = submitBtn.find('.spinner-border');

                    // Get selected client and branch IDs
                    const clientId = $('#client_id').val();
                    const branchId = $('#branch_id').val();
                    const name = $('#name').val().trim();
                    const radius = $('#radius').val();

                    console.log('Form data:', {
                        clientId: clientId,
                        branchId: branchId,
                        name: name,
                        radius: radius
                    });

                    // Enhanced validation
                    if (!clientId) {
                        showToast('error', 'Please select a client');
                        $('#client_id').focus();
                        return;
                    }

                    if (!branchId) {
                        showToast('error', 'Please select a branch');
                        $('#branch_id').focus();
                        return;
                    }

                    if (!name) {
                        showToast('error', 'Please enter a checkpoint name');
                        $('#name').focus();
                        return;
                    }

                    if (!radius || radius <= 0) {
                        showToast('error', 'Please enter a valid radius (greater than 0)');
                        $('#radius').focus();
                        return;
                    }

                    // Show loading state
                    submitBtn.prop('disabled', true);
                    spinner.removeClass('d-none');

                    // Clear previous errors
                    $('#formErrors').addClass('d-none');

                    console.log('Making AJAX request to:', `/clients/${clientId}/branches/${branchId}/checkpoints`);

                    $.ajax({
                        url: `/clients/${clientId}/branches/${branchId}/checkpoints`,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            console.log('Success response:', response);

                            if (response.success) {
                                // Show success message
                                showToast('success', response.message || 'Checkpoint saved successfully');

                                // Close modal
                                addCheckpointModal.hide();

                                // Reload page to show new checkpoint
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                showToast('error', response.message || 'Failed to save checkpoint');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', {
                                status: status,
                                error: error,
                                responseText: xhr.responseText,
                                statusText: xhr.statusText
                            });

                            let errorMessage = 'An error occurred while saving the checkpoint';

                            // Try to parse JSON response
                            try {
                                if (xhr.responseJSON) {
                                    if (xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }

                                    if (xhr.responseJSON.errors) {
                                        // Display validation errors
                                        const errors = xhr.responseJSON.errors;
                                        let errorHtml = '<ul class="mb-0">';
                                        Object.keys(errors).forEach(function(key) {
                                            errors[key].forEach(function(error) {
                                                errorHtml += `<li><strong>${key}:</strong> ${error}</li>`;
                                            });
                                        });
                                        errorHtml += '</ul>';

                                        $('#formErrors').html(errorHtml).removeClass('d-none');
                                        return; // Don't show toast if we're showing detailed errors
                                    }
                                }
                            } catch (e) {
                                console.error('Error parsing response:', e);
                            }

                            // Show generic error message
                            showToast('error', errorMessage);
                        },
                        complete: function() {
                            // Hide loading state
                            submitBtn.prop('disabled', false);
                            spinner.addClass('d-none');
                        }
                    });
                });

                // Handle default radius button
                $('#setDefaultRadius').on('click', function() {
                    $('#radius').val('50');
                });

                // Initialize map for location selection in modal
                let selectLocationMap = null;
                let selectLocationMarker = null;

                // Initialize map when modal is shown
                document.getElementById('addCheckpointModal').addEventListener('shown.bs.modal', function() {
                    if (selectLocationMap) {
                        selectLocationMap.remove();
                    }

                    // Create map for location selection
                    selectLocationMap = L.map('selectLocationMap').setView([0, 0], 2);

                    // Add tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(selectLocationMap);

                    // Handle map click to set coordinates
                    selectLocationMap.on('click', function(e) {
                        const lat = e.latlng.lat;
                        const lng = e.latlng.lng;

                        $('#latitude').val(lat.toFixed(6));
                        $('#longitude').val(lng.toFixed(6));

                        // Update marker
                        if (selectLocationMarker) {
                            selectLocationMap.removeLayer(selectLocationMarker);
                        }
                        selectLocationMarker = L.marker([lat, lng]).addTo(selectLocationMap);
                        selectLocationMarker.bindPopup('Selected Location').openPopup();
                    });
                });

                // Clean up map when modal is hidden
                document.getElementById('addCheckpointModal').addEventListener('hidden.bs.modal', function() {
                    if (selectLocationMap) {
                        selectLocationMap.remove();
                        selectLocationMap = null;
                        selectLocationMarker = null;
                    }
                });
            });
        </script>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH K:\Laravel\security-master\resources\views\checkpoints\index.blade.php ENDPATH**/ ?>