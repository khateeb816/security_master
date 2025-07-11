@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0">Client Companies</h4>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addClientModal">
            <i class="fas fa-building me-1"></i> Add New Company
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $index => $client)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->phone ?? 'N/A' }}</td>
                            <td>{{ $client->city ?? 'N/A' }}</td>
                            <td>{{ $client->country ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex gap-1" role="group">
                                    <button class="btn btn-warning edit-client-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editClientModal"
                                            data-id="{{ $client->id }}"
                                            data-name="{{ $client->name }}"
                                            data-email="{{ $client->email }}"
                                            data-phone="{{ $client->phone }}"
                                            data-address="{{ $client->address }}"
                                            data-city="{{ $client->city }}"
                                            data-state="{{ $client->state }}"
                                            data-postal-code="{{ $client->zip }}"
                                            data-country="{{ $client->country }}"
                                            data-status="{{ $client->status }}"
                                            data-language="{{ $client->language }}"
                                            data-nfc-uid="{{ $client->nfc_uid}}"
                                            data-cnic="{{ $client->cnic }}"
                                            data-notes="{{ $client->notes }}"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-info ms-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#addBranchModal"
                                            data-company="{{ $client->name }}"
                                            data-company-name="{{ $client->name }}"
                                            data-company-id="{{ $client->id }}"
                                            onclick="setBranchCompany(this)"
                                            title="Manage Branches">
                                        <i class="fas fa-code-branch"></i>
                                    </button>
                                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this client?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" id="id" value="{{ $client->id }}">
                                        <button type="submit" class="btn btn-danger ms-1" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                </button>
                            </td>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No clients found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- 🟦 Add Branch Modal -->
<div class="modal fade" id="addBranchModal" tabindex="-1" aria-labelledby="addBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="addBranchModalLabel"><i class="fas fa-code-branch me-1"></i> <span id="branch_company_name">Branches</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Add/Edit Branch</h6>
                    </div>
                    <div class="card-body">
                        <form id="addBranchForm" method="POST" action="" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" id="branch_company_id" name="user_id">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required>
                                    <div class="invalid-feedback">Please provide a branch name</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" required>
                                    <div class="invalid-feedback">Please provide a valid email</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" required>
                                    <div class="invalid-feedback">Please provide a phone number</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" name="address" class="form-control" required>
                                    <div class="invalid-feedback">Please provide an address</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control" required>
                                    <div class="invalid-feedback">Please provide a city</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">State/Province <span class="text-danger">*</span></label>
                                    <input type="text" name="state" class="form-control" required>
                                    <div class="invalid-feedback">Please provide a state/province</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ZIP/Postal Code <span class="text-danger">*</span></label>
                                    <input type="text" name="zip" class="form-control" required>
                                    <div class="invalid-feedback">Please provide a ZIP/postal code</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Country <span class="text-danger">*</span></label>
                                    <input type="text" name="country" class="form-control" required>
                                    <div class="invalid-feedback">Please provide a country</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>

                                <!-- Map Section -->
                                <div class="col-12">
                                    <label class="form-label">Location <span class="text-danger">*</span></label>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Click on the map below to set the branch location coordinates, or use the "Find Location" button to get coordinates from your address
                                    </div>
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="geocodeAddress()">
                                            <i class="fas fa-search-location me-1"></i>
                                            Find Location from Address
                                        </button>
                                    </div>
                                    <div id="branchMap" style="height: 400px; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Latitude <span class="text-danger">*</span></label>
                                    <input type="number" name="latitude" id="branch_latitude" step="0.000001" class="form-control" required>
                                    <div class="invalid-feedback">Please provide a valid latitude (-90 to 90)</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Longitude <span class="text-danger">*</span></label>
                                    <input type="number" name="longitude" id="branch_longitude" step="0.000001" class="form-control" required>
                                    <div class="invalid-feedback">Please provide a valid longitude (-180 to 180)</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-12 text-end mt-3">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Branch</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Existing Branches</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Branch Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>City</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="branchesTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">No branches found. Add a new branch using the form above.</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- 🟩 Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="addClientForm" class="modal-content" method="POST" action="{{ route('clients.store') }}">
            @csrf
            @method('POST')
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="addClientModalLabel"><i class="fas fa-plus me-1"></i> Add New Client</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div id="form-errors" class="alert alert-danger d-none"></div>
            <div class="modal-body row g-3">
                <div id="add-form-errors" class="alert alert-danger d-none">
                    <ul class="mb-0"></ul>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                    <div class="invalid-feedback">Please provide a company name</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                    <div class="invalid-feedback">Please provide a valid email address</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="">Select Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Postal Code</label>
                    <input type="text" name="postal_code" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <select name="country" class="form-select">
                        <option value="">Select Country</option>
                        <option value="Pakistan" selected>Pakistan</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="United States">United States</option>
                        <option value="UAE">UAE</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Language</label>
                    <select name="language" class="form-select">
                        <option>English</option>
                        <option>Urdu</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">CNIC</label>
                    <input type="text" name="cnic" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">NFC UID (Optional)</label>
                    <input type="text" name="nfc_uid" class="form-control">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">Add Client</button>
            </div>
        </form>
    </div>
</div>

<!-- 🟦 Edit Client Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editClientForm" class="modal-content" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editClientModalLabel"><i class="fas fa-edit me-1"></i> Edit Client</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div id="edit-form-errors" class="alert alert-danger d-none">
                <ul class="mb-0"></ul>
            </div>
            <div class="modal-body row g-3">
                <input type="hidden" id="edit_id" name="id">
                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                    <div class="invalid-feedback">Please provide a company name</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" id="edit_email" name="email" class="form-control" required>
                    <div class="invalid-feedback">Please provide a valid email address</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" id="edit_phone" name="phone" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select id="edit_status" name="status" class="form-select" required>
                        <option value="">Select Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Address</label>
                    <input type="text" id="edit_address" name="address" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" id="edit_city" name="city" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" id="edit_state" name="state" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Postal Code</label>
                    <input type="text" id="edit_postal_code" name="postal_code" class="form-control">
                </div>


                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <select id="edit_country" name="country" class="form-select">
                        <option value="Pakistan">Pakistan</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="United States">United States</option>
                        <option value="UAE">UAE</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Language</label>
                    <select id="edit_language" name="language" class="form-select">
                        <option value="English">English</option>
                        <option value="Urdu">Urdu</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">CNIC</label>
                    <input type="text" id="edit_cnic" name="cnic" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">NFC UID (Optional)</label>
                    <input type="text" id="edit_nfc_uid" name="nfc_uid" class="form-control">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Notes</label>
                    <textarea id="edit_notes" name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Helper function to escape HTML to prevent XSS
function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return unsafe
        .toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
function fillEditForm(button) {
    console.log('fillEditForm called with button:', button);

    // Get the client ID and form
    const clientId = button.getAttribute('data-id');
    const form = document.getElementById('editClientForm');
    form.action = `/clients/${clientId}`;

    // Set form fields based on data attributes
    const fields = [
        'id', 'name', 'email', 'phone', 'address',
        'city', 'state', 'postal_code', 'country', 'status', 'language',
        'nfc_uid', 'cnic'
    ];

    console.log('Setting fields:', fields);

    // Handle regular fields and select elements
    fields.forEach(field => {
        // Special mapping for postal_code (which comes from data-postal-code, value = $client->zip)
        let dataAttr = `data-${field.replace(/_/g, '-')}`;
        let value = button.getAttribute(dataAttr);

        // Handle null/undefined values
        if (value === 'null' || value === 'undefined') value = '';

        const element = document.getElementById(`edit_${field}`);

        console.log(`Field: ${field}, Data Attribute: ${dataAttr}, Value: ${value}, Element:`, element);

        if (element) {
            if (element.tagName === 'SELECT') {
                const option = Array.from(element.options).find(
                    opt => opt.value === value
                );
                if (option) {
                    option.selected = true;
                } else if (element.multiple) {
                    const values = value ? value.split(',') : [];
                    Array.from(element.options).forEach(opt => {
                        opt.selected = values.includes(opt.value);
                    });
                }
            } else if (element.type === 'checkbox') {
                element.checked = value === '1' || value === 'true' || value === true;
            } else {
                element.value = value || '';
            }
        }
    });
}
function setBranchCompany(button) {
    try {
        const companyId = button.getAttribute('data-company-id');
        const companyName = button.getAttribute('data-company-name') || 'Branches';
        const form = document.getElementById('addBranchForm');

        if (!form) {
            console.error('Branch form not found');
            return;
        }

        // Reset form and clear any validation states
        form.reset();
        form.classList.remove('was-validated');
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.is-valid').forEach(el => el.classList.remove('is-valid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
        form.querySelectorAll('.valid-feedback').forEach(el => el.style.display = 'none');

        // Set form action for adding a new branch
        form.action = `/clients/${companyId}/branches`;
        form.setAttribute('method', 'POST');

        // Remove any existing _method field
        const existingMethod = form.querySelector('input[name="_method"]');
        if (existingMethod) {
            existingMethod.remove();
        }

        // Set company ID in the form
        const branchCompanyId = document.getElementById('branch_company_id');
        if (branchCompanyId) {
            branchCompanyId.value = companyId;
        } else {
            console.error('branch_company_id element not found');
        }

        const branchCompanyName = document.getElementById('branch_company_name');
        if (branchCompanyName) {
            branchCompanyName.textContent = companyName;
        } else {
            console.error('branch_company_name element not found');
        }

        // Update modal title
        const modalTitle = document.getElementById('addBranchModalLabel');
        if (modalTitle) {
            modalTitle.innerHTML = `<i class="fas fa-code-branch me-1"></i> ${companyName} - Branches`;
        }

        // Initialize modal if not already done
        if (!branchModal) {
            const modalElement = document.getElementById('addBranchModal');
            if (modalElement) {
                branchModal = new bootstrap.Modal(modalElement);
            }
        }

        // Show the modal
        if (branchModal) {
            branchModal.show();
        }

        // Load branches for this company
        loadBranches(companyId);

    } catch (error) {
        console.error('Error in setBranchCompany:', error);
        showToast('danger', 'Failed to initialize branch management. Please try again.');
    }
}

// Handle form submissions with AJAX
// Handle branch form submission separately
const branchForm = document.getElementById('addBranchForm');
if (branchForm) {
    branchForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Check form validity
        if (!this.checkValidity()) {
            this.classList.add('was-validated');
            return;
        }

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;

        // Reset form validation states
        this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        this.querySelectorAll('.is-valid').forEach(el => el.classList.remove('is-valid'));
        this.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
        this.querySelectorAll('.valid-feedback').forEach(el => el.style.display = 'none');

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        try {
            const formData = new FormData(this);
            const isUpdate = this.action.includes('/branches/') && !this.action.endsWith('/branches');
            const requestOptions = {
                method: isUpdate ? 'PUT' : 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData,
                credentials: 'same-origin'
            };

            // For non-POST requests, we need to add _method parameter for Laravel
            if (isUpdate) {
                formData.append('_method', 'PUT');
                requestOptions.method = 'POST'; // Laravel needs POST with _method=PUT
            }

            const response = await fetch(this.action, requestOptions);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'An error occurred while saving the branch');
            }

            // Show success message
            showToast('success', data.message || 'Branch saved successfully');

            // Reset form and close modal
            this.reset();
            this.classList.remove('was-validated');

            // Reset form action for new entries
            if (isUpdate) {
                const clientId = document.getElementById('branch_company_id').value;
                this.action = `/clients/${clientId}/branches`;
                const methodInput = this.querySelector('input[name="_method"]');
                if (methodInput) {
                    methodInput.remove();
                }
                // Update modal title
                const modalTitle = document.getElementById('addBranchModalLabel');
                if (modalTitle) {
                    const companyName = document.querySelector('#addBranchModal [data-company-name]')?.textContent || 'Branches';
                    modalTitle.innerHTML = `<i class="fas fa-code-branch me-1"></i> Add New Branch - ${companyName}`;
                }
            }

            // Reload branches
            const clientId = document.getElementById('branch_company_id').value;
            if (clientId) {
                await loadBranches(clientId);
            }

            // Close the modal after a short delay
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addBranchModal'));
                if (modal) {
                    modal.hide();
                }
            }, 1500);

        } catch (error) {
            console.error('Error:', error);
            showToast('danger', error.message || 'An error occurred. Please try again.');

            // Handle validation errors
            if (error.errors) {
                // Clear previous error messages
                this.querySelectorAll('.invalid-feedback').forEach(el => {
                    el.textContent = '';
                    el.style.display = 'none';
                });

                // Show new error messages
                Object.entries(error.errors).forEach(([field, messages]) => {
                    const input = this.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.textContent = Array.isArray(messages) ? messages[0] : messages;
                            feedback.style.display = 'block';
                        }
                    }
                });
            }
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
}


