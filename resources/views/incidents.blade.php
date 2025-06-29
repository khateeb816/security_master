@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-semibold mb-0">Incident Reports</h4>
        </div>

        <!-- Search and filter controls - properly aligned -->
        <form method="GET" action="{{ route('incidents') }}" class="row mb-4 align-items-center">
            <div class="col-lg-4 col-md-12 mb-2 mb-lg-0 d-flex gap-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by guard or incident type" value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-lg-3 col-md-4 mb-2 mb-lg-0">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
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
                            @forelse($incidents as $i => $incident)
                                <tr>
                                    <td>{{ $i + 1 + (($incidents->currentPage() - 1) * $incidents->perPage()) }}</td>
                                    <td>{{ $incident->user->name ?? '-' }}</td>
                                    <td>{{ $incident->type }}</td>
                                    <td>{{ $incident->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('incidents.updateStatus', $incident->id) }}">
                                            @csrf
                                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                                <option value="active" {{ $incident->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $incident->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>{{ $incident->message }}</td>
                                    <td>
                                        @if($incident->latitude && $incident->longitude)
                                            <button type="button"
                                                class="btn btn-sm btn-info"
                                                onclick="showIncidentMap({{ $incident->latitude }}, {{ $incident->longitude }})">
                                                Show on Map
                                            </button>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No incidents found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $incidents->withQueryString()->links() }}
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
    </script>
@endsection
