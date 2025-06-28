@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Guard Details Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-shield me-2"></i>Guard Details
                    </h5>
                    <a href="{{ route('guards.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to Guards
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Name:</div>
                                <div class="col-sm-8">{{ $guard->name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Email:</div>
                                <div class="col-sm-8">{{ $guard->email }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Phone:</div>
                                <div class="col-sm-8">{{ $guard->phone ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Status:</div>
                                <div class="col-sm-8">
                                    <span class="badge bg-{{ $guard->status === 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($guard->status ?? 'inactive') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Address:</div>
                                <div class="col-sm-8">{{ $guard->address ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">City:</div>
                                <div class="col-sm-8">{{ $guard->city ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Country:</div>
                                <div class="col-sm-8">{{ $guard->country ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Language:</div>
                                <div class="col-sm-8">{{ $guard->language ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    @if($guard->notes)
                    <div class="row">
                        <div class="col-12">
                            <div class="row mb-3">
                                <div class="col-sm-2 fw-bold">Notes:</div>
                                <div class="col-sm-10">{{ $guard->notes }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Checkpoints Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Assigned Checkpoints
                    </h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#assignCheckpointModal">
                        <i class="fas fa-plus me-1"></i>Assign New Checkpoint
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Checkpoint Name</th>
                                    <th>Client</th>
                                    <th>Branch</th>
                                    <th>Date to Check</th>
                                    <th>Time to Check</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignedCheckpoints as $index => $assignment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $assignment->checkpoint->name }}</td>
                                    <td>{{ $assignment->checkpoint->client->name ?? 'N/A' }}</td>
                                    <td>{{ $assignment->checkpoint->branch->name ?? 'N/A' }}</td>
                                    <td>{{ $assignment->date_to_check ? \Carbon\Carbon::parse($assignment->date_to_check)->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $assignment->time_to_check ? \Carbon\Carbon::parse($assignment->time_to_check)->format('H:i') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->status === 'completed' ? 'success' : ($assignment->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($assignment->status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->priority === 'high' ? 'danger' : ($assignment->priority === 'medium' ? 'warning' : 'info') }}">
                                            {{ ucfirst($assignment->priority ?? 'normal') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-warning btn-sm edit-assignment-btn"
                                                    title="Edit Assignment"
                                                    data-assignment-id="{{ $assignment->id }}"
                                                    data-checkpoint-id="{{ $assignment->checkpoint_id }}"
                                                    data-client-id="{{ $assignment->checkpoint->client->id }}"
                                                    data-branch-id="{{ $assignment->checkpoint->branch->id }}"
                                                    data-priority="{{ $assignment->priority }}"
                                                    data-date="{{ $assignment->date_to_check }}"
                                                    data-time="{{ $assignment->time_to_check }}"
                                                    data-notes="{{ $assignment->notes }}"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('guards.removeAssignment', $assignment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this assignment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Remove Assignment">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                                            <p class="mb-0">No checkpoints assigned to this guard yet.</p>
                                            <small>Click "Assign New Checkpoint" to get started.</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Checkpoint Modal -->
<div class="modal fade" id="assignCheckpointModal" tabindex="-1" aria-labelledby="assignCheckpointModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="assignCheckpointForm" class="modal-content" method="POST" action="{{ route('guards.assignCheckpoint') }}">
            @csrf
            <input type="hidden" name="guard_id" value="{{ $guard->id }}">
            <input type="hidden" name="assignment_id" id="assignment_id">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="assignCheckpointModalLabel">
                    <i class="fas fa-map-marker-alt me-1"></i> Assign Checkpoint to {{ $guard->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="assign_client_id" class="form-label">Client</label>
                            <select class="form-select" id="assign_client_id" name="client_id" required>
                                <option value="">Select Client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="assign_branch_id" class="form-label">Branch</label>
                            <select class="form-select" id="assign_branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="assign_checkpoint_id" class="form-label">Checkpoint</label>
                            <select class="form-select" id="assign_checkpoint_id" name="checkpoint_id" required>
                                <option value="">Select Checkpoint</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="assign_priority" class="form-label">Priority</label>
                            <input type="number" class="form-control" id="assign_priority" name="priority" value="0" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="assign_date" class="form-label">Date to Check</label>
                            <input type="date" class="form-control" id="assign_date" name="date" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="assign_time" class="form-label">Time to Check</label>
                            <input type="time" class="form-control" id="assign_time" name="time" required>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="assign_notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="assign_notes" name="notes" rows="3" placeholder="Any additional notes for this assignment..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-info">Assign Checkpoint</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Show toast notification function
function showToast(type, message) {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        // Create toast container if it doesn't exist
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '1100';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${type === 'success' ?
                    '<i class="fas fa-check-circle me-2"></i>' :
                    '<i class="fas fa-exclamation-circle me-2"></i>'}
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    document.getElementById('toastContainer').appendChild(toast);

    // Initialize and show the toast
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 3000
    });

    bsToast.show();

    // Remove the toast after it's hidden
    toast.addEventListener('hidden.bs.toast', function () {
        toast.remove();
    });
}

$(document).ready(function() {
    // Set default date to today
    $('#assign_date').val(new Date().toISOString().split('T')[0]);

    // Set default time to current time
    const now = new Date();
    const timeString = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    $('#assign_time').val(timeString);

    // Edit assignment button functionality
    $(document).on('click', '.edit-assignment-btn', function() {
        const assignmentId = $(this).data('assignment-id');
        const checkpointId = $(this).data('checkpoint-id');
        const clientId = $(this).data('client-id');
        const branchId = $(this).data('branch-id');
        const priority = $(this).data('priority');
        const date = $(this).data('date');
        const time = $(this).data('time');
        const notes = $(this).data('notes');

        console.log('Edit assignment data:', {
            assignmentId,
            checkpointId,
            clientId,
            branchId,
            priority,
            date,
            time,
            notes
        });

        // Set client
        $('#assign_client_id').val(clientId);

        // Load branches for this client
        $.ajax({
            url: `/clients/${clientId}/branches`,
            method: 'GET',
            dataType: 'json',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(branchResponse) {
                let branchOptions = '<option value="">Select Branch</option>';
                let branches = [];

                if (Array.isArray(branchResponse)) {
                    branches = branchResponse;
                } else if (branchResponse && Array.isArray(branchResponse.data)) {
                    branches = branchResponse.data;
                } else if (branchResponse && branchResponse.branches) {
                    branches = branchResponse.branches;
                }

                branches.forEach(function(branch) {
                    const selected = branch.id == branchId ? 'selected' : '';
                    branchOptions += `<option value="${branch.id}" ${selected}>${branch.name}</option>`;
                });
                $('#assign_branch_id').html(branchOptions);

                // Load checkpoints for this branch
                $.ajax({
                    url: `/clients/${clientId}/branches/${branchId}/checkpoints`,
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(checkpointResponse) {
                        let checkpointOptions = '<option value="">Select Checkpoint</option>';
                        let checkpoints = [];

                        if (Array.isArray(checkpointResponse)) {
                            checkpoints = checkpointResponse;
                        } else if (checkpointResponse && Array.isArray(checkpointResponse.data)) {
                            checkpoints = checkpointResponse.data;
                        } else if (checkpointResponse && checkpointResponse.checkpoints) {
                            checkpoints = checkpointResponse.checkpoints;
                        }

                        checkpoints.forEach(function(checkpoint) {
                            const selected = checkpoint.id == checkpointId ? 'selected' : '';
                            checkpointOptions += `<option value="${checkpoint.id}" ${selected}>${checkpoint.name}</option>`;
                        });
                        $('#assign_checkpoint_id').html(checkpointOptions);

                        // Now populate the other fields
                        $('#assignment_id').val(assignmentId);
                        $('#assign_priority').val(priority);

                        // Format and set the date (ensure it's in YYYY-MM-DD format)
                        if (date) {
                            const formattedDate = new Date(date).toISOString().split('T')[0];
                            $('#assign_date').val(formattedDate);
                            console.log('Setting date:', date, '→', formattedDate);
                        } else {
                            console.log('No date provided');
                        }

                        // Format and set the time (ensure it's in HH:MM format)
                        if (time) {
                            // If time is in format like "14:30:00", extract just "14:30"
                            const timeParts = time.split(':');
                            const formattedTime = timeParts[0] + ':' + timeParts[1];
                            $('#assign_time').val(formattedTime);
                            console.log('Setting time:', time, '→', formattedTime);
                        } else {
                            console.log('No time provided');
                        }

                        $('#assign_notes').val(notes);

                        // Debug: Check if fields are actually set
                        setTimeout(() => {
                            console.log('Form field values after setting:');
                            console.log('Date field value:', $('#assign_date').val());
                            console.log('Time field value:', $('#assign_time').val());
                            console.log('Priority field value:', $('#assign_priority').val());
                        }, 100);

                        // Update modal title to indicate editing
                        $('#assignCheckpointModalLabel').html('<i class="fas fa-edit me-1"></i> Edit Assignment for {{ $guard->name }}');

                        // Update submit button text
                        $('#assignCheckpointForm button[type="submit"]').text('Update Assignment');

                        // Show the modal
                        $('#assignCheckpointModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading checkpoints:', xhr.responseText);
                        $('#assign_checkpoint_id').html('<option value="">Error loading checkpoints</option>');
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading branches:', xhr.responseText);
                $('#assign_branch_id').html('<option value="">Error loading branches</option>');
            }
        });
    });

    // When client is selected, load branches
    $('#assign_client_id').on('change', function() {
        const clientId = $(this).val();
        const branchSelect = $('#assign_branch_id');
        const checkpointSelect = $('#assign_checkpoint_id');
        branchSelect.html('<option value="">Loading branches...</option>');
        checkpointSelect.html('<option value="">Select Checkpoint</option>');
        if (!clientId) return;

        $.ajax({
            url: `/clients/${clientId}/branches`,
            method: 'GET',
            dataType: 'json',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Branches response:', response);
                let options = '<option value="">Select Branch</option>';

                let branches = [];
                if (Array.isArray(response)) {
                    branches = response;
                } else if (response && Array.isArray(response.data)) {
                    branches = response.data;
                } else if (response && response.branches) {
                    branches = response.branches;
                }

                if (branches.length > 0) {
                    branches.forEach(function(branch) {
                        options += `<option value="${branch.id}">${branch.name}</option>`;
                    });
                } else {
                    options = '<option value="">No branches found</option>';
                }
                branchSelect.html(options);
            },
            error: function(xhr, status, error) {
                console.error('Error loading branches:', xhr.responseText);
                branchSelect.html('<option value="">Error loading branches</option>');
            }
        });
    });

    // When branch is selected, load checkpoints
    $('#assign_branch_id').on('change', function() {
        const branchId = $(this).val();
        const clientId = $('#assign_client_id').val();
        const checkpointSelect = $('#assign_checkpoint_id');
        checkpointSelect.html('<option value="">Loading checkpoints...</option>');
        if (!clientId || !branchId) return;

        console.log('Loading checkpoints for client:', clientId, 'branch:', branchId);

        $.ajax({
            url: `/clients/${clientId}/branches/${branchId}/checkpoints`,
            method: 'GET',
            dataType: 'json',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Checkpoints response:', response);
                let options = '<option value="">Select Checkpoint</option>';

                let checkpoints = [];
                if (Array.isArray(response)) {
                    checkpoints = response;
                } else if (response && Array.isArray(response.data)) {
                    checkpoints = response.data;
                } else if (response && response.checkpoints) {
                    checkpoints = response.checkpoints;
                }

                if (checkpoints.length > 0) {
                    checkpoints.forEach(function(checkpoint) {
                        options += `<option value="${checkpoint.id}">${checkpoint.name}</option>`;
                    });
                } else {
                    options = '<option value="">No checkpoints found</option>';
                }
                checkpointSelect.html(options);
            },
            error: function(xhr, status, error) {
                console.error('Error loading checkpoints:', xhr.responseText);
                checkpointSelect.html('<option value="">Error loading checkpoints</option>');
            }
        });
    });

    // Handle form submission for both new assignments and updates
    $('#assignCheckpointForm').on('submit', function(e) {
        e.preventDefault();

        const assignmentId = $('#assignment_id').val();
        const isEdit = assignmentId && assignmentId !== '';

        // Check form validity
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();

        // Show loading state
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

        // Get form data
        const formData = new FormData(this);

        // Always use POST method - the controller handles create/update logic internally
        // No need to add _method: 'PUT' since the route only supports POST

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                showToast('success', response.message || (isEdit ? 'Assignment updated successfully!' : 'Checkpoint assigned successfully!'));

                // Close modal after success
                setTimeout(() => {
                    $('#assignCheckpointModal').modal('hide');
                    // Reload the page to show updated data
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while processing the request.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Handle validation errors
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = Object.values(errors).flat();
                    errorMessage = errorMessages.join('<br>');
                }

                showToast('danger', errorMessage);
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
@endpush
