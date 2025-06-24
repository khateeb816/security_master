<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-semibold mb-0">Live Alerts</h4>

            <form class="d-flex align-items-center gap-2">
                <input type="text" class="form-control form-control-sm" placeholder="Search alerts or guards">
                <input type="date" class="form-control form-control-sm">
                <button class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i></button>
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
                                <th>Severity</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <tr>
                                <td>1</td>
                                <td>John Doe</td>
                                <td>Panic Button</td>
                                <td>11:02 AM</td>
                                <td><span class="badge bg-danger">Critical</span></td>
                                <td><i class="fas fa-eye text-primary"></i></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Ali Raza</td>
                                <td>Missed Patrol</td>
                                <td>10:55 AM</td>
                                <td><span class="badge bg-warning text-dark">High</span></td>
                                <td><i class="fas fa-eye text-primary"></i></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Ayesha Khan</td>
                                <td>Low Battery</td>
                                <td>10:12 AM</td>
                                <td><span class="badge bg-info">Medium</span></td>
                                <td><i class="fas fa-eye text-primary"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH K:\Laravel\security-master\resources\views/alerts.blade.php ENDPATH**/ ?>