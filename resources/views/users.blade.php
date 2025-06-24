@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Toast container for dynamic notifications -->
<div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11"></div>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0">User Management</h4>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-user-plus me-1"></i> Add New User
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary"
                                    onclick="editUser(this)"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    data-phone="{{ $user->phone }}"
                                    data-cnic="{{ $user->cnic }}"
                                    data-nfc-uid="{{ $user->nfc_uid }}"
                                    data-designation="{{ $user->designation }}"
                                    data-role="{{ $user->role }}"
                                    data-status="{{ $user->status }}"
                                    data-client-id="{{ $user->client_id }}"
                                    data-branch-id="{{ $user->branch_id }}"
                                    data-latitude="{{ $user->latitude }}"
                                    data-longitude="{{ $user->longitude }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>

                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return deleteUser(event, this)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($users->hasPages())
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end">
                                {{ $users->links() }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 👇 Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addUserModalLabel"><i class="fas fa-user-plus me-1"></i> Add New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('users.store') }}" id="userForm" enctype="multipart/form-data">
                @csrf
                <div id="formErrors" class="alert alert-danger d-none mb-3">
                    <button type="button" class="btn-close float-end" onclick="this.parentElement.classList.add('d-none')"></button>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                    </div>
                    <ul class="mb-0 mt-2 ps-4"></ul>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Waqar Abbas" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="e.g. user@email.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="e.g. 0300xxxxxxx">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CNIC</label>
                        <input type="text" name="cnic" class="form-control" placeholder="e.g. 12345-6789012-3">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NFC Tag UID</label>
                        <input type="text" name="nfc_uid" class="form-control" placeholder="e.g. 04A3CFC1B2">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designation</label>
                        <input type="text" name="designation" class="form-control" placeholder="e.g. Guard, Supervisor">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="guard">Guard</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Company and Branch Selection -->
                    <div class="col-md-6">
                        <label class="form-label">Assigned Company</label>
                        <select name="client_id" id="client_id" class="form-select" onchange="loadBranches(this.value)">
                            <option value="">-- Select Company --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" id="branch_id" class="form-select" onchange="updateCoordinates(this)" disabled>
                            <option value="">-- Select Branch --</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" id="latitude" class="form-control" placeholder="e.g. 24.8607" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" id="longitude" class="form-control" placeholder="e.g. 67.0011" readonly>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" id="editUserForm" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="editFormErrors" class="alert alert-danger d-none mb-3">
              <button type="button" class="btn-close float-end" onclick="this.parentElement.classList.add('d-none')"></button>
              <div class="d-flex align-items-center">
                  <i class="fas fa-exclamation-triangle me-2"></i>
                  <strong>Please fix the following errors:</strong>
              </div>
              <ul class="mb-0 mt-2 ps-4"></ul>
          </div>
          
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="editName" class="form-control" required>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" id="editEmail" class="form-control" required>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" id="editPhone" class="form-control">
            </div>
            
            <div class="col-md-6">
              <label class="form-label">CNIC</label>
              <input type="text" name="cnic" id="editCnic" class="form-control">
            </div>
            
            <div class="col-md-6">
              <label class="form-label">NFC Tag UID</label>
              <input type="text" name="nfc_uid" id="editNfcUid" class="form-control">
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Designation</label>
              <input type="text" name="designation" id="editDesignation" class="form-control">
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Role <span class="text-danger">*</span></label>
              <select name="role" id="editRole" class="form-select" required>
                <option value="guard">Guard</option>
                <option value="supervisor">Supervisor</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Status <span class="text-danger">*</span></label>
              <select name="status" id="editStatus" class="form-select" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Password (leave blank to keep current)</label>
              <input type="password" name="password" id="editPassword" class="form-control" placeholder="Enter new password">
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Assigned Company</label>
              <select name="client_id" id="editClientId" class="form-select" onchange="loadBranchesForEdit(this.value, document.getElementById('editLatitude').value, document.getElementById('editLongitude').value)">
                <option value="">-- Select Company --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Branch</label>
              <select name="branch_id" id="editBranchId" class="form-select" onchange="updateEditCoordinates(this)" disabled>
                <option value="">-- Select Branch --</option>
              </select>
            </div>
            
            <div class="col-md-3">
              <label class="form-label">Latitude</label>
              <input type="text" name="latitude" id="editLatitude" class="form-control" readonly>
            </div>
            
            <div class="col-md-3">
              <label class="form-label">Longitude</label>
              <input type="text" name="longitude" id="editLongitude" class="form-control" readonly>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
        </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
// Function to handle user deletion with AJAX
function deleteUser(event, form) {
    event.preventDefault();
    
    if (!confirm('Are you sure you want to delete this user?')) {
        return false;
    }
    
    const deleteUrl = form.action;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(deleteUrl, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showToast('success', data.message || 'User deleted successfully');
            
            // If we have a redirect URL, use it after a short delay
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                // Otherwise, reload the page
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } else {
            throw new Error(data.message || 'Failed to delete user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('danger', error.message || 'An error occurred while deleting the user');
    });
    
    return false;
}

// Function to show toast notifications
function showToast(type, message) {
    const toastContainer = document.getElementById('toastContainer');
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0 show`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    // Add to container
    if (toastContainer) {
        toastContainer.appendChild(toast);
    }
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

// Make sure the script runs after the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    
    // Initialize the edit form submission
    const editForm = document.getElementById('editUserForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submission started');
            
            const formData = new FormData(editForm);
            const submitButton = editForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
            
            // Add CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Submit the form via AJAX
            fetch(editForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Form submission response:', data);
                if (data.success) {
                    // Show success toast notification
                    const toast = document.createElement('div');
                    toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 m-3';
                    toast.setAttribute('role', 'alert');
                    toast.setAttribute('aria-live', 'assertive');
                    toast.setAttribute('aria-atomic', 'true');
                    toast.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-check-circle me-2"></i>
                                User updated successfully
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>`;
                    
                    document.body.appendChild(toast);
                    const bsToast = new bootstrap.Toast(toast);
                    bsToast.show();

                    // Reload the page after toast is shown
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    // Show validation errors
                    const errorDiv = document.getElementById('editFormErrors');
                    if (errorDiv) {
                        const errorList = errorDiv.querySelector('ul');
                        errorList.innerHTML = '';
                        
                        if (data.errors) {
                            Object.values(data.errors).forEach(error => {
                                const li = document.createElement('li');
                                li.textContent = error[0];
                                errorList.appendChild(li);
                            });
                            errorDiv.classList.remove('d-none');
                        } else {
                            errorDiv.classList.add('d-none');
                        }
                    }
                    throw new Error(data.message || 'Failed to update user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (!error.message.includes('Failed to update user')) {
                    // Show error toast
                    const toast = document.createElement('div');
                    toast.className = 'toast align-items-center text-white bg-danger border-0 position-fixed bottom-0 end-0 m-3';
                    toast.setAttribute('role', 'alert');
                    toast.setAttribute('aria-live', 'assertive');
                    toast.setAttribute('aria-atomic', 'true');
                    toast.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                ${error.message || 'An error occurred. Please try again.'}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>`;
                    
                    document.body.appendChild(toast);
                    const bsToast = new bootstrap.Toast(toast);
                    bsToast.show();
                }
            })
            .finally(() => {
                // Reset button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });
    }
});
</script>

<!-- Load the main users.js file -->
<script src="{{ asset('js/users.js') }}"></script>
@endpush

@endsection
