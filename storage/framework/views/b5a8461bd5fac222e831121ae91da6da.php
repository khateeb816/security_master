<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-semibold mb-0">Live Alerts</h4>

            <form method="GET" action="<?php echo e(route('alerts')); ?>" class="d-flex align-items-center gap-2 mb-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search alerts or guards" value="<?php echo e(request('search')); ?>">
                <input type="date" name="date" class="form-control form-control-sm" value="<?php echo e(request('date')); ?>">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i></button>
            </form>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Guard Name</th>
                                <th>Alert Type</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th>Map</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($i + 1 + (($alerts->currentPage() - 1) * $alerts->perPage())); ?></td>
                                    <td><?php echo e($alert->user->name ?? '-'); ?></td>
                                    <td><?php echo e($alert->type); ?></td>
                                    <td><?php echo e($alert->created_at->format('Y-m-d h:i A')); ?></td>
                                    <td><?php echo e($alert->status); ?></td>

                                    <td>
                                        <?php if($alert->status != 'read'): ?>
                                            <form method="POST" action="<?php echo e(route('alerts.markRead', $alert->id)); ?>" style="display:inline;">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-success">Mark as Read</button>
                                            </form>
                                        <?php else: ?>
                                            <i class="fas fa-eye text-primary"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($alert->latitude && $alert->longitude): ?>
                                            <button type="button"
                                                class="btn btn-sm btn-info"
                                                onclick="showAlertMap(<?php echo e($alert->latitude); ?>, <?php echo e($alert->longitude); ?>)">
                                                Show on Map
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No alerts found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <?php echo e($alerts->withQueryString()->links()); ?>

        </div>
    </div>

    <!-- Alert Map Modal -->
    <div class="modal fade" id="alertMapModal" tabindex="-1" aria-labelledby="alertMapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertMapModalLabel">Alert Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height:400px;">
                    <div id="alertMap" style="width:100%; height:100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script>
    function showAlertMap(lat, lng) {
        var modal = new bootstrap.Modal(document.getElementById('alertMapModal'));
        modal.show();

        setTimeout(function() {
            // Remove any previous map instance
            if (window.alertMapInstance) {
                window.alertMapInstance.remove();
            }
            window.alertMapInstance = L.map('alertMap').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(window.alertMapInstance);
            L.marker([lat, lng]).addTo(window.alertMapInstance)
                .bindPopup('Alert Location').openPopup();
        }, 300); // Wait for modal to render
    }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH K:\Laravel\security-master\resources\views/alerts.blade.php ENDPATH**/ ?>