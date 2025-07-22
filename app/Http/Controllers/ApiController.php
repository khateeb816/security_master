<?php

namespace App\Http\Controllers;

use App\Models\incident;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\DistanceHelper;
use App\Models\AssignCheckpoint;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Alert;
use App\Models\ClearedCheckpoints;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        //NFC Login
        if ($request->has('nfc_uid')) {
            $validated = $request->validate([
                'nfc_uid' => 'required',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric'
            ]);

            $user = User::where('nfc_uid', $validated['nfc_uid'])->first();
            if (!$user) {
                return $this->errorResponse('Invalid NFC UID', 401);
            }
        }
        //Email password login
        else {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric'
            ]);

            if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
                return $this->errorResponse('Invalid credentials', 401);
            }
            $user = Auth::user();
        }

        // Shared logic for both login types
        $today = now()->toDateString();
        $checkpoints = AssignCheckpoint::where('guard_id', $user->id)
            ->whereDate('date_from', '<=', $today)
            ->whereDate('date_to', '>=', $today)
            ->with('checkpoint')
            ->orderBy('date_from', 'asc')
            ->get();

        foreach ($checkpoints as $assignCheckpoint) {
            $checkpoint = $assignCheckpoint->checkpoint;
            if (!$checkpoint) continue;

            $distance = DistanceHelper::calculateDistance(
                $validated['latitude'],
                $validated['longitude'],
                $checkpoint->latitude,
                $checkpoint->longitude
            );

            if ($distance <= $checkpoint->radius) {
                $token = $user->createToken('auth-token')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'message' => 'Login Successful',
                    'user' => $user
                ], 200);
            }
        }

        return $this->errorResponse('Not in checkpoint area', 403);
    }

    private function errorResponse($message, $code)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }

    public function showCheckpoints()
    {
        // Verify Sanctum token by adding middleware
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired token'
                ], 401);
            }

            $today = now()->toDateString();
            $checkpoints = AssignCheckpoint::where('guard_id', $user->id)
                ->whereDate('date_from', '<=', $today)
                ->whereDate('date_to', '>=', $today)
                ->with('checkpoint')
                ->orderBy('date_from', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'checkpoints' => $checkpoints
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed'
            ], 401);
        }
    }

    public function clearCheckpoint(Request $request)
    {
        $user = auth('sanctum')->user();
        $request->validate([
            'checkpoint_id' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'time' => 'required',
            'date' => 'required',
            'type' => 'required',
            'value' => 'nullable'
        ]);

        try {
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired token'
                ], 401);
            }

            $assignCheckpoint = AssignCheckpoint::where('id', $request->checkpoint_id)
                ->where('guard_id', $user->id)
                ->with('checkpoint')
                ->first();

            if (!$assignCheckpoint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkpoint not found or not assigned to you for today'
                ], 404);
            }

            // Calculate distance between current location and checkpoint
            $distance = DistanceHelper::calculateDistance(
                $request->latitude,
                $request->longitude,
                $assignCheckpoint->checkpoint->latitude,
                $assignCheckpoint->checkpoint->longitude
            );

            // Check if within radius
            if ($distance > $assignCheckpoint->checkpoint->radius) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not within the checkpoint area',
                    'distance' => $distance,
                    'allowed_radius' => $assignCheckpoint->checkpoint->radius
                ], 400);
            }


            $round = ClearedCheckpoints::where('user_id', $user->id)
                ->where('checkpoint_id', $request->checkpoint_id)
                ->count();

            // Update checkpoint details
            $clearedCheckpoint = new ClearedCheckpoints();
            $clearedCheckpoint->checkpoint_id = $request->checkpoint_id;
            $clearedCheckpoint->user_id = $user->id;
            $clearedCheckpoint->round = $round + 1;
            $clearedCheckpoint->value = $request->value;
            $clearedCheckpoint->longitude = $request->longitude;
            $clearedCheckpoint->latitude = $request->latitude;
            $clearedCheckpoint->status = 'cleared';
            $clearedCheckpoint->time = $request->time;
            $clearedCheckpoint->date = $request->date;
            $clearedCheckpoint->type = $request->type;
            $clearedCheckpoint->save();

            return response()->json([
                'success' => true,
                'message' => 'Checkpoint cleared successfully',
                'checkpoint' => $clearedCheckpoint
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear checkpoint: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeIncident(Request $request)
    {
        $user = auth('sanctum')->user();
        $request->validate([
            'longitude' => 'required',
            'latitude' => 'required',
            'time' => 'required',
            'image' => 'nullable',
            'video' => 'nullable',
            'audio' => 'nullable',
            'type' => 'required',
            'message' => 'nullable'
        ]);

        try {
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            // Store full image json (handle base64)
            if (isset($request->image['base64']) && isset($request->image['type'])) {
                $imageType = $request->image['type'];
                $base64String = $request->image['base64'];
                $extension = explode('/', $imageType)[1] ?? 'bin';
                $fileName = uniqid('image_') . '.' . $extension;
                $filePath = 'uploads/incidents/' . $fileName;
                file_put_contents(public_path($filePath), base64_decode($base64String));
                $imageJson = json_encode([
                    'type' => $imageType,
                    'path' => $filePath
                ]);
            } else {
                $imageJson = null;
            }

            // Store full videos json (handle base64)
            if (isset($request->video['base64']) && isset($request->video['type'])) {
                $videoType = $request->video['type'];
                $base64String = $request->video['base64'];
                $extension = explode('/', $videoType)[1] ?? 'bin';
                $fileName = uniqid('video_') . '.' . $extension;
                $filePath = 'uploads/incidents/' . $fileName;
                file_put_contents(public_path($filePath), base64_decode($base64String));
                $videoJson = json_encode([
                    'type' => $videoType,
                    'path' => $filePath
                ]);
            } else {
                $videoJson = null;
            }

            // Store full image json (handle base64)
            if (isset($request->audio['base64']) && isset($request->audio['type'])) {
                $audioType = $request->audio['type'];
                $base64String = $request->audio['base64'];
                $extension = explode('/', $audioType)[1] ?? 'bin';
                $fileName = uniqid('audio_') . '.' . $extension;
                $filePath = 'uploads/incidents/' . $fileName;
                file_put_contents(public_path($filePath), base64_decode($base64String));
                $audioJson = json_encode([
                    'type' => $audioType,
                    'path' => $filePath
                ]);
            } else {
                $audioJson = null;
            }
            $incident = new \App\Models\incident();
            $incident->longitude = $request->longitude;
            $incident->latitude = $request->latitude;
            $incident->time = $request->time;
            $incident->images = $imageJson;
            $incident->videos = $videoJson;
            $incident->audios = $audioJson;
            $incident->type = $request->type;
            $incident->status = 'active';
            $incident->message = $request->message;
            $incident->user_id = $user->id;
            $incident->save();

            return response()->json([
                'success' => true,
                'message' => 'Incident reported successfully',
                'incident' => $incident
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store incident: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showIncidents()
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $incidents = incident::with('user')
                ->whereDate('created_at', today())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'incidents' => $incidents
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch incidents: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeAlert(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'longitude' => 'required',
            'latitude' => 'required',
            'time' => 'required',
            'type' => 'required',
            'message' => 'nullable'
        ]);

        try {
            $alert = Alert::create([
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'time' => $request->time,
                'type' => $request->type,
                'message' => $request->message,
                'user_id' => $user->id,
                'status' => 'unread'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alert created successfully',
                'alert' => $alert
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create alert: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showCheckpointsbyDate(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'date' => 'required|date_format:Y-m-d'
        ]);

        $checkpoints = ClearedCheckpoints::where('user_id', $user->id)
            ->where('date', Carbon::createFromFormat('Y-m-d', $request->date)->toDateString())
            ->with('checkpoint')
            ->get()
            ->map(function ($clearedCheckpoint) {
                return [
                    'id' => $clearedCheckpoint->id,
                    'name' => $clearedCheckpoint->checkpoint->name,
                    'description' => $clearedCheckpoint->checkpoint->description,
                    'latitude' => $clearedCheckpoint->checkpoint->latitude,
                    'longitude' => $clearedCheckpoint->checkpoint->longitude,
                    'radius' => $clearedCheckpoint->checkpoint->radius,
                    'time' => $clearedCheckpoint->time
                ];
            });

        return response()->json([
            'success' => true,
            'checkpoints' => $checkpoints
        ], 200);
    }

    public function showIncidentsbyDate(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'date' => 'required|date_format:Y-m-d'
        ]);

        try {
            $incidents = incident::with('user')
                ->whereDate('created_at', Carbon::createFromFormat('Y-m-d', $request->date)->toDateString())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'incidents' => $incidents
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch incidents: ' . $e->getMessage()
            ], 500);
        }
    }
    public function logout()
    {
        try {
            // Get authenticated user
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Revoke all tokens for this user
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateGuardProfile(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'name' => 'nullable',
            'phone' => 'nullable',
            'address' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'zip' => 'nullable',
            'language' => 'nullable',
            'cnic' => 'nullable',
            'country' => 'nullable',
            'password' => 'nullable',
            'confirm_password' => 'nullable|same:password'
        ]);

        try {
            $fields = [
                'name',
                'phone',
                'address',
                'city',
                'state',
                'zip',
                'language',
                'cnic',
                'country'
            ];
            foreach ($fields as $field) {
                if ($request->filled($field)) {
                    $user->$field = $request->$field;
                }
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            // Return only safe fields
            $safeUser = $user->only(array_merge($fields, ['id', 'email', 'created_at', 'updated_at']));

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $safeUser
            ], 200);
        } catch (\Exception $e) {
            Log::error('Profile update failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile.'
            ], 500);
        }
    }

    public function lastClearedCheckpoint(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        $lastCleared = \App\Models\ClearedCheckpoints::where('user_id', $user->id)
            ->with('checkpoint')
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->first();

        if (!$lastCleared) {
            return response()->json([
                'success' => false,
                'message' => 'No cleared checkpoints found.'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'checkpoint' => $lastCleared
        ], 200);
    }

    public function activeIncidents(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        $incidents = \App\Models\incident::where('status', 'active')
            ->orderByDesc('created_at')
            ->get();
        return response()->json([
            'success' => true,
            'incidents' => $incidents
        ], 200);
    }

    public function updateIncidentStatus(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'incident_id' => 'required|exists:incidents,id',
            'status' => 'required|string'
        ]);

        $incident = \App\Models\incident::where('id', $request->incident_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$incident) {
            return response()->json([
                'success' => false,
                'message' => 'Incident not found or not set by you'
            ], 404);
        }

        $incident->status = $request->status;
        $incident->save();

        return response()->json([
            'success' => true,
            'message' => 'Incident status updated successfully',
            'incident' => $incident
        ], 200);
    }

    public function showAllClearedCheckpoints(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        $checkpoints = \App\Models\ClearedCheckpoints::where('user_id', $user->id)
            ->orderByDesc('date')
            ->with('checkpoint')
            ->get();
        return response()->json([
            'success' => true,
            'checkpoints' => $checkpoints
        ], 200);
    }
}
