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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    public function login (Request $request) {


        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'longitude' => 'required',
            'latitude' => 'required'
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $checkpoints = AssignCheckpoint::where('guard_id', $user->id)
                ->where('date_to_check', today())
                ->with('checkpoint')
                ->orderBy('priority', 'desc')
                ->get();



            foreach($checkpoints as $assignCheckpoint) {
                $checkpoint = $assignCheckpoint->checkpoint;


                if (!$checkpoint) {

                    continue;
                }

                $distance = DistanceHelper::calculateDistance(
                    $request->latitude,
                    $request->longitude,
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

            return response()->json([
                'success' => false,
                'message' => 'Not in checkpoint area'
            ], 403);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function showCheckpoints(){
        // Verify Sanctum token by adding middleware
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired token'
                ], 401);
            }

            $checkpoints = AssignCheckpoint::where('guard_id', $user->id)
                ->where('date_to_check', today())
                ->with('checkpoint')
                ->get()
                ->map(function($assignCheckpoint) {
                    return [
                        'id' => $assignCheckpoint->id,
                        'name' => $assignCheckpoint->checkpoint->name,
                        'description' => $assignCheckpoint->checkpoint->description,
                        'latitude' => $assignCheckpoint->checkpoint->latitude,
                        'longitude' => $assignCheckpoint->checkpoint->longitude,
                        'radius' => $assignCheckpoint->checkpoint->radius,
                        'status' => $assignCheckpoint->status,
                        'time' => $assignCheckpoint->time_to_check,
                        'priority' => $assignCheckpoint->priority,
                    ];
                });

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

    public function clearCheckpoint(Request $request){
        $user = auth('sanctum')->user();
        $request->validate([
            'checkpoint_id' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'time' => 'required',
            'image' => 'nullable',
            'video' => 'nullable',
            'audio' => 'nullable'
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
                ->where('date_to_check', today())
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

            // Store full image json (handle base64)
            if (isset($request->image['base64']) && isset($request->image['type'])) {
                $imageType = $request->image['type'];
                $base64String = $request->image['base64'];
                $extension = explode('/', $imageType)[1] ?? 'bin';
                $fileName = uniqid('image_') . '.' . $extension;
                $filePath = 'uploads/checkpoints/' . $fileName;
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
                $filePath = 'uploads/checkpoints/' . $fileName;
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
                $filePath = 'uploads/checkpoints/' . $fileName;
                file_put_contents(public_path($filePath), base64_decode($base64String));
                $audioJson = json_encode([
                    'type' => $audioType,
                    'path' => $filePath
                ]);
            } else {
                $audioJson = null;
            }

            // Update checkpoint details
            $assignCheckpoint->longitude = $request->longitude;
            $assignCheckpoint->latitude = $request->latitude;
            $assignCheckpoint->checked_time = $request->time;
            $assignCheckpoint->notes = $request->notes;
            $assignCheckpoint->images = $imageJson;
            $assignCheckpoint->videos = $videoJson;
            $assignCheckpoint->audios = $audioJson;

            // Check if checkpoint was cleared on time
            $checkTime = Carbon::parse($request->time);
            $timeToCheck = Carbon::parse($assignCheckpoint->time_to_check);

            $assignCheckpoint->status = $checkTime->lte($timeToCheck) ? 'completed' : 'late';
            $assignCheckpoint->save();

            return response()->json([
                'success' => true,
                'message' => 'Checkpoint cleared successfully',
                'checkpoint' => $assignCheckpoint
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear checkpoint: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeIncident(Request $request){
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
            if(!$user){
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

    public function showIncidents(){
        $user = auth('sanctum')->user();

        if(!$user){
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

    public function storeAlert(Request $request){
        $user = auth('sanctum')->user();

        if(!$user){
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

    public function showCheckpointsbyDate(Request $request){
        $user = auth('sanctum')->user();

        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
           'date' => 'required|date_format:Y-m-d'
        ]);

        $checkpoints = AssignCheckpoint::where('guard_id', $user->id)
        ->where('date_to_check', Carbon::createFromFormat('Y-m-d', $request->date)->toDateString())
        ->with('checkpoint')
        ->get()
        ->map(function($assignCheckpoint) {
            return [
                'id' => $assignCheckpoint->id,
                'name' => $assignCheckpoint->checkpoint->name,
                'description' => $assignCheckpoint->checkpoint->description,
                'latitude' => $assignCheckpoint->checkpoint->latitude,
                'longitude' => $assignCheckpoint->checkpoint->longitude,
                'radius' => $assignCheckpoint->checkpoint->radius,
                'status' => $assignCheckpoint->status,
                'time' => $assignCheckpoint->time_to_check
            ];
        });

        return response()->json([
            'success' => true,
            'checkpoints' => $checkpoints
        ], 200);
    }

    public function showIncidentsbyDate(Request $request){
        $user = auth('sanctum')->user();

        if(!$user){
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
    public function logout(){
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

    public function updateGuardProfile(Request $request){
        $user = auth('sanctum')->user();

        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'name' => 'nullable',
            'email' => 'nullable|email',
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
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->city = $request->city;
            $user->state = $request->state;
            $user->zip = $request->zip;
            $user->language = $request->language;
            $user->cnic = $request->cnic;
            $user->country = $request->country;
            if($request->password){
                $user->password = Hash::make($request->password);
            }
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }
}
