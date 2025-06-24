<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-semibold mb-0">Incident Reports</h4>
        </div>
        
        <!-- Search and filter controls - properly aligned -->
        <div class="row mb-4 align-items-center">
            <div class="col-lg-4 col-md-12 mb-2 mb-lg-0 d-flex gap-2">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search by guard or incident type" aria-label="Search">
                </div>
            </div>
            
            <div class="col-lg-3 col-md-4 mb-2 mb-lg-0">
                <input type="date" class="form-control">
            </div>
            
            <div class="col-lg-2 col-md-4 mb-2 mb-lg-0">
                <button class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
            
            <div class="col-lg-3 col-md-4 mb-2 mb-lg-0">
                <button class="btn btn-outline-secondary w-100" id="exportCSV">
                    <i class="fas fa-download me-1"></i> Download Report
                </button>
            </div>
        </div>
        
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
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>John Doe</td>
                                <td>Unauthorized Entry</td>
                                <td>10:45 AM</td>
                                <td><span class="badge bg-danger rounded-pill px-3">New</span></td>
                                <td><i class="fas fa-eye text-primary"></i></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Ali Raza</td>
                                <td>Suspicious Behavior</td>
                                <td>Yesterday</td>
                                <td><span class="badge bg-warning text-dark rounded-pill px-3">Ongoing</span></td>
                                <td><i class="fas fa-eye text-primary"></i></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Ayesha Khan</td>
                                <td>Equipment Tampering</td>
                                <td>2 days ago</td>
                                <td><span class="badge bg-success rounded-pill px-3">Resolved</span></td>
                                <td><i class="fas fa-eye text-primary"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH K:\Laravel\security-master\resources\views/incidents.blade.php ENDPATH**/ ?>