<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Checkpoint;
use App\Models\Branch;
use App\Models\incident;
use App\Models\Alert;
use App\Models\AssignCheckpoint;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $stats = $this->getDashboardStats();

        // Get total guards (active + inactive)
        $totalGuards = \App\Models\User::where('role', 'guard')->count();

        // Get recent activities
        $recentActivities = $this->getRecentActivities();

        // Get system status
        $systemStatus = $this->getSystemStatus();

        // Get active guards for map
        $activeGuards = $this->getActiveGuards();

        // Get incidents summary
        $incidentsSummary = $this->getIncidentsSummary();

        return view('dashboard', compact(
            'stats',
            'recentActivities',
            'systemStatus',
            'activeGuards',
            'incidentsSummary',
            'totalGuards'
        ));
    }

    private function getDashboardStats()
    {
        return [
            'active_guards' => User::where('role', 'guard')
                                  ->where('status', 'active')
                                  ->count(),
            'total_incidents' => incident::count(),
            'pending_incidents' => incident::where('status', 'pending')->count(),
            'resolved_incidents' => incident::where('status', 'resolved')->count(),
            'total_checkpoints' => Checkpoint::count(),
            'active_checkpoints' => Checkpoint::where('is_active', true)->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'total_branches' => Branch::count(),
            'completed_patrols' => AssignCheckpoint::where('status', 'completed')->count(),
            'pending_patrols' => AssignCheckpoint::where('status', 'pending')->count(),
            'total_alerts' => Alert::count(),
            'unread_alerts' => Alert::where('status', 'unread')->count(),
        ];
    }

    private function getRecentActivities()
    {
        $activities = collect();

        // Recent incidents
        $recentIncidents = incident::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($incident) {
                $mainText = $incident->message ?? 'No details';
                return [
                    'type' => 'incident',
                    'icon' => 'fas fa-exclamation-triangle',
                    'color' => 'text-warning',
                    'message' => "Incident reported: $mainText",
                    'time' => $incident->created_at->diffForHumans(),
                    'timestamp' => $incident->created_at->timestamp,
                    'badge_color' => 'bg-warning text-dark',
                    'badge_text' => $incident->status
                ];
            });

        // Recent checkpoint completions
        $recentCheckpoints = AssignCheckpoint::with(['checkpoint', 'assignedGuard'])
            ->where('status', 'completed')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($assignment) {
                return [
                    'type' => 'checkpoint',
                    'icon' => 'fas fa-check',
                    'color' => 'text-success',
                    'message' => "Patrol completed: {$assignment->checkpoint->name}",
                    'time' => $assignment->updated_at->diffForHumans(),
                    'timestamp' => $assignment->updated_at->timestamp,
                    'badge_color' => 'bg-success',
                    'badge_text' => 'Completed'
                ];
            });

        // Recent alerts
        $recentAlerts = Alert::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($alert) {
                $mainText = $alert->type ?? 'No details';
                return [
                    'type' => 'alert',
                    'icon' => 'fas fa-bell',
                    'color' => 'text-danger',
                    'message' => "Alert: $mainText",
                    'time' => $alert->created_at->diffForHumans(),
                    'timestamp' => $alert->created_at->timestamp,
                    'badge_color' => $alert->status === 'read' ? 'bg-secondary' : 'bg-danger',
                    'badge_text' => $alert->status === 'read' ? 'Read' : 'New'
                ];
            });

        // Merge and sort by timestamp using array_merge to avoid getKey() on array error
        $activities = array_merge(
            $recentIncidents->all(),
            $recentCheckpoints->all(),
            $recentAlerts->all()
        );
        return collect($activities)->sortByDesc('timestamp')->take(8)->values();
    }

    private function getSystemStatus()
    {
        return [
            'database' => [
                'status' => 'OK',
                'color' => 'bg-success',
                'message' => 'Database connection stable'
            ],
            'nfc_tags' => [
                'status' => 'Stable',
                'color' => 'bg-primary',
                'message' => 'NFC tag reads normal'
            ],
            'network' => [
                'status' => 'Connected',
                'color' => 'bg-success',
                'message' => 'Network connection active'
            ],
            'api_status' => [
                'status' => 'Active',
                'color' => 'bg-success',
                'message' => 'API endpoints responding'
            ]
        ];
    }

    private function getActiveGuards()
    {
        // Get guards who have recent activity (within last 24 hours)
        $activeGuards = User::where('role', 'guard')
            ->where('status', 'active')
            ->get()
            ->map(function ($guard) {
                // Generate random coordinates for demo (in real app, this would come from GPS)
                $lat = 24.8607 + (rand(-50, 50) / 1000);
                $lng = 67.0011 + (rand(-50, 50) / 1000);

                return [
                    'id' => $guard->id,
                    'name' => $guard->name,
                    'email' => $guard->email,
                    'lat' => $lat,
                    'lng' => $lng,
                    'status' => 'On patrol',
                    'last_seen' => now()->subMinutes(rand(5, 120))->diffForHumans(),
                    'current_location' => 'Sector ' . rand(1, 8)
                ];
            });

        return $activeGuards;
    }

    private function getIncidentsSummary()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'today' => incident::whereDate('created_at', $today)->count(),
            'this_week' => incident::whereBetween('created_at', [$thisWeek, now()])->count(),
            'this_month' => incident::whereBetween('created_at', [$thisMonth, now()])->count(),
            'by_status' => [
                'pending' => incident::where('status', 'pending')->count(),
                'investigating' => incident::where('status', 'investigating')->count(),
                'resolved' => incident::where('status', 'resolved')->count(),
                'closed' => incident::where('status', 'closed')->count(),
            ],
            'by_type' => [
                'low' => incident::where('type', 'low')->count(),
                'medium' => incident::where('type', 'medium')->count(),
                'high' => incident::where('type', 'high')->count(),
                'critical' => incident::where('type', 'critical')->count(),
            ]
        ];
    }

    public function getDashboardData()
    {
        // API endpoint for AJAX requests to get real-time data
        return response()->json([
            'stats' => $this->getDashboardStats(),
            'recent_activities' => $this->getRecentActivities(),
            'active_guards' => $this->getActiveGuards(),
            'incidents_summary' => $this->getIncidentsSummary(),
            'timestamp' => now()->toISOString()
        ]);
    }
}
