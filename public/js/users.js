// Users Management JavaScript

// Load branches for a client
window.loadBranches = function(clientId, isEdit = false) {
    const prefix = isEdit ? 'edit' : '';
    // For the add form, the ID is 'branch_id' (with underscore) instead of 'branchId'
    const branchSelectId = isEdit ? 'editBranchId' : 'branch_id';
    const branchSelect = document.getElementById(branchSelectId);
    const latitudeInput = document.getElementById(isEdit ? 'editLatitude' : 'latitude');
    const longitudeInput = document.getElementById(isEdit ? 'editLongitude' : 'longitude');
    
    console.log('loadBranches called with:', { clientId, isEdit, branchSelectId, branchSelect });

    if (!clientId) {
        if (branchSelect) {
            branchSelect.innerHTML = '<option value="">-- Select Branch --</option>';
            branchSelect.disabled = true;
        }
        if (latitudeInput) latitudeInput.value = '';
        if (longitudeInput) longitudeInput.value = '';
        return;
    }

    // Show loading state
    if (branchSelect) {
        branchSelect.innerHTML = '<option value="" disabled>Loading branches...</option>';
        branchSelect.disabled = false;
    }

    // Fetch branches for the selected client
    fetch(`/clients/${clientId}/branches`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.length > 0) {
                // Populate branch dropdown
                branchSelect.innerHTML = '<option value="">-- Select Branch --</option>';
                data.data.forEach(branch => {
                    const option = new Option(branch.branch_name || branch.name, branch.id);
                    option.dataset.latitude = branch.latitude || '';
                    option.dataset.longitude = branch.longitude || '';
                    branchSelect.add(option);
                });
                branchSelect.disabled = false;
            } else {
                branchSelect.innerHTML = '<option value="">No branches available</option>';
                branchSelect.disabled = true;
            }
        })
        .catch(error => {
            console.error('Error loading branches:', error);
            if (branchSelect) {
                branchSelect.innerHTML = '<option value="">Error loading branches</option>';
                branchSelect.disabled = true;
            }
        });
};

// Update coordinates when a branch is selected in edit modal
window.updateEditCoordinates = function(select) {
    const selectedOption = select.options[select.selectedIndex];
    const latInput = document.getElementById('editLatitude');
    const lngInput = document.getElementById('editLongitude');
    
    if (selectedOption && selectedOption.dataset.latitude && selectedOption.dataset.longitude) {
        if (latInput) latInput.value = selectedOption.dataset.latitude;
        if (lngInput) lngInput.value = selectedOption.dataset.longitude;
    } else {
        if (latInput) latInput.value = '';
        if (lngInput) lngInput.value = '';
    }
};

// Update coordinates when a branch is selected
window.updateCoordinates = function(select, isEdit = false) {
    const prefix = isEdit ? 'edit' : '';
    const latitudeInput = document.getElementById(`${prefix}Latitude`);
    const longitudeInput = document.getElementById(`${prefix}Longitude`);
    const selectedOption = select.options[select.selectedIndex];
    
    console.log('updateCoordinates called:', { 
        isEdit, 
        selectedValue: select.value,
        latitude: selectedOption ? selectedOption.dataset.latitude : 'none',
        longitude: selectedOption ? selectedOption.dataset.longitude : 'none'
    });

    if (selectedOption && selectedOption.dataset) {
        if (latitudeInput) latitudeInput.value = selectedOption.dataset.latitude || '';
        if (longitudeInput) longitudeInput.value = selectedOption.dataset.longitude || '';
    } else {
        if (latitudeInput) latitudeInput.value = '';
        if (longitudeInput) longitudeInput.value = '';
    }
    
    console.log('Updated coordinates:', {
        latitude: latitudeInput ? latitudeInput.value : 'no input',
        longitude: longitudeInput ? longitudeInput.value : 'no input'
    });
};

