<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

//Optimize Route
Route::get('/optimize', function () {
    Artisan::call('optimize');
    return 'Optimized';
});

//Link Storage
Route::get('/link-storage', function () {
    Artisan::call('storage:link');
    return 'Storage Linked';
});

// Authentication Routes
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Redirect root URL to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Protected Routes - Only Admin Access
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [\App\Http\Controllers\DashboardController::class, 'getDashboardData'])->name('dashboard.data');

    Route::get('/patrol-logs', [\App\Http\Controllers\GuardController::class, 'patrolLogs'])->name('patrol.logs');
    Route::get('/incidents', [\App\Http\Controllers\GuardController::class, 'incidents'])->name('incidents');
    Route::get('/alerts', [\App\Http\Controllers\GuardController::class, 'alerts'])->name('alerts');
    Route::get('/edit', function () {
        return view('edit');
    })->name('edit');

    // Branch Management Routes
    Route::get('/clients/{client}/branches', [\App\Http\Controllers\BranchController::class, 'getBranchesByClient'])->name('clients.branches');

    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/{client}/list', [\App\Http\Controllers\BranchController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\BranchController::class, 'store'])->name('store');
        Route::get('/{branch}', [\App\Http\Controllers\BranchController::class, 'show'])->name('show');
        Route::put('/{branch}', [\App\Http\Controllers\BranchController::class, 'update'])->name('update');
        Route::delete('/{branch}', [\App\Http\Controllers\BranchController::class, 'destroy'])->name('destroy');
    });

    // Checkpoints Routes (Global)
    Route::get('/checkpoints', [\App\Http\Controllers\CheckpointController::class, 'index'])->name('checkpoints.all');

    // Client Management Routes
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ClientController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\ClientController::class, 'store'])->name('store');
        Route::get('/create', [\App\Http\Controllers\ClientController::class, 'create'])->name('create');
        Route::get('/{client}', [\App\Http\Controllers\ClientController::class, 'show'])->name('show');
        Route::put('/{client}', [\App\Http\Controllers\ClientController::class, 'update'])->name('update');
        Route::delete('/{client}', [\App\Http\Controllers\ClientController::class, 'destroy'])->name('destroy');
        Route::get('/{client}/edit', [\App\Http\Controllers\ClientController::class, 'edit'])->name('edit');

        // Checkpoints for specific client
        Route::get('/{client}/checkpoints', [\App\Http\Controllers\CheckpointController::class, 'index'])->name('checkpoints.client');

        // Branch Management Routes
        Route::prefix('{client}/branches')->name('branches.')->group(function () {
            Route::post('/', [\App\Http\Controllers\BranchController::class, 'store'])->name('store');
            Route::get('/create', [\App\Http\Controllers\BranchController::class, 'create'])->name('create');

            // Nested routes that need both client and branch
            Route::prefix('{branch}')->group(function () {
                Route::get('/edit', [\App\Http\Controllers\BranchController::class, 'edit'])->name('edit');
                Route::put('/', [\App\Http\Controllers\BranchController::class, 'update'])->name('update');
                Route::delete('/', [\App\Http\Controllers\BranchController::class, 'destroy'])->name('destroy');

                // Checkpoints Routes
                Route::prefix('checkpoints')->name('checkpoints.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\CheckpointController::class, 'index'])->name('index');
                    Route::post('/', [\App\Http\Controllers\CheckpointController::class, 'store'])->name('store');
                    Route::get('/create', [\App\Http\Controllers\CheckpointController::class, 'create'])->name('create');

                    // Nested routes that need client, branch, and checkpoint
                    Route::prefix('{checkpoint}')->group(function () {
                        Route::get('/', [\App\Http\Controllers\CheckpointController::class, 'show'])->name('show');
                        Route::put('/', [\App\Http\Controllers\CheckpointController::class, 'update'])->name('update');
                        Route::delete('/', [\App\Http\Controllers\CheckpointController::class, 'destroy'])->name('destroy');
                        Route::get('/edit', [\App\Http\Controllers\CheckpointController::class, 'edit'])->name('edit');
                        Route::get('/qrcode', [\App\Http\Controllers\CheckpointController::class, 'getQrCode'])->name('qrcode');
                    });
                });
            });
        });
    });

    // Guards Management Routes
    Route::post('/assign-checkpoint', [\App\Http\Controllers\GuardController::class, 'assignCheckpoint'])->name('guards.assignCheckpoint');
    Route::delete('/remove-assignment/{assignment}', [\App\Http\Controllers\GuardController::class, 'removeAssignment'])->name('guards.removeAssignment');

    Route::prefix('guards')->name('guards.')->group(function () {
        Route::get('/', [\App\Http\Controllers\GuardController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\GuardController::class, 'store'])->name('store');
        Route::get('/create', [\App\Http\Controllers\GuardController::class, 'create'])->name('create');
        Route::get('/{guard}', [\App\Http\Controllers\GuardController::class, 'show'])->name('show');
        Route::put('/{guard}', [\App\Http\Controllers\GuardController::class, 'update'])->name('update');
        Route::delete('/{guard}', [\App\Http\Controllers\GuardController::class, 'destroy'])->name('destroy');
        Route::get('/{guard}/edit', [\App\Http\Controllers\GuardController::class, 'edit'])->name('edit');
    });

    // Test route for serving JavaScript file
    Route::get('/js/users.js', function () {
        $path = public_path('js/users.js');
        if (file_exists($path)) {
            return response()->file($path, ['Content-Type' => 'application/javascript']);
        }
        abort(404, 'File not found');
    })->name('js.users');

    Route::post('/alerts/{alert}/mark-read', [\App\Http\Controllers\GuardController::class, 'markAlertAsRead'])->name('alerts.markRead');
    Route::post('/incidents/{incident}/update-status', [\App\Http\Controllers\GuardController::class, 'updateIncidentStatus'])->name('incidents.updateStatus');
});
