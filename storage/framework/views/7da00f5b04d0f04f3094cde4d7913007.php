<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-semibold mb-0">Patrol Logs</h4>
        </div>

        <!-- Search and filter controls - properly aligned -->
        <form method="GET" action="<?php echo e(route('patrol.logs')); ?>" class="row mb-4 align-items-center">
            <div class="col-lg-4 col-md-12 mb-2 mb-lg-0 d-flex gap-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by Guard or Checkpoint" value="<?php echo e(request('search')); ?>">
                </div>
            </div>

            <div class="col-lg-3 col-md-4 mb-2 mb-lg-0">
                <input type="date" name="date" class="form-control" value="<?php echo e(request('date')); ?>">
            </div>

            <div class="col-lg-2 col-md-4 mb-2 mb-lg-0">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Filter
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
                    <table class="table table-hover align-middle mb-0" id="patrolLogsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Guard Name</th>
                                <th>Checkpoint</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Map</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($i + 1); ?></td>
                                    <td><?php echo e($log->user_guard->name ?? '-'); ?></td>
                                    <td><?php echo e($log->checkpoint->name ?? '-'); ?></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($log->date_to_check)->format('Y-m-d')); ?></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($log->time_to_check)->format('h:i A')); ?></td>
                                    <td>
                                        <?php if($log->status === 'Completed'): ?>
                                            <span class="badge bg-success rounded-pill px-3">Completed</span>
                                        <?php elseif($log->status === 'Missed'): ?>
                                            <span class="badge bg-danger rounded-pill px-3">Missed</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3"><?php echo e($log->status); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <?php if($log->checkpoint && $log->checkpoint->latitude && $log->checkpoint->longitude): ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    onclick="showPatrolLogMap(<?php echo e($log->checkpoint->latitude); ?>, <?php echo e($log->checkpoint->longitude); ?>, 'Checkpoint Location')">
                                                    Checkpoint Map
                                                </button>
                                            <?php endif; ?>
                                            <?php if($log->latitude && $log->longitude): ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-info"
                                                    onclick="showPatrolLogMap(<?php echo e($log->latitude); ?>, <?php echo e($log->longitude); ?>, 'Checked Location')">
                                                    Checked Map
                                                </button>
                                            <?php endif; ?>
                                            <?php if(!($log->checkpoint && $log->checkpoint->latitude && $log->checkpoint->longitude) && !($log->latitude && $log->longitude)): ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if(strtolower($log->status) !== 'pending'): ?>
                                            <button type="button"
                                                class="btn btn-sm btn-primary mt-1"
                                                onclick="showPatrolMedia('<?php echo e($log->images); ?>', '<?php echo e($log->videos); ?>', '<?php echo e($log->audios); ?>')">
                                                Show Media
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No patrol logs found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <?php echo e($logs->withQueryString()->links()); ?>

        </div>
    </div>

    <!-- Include JavaScript for export functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSV Export functionality
            document.getElementById('exportCSV').addEventListener('click', function(e) {
                e.preventDefault();
                exportTableToCSV('patrol_logs.csv');
            });

            // Function to export table data to CSV
            function exportTableToCSV(filename) {
                const table = document.getElementById('patrolLogsTable');
                let csv = [];
                const rows = table.querySelectorAll('tr');

                for (let i = 0; i < rows.length; i++) {
                    const row = [], cols = rows[i].querySelectorAll('td, th');

                    for (let j = 0; j < cols.length; j++) {
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

    <!-- Patrol Log Map Modal -->
    <div class="modal fade" id="patrolLogMapModal" tabindex="-1" aria-labelledby="patrolLogMapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="patrolLogMapModalLabel">Patrol Log Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height:400px;">
                    <div id="patrolLogMap" style="width:100%; height:100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patrol Media Modal -->
    <div class="modal fade" id="patrolMediaModal" tabindex="-1" aria-labelledby="patrolMediaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="patrolMediaModalLabel">Patrol Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="patrolMediaContent">
                    <!-- Media will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script>
    function showPatrolLogMap(lat, lng, label) {
        var modal = new bootstrap.Modal(document.getElementById('patrolLogMapModal'));
        modal.show();

        setTimeout(function() {
            // Remove any previous map instance
            if (window.patrolLogMapInstance) {
                window.patrolLogMapInstance.remove();
            }
            window.patrolLogMapInstance = L.map('patrolLogMap').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(window.patrolLogMapInstance);
            L.marker([lat, lng]).addTo(window.patrolLogMapInstance)
                .bindPopup(label || 'Location').openPopup();
        }, 300); // Wait for modal to render
    }

    function showPatrolMedia(images, videos, audios) {
        let content = '';
        function safeParse(json) {
            try { return JSON.parse(json); } catch { return null; }
        }
        // Images
        const imageObj = safeParse(images);
        if (imageObj && imageObj.path) {
            content += `<div><strong>Image:</strong><br><img src='${imageObj.path}' alt='Patrol Image' style='max-width:100%;height:auto;'/></div><hr>`;
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
            content = '<div class="text-muted">No media available for this patrol log.</div>';
        }
        document.getElementById('patrolMediaContent').innerHTML = content;
        var modal = new bootstrap.Modal(document.getElementById('patrolMediaModal'));
        modal.show();
    }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH K:\Laravel\security-master\resources\views/patrol_logs.blade.php ENDPATH**/ ?>