// Function to fill edit form with client data
function fillEditForm(buttonData) {
    if (!buttonData) {
        console.error('No button data provided to fillEditForm');
        return;
    }

    console.log('fillEditForm called with data:', buttonData);

    const form = document.getElementById('editClientForm');
    if (!form) {
        console.error('Edit form not found');
        return;
    }

    // Set the form action with the client ID
    form.action = `/clients/${buttonData.id}`;
    console.log('Form action set to:', form.action);

    // Fields to populate
    const fields = [
        'id', 'name', 'email', 'phone', 'address',
        'city', 'state', 'postal_code', 'country', 'status', 'language',
        'nfc_uid', 'cnic', 'notes',
    ];

    // Handle regular fields
    fields.forEach(field => {

        const value = buttonData[field] || '';
        const element = document.getElementById(`edit_${field}`);

        console.log(`Processing field: ${field}`, {
            value,
            element: element ? 'found' : 'not found',
            elementType: element ? element.tagName : 'N/A'
        });

        if (element) {
            if (element.tagName === 'SELECT') {
                console.log(`Setting select ${field} to value:`, value);
                console.log('Available options:', Array.from(element.options).map(o => o.value));

                const option = Array.from(element.options).find(
                    opt => opt.value === value
                );

                if (option) {
                    option.selected = true;
                    console.log(`Set select ${field} to:`, option.value);
                } else {
                    console.warn(`Option with value "${value}" not found in select ${field}`);
                    // Set the first option as fallback if no match found
                    if (element.options.length > 0) {
                        element.options[0].selected = true;
                        console.warn(`Set ${field} to first option:`, element.options[0].value);
                    }
                }
            } else if (element.tagName === 'TEXTAREA') {
                console.log(`Setting textarea ${field} to:`, value);
                element.value = value || ''; // Ensure empty string if value is null/undefined
            } else {
                console.log(`Setting input ${field} to:`, value);
                element.value = value || ''; // Ensure empty string if value is null/undefined
            }

            // Trigger change event in case any listeners are attached
            const event = new Event('change', { bubbles: true });
            element.dispatchEvent(event);
        } else {
            console.warn(`Element not found: edit_${field}`);
        }
    });

    console.log('Finished populating form');
}

