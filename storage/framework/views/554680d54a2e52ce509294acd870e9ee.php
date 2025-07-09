<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-semibold mb-0">Incident Reports</h4>
        </div>

        <!-- Search and filter controls - properly aligned -->
        <form method="GET" action="<?php echo e(route('incidents')); ?>" class="row mb-4 align-items-center">
            <div class="col-lg-4 col-md-12 mb-2 mb-lg-0 d-flex gap-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by guard or incident type" value="<?php echo e(request('search')); ?>">
                </div>
            </div>

            <div class="col-lg-3 col-md-4 mb-2 mb-lg-0">
                <input type="date" name="date" class="form-control" value="<?php echo e(request('date')); ?>">
            </div>

            <div class="col-lg-2 col-md-4 mb-2 mb-lg-0">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>

            <div class="col-lg-3 col-md-4 mb-2 mb-lg-0">
                <button class="btn btn-outline-secondary w-100" id="exportCSV" type="button">
                    <i class="fas fa-download me-1"></i> Download Report
                </button>
            </div>
        </form>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="incidentReportsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Guard Name</th>
                                <th>Incident Type</th>
                                <th>Reported At</th>
                                <th>Status</th>
                                <th>Message</th>
                                <th>Map</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($i + 1 + (($incidents->currentPage() - 1) * $incidents->perPage())); ?></td>
                                    <td><?php echo e($incident->user->name ?? '-'); ?></td>
                                    <td><?php echo e($incident->type); ?></td>
                                    <td><?php echo e($incident->created_at->format('Y-m-d h:i A')); ?></td>
                                    <td>
                                        <form method="POST" action="<?php echo e(route('incidents.updateStatus', $incident->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                                <option value="active" <?php echo e($incident->status === 'active' ? 'selected' : ''); ?>>Active</option>
                                                <option value="inactive" <?php echo e($incident->status === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><?php echo e($incident->message); ?></td>
                                    <td>
                                        <?php if($incident->latitude && $incident->longitude): ?>
                                            <button type="button"
                                                class="btn btn-sm btn-info"
                                                onclick="showIncidentMap(<?php echo e($incident->latitude); ?>, <?php echo e($incident->longitude); ?>)">
                                                Show on Map
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                        <button type="button"
                                            class="btn btn-sm btn-primary mt-1"
                                            onclick="showIncidentMedia(<?php echo e($incident->id); ?>, '<?php echo e($incident->images); ?>', '<?php echo e($incident->videos); ?>', '<?php echo e($incident->audios); ?>')">
                                            Show Media
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No incidents found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <?php echo e($incidents->withQueryString()->links()); ?>

        </div>
    </div>

    <!-- Include JavaScript for export functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSV Export functionality
            document.getElementById('exportCSV').addEventListener('click', function(e) {
                e.preventDefault();
                exportTableToCSV('incident_reports.csv');
            });

            // Function to export table data to CSV
            function exportTableToCSV(filename) {
                const table = document.getElementById('incidentReportsTable');
                let csv = [];
                const rows = table.querySelectorAll('tr');

                for (let i = 0; i < rows.length; i++) {
                    const row = [], cols = rows[i].querySelectorAll('td, th');

                    for (let j = 0; j < cols.length - 1; j++) { // Skip the last column (Notes icon)
                        // Get the text content, handling badge elements
                        let content = cols[j].innerText || cols[j].textContent;

                        // Replace any commas in the content with spaces to avoid CSV format issues
                        content = content.replace(/,/g, ' ');

                        // Add quotes around the content and add to row
                        row.push('"' + content + '"');
                    }

                    csv.push(row.join(','));
                }

                // Download CSV file
                downloadCSV(csv.join('\n'), filename);
            }

            function downloadCSV(csv, filename) {
                const csvFile = new Blob([csv], {type: 'text/csv'});
                const downloadLink = document.createElement('a');

                // Create a download link
                downloadLink.download = filename;
                downloadLink.href = window.URL.createObjectURL(csvFile);
                downloadLink.style.display = 'none';

                // Add the link to the DOM, trigger the download, and remove the link
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            }
        });
    </script>

    <!-- Incident Map Modal -->
    <div class="modal fade" id="incidentMapModal" tabindex="-1" aria-labelledby="incidentMapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="incidentMapModalLabel">Incident Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height:400px;">
                    <div id="incidentMap" style="width:100%; height:100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incident Media Modal -->
    <div class="modal fade" id="incidentMediaModal" tabindex="-1" aria-labelledby="incidentMediaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="incidentMediaModalLabel">Incident Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="incidentMediaContent">
                    <!-- Media will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script>
    function showIncidentMap(lat, lng) {
        var modal = new bootstrap.Modal(document.getElementById('incidentMapModal'));
        modal.show();

        setTimeout(function() {
            // Remove any previous map instance
            if (window.incidentMapInstance) {
                window.incidentMapInstance.remove();
            }
            window.incidentMapInstance = L.map('incidentMap').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(window.incidentMapInstance);
            L.marker([lat, lng]).addTo(window.incidentMapInstance)
                .bindPopup('Incident Location').openPopup();
        }, 300); // Wait for modal to render
    }

    function showIncidentMedia(id, images, videos, audios) {
        let content = '';
        function safeParse(json) {
            try { return JSON.parse(json); } catch { return null; }
        }
        // Images
        const imageObj = safeParse(images);
        if (imageObj && imageObj.path) {
            content += `<div><strong>Image:</strong><br><img src='${imageObj.path}' alt='Incident Image' style='max-width:100%;height:auto;'/></div><hr>`;
        }
        // Videos
        const videoObj = safeParse(videos);
        if (videoObj && videoObj.path) {
            content += `<div><strong>Video:</strong><br><video controls style='max-width:100%;height:auto;'><source src='${videoObj.path}' type='${videoObj.type}'></video></div><hr>`;
        }
        // Audios
        const audioObj = safeParse(audios);
        if (audioObj && audioObj.path) {
            content += `<div><strong>Audio:</strong><br><audio controls style='width:100%;'><source src='${audioObj.path}' type='${audioObj.type}'></audio></div>`;
        }
        if (!content) {
            content = '<div class="text-muted">No media available for this incident.</div>';
        }
        document.getElementById('incidentMediaContent').innerHTML = content;
        var modal = new bootstrap.Modal(document.getElementById('incidentMediaModal'));
        modal.show();
    }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH K:\Laravel\security-master\resources\views/incidents.blade.php ENDPATH**/ ?>