// Load branches for edit modal with coordinate matching
window.loadBranchesForEdit = function(clientId, latitude, longitude, branchId = null, userId = null) {
    console.log('loadBranchesForEdit called with:', { clientId, latitude, longitude });
    const branchSelect = document.getElementById('editBranchId');
    const latInput = document.getElementById('editLatitude');
    const lngInput = document.getElementById('editLongitude');
    const clientSelect = document.getElementById('editClientId');

    if (!branchSelect) {
        console.error('Branch select element not found');
        return;
    }

    // If no client ID is provided but we have a selected client, use that
    if (!clientId && clientSelect && clientSelect.value) {
        clientId = clientSelect.value;
    }

    if (!clientId) {
        branchSelect.innerHTML = '<option value="">-- Select Branch --</option>';
        branchSelect.disabled = true;
        if (latInput) latInput.value = '';
        if (lngInput) lngInput.value = '';
        return;
    }

    // Show loading state
    branchSelect.innerHTML = '<option value="" disabled>Loading branches...</option>';
    branchSelect.disabled = false;

    // Clear any existing loading state
    branchSelect.innerHTML = '<option value="">-- Select Branch --</option>';
    branchSelect.disabled = false;
    
    // Show loading state
    const loadingOption = document.createElement('option');
    loadingOption.value = '';
    loadingOption.textContent = 'Loading branches...';
    loadingOption.disabled = true;
    loadingOption.selected = true;
    branchSelect.appendChild(loadingOption);
    
    // Fetch branches for the selected client
    fetch(`/clients/${clientId}/branches`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Branches loaded:', data);
            
            // Clear loading state and add default option
            branchSelect.innerHTML = '<option value="">-- Select Branch --</option>';
            
            if (data.success && data.data && data.data.length > 0) {
                // Add all branch options
                data.data.forEach(branch => {
                    const option = new Option(branch.branch_name || branch.name, branch.id);
                    const branchLat = branch.latitude ? parseFloat(branch.latitude).toFixed(6) : '';
                    const branchLng = branch.longitude ? parseFloat(branch.longitude).toFixed(6) : '';
                    
                    option.dataset.latitude = branchLat;
                    option.dataset.longitude = branchLng;
                    branchSelect.add(option);
                });
                
                // Log all available branches for debugging
                console.log('Available branches:', Array.from(branchSelect.options).map(opt => ({
                    value: opt.value,
                    text: opt.text,
                    lat: opt.dataset.latitude,
                    lng: opt.dataset.longitude
                })));

                // Determine which branch ID to use (prefer the one passed as parameter)
                let branchIdToSelect = branchId || branchSelect.getAttribute('data-current-branch-id');
                console.log('Branch ID to select (initial):', { 
                    fromParam: branchId, 
                    fromDataAttr: branchSelect.getAttribute('data-current-branch-id'), 
                    selected: branchIdToSelect 
                });
                
                // If we have coordinates, find and select the matching branch
                if (latitude && longitude) {
                    console.log('Looking for branch matching coordinates:', { latitude, longitude });
                    
                    // Convert coordinates to numbers with fixed precision for comparison
                    const userLat = parseFloat(latitude).toFixed(6);
                    const userLng = parseFloat(longitude).toFixed(6);
                    
                    // Find a branch that matches the coordinates
                    const matchingBranch = Array.from(branchSelect.options).find(opt => {
                        if (!opt.dataset.latitude || !opt.dataset.longitude) return false;
                        
                        const branchLat = parseFloat(opt.dataset.latitude).toFixed(6);
                        const branchLng = parseFloat(opt.dataset.longitude).toFixed(6);
                        
                        const isMatch = branchLat === userLat && branchLng === userLng;
                        
                        console.log('Checking branch:', {
                            id: opt.value,
                            branchLat,
                            branchLng,
                            matches: isMatch
                        });
                        
                        return isMatch;
                    });
                    
                    if (matchingBranch) {
                        branchIdToSelect = matchingBranch.value;
                        console.log('Found matching branch by coordinates:', branchIdToSelect);
                        
                        // Directly set the value and trigger change events
                        if (branchIdToSelect) {
                            console.log('Setting branch select value to:', branchIdToSelect);
                            branchSelect.value = branchIdToSelect;
                            branchSelect.dispatchEvent(new Event('change', { bubbles: true }));
                            
                            // If using Select2, update it as well
                            if (typeof $ !== 'undefined' && $(branchSelect).hasClass('select2-hidden-accessible')) {
                                $(branchSelect).trigger('change.select2');
                            }
                            
                            // Ensure the dropdown is enabled
                            branchSelect.disabled = false;
                            
                            // Remove any loading classes or states
                            branchSelect.classList.remove('loading');
                            const loadingOption = branchSelect.querySelector('option[disabled]');
                            if (loadingOption) {
                                loadingOption.remove();
                            }
                        }
                    } else {
                        console.log('No matching branch found for coordinates:', { userLat, userLng });
                        // Ensure dropdown is enabled even if no match found
                        branchSelect.disabled = false;
                    }
                }
                
                // Function to select a branch by ID
                const selectBranch = (branchId) => {
                    console.log('Attempting to select branch:', branchId);
                    const option = branchSelect.querySelector(`option[value="${branchId}"]`);
                    
                    if (option) {
                        console.log('Found and selecting branch:', branchId);
                        branchSelect.value = branchId;
                        
                        // Update the data attribute
                        branchSelect.setAttribute('data-current-branch-id', branchId);
                        
                        // Force the select2 to update if it exists
                        if (typeof $ !== 'undefined' && $(branchSelect).hasClass('select2-hidden-accessible')) {
                            $(branchSelect).trigger('change.select2');
                        } else {
                            // Dispatch change event to trigger any dependent logic
                            branchSelect.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                        return true;
                    } else {
                        console.warn('Branch option not found, will retry:', branchId);
                        return false;
                    }
                };
                
                // Try to select the branch immediately
                // Track if we found and selected a branch
                let branchSelected = false;
                
                // Try to select the branch by ID if provided
                if (branchIdToSelect && branchIdToSelect !== 'null') {
                    console.log('Attempting to select branch by ID:', branchIdToSelect);
                    console.log('Current select options:', Array.from(branchSelect.options).map(o => ({
                        value: o.value,
                        text: o.text,
                        selected: o.selected
                    })));
                    
                    const option = branchSelect.querySelector(`option[value="${branchIdToSelect}"]`);
                    if (option) {
                        console.log('Found matching option:', option);
                        
                        // First, clear any existing selections
                        Array.from(branchSelect.options).forEach(opt => opt.selected = false);
                        
                        // Set the selected option
                        option.selected = true;
                        branchSelect.value = branchIdToSelect;
                        
                        console.log('After setting value, selectedIndex:', branchSelect.selectedIndex);
                        branchSelected = true;
                        
                        // Use native JavaScript to handle the selection
                        console.log('Using native select behavior');
                        
                        // Clear any existing selections
                        Array.from(branchSelect.options).forEach(opt => {
                            opt.selected = false;
                            opt.removeAttribute('selected');
                        });
                        
                        // Set the selected option
                        if (option) {
                            console.log('Setting branch selection for option:', option);
                            
                            // First, ensure the select is enabled
                            branchSelect.disabled = false;
                            
                            // Clear any existing selections
                            Array.from(branchSelect.options).forEach(opt => {
                                opt.selected = false;
                                opt.removeAttribute('selected');
                            });
                            
                            // Set the new selection
                            option.selected = true;
                            option.setAttribute('selected', 'selected');
                            
                            // Set the value directly
                            branchSelect.value = branchIdToSelect;
                            
                            // Force a UI update by toggling the select's display
                            const originalDisplay = branchSelect.style.display;
                            branchSelect.style.display = 'none';
                            
                            setTimeout(() => {
                                branchSelect.style.display = originalDisplay;
                                
                                // One more attempt to set the value after a small delay
                                setTimeout(() => {
                                    branchSelect.value = branchIdToSelect;
                                    console.log('Final branch select value:', branchSelect.value);
                                    
                                    // Create and dispatch change event
                                    const event = new Event('change', {
                                        bubbles: true,
                                        cancelable: true
                                    });
                                    branchSelect.dispatchEvent(event);
                                }, 50);
                            }, 50);
                            
                            // Function to set the branch with retry mechanism
                            const setBranchWithRetry = (retryCount = 0, maxRetries = 3) => {
                                if (retryCount >= maxRetries) {
                                    console.warn('Max retries reached for setting branch');
                                    return;
                                }

                                try {
                                    // Set the value directly
                                    branchSelect.value = branchIdToSelect;
                                    
                                    // Force a reflow to ensure UI updates
                                    const originalDisplay = branchSelect.style.display;
                                    branchSelect.style.display = 'none';
                                    branchSelect.offsetHeight; // Trigger reflow
                                    branchSelect.style.display = originalDisplay;
                                    
                                    // Create and dispatch change event
                                    const changeEvent = new Event('change', {
                                        bubbles: true,
                                        cancelable: true
                                    });
                                    
                                    // Set selected attribute on the option
                                    if (option) {
                                        option.selected = true;
                                        option.setAttribute('selected', 'selected');
                                    }
                                    
                                    // Dispatch the event
                                    branchSelect.dispatchEvent(changeEvent);
                                    
                                    // Verify the value was set
                                    if (branchSelect.value !== branchIdToSelect && retryCount < maxRetries - 1) {
                                        console.log(`Retry ${retryCount + 1}: Value not set correctly, retrying...`);
                                        setTimeout(() => setBranchWithRetry(retryCount + 1, maxRetries), 100);
                                        return;
                                    }
                                    
                                    console.log('Branch selection successful after', retryCount + 1, 'attempt(s)');
                                    
                                } catch (error) {
                                    console.error('Error setting branch:', error);
                                    if (retryCount < maxRetries - 1) {
                                        setTimeout(() => setBranchWithRetry(retryCount + 1, maxRetries), 100);
                                    }
                                }
                            };
                            
                            // Function to force select update
                            const forceSelectUpdate = () => {
                                try {
                                    // 1. First, ensure the select is enabled and visible
                                    branchSelect.disabled = false;
                                    branchSelect.style.display = '';
                                    
                                    // 2. Store the current value
                                    const currentValue = branchSelect.value;
                                    
                                    // 3. Force a value change by setting to empty and back
                                    branchSelect.value = '';
                                    setTimeout(() => {
                                        branchSelect.value = currentValue || branchIdToSelect;
                                        
                                        // 4. Trigger a change event
                                        const event = new Event('change', {
                                            bubbles: true,
                                            cancelable: true
                                        });
                                        branchSelect.dispatchEvent(event);
                                        
                                        // 5. Force focus and blur to ensure UI updates
                                        branchSelect.focus();
                                        setTimeout(() => branchSelect.blur(), 10);
                                        
                                        console.log('Forced select update completed');
                                    }, 10);
                                } catch (error) {
                                    console.error('Error forcing select update:', error);
                                }
                            };
                            
                            // Initial attempt to set the branch
                            setBranchWithRetry();
                            
                            // Force UI update with optimized timing
                            setTimeout(forceSelectUpdate, 0);  // Immediate execution on next tick
                            setTimeout(forceSelectUpdate, 50);  // Quick follow-up
                            setTimeout(forceSelectUpdate, 150); // Final attempt after animations
                            
                            // Log the current state
                            console.log('Native select value set to:', branchSelect.value, {
                                selectedIndex: branchSelect.selectedIndex,
                                selectedOptions: Array.from(branchSelect.selectedOptions).map(o => o.value),
                                options: Array.from(branchSelect.options).map(o => ({
                                    value: o.value,
                                    text: o.text,
                                    selected: o.selected
                                }))
                            });
                            
                            // If there's a change handler, call it directly
                            if (typeof window.onBranchSelectChange === 'function') {
                                window.onBranchSelectChange(event);
                            }
                        }
                    } else {
                        console.warn('Option not found for branch ID:', branchIdToSelect);
                    }
                }
                
                // If no branch was selected by ID but we have coordinates, try to match by coordinates
                if (!branchSelected && latitude && longitude) {
                    const userLat = parseFloat(latitude).toFixed(6);
                    const userLng = parseFloat(longitude).toFixed(6);
                    
                    console.log('Attempting to match by coordinates:', { userLat, userLng });
                    
                    // Find matching branch by coordinates
                    const matchingBranch = Array.from(branchSelect.options).find(opt => {
                        if (!opt.dataset.latitude || !opt.dataset.longitude) {
                            console.log('Option missing coordinates:', opt.value, opt.text);
                            return false;
                        }
                        
                        const branchLat = parseFloat(opt.dataset.latitude).toFixed(6);
                        const branchLng = parseFloat(opt.dataset.longitude).toFixed(6);
                        const isMatch = branchLat === userLat && branchLng === userLng;
                        
                        console.log('Checking branch:', {
                            value: opt.value,
                            text: opt.text,
                            branchLat,
                            branchLng,
                            isMatch
                        });
                        
                        return isMatch;
                    });
                    
                    if (matchingBranch) {
                        console.log('Found matching branch by coordinates:', {
                            value: matchingBranch.value,
                            text: matchingBranch.text,
                            lat: matchingBranch.dataset.latitude,
                            lng: matchingBranch.dataset.longitude
                        });
                        
                        // Set the value and trigger change events
                        matchingBranch.selected = true;
                        branchSelect.value = matchingBranch.value;
                        branchSelected = true;
                        
                        // Update coordinates if inputs exist
                        if (latInput) latInput.value = matchingBranch.dataset.latitude || '';
                        if (lngInput) lngInput.value = matchingBranch.dataset.longitude || '';
                        
                        console.log('Updated select value to:', branchSelect.value);
                    } else if (branchSelect.options.length > 1) {
                        console.log('No matching branch found by coordinates, selecting first available branch');
                        // If no match found but we have branches, select the first one
                        branchSelect.selectedIndex = 1; // Skip the default "Select Branch" option
                        const selectedOption = branchSelect.options[branchSelect.selectedIndex];
                        
                        if (selectedOption) {
                            console.log('Selected first available branch:', {
                                value: selectedOption.value,
                                text: selectedOption.text
                            });
                            
                            if (selectedOption.dataset) {
                                if (latInput) latInput.value = selectedOption.dataset.latitude || '';
                                if (lngInput) lngInput.value = selectedOption.dataset.longitude || '';
                            }
                            branchSelected = true;
                        }
                    }
                }
                
                // Enable the dropdown
                branchSelect.disabled = false;
                
                // Trigger change event if we selected a branch
                if (branchSelected) {
                    branchSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    // If using Select2, update it as well
                    if (typeof $ !== 'undefined' && $(branchSelect).hasClass('select2-hidden-accessible')) {
                        $(branchSelect).trigger('change.select2');
                    }
                }
                
                // Dispatch custom event to notify that branches are loaded
                const clientSelect = document.getElementById('editClientId');
                if (clientSelect) {
                    clientSelect.dispatchEvent(new Event('branches-loaded'));
                }
            } else {
                branchSelect.innerHTML = '<option value="">No branches available</option>';
                branchSelect.disabled = true;
                if (latInput) latInput.value = '';
                if (lngInput) lngInput.value = '';
            }
        })
        .catch(error => {
            console.error('Error loading branches:', error);
            branchSelect.innerHTML = '<option value="">Error loading branches</option>';
            branchSelect.disabled = true;
            if (latInput) latInput.value = '';
            if (lngInput) lngInput.value = '';
            
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
                        Failed to load branches. Please try again.
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>`;
            
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        });
};

// Helper function to safely set input values
function setValueIfExists(elementId, value) {
    const element = document.getElementById(elementId);
    if (element && value !== null && value !== undefined) {
        element.value = value;
    } else if (element) {
        element.value = '';
    }
}

// Helper function to set select values
function setSelectValue(selectId, value) {
    const select = document.getElementById(selectId);
    if (select) {
        // First, try to find an exact match
        const option = Array.from(select.options).find(opt => opt.value === value);
        if (option) {
            select.value = value;
        } else if (select.options.length > 0) {
            // If no exact match, set to first option
            select.selectedIndex = 0;
        }
    }
}

// Handle edit user button click
window.editUser = function(button) {
    // Get all data attributes from the button
    const userId = button.getAttribute('data-id');
    const userName = button.getAttribute('data-name');
    const userEmail = button.getAttribute('data-email');
    const userPhone = button.getAttribute('data-phone') || '';
    const userCnic = button.getAttribute('data-cnic') || '';
    const userNfcUid = button.getAttribute('data-nfc-uid') || '';
    const userDesignation = button.getAttribute('data-designation') || '';
    const role = button.getAttribute('data-role') || 'user';
    const status = button.getAttribute('data-status') || 'active';
    
    // Get client and branch IDs, ensuring they're properly handled
    let clientId = button.getAttribute('data-client-id');
    let branchId = button.getAttribute('data-branch-id');
    
    // Convert empty strings to null
    clientId = (clientId && clientId !== 'null' && clientId !== '') ? clientId : null;
    branchId = (branchId && branchId !== 'null' && branchId !== '') ? branchId : null;
    
    const latitude = button.getAttribute('data-latitude') || '';
    const longitude = button.getAttribute('data-longitude') || '';
    
    console.log('Raw data attributes:', {
        clientId: button.getAttribute('data-client-id'),
        branchId: button.getAttribute('data-branch-id'),
        processed: { clientId, branchId }
    });
    
    console.log('Edit user data:', {
        userId, userName, userEmail, clientId, branchId, latitude, longitude
    });
    
    console.log('Editing user:', { userId, userName, userEmail, clientId, branchId });
    
    // Set form action URL
    const form = document.getElementById('editUserForm');
    form.action = `/users/${userId}`;
    
    // Set basic form fields
    setValueIfExists('editName', userName);
    setValueIfExists('editEmail', userEmail);
    setValueIfExists('editPhone', userPhone);
    setValueIfExists('editCnic', userCnic);
    setValueIfExists('editNfcUid', userNfcUid);
    setValueIfExists('editDesignation', userDesignation);
    setValueIfExists('editLatitude', latitude);
    setValueIfExists('editLongitude', longitude);
    
    // Set dropdown values
    setSelectValue('editRole', role);
    setSelectValue('editStatus', status);
    
    // Set client and branch dropdowns
    const clientSelect = document.getElementById('editClientId');
    const branchSelect = document.getElementById('editBranchId');
    
    // Initialize branch select
    if (branchSelect) {
        // Clear existing options but keep the first "Select Branch" option
        branchSelect.innerHTML = '<option value="">-- Select Branch --</option>';
        branchSelect.disabled = !clientId;
        
        // Always set the data attribute, even if branchId is null
        console.log('Setting data-current-branch-id to:', branchId);
        branchSelect.setAttribute('data-current-branch-id', branchId || '');
    }
    
    // Set the client value
    if (clientSelect) {
        clientSelect.value = clientId || '';
        console.log('Set client ID to:', clientId);
    }
    
    // Log the state before loading branches
    console.log('Before loading branches:', {
        clientId,
        branchId,
        hasClientSelect: !!clientSelect,
        hasBranchSelect: !!branchSelect,
        branchSelectData: branchSelect ? branchSelect.getAttribute('data-current-branch-id') : 'no branch select'
    });
    
    // Initialize branch select
    if (branchSelect) {
        // Clear existing options
        branchSelect.innerHTML = '<option value="">-- Select Branch --</option>';
        branchSelect.disabled = !clientId;
        
        // Set coordinates if available
        const latInput = document.getElementById('editLatitude');
        const lngInput = document.getElementById('editLongitude');
    }
    
    // Make sure branch ID is set before loading branches
    console.log('Current branch ID before loading branches:', branchId);
    
    // Load branches for the selected client
    if (clientId) {
        console.log('Loading branches for client ID:', clientId);
        // Pass the branchId and userId to the loadBranchesForEdit function
        loadBranchesForEdit(clientId, latitude, longitude, branchId, userId);
    } else if (branchSelect) {
        // If no client ID, clear branch select
        branchSelect.innerHTML = '<option value="">-- Select Branch --</option>';
        branchSelect.disabled = true;
    }
    
    // Set role
    const roleSelect = document.getElementById('editRole');
    if (roleSelect) {
        roleSelect.value = role || '';
    }
    
    // Show the modal
    const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    editModal.show();
    
    // Focus the first input field
    const firstInput = document.querySelector('#editUserModal input:not([type="hidden"])');
    if (firstInput) {
        setTimeout(() => firstInput.focus(), 500);
    }
};

// Initialize the application when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('User management script loaded');
    
    // Handle edit form submission
    const editForm = document.getElementById('editUserForm');
    if (editForm) {
        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Edit form submission started');
            
            const form = e.target;
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            const errorDiv = document.getElementById('editFormErrors');
            
            // Reset error display
            if (errorDiv) {
                errorDiv.classList.add('d-none');
                const errorList = errorDiv.querySelector('ul');
                if (errorList) errorList.innerHTML = '';
            }

            try {
                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

                // Submit the form
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();
                console.log('Form submission response:', data);

                if (response.ok && data.success) {
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
                    // Show validation errors if any
                    if (data.errors) {
                        if (errorDiv) {
                            const errorList = errorDiv.querySelector('ul') || document.createElement('ul');
                            errorList.innerHTML = ''; // Clear previous errors
                            
                            Object.values(data.errors).forEach(errorMessages => {
                                errorMessages.forEach(message => {
                                    const li = document.createElement('li');
                                    li.textContent = message;
                                    errorList.appendChild(li);
                                });
                            });

                            errorDiv.appendChild(errorList);
                            errorDiv.classList.remove('d-none');
                        }
                        throw new Error('Please fix the form errors and try again.');
                    }
                    throw new Error(data.message || 'Failed to update user');
                }
            } catch (error) {
                console.error('Error:', error);

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
            } finally {
                // Reset button state
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
            }
        });
    }
    
    // Initialize event listeners for client and branch selects
    function initializeEventListeners() {
        console.log('Initializing event listeners');
        
        // Add User form
        const clientSelect = document.getElementById('client_id');
        if (clientSelect) {
            console.log('Found client select, adding change handler');
            clientSelect.addEventListener('change', function() {
                console.log('Client selected:', this.value);
                loadBranches(this.value);
            });
        }

        // Edit User form
        const editClientSelect = document.getElementById('editClientId');
        if (editClientSelect) {
            console.log('Found edit client select, adding change handler');
            editClientSelect.addEventListener('change', function() {
                console.log('Edit client selected:', this.value);
                loadBranchesForEdit(this.value);
            });
        }

        // Branch selects
        const branchSelect = document.getElementById('branch_id');
        if (branchSelect) {
            console.log('Found branch select, adding change handler');
            branchSelect.addEventListener('change', function() {
                console.log('Branch selected:', this.value);
                updateCoordinates(this, false);
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.dataset) {
                    const latInput = document.getElementById('latitude');
                    const lngInput = document.getElementById('longitude');
                    if (latInput) latInput.value = selectedOption.dataset.latitude || '';
                    if (lngInput) lngInput.value = selectedOption.dataset.longitude || '';
                }
            });
            
            // Update coordinates if a branch is already selected
            if (branchSelect.value) {
                branchSelect.dispatchEvent(new Event('change'));
            }
        }

        const editBranchSelect = document.getElementById('editBranchId');
        if (editBranchSelect) {
            console.log('Found edit branch select, adding change handler');
            // Remove any existing change event listeners to prevent duplicates
            const newEditBranchSelect = editBranchSelect.cloneNode(true);
            editBranchSelect.parentNode.replaceChild(newEditBranchSelect, editBranchSelect);
            
            newEditBranchSelect.addEventListener('change', function() {
                console.log('Edit branch selected:', this.value);
                updateEditCoordinates(this);
            });
            
            // Also update coordinates when the select changes
            if (newEditBranchSelect.value) {
                updateEditCoordinates(newEditBranchSelect);
            }
        }
    }
    
    // Initialize all event listeners
    initializeEventListeners();
    
    // Re-initialize event listeners when the edit modal is shown
    const editModal = document.getElementById('editUserModal');
    if (editModal) {
        editModal.addEventListener('shown.bs.modal', function() {
            console.log('Edit modal shown, re-initializing event listeners');
            initializeEventListeners();
        });
    }
});