// Add event listener for edit client buttons
document.addEventListener('click', function(e) {
    const editBtn = e.target.closest('.edit-client-btn');
    if (editBtn) {
        e.preventDefault(); // Prevent default button behavior
        e.stopPropagation();

        console.log('Edit button clicked, storing button reference');

        // Store the button data before showing the modal
        const buttonData = {
            id: editBtn.getAttribute('data-id'),
            name: editBtn.getAttribute('data-name'),
            email: editBtn.getAttribute('data-email'),
            phone: editBtn.getAttribute('data-phone'),
            contact_person: editBtn.getAttribute('data-contact-person'),
            address: editBtn.getAttribute('data-address'),
            city: editBtn.getAttribute('data-city'),
            state: editBtn.getAttribute('data-state'),
            postal_code: editBtn.getAttribute('data-postal-code'),
            country: editBtn.getAttribute('data-country'),
            status: editBtn.getAttribute('data-status'),
            notes: editBtn.getAttribute('data-notes'),
            language: editBtn.getAttribute('data-language'),
            nfc_uid: editBtn.getAttribute('data-nfc-uid'),
            cnic: editBtn.getAttribute('data-cnic'),
        };

        console.log('Button data:', buttonData);

        // Store the data in the window object
        window.currentEditButton = buttonData;

        const modalElement = document.getElementById('editClientModal');
        if (!modalElement) return;

        // Remove any existing modal backdrops
        const existingBackdrops = document.querySelectorAll('.modal-backdrop');
        existingBackdrops.forEach(backdrop => backdrop.remove());

        // Reset modal state
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');

        // Create modal instance with keyboard and backdrop options
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static', // Prevents closing when clicking outside
            keyboard: false    // Prevents closing with ESC key
        });

        // Handle modal shown event
        const onModalShown = () => {
            if (!window.currentEditButton) {
                console.warn('No currentEditButton data found when modal was shown');
                modal.hide();
                return;
            }

            // Ensure the modal is fully rendered before populating
            setTimeout(() => {
                try {
                    fillEditForm(window.currentEditButton);

                    // Set focus to the first form element for better accessibility
                    const firstInput = modalElement.querySelector('input, select, textarea, button');
                    if (firstInput) {
                        firstInput.focus();
                    }

                    // Debug: Log form values after population
                    const form = document.getElementById('editClientForm');
                    if (form) {
                        console.log('Form values after population:');
                        Array.from(form.elements).forEach(el => {
                            if (el.name) {
                                console.log(`${el.name}:`, el.value);
                            }
                        });
                    }
                } catch (error) {
                    console.error('Error populating form:', error);
                }
            }, 50);

            // Remove the event listener to prevent multiple bindings
            modalElement.removeEventListener('shown.bs.modal', onModalShown);
        };

        // Add shown event listener
        modalElement.addEventListener('shown.bs.modal', onModalShown, { once: true });

        // Handle modal hidden event to clean up
        const onModalHidden = () => {
            // Clean up the currentEditButton
            delete window.currentEditButton;

            // Remove any remaining modal backdrops
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());

            // Reset body classes
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            // Remove the event listener
            modalElement.removeEventListener('hidden.bs.modal', onModalHidden);
        };

        modalElement.addEventListener('hidden.bs.modal', onModalHidden, { once: true });

        // Show the modal
        modal.show();

        // Ensure the modal is visible
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        modalElement.setAttribute('aria-modal', 'true');
        modalElement.removeAttribute('aria-hidden');
        modalElement.scrollTop = 0;
    }
});

