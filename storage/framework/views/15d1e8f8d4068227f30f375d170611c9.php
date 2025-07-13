<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Active Guards</p>
                            <h4 class="fw-bold mb-0"><?php echo e($stats['active_guards']); ?></h4>
                            <small class="text-muted">Total: <?php echo e($totalGuards); ?></small>
                        </div>
                        <div class="text-primary fs-2">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Incidents</p>
                            <h4 class="fw-bold mb-0"><?php echo e($stats['total_incidents']); ?></h4>
                            <small class="text-warning"><?php echo e($stats['pending_incidents']); ?> pending</small>
                        </div>
                        <div class="text-warning fs-2">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Active Checkpoints</p>
                            <h4 class="fw-bold mb-0"><?php echo e($stats['active_checkpoints']); ?></h4>
                            <small class="text-muted">Total: <?php echo e($stats['total_checkpoints']); ?></small>
                        </div>
                        <div class="text-info fs-2">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Completed Patrols</p>
                            <h4 class="fw-bold mb-0"><?php echo e($stats['completed_patrols']); ?></h4>
                            <small class="text-muted"><?php echo e($stats['pending_patrols']); ?> pending</small>
                        </div>
                        <div class="text-success fs-2">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Clients</p>
                            <h5 class="fw-bold mb-0"><?php echo e($stats['total_clients']); ?></h5>
                        </div>
                        <div class="text-secondary">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Branches</p>
                            <h5 class="fw-bold mb-0"><?php echo e($stats['total_branches']); ?></h5>
                        </div>
                        <div class="text-secondary">
                            <i class="fas fa-sitemap"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Alerts</p>
                            <h5 class="fw-bold mb-0"><?php echo e($stats['total_alerts']); ?></h5>
                            <small class="text-danger"><?php echo e($stats['unread_alerts']); ?> unread</small>
                        </div>
                        <div class="text-secondary">
                            <i class="fas fa-bell"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Resolved Incidents</p>
                            <h5 class="fw-bold mb-0"><?php echo e($stats['resolved_incidents']); ?></h5>
                        </div>
                        <div class="text-secondary">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row g-4">
            <div class="col-md-8">
                <div class="card p-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-semibold">Recent Activities</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshActivities()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <div id="activitiesList">
                        <ul class="list-group list-group-flush">
                            <?php $__empty_1 = true; $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="<?php echo e($activity['icon']); ?> <?php echo e($activity['color']); ?> me-2"></i>
                                        <?php echo e($activity['message']); ?>

                                    </span>
                                    <span class="badge <?php echo e($activity['badge_color']); ?> rounded-pill"><?php echo e($activity['badge_text']); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <li class="list-group-item text-center text-muted">
                                    <i class="fas fa-info-circle me-2"></i> No recent activities
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 shadow-sm">
                    <h5 class="fw-semibold mb-3">System Status</h5>
                    <?php $__currentLoopData = $systemStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p class="mb-2">
                            <i class="fas fa-<?php echo e($key === 'database' ? 'database' : ($key === 'nfc_tags' ? 'signal' : ($key === 'network' ? 'wifi' : 'api'))); ?> text-secondary me-2"></i>
                            <?php echo e(ucfirst(str_replace('_', ' ', $key))); ?>:
                            <span class="badge <?php echo e($status['color']); ?>"><?php echo e($status['status']); ?></span>
                        </p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <hr class="my-3">

                    <h6 class="fw-semibold mb-2">Incidents Summary</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted">Today</small>
                            <div class="fw-bold"><?php echo e($incidentsSummary['today']); ?></div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">This Week</small>
                            <div class="fw-bold"><?php echo e($incidentsSummary['this_week']); ?></div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">This Month</small>
                            <div class="fw-bold"><?php echo e($incidentsSummary['this_month']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i> Live Guard Tracker
                        </h5>
                        <div class="d-flex gap-2">
                            <select id="guardSelect" class="form-select form-select-sm" style="max-width: 250px;">
                                <option value="all">Show All Guards</option>
                                <?php $__currentLoopData = $activeGuards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($guard['name']); ?>"><?php echo e($guard['name']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <button class="btn btn-sm btn-outline-primary" onclick="refreshMap()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    <div id="guardMap" style="height: 400px; border-radius: 12px; overflow: hidden;"></div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let map;
    let markers = [];
    let guards = <?php echo json_encode($activeGuards, 15, 512) ?>;

    document.addEventListener("DOMContentLoaded", function () {
        initializeMap();
        setupEventListeners();

        // Auto-refresh every 30 seconds
        setInterval(refreshDashboardData, 30000);
    });

    function initializeMap() {
        map = L.map('guardMap').setView([24.8607, 67.0011], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        addGuardMarkers();
    }

    function addGuardMarkers() {
        // Clear existing markers
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        guards.forEach(guard => {
            const marker = L.marker([guard.lat, guard.lng])
                .bindPopup(`
                    <strong>${guard.name}</strong><br>
                    Status: ${guard.status}<br>
                    Location: ${guard.current_location}<br>
                    Last seen: ${guard.last_seen}
                `);
            marker.addTo(map);
            markers.push({ name: guard.name, marker });
        });
    }

    function setupEventListeners() {
        document.getElementById('guardSelect').addEventListener('change', function () {
            const selected = this.value;

            markers.forEach(({ name, marker }) => {
                if (selected === 'all' || name === selected) {
                    marker.addTo(map);
                    if (name === selected) {
                        map.setView(marker.getLatLng(), 16);
                        marker.openPopup();
                    }
                } else {
                    map.removeLayer(marker);
                }
            });
        });
    }

    function refreshDashboardData() {
        fetch('<?php echo e(route("dashboard.data")); ?>')
            .then(response => response.json())
            .then(data => {
                // Update stats
                updateStats(data.stats);

                // Update activities
                updateActivities(data.recent_activities);

                // Update guards
                guards = data.active_guards;
                addGuardMarkers();

                console.log('Dashboard data refreshed at:', new Date().toLocaleTimeString());
            })
            .catch(error => {
                console.error('Error refreshing dashboard data:', error);
            });
    }

    function updateStats(stats) {
        // Update the stats cards with new data
        document.querySelector('.card:nth-child(1) h4').textContent = stats.active_guards;
        document.querySelector('.card:nth-child(2) h4').textContent = stats.total_incidents;
        document.querySelector('.card:nth-child(3) h4').textContent = stats.active_checkpoints;
        document.querySelector('.card:nth-child(4) h4').textContent = stats.completed_patrols;
    }

    function updateActivities(activities) {
        const activitiesList = document.getElementById('activitiesList');
        let html = '<ul class="list-group list-group-flush">';

        if (activities.length > 0) {
            activities.forEach(activity => {
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="${activity.icon} ${activity.color} me-2"></i>
                            ${activity.message}
                        </span>
                        <span class="badge ${activity.badge_color} rounded-pill">${activity.badge_text}</span>
                    </li>
                `;
            });
        } else {
            html += `
                <li class="list-group-item text-center text-muted">
                    <i class="fas fa-info-circle me-2"></i> No recent activities
                </li>
            `;
        }

        html += '</ul>';
        activitiesList.innerHTML = html;
    }

    function refreshActivities() {
        fetch('<?php echo e(route("dashboard.data")); ?>')
            .then(response => response.json())
            .then(data => {
                updateActivities(data.recent_activities);
            })
            .catch(error => {
                console.error('Error refreshing activities:', error);
            });
    }

    function refreshMap() {
        fetch('<?php echo e(route("dashboard.data")); ?>')
            .then(response => response.json())
            .then(data => {
                guards = data.active_guards;
                addGuardMarkers();
            })
            .catch(error => {
                console.error('Error refreshing map:', error);
            });
    }
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH K:\Laravel\security-master\resources\views\dashboard.blade.php ENDPATH**/ ?>