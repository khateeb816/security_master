

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        
        <div class="row g-4 mb-4">

            <div class="col-md-4">
                <div class="card shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Active Guards</p>
                            <h4 class="fw-bold mb-0">12</h4>
                        </div>
                        <div class="text-primary fs-2">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Incidents</p>
                            <h4 class="fw-bold mb-0">3</h4>
                        </div>
                        <div class="text-warning fs-2">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Completed Patrols</p>
                            <h4 class="fw-bold mb-0">34</h4>
                        </div>
                        <div class="text-success fs-2">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        
        <div class="row g-4">
            <div class="col-md-8">
                <div class="card p-4 shadow-sm">
                    <h5 class="mb-3 fw-semibold">Activity Snapshot</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-map-marker-alt text-primary me-2"></i> Checkpoint missed by Guard #A12</span>
                            <span class="badge bg-danger rounded-pill">10:12 AM</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-bolt text-warning me-2"></i> Incident reported: Unauthorized Entry</span>
                            <span class="badge bg-warning text-dark rounded-pill">Yesterday</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-check text-success me-2"></i> Patrol completed: Sector 5</span>
                            <span class="badge bg-success rounded-pill">Today</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 shadow-sm">
                    <h5 class="fw-semibold mb-3">System Status</h5>
                    <p class="mb-2"><i class="fas fa-database text-secondary me-2"></i> DB Sync: <span class="badge bg-success">OK</span></p>
                    <p class="mb-2"><i class="fas fa-signal text-secondary me-2"></i> NFC Tag Reads: <span class="badge bg-primary">Stable</span></p>
                    <p class="mb-0"><i class="fas fa-wifi text-secondary me-2"></i> Network: <span class="badge bg-success">Connected</span></p>
                </div>
            </div>
        </div>
    </div>
    
<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Live Guard Tracker</h5>

                    <select id="guardSelect" class="form-select form-select-sm" style="max-width: 250px;">
                        <option value="all">Show All Guards</option>
                        <option value="John Doe">John Doe</option>
                        <option value="Ali Raza">Ali Raza</option>
                        <option value="Ayesha Khan">Ayesha Khan</option>
                    </select>
                </div>

                <div id="guardMap" style="height: 400px; border-radius: 12px; overflow: hidden;"></div>
            </div>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var map = L.map('guardMap').setView([24.8607, 67.0011], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Dummy guard data
        const guards = [
            { name: "John Doe", lat: 24.8615, lng: 67.0099 },
            { name: "Ali Raza", lat: 24.8582, lng: 67.0035 },
            { name: "Ayesha Khan", lat: 24.8649, lng: 67.0120 }
        ];

        // Add markers and store references
        let markers = [];

        guards.forEach(guard => {
            const marker = L.marker([guard.lat, guard.lng])
                .bindPopup(`<strong>${guard.name}</strong><br>On patrol`);
            marker.addTo(map);
            markers.push({ name: guard.name, marker });
        });

        // Filter logic
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
    });
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Bilawal\CascadeProjects\remote_project\resources\views/dashboard.blade.php ENDPATH**/ ?>