// Initialize modal instance once
let branchModal = null;

// Set company ID when branch modal is opened
function setBranchCompany(button) {
    const companyId = button.getAttribute('data-company-id');
    const companyName = button.getAttribute('data-company');
    const modalElement = document.getElementById('addBranchModal');

    // Initialize modal if not already done
    if (!branchModal) {
        branchModal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });

        // Clean up on modal hide
        modalElement.addEventListener('hidden.bs.modal', function() {
            // Reset form and clear validation
            const form = document.getElementById('addBranchForm');
            form.reset();
            form.classList.remove('was-validated');

            // Remove any error messages
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');

            // Reset scrollbar and remove backdrop
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            // Manually remove the backdrop
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());

            // Remove modal-open class from body
            document.body.classList.remove('modal-open');
        });
    }

    // Reset form and validation
    const form = document.getElementById('addBranchForm');
    form.reset();
    form.action = '{{ route("branches.store") }}';
    form.querySelector('input[name="_method"]')?.remove();
    form.classList.remove('was-validated');

        // Set company ID in the form
    const branchCompanyId = document.getElementById('branch_company_id');
    if (branchCompanyId) {
        branchCompanyId.value = companyId;

        // Load branches for this company
        console.log('Loading branches for company ID:', companyId);
        loadBranches(companyId).catch(error => {
            console.error('Error loading branches:', error);
            const tbody = document.getElementById('branchesTableBody');
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error loading branches: ${error.message}</td></tr>`;
            }
        });
    } else {
        console.error('branch_company_id element not found');
    }

    // Show the modal
    branchModal.show();
}

// Load branches for a company
async function loadBranches(companyId) {
    const tbody = document.getElementById('branchesTableBody');
    if (!tbody) {
        console.error('Branches table body not found');
        return;
    }

    // Show loading state
    tbody.innerHTML = '<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';

    try {
        console.log(`Fetching branches for company ID: ${companyId}`);
        const response = await fetch(`/clients/${companyId}/branches`);
        console.log('Response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            throw new Error(`Failed to load branches: ${response.status} ${response.statusText}`);
        }

        const responseData = await response.json();
        console.log('Raw API response:', responseData);

        // Extract branches from the response
        const branches = Array.isArray(responseData) ? responseData : (responseData.data || []);
        console.log('Processed branches data:', branches);

        if (!branches || branches.length === 0) {
            const noBranchesMsg = '<tr><td colspan="7" class="text-center text-muted">No branches found for this company</td></tr>';
            tbody.innerHTML = noBranchesMsg;
            console.log('No branches found in the response');
            return;
        }

        tbody.innerHTML = '';

        branches.forEach((branch, index) => {
            if (!branch) return;

            try {
                const row = `
                    <tr class="branch-row" style="cursor: pointer;"
                        onclick="redirectToCheckpoints(${document.getElementById('branch_company_id').value}, ${branch.id})"
                        data-client-id="${document.getElementById('branch_company_id').value}"
                        data-branch-id="${branch.id}">
                        <td>${index + 1}</td>
                        <td>
                            <div class="fw-semibold">${escapeHtml(branch.name || 'N/A')}</div>
                        </td>
                        <td>${branch.email ? `<a href="mailto:${escapeHtml(branch.email)}" onclick="event.stopPropagation()">${escapeHtml(branch.email)}</a>` : '<span class="text-muted">-</span>'}</td>
                        <td>${branch.phone ? `<a href="tel:${escapeHtml(branch.phone)}" onclick="event.stopPropagation()">${escapeHtml(branch.phone)}</a>` : '<span class="text-muted">-</span>'}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span>${branch.city || '<span class="text-muted">-</span>'}</span>
                                ${branch.country ? `<small class="text-muted">${escapeHtml(branch.country)}</small>` : ''}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-1" onclick="event.stopPropagation()">
                                <button class="btn btn-sm btn-primary"
                                        onclick="editBranch(${JSON.stringify(branch).replace(/"/g, '&quot;')})"
                                        data-bs-toggle="tooltip"
                                        title="Edit Branch">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="deleteBranch(${branch.id || 0}, this)"
                                        data-bs-toggle="tooltip"
                                        title="Delete Branch">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            } catch (error) {
                console.error('Error rendering branch row:', error, branch);
            }
        });
    } catch (error) {
        console.error('Error loading branches:', error);
    }
}

