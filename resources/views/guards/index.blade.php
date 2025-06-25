@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0">Guards</h4>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addguardModal">
            <i class="fas fa-building me-1"></i> Add New Guard
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
                            <th>Phone</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guards as $index => $guard)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $guard->name }}</td>
                            <td>{{ $guard->email }}</td>
                            <td>{{ $guard->phone ?? 'N/A' }}</td>
                            <td>{{ $guard->city ?? 'N/A' }}</td>
                            <td>{{ $guard->country ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex gap-1" role="group">
                                    <button class="btn btn-warning edit-guard-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editguardModal"
                                            data-id="{{ $guard->id }}"
                                            data-name="{{ $guard->name }}"
                                            data-email="{{ $guard->email }}"
                                            data-phone="{{ $guard->phone }}"
                                            data-address="{{ $guard->address }}"
                                            data-city="{{ $guard->city }}"
                                            data-state="{{ $guard->state }}"
                                            data-postal-code="{{ $guard->zip }}"
                                            data-country="{{ $guard->country }}"
                                            data-status="{{ $guard->status }}"
                                            data-language="{{ $guard->language }}"
                                            data-nfc-uid="{{ $guard->nfc_uid }}"
                                            data-cnic="{{ $guard->cnic }}"
                                            data-notes="{{ $guard->notes }}"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('guards.destroy', $guard->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this guard?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" id="id" value="{{ $guard->id }}">
                                        <button type="submit" class="btn btn-danger ms-1" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                </button>
                            </td>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No guards found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 🟩 Add guard Modal -->
<div class="modal fade" id="addguardModal" tabindex="-1" aria-labelledby="addguardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="addguardForm" class="modal-content" method="POST" action="{{ route('guards.store') }}">
            @csrf
            @method('POST')
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="addguardModalLabel"><i class="fas fa-plus me-1"></i> Add New guard</h5>
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
                    <div class="invalid-feedback">Please provide a guard name</div>
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
                <button type="submit" class="btn btn-primary btn-sm">Add guard</button>
            </div>
        </form>
    </div>
</div>

<!-- 🟦 Edit guard Modal -->
<div class="modal fade" id="editguardModal" tabindex="-1" aria-labelledby="editguardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editguardForm" class="modal-content" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editguardModalLabel"><i class="fas fa-edit me-1"></i> Edit guard</h5>
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

    // Get the guard ID and form
    const guardId = button.getAttribute('data-id');
    const form = document.getElementById('editguardForm');
    form.action = `/guards/${guardId}`;

    // Set form fields based on data attributes
    const fields = [
    'id', 'name', 'email', 'phone', 'address',
    'city', 'state', 'postal_code', 'country', 'status', 'language',
    'cnic', 'nfc_uid', 'notes',
];


    console.log('Setting fields:', fields);

    // Handle regular fields and select elements
    fields.forEach(field => {
        const dataAttr = `data-${field.replace(/_/g, '-')}`;
        let value = button.getAttribute(dataAttr);

        // Handle null/undefined values
        if (value === 'null' || value === 'undefined') value = '';

        const element = document.getElementById(`edit_${field}`);

        console.log(`Field: ${field}, Data Attribute: ${dataAttr}, Value: ${value}, Element:`, element);

        if (element) {
            if (element.tagName === 'SELECT') {
                // For select elements, find and select the option
                const option = Array.from(element.options).find(
                    opt => opt.value === value
                );
                // console.log(`Select ${field} options:`, Array.from(element.options).map(o => o.value), 'Selected:`, value);
                if (option) {
                    option.selected = true;
                    console.log(`Set select ${field} to:`, option.value);
                } else if (element.multiple) {
                    // Handle multiple select if needed
                    const values = value ? value.split(',') : [];
                    Array.from(element.options).forEach(opt => {
                        opt.selected = values.includes(opt.value);
                    });
                }
            } else if (element.type === 'checkbox') {
                // For checkboxes
                element.checked = value === '1' || value === 'true' || value === true;
                console.log(`Set checkbox ${field} to:`, element.checked);
            } else {
                // For input/textarea elements
                element.value = value || '';
                console.log(`Set input ${field} to:`, value);
            }
        }
    });

    // Handle checkboxes separately for better reliability
    const checkboxes = [
        'incident_report_email',
        'mobile_form_email'
    ];

    checkboxes.forEach(field => {
        const dataAttr = `data-${field.replace(/_/g, '-')}`;
        const value = button.getAttribute(dataAttr);
        const element = document.getElementById(`edit_${field}`);

        console.log(`Checkbox ${field}, Data Attribute: ${dataAttr}, Value: ${value}, Element:`, element);

        if (element) {
            const isChecked = value === '1' || value === 'true' || value === true;
            element.checked = isChecked;
            console.log(`Set checkbox ${field} to:`, isChecked);
        }
    });

    // Debug: Log all data attributes
    console.log('All data attributes:');
    Array.from(button.attributes).forEach(attr => {
        if (attr.name.startsWith('data-')) {
            console.log(`${attr.name}: ${attr.value}`);
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
        form.action = `/guards/${companyId}/branches`;
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
                const guardId = document.getElementById('branch_company_id').value;
                this.action = `/guards/${guardId}/branches`;
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
            const guardId = document.getElementById('branch_company_id').value;
            if (guardId) {
                await loadBranches(guardId);
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


// Function to fill edit form with guard data
function fillEditForm(buttonData) {
    if (!buttonData) {
        console.error('No button data provided to fillEditForm');
        return;
    }

    console.log('fillEditForm called with data:', buttonData);

    const form = document.getElementById('editguardForm');
    if (!form) {
        console.error('Edit form not found');
        return;
    }

    // Set the form action with the guard ID
    form.action = `/guards/${buttonData.id}`;
    console.log('Form action set to:', form.action);

    // Fields to populate
    const fields = [
        'id', 'name', 'contact_person', 'email', 'phone', 'address',
        'city', 'state', 'postal_code', 'country', 'status', 'language',
        'arc_id', 'additional_recipients', 'notes', 'incident_report_email', 'mobile_form_email',
        'cnic', 'nfc_uid'
    ];

    // Special handling for checkboxes
    const checkboxFields = ['incident_report_email', 'mobile_form_email'];

    // Handle checkboxes first
    checkboxFields.forEach(field => {
        const element = document.getElementById(`edit_${field}`);
        if (element) {
            // Convert various truthy values to boolean
            const value = buttonData[field];
            const isChecked = value === '1' || value === 1 || value === 'true' || value === true || value === 'on';

            console.log(`Setting checkbox ${field} to:`, isChecked, 'from value:', value);
            element.checked = isChecked;

            // Also set the value to '1' or '0' for form submission
            element.value = isChecked ? '1' : '0';
        }
    });

    // Handle regular fields
    fields.forEach(field => {
        // Skip checkboxes as we've already handled them
        if (checkboxFields.includes(field)) return;

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

// Add event listener for edit guard buttons
document.addEventListener('click', function(e) {
    const editBtn = e.target.closest('.edit-guard-btn');
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
            nfc_uid: editBtn.getAttribute('data-nfc-uid'),
            language: editBtn.getAttribute('data-language'),
            cnic: editBtn.getAttribute('data-cnic'),
        };

        console.log('Button data:', buttonData);

        // Store the data in the window object
        window.currentEditButton = buttonData;

        const modalElement = document.getElementById('editguardModal');
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
                    const form = document.getElementById('editguardForm');
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
</script>
@endpush
