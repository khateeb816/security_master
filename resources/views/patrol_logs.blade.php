@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-semibold mb-0">Patrol Logs</h4>
        </div>

        <!-- Search and filter controls - properly aligned -->
        <form method="GET" action="{{ route('patrol.logs') }}" class="row mb-4 align-items-center">
            <div class="col-lg-4 col-md-12 mb-2 mb-lg-0 d-flex gap-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by Guard or Checkpoint" value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-lg-3 col-md-4 mb-2 mb-lg-0">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
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
                <a href="{{ route('patrol.logs.pdf', request()->all()) }}" class="btn btn-outline-danger w-100 ms-2" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                </a>
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
                                <th>Round</th>
                                <th>Type</th>
                                <th>Map</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $i => $log)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $log->user->name ?? '-' }}</td>
                                    <td>{{ $log->checkpoint->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($log->date)->format('Y-m-d') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($log->time)->format('h:i A') }}</td>
                                    <td>{{ $log->round ?? '-' }}</td>
                                    <td>{{ $log->type ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if($log->checkpoint && $log->checkpoint->latitude && $log->checkpoint->longitude)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    onclick="showPatrolLogMap(
                                                        '{{ $log->checkpoint->latitude ?? '' }}',
                                                        '{{ $log->checkpoint->longitude ?? '' }}',
                                                        `Check Point Name: {{ $log->checkpoint->name ?? '---' }}<br>
                                                        Date: {{ \Carbon\Carbon::parse($log->created_at)->format('d-m-Y H:i:s') }}<br>
                                                        Guard: {{ $log->user->name ?? '-' }} ({{ $log->user->id ?? '' }})<br>
                                                        Lat/Lon: {{ $log->checkpoint->latitude ?? '' }}/{{ $log->checkpoint->longitude ?? '' }}`
                                                    )">
                                                    Checkpoint Map
                                                </button>
                                            @endif
                                            @php
                                                $distance = null;
                                                if ($log->checkpoint && $log->checkpoint->latitude && $log->checkpoint->longitude && $log->latitude && $log->longitude) {
                                                    $distance = \App\Helpers\DistanceHelper::calculateDistance($log->checkpoint->latitude, $log->checkpoint->longitude, $log->latitude, $log->longitude);
                                                }
                                            @endphp
                                            @if($log->latitude && $log->longitude)
                                                <button type="button"
                                                    class="btn btn-sm btn-info"
                                                    onclick="showPatrolLogMap(
                                                        '{{ $log->latitude }}',
                                                        '{{ $log->longitude }}',
                                                        `Check Point Name: {{ $log->checkpoint->name ?? '---' }}<br>
                                                        Date: {{ \Carbon\Carbon::parse($log->created_at)->format('d-m-Y H:i:s') }}<br>
                                                        Guard: {{ $log->user->name ?? '-' }} ({{ $log->user->id ?? '' }})<br>
                                                        Lat/Lon: {{ $log->latitude }}/{{ $log->longitude }}`
                                                    )">
                                                    Checked Map
                                                </button>
                                            @endif
                                            @if(!($log->checkpoint && $log->checkpoint->latitude && $log->checkpoint->longitude) && !($log->latitude && $log->longitude))
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No patrol logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- Remove pagination links since logs is a collection and not paginated --}}
        {{-- {{ $logs->withQueryString()->links() }} --}}
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
    </script>
@endsection