// Edit branch
function editBranch(branch) {
    const form = document.getElementById('addBranchForm');
    const clientId = document.getElementById('branch_company_id').value;

    // Set form action for update using the correct route
    form.action = `/clients/${clientId}/branches/${branch.id}`;

    // Set method to POST with _method=PUT for Laravel
    form.setAttribute('method', 'POST');

    // Ensure we have the _method field
    let methodInput = form.querySelector('input[name="_method"]');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        form.appendChild(methodInput);
    }
    methodInput.value = 'PUT';

    // Ensure we have the CSRF token
    let csrfToken = form.querySelector('input[name="_token"]');
    if (!csrfToken) {
        csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfToken);
    }

    // Reset form and clear any validation states
    form.reset();
    form.classList.remove('was-validated');
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.is-valid').forEach(el => el.classList.remove('is-valid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
    form.querySelectorAll('.valid-feedback').forEach(el => el.style.display = 'none');

    // Set form values from the branch object
    Object.keys(branch).forEach(key => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input) {
            // Handle different input types
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = branch[key];
            } else if (input.type === 'select-one') {
                input.value = branch[key] || '';
            } else {
                input.value = branch[key] || '';
            }

            // Add validation classes if the field has a value
            if (branch[key]) {
                input.classList.add('is-valid');
                const feedback = input.nextElementSibling;
                if (feedback && feedback.classList.contains('valid-feedback')) {
                    feedback.style.display = 'block';
                }
            }
        }
    });

    // Update modal title and button text
    const modalTitle = document.getElementById('addBranchModalLabel');
    if (modalTitle) {
        modalTitle.innerHTML = '<i class="fas fa-edit me-1"></i> Edit Branch';
    }

    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('addBranchModal'));
    modal.show();

    // Scroll to top of modal
    const modalContent = document.querySelector('#addBranchModal .modal-content');
    if (modalContent) {
        modalContent.scrollTop = 0;
    }
}

