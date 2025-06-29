<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h4 class="fw-semibold mb-4">Edit Checkpoint</h4>
    <?php if(
        $errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" action="<?php echo e(route('clients.branches.checkpoints.update', [
                'client' => $checkpoint->client_id,
                'branch' => $checkpoint->branch_id,
                'checkpoint' => $checkpoint->id,
            ])); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                        <select class="form-select" id="client_id" name="client_id" required>
                            <option value="">Select Client</option>
                            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($client->id); ?>" <?php echo e($client->id == $checkpoint->client_id ? 'selected' : ''); ?>><?php echo e($client->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                        <select class="form-select" id="branch_id" name="branch_id" required>
                            <option value="">Select Branch</option>
                            <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($branch->id); ?>" <?php echo e($branch->id == $checkpoint->branch_id ? 'selected' : ''); ?>><?php echo e($branch->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="name" class="form-label">Checkpoint Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo e($checkpoint->name); ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label for="description" class="form-label">Checkpoint Description</label>
                        <textarea class="form-control" id="description" name="description"><?php echo e($checkpoint->description); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="number" step="any" class="form-control" id="latitude" name="latitude" value="<?php echo e($checkpoint->latitude); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="number" step="any" class="form-control" id="longitude" name="longitude" value="<?php echo e($checkpoint->longitude); ?>">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Select Location on Map</label>
                        <div id="selectLocationMap" style="height: 250px; width: 100%; border: 1px solid #ccc; border-radius: 8px;"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="radius" class="form-label">Geofence Radius (meters) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="radius" name="radius" value="<?php echo e($checkpoint->radius); ?>" required>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo e($checkpoint->is_active ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-primary">Update Checkpoint</button>
                    <a href="<?php echo e(url()->previous()); ?>" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectMap = null;
    let selectMarker = null;
    const selectMapId = 'selectLocationMap';
    const $lat = document.getElementById('latitude');
    const $lng = document.getElementById('longitude');
    // Default center (if no lat/lng): somewhere generic
    let lat = parseFloat($lat.value) || 24.7136;
    let lng = parseFloat($lng.value) || 46.6753;
    selectMap = L.map(selectMapId).setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(selectMap);
    // If lat/lng present, add marker
    if ($lat.value && $lng.value) {
        selectMarker = L.marker([lat, lng], {draggable: true}).addTo(selectMap);
    }
    // On map click, set marker and update fields
    selectMap.on('click', function(e) {
        lat = e.latlng.lat;
        lng = e.latlng.lng;
        $lat.value = lat.toFixed(6);
        $lng.value = lng.toFixed(6);
        if (selectMarker) {
            selectMarker.setLatLng([lat, lng]);
        } else {
            selectMarker = L.marker([lat, lng], {draggable: true}).addTo(selectMap);
        }
    });
    // On marker drag, update fields
    if (selectMarker) {
        selectMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            $lat.value = pos.lat.toFixed(6);
            $lng.value = pos.lng.toFixed(6);
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH K:\Laravel\security-master\resources\views\checkpoints\edit.blade.php ENDPATH**/ ?>