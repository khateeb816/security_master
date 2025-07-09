<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Get authenticated user details
// GET /api/user
// Headers:
//   - Authorization: Bearer {token}
// Response:
//   - 200: Returns authenticated user details
//   - 401: Unauthorized if invalid/missing token
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Guard login Api
Route::post('guard/login', [ApiController::class , 'login']);

//show checkpoints
Route::get('guard/checkpoints' , [ApiController::class , 'showCheckpoints']);

//store checkpoint
Route::post('guard/checkpoint/clear' , [ApiController::class,'clearCheckpoint']);

//store incident
Route::post('guard/incident' , [ApiController::class , 'storeIncident']);

//show incidents
Route::get('guard/incidents/show' , [ApiController::class , 'showIncidents']);

//store Alert
Route::post('guard/alert' , [ApiController::class , 'storeAlert']);

//show checkpoints of user by date
Route::post('guard/checkpoints/show' , [ApiController::class , 'showCheckpointsbyDate']);

//show Incidents of user by date
Route::post('guard/incidents/show' , [ApiController::class , 'showIncidentsbyDate']);

// update Guard profile
Route::post('guard/profile/update' , [ApiController::class , 'updateGuardProfile']);

//logout
Route::get('guard/logout' , [ApiController::class , 'logout']);