// Add input event listeners for real-time validation
function setupFormValidation() {
    const form = document.getElementById('addBranchForm');
    if (!form) return;

    // Add input event listeners to all required fields
    form.querySelectorAll('input[required], select[required], textarea[required]').forEach(input => {
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');

                // Show valid feedback
                const feedback = this.nextElementSibling;
                if (feedback && feedback.classList.contains('valid-feedback')) {
                    feedback.style.display = 'block';
                }

                // Hide invalid feedback if it exists
                const invalidFeedback = this.nextElementSibling;
                if (invalidFeedback && invalidFeedback.classList.contains('invalid-feedback')) {
                    invalidFeedback.style.display = 'none';
                }
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');

                // Show invalid feedback
                const feedback = this.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.style.display = 'block';
                }

                // Hide valid feedback if it exists
                const validFeedback = this.nextElementSibling;
                if (validFeedback && validFeedback.classList.contains('valid-feedback')) {
                    validFeedback.style.display = 'none';
                }
            }
        });
    });
}

// Initialize form validation when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    setupFormValidation();
    initializeBranchMap();
});

// Branch Map functionality
let branchMap = null;
let branchMarker = null;

function initializeBranchMap() {
    // Initialize map when branch modal is shown
    document.getElementById('addBranchModal').addEventListener('shown.bs.modal', function () {
        if (!branchMap) {
            // Initialize the map
            branchMap = L.map('branchMap').setView([24.8607, 67.0011], 10); // Default to Karachi

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(branchMap);

            // Add click event to map
            branchMap.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                // Update input fields
                document.getElementById('branch_latitude').value = lat.toFixed(6);
                document.getElementById('branch_longitude').value = lng.toFixed(6);

                // Update or create marker
                if (branchMarker) {
                    branchMarker.setLatLng(e.latlng);
                } else {
                    branchMarker = L.marker(e.latlng).addTo(branchMap);
                }

                // Show coordinates in popup
                branchMarker.bindPopup(`Selected Location:<br>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`).openPopup();

                // Trigger validation
                document.getElementById('branch_latitude').dispatchEvent(new Event('input'));
                document.getElementById('branch_longitude').dispatchEvent(new Event('input'));
            });
        }
    });

    // Clear map when modal is hidden
    document.getElementById('addBranchModal').addEventListener('hidden.bs.modal', function () {
        if (branchMap) {
            branchMap.remove();
            branchMap = null;
            branchMarker = null;
        }
    });
}

// Function to set coordinates from address (geocoding)
async function geocodeAddress() {
    const address = document.querySelector('input[name="address"]').value;
    const city = document.querySelector('input[name="city"]').value;
    const country = document.querySelector('input[name="country"]').value;

    if (!address || !city) {
        showToast('warning', 'Please enter address and city first');
        return;
    }

    const fullAddress = `${address}, ${city}, ${country}`;

    try {
        // Using Nominatim (OpenStreetMap geocoding service)
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullAddress)}&limit=1`);
        const data = await response.json();

        if (data && data.length > 0) {
            const lat = parseFloat(data[0].lat);
            const lng = parseFloat(data[0].lon);

            // Update input fields
            document.getElementById('branch_latitude').value = lat.toFixed(6);
            document.getElementById('branch_longitude').value = lng.toFixed(6);

            // Update map
            if (branchMap) {
                branchMap.setView([lat, lng], 15);

                if (branchMarker) {
                    branchMarker.setLatLng([lat, lng]);
                } else {
                    branchMarker = L.marker([lat, lng]).addTo(branchMap);
                }

                branchMarker.bindPopup(`Geocoded Location:<br>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`).openPopup();
            }

            showToast('success', 'Location found! Click on map to adjust if needed.');
        } else {
            showToast('warning', 'Address not found. Please click on the map to set location manually.');
        }
    } catch (error) {
        console.error('Geocoding error:', error);
        showToast('warning', 'Could not find address. Please click on the map to set location manually.');
    }
}

// Delete branch with confirmation and AJAX
async function deleteBranch(branchId, button) {
    if (!confirm('Are you sure you want to delete this branch?')) {
        return;
    }

    const row = button.closest('tr');
    const originalContent = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

    try {
        const response = await fetch(`/branches/${branchId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok) {
            // Remove the row from the table
            row.remove();
            showToast('success', data.message || 'Branch deleted successfully');

            // Reload branches to update the list
            const companyId = document.getElementById('branch_company_id').value;
            if (companyId) {
                loadBranches(companyId);
            }
        } else {
            throw new Error(data.message || 'Failed to delete branch');
        }
    } catch (error) {
        console.error('Error deleting branch:', error);
        showToast('danger', error.message || 'An error occurred while deleting the branch');
    } finally {
        button.disabled = false;
        button.innerHTML = originalContent;
    }
}

// Reset branch form when modal is hidden
document.getElementById('addBranchModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('addBranchForm');
    form.reset();
    form.action = '';
    form.querySelector('input[name="_method"]').value = 'POST';
    document.getElementById('addBranchModalLabel').innerHTML =
        '<i class="fas fa-code-branch me-1"></i> Add Branch Office';
});

// Show toast notification
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

// Add Bootstrap validation styles
(function () {
    'use strict';

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation');

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });
})();

// Function to redirect to checkpoints page
function redirectToCheckpoints(clientId, branchId) {
    // Close the modal first
    const modal = bootstrap.Modal.getInstance(document.getElementById('addBranchModal'));
    if (modal) {
        modal.hide();
    }

    // Redirect to the checkpoints page using URL path parameters only
    // This avoids conflicts with the JavaScript that reads query parameters
    const url = `/clients/${clientId}/branches/${branchId}/checkpoints`;
    window.location.href = url;
}
</script>
@endpush
