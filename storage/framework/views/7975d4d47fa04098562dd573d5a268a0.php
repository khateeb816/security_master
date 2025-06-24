

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h4 class="fw-semibold mb-4">Edit Checkpoint</h4>

    <div class="row">
        <!-- Map Section -->
        <div class="col-md-6">
            <div class="border rounded p-3 mb-3">
                <strong class="d-block mb-2">📶 NFC - ID: <span>2343</span></strong>
                <div id="map" style="height: 400px; width: 100%; background: #f2f2f2;">
                    <p class="text-muted text-center mt-5">[Map preview of location]</p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="col-md-6">
            <form method="POST" action="">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-3">
                    <label class="form-label">Point Code</label>
                    <input type="text" name="point_code" class="form-control" value="<?php echo e($checkpoint->point_code); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Checkpoint Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($checkpoint->name); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control"><?php echo e($checkpoint->notes); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control" value="<?php echo e($checkpoint->latitude); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control" value="<?php echo e($checkpoint->longitude); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Geofence Radius</label>
                    <input type="text" name="geofence_radius" class="form-control" value="<?php echo e($checkpoint->geofence_radius); ?>">
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="geofence_enabled" id="geofence_enabled" <?php echo e($checkpoint->geofence_enabled ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="geofence_enabled">Enable Geofence</label>
                </div>

                <div class="mb-3">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select">
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($branch->id); ?>" <?php echo e($branch->id == $checkpoint->branch_id ? 'selected' : ''); ?>><?php echo e($branch->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Client</label>
                    <select name="client_id" class="form-select">
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($client->id); ?>" <?php echo e($client->id == $checkpoint->client_id ? 'selected' : ''); ?>><?php echo e($client->company_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Site</label>
                    <input type="text" name="site" class="form-control" value="<?php echo e($checkpoint->site); ?>">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" name="lock" id="lock" <?php echo e($checkpoint->lock ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="lock">Lock</label>
                </div>

                <div class="mb-3">
                    <label class="form-label">Client Site Code</label>
                    <input type="text" name="client_site_code" class="form-control" value="<?php echo e($checkpoint->client_site_code); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Checkpoint Code</label>
                    <input type="text" name="checkpoint_code" class="form-control" value="<?php echo e($checkpoint->checkpoint_code); ?>">
                </div>

                <button class="btn btn-primary">Update Checkpoint</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Bilawal\CascadeProjects\remote_project\resources\views/checkpoints/edit.blade.php ENDPATH**/ ?>