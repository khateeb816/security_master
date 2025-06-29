<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Patrol Sync</title>

    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome (Latest CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
        }

        #guardMap {
            height: 400px;
            border-radius: 12px;
            overflow: hidden;
        }


        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #0f172a, #0284c7, #06b6d4);
            color: white;
            padding-top: 30px;
            position: fixed;
            width: 240px;
        }

        .sidebar .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .sidebar .logo img {
            width: 120px;
            height: auto;
        }

        .sidebar a {
            color: #ffffffd9;
            display: block;
            padding: 12px 24px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s ease;
            border-radius: 8px;
            margin: 6px 12px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .main-content {
            margin-left: 240px;
            padding: 30px;
        }

        .navbar {
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            padding: 1rem 2rem;
            border-radius: 12px;
        }

        .card {
            border: none;
            border-radius: 16px;
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .badge {
            font-size: 0.6rem;
            padding: 0.3em 0.45em;
        }

        .dropdown-menu .dropdown-item {
            line-height: 1.2;
            padding: 0.5rem 0.75rem;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
            color: #000;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="<?php echo e(asset('assets/logo.png')); ?>" alt="Logo">
        </div>
        <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>"><i
                class="fas fa-chart-line me-2"></i> Dashboard</a>
        <a href="<?php echo e(route('clients.index')); ?>" class="<?php echo e(request()->routeIs('clients.index') ? 'active' : ''); ?>"><i
                class="fas fa-building me-2"></i> Add Clients</a>
        <a href="<?php echo e(route('guards.index')); ?>" class="<?php echo e(request()->routeIs('guards.index') ? 'active' : ''); ?>"><i
                class="fas fa-user-shield me-2"></i> Add Guards</a>
        <?php
            $firstClient = \App\Models\User::where('role', 'client')->first();
            $firstBranch = $firstClient ? $firstClient->branches()->first() : null;
            $checkpointUrl =
                $firstClient && $firstBranch
                    ? route('clients.branches.checkpoints.index', [
                        'client' => $firstClient->id,
                        'branch' => $firstBranch->id,
                    ])
                    : '#';

            // Debug info - can be removed later
            // dd(route('clients.branches.checkpoints.index', ['client' => $firstClient->id, 'branch' => $firstBranch->id]));

        ?>
        <a href="<?php echo e($checkpointUrl); ?>"
            class="<?php echo e(request()->routeIs('clients.branches.checkpoints.*') ? 'active' : ''); ?>"
            id="checkpoints-nav-link">
            <i class="fas fa-map-marker-alt me-2"></i> Checkpoints
        </a>

        <?php $__env->startPush('scripts'); ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const checkpointsLink = document.getElementById('checkpoints-nav-link');
                    if (checkpointsLink && checkpointsLink.href.endsWith('#')) {
                        checkpointsLink.addEventListener('click', function(e) {
                            e.preventDefault();
                            alert('Please add at least one client and branch first.');
                        });
                    }
                });
            </script>
        <?php $__env->stopPush(); ?>
        <a href="<?php echo e(route('patrol.logs')); ?>" class="<?php echo e(request()->routeIs('patrol.logs') ? 'active' : ''); ?>"><i
                class="fas fa-route me-2"></i> Patrol Logs</a>
        <a href="<?php echo e(route('incidents')); ?>" class="<?php echo e(request()->routeIs('incidents') ? 'active' : ''); ?>"><i
                class="fas fa-bolt me-2"></i> Incidents</a>
        <a href="<?php echo e(route('alerts')); ?>" class="<?php echo e(request()->routeIs('alerts') ? 'active' : ''); ?>"><i
                class="fas fa-bell me-2"></i> Alerts</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="navbar d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold">Welcome, Supervisor</h4>
            <div class="d-flex align-items-center gap-3">
                <!-- Notification Icon with Dropdown -->
                <div class="dropdown">
                    <a class="position-relative" href="#" id="notifDropdown" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-bell fa-lg"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm p-2" aria-labelledby="notifDropdown"
                        style="min-width: 300px;">
                        <li class="dropdown-header fw-semibold">Notifications</li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item small text-wrap" href="#">
                                <i class="fas fa-shield-alt text-primary me-2"></i>
                                Patrol missed by Guard <strong>#A12</strong>
                                <div class="text-muted small">10:12 AM</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item small text-wrap" href="#">
                                <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                Incident reported: Unauthorized Entry
                                <div class="text-muted small">Yesterday</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item small text-wrap" href="#">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Guard Ayesha completed Sector 5
                                <div class="text-muted small">Today</div>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a href="#" class="dropdown-item text-center text-primary small">View All
                                Notifications</a>
                        </li>
                    </ul>
                </div>


                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" id="profileDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-lg text-dark"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="profileDropdown">
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('edit')); ?>">
                                <i class="fas fa-user-edit me-2 text-primary"></i> Edit Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2 text-danger"></i> Logout
                            </a>

                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                                <?php echo csrf_field(); ?>
                            </form>

                        </li>
                    </ul>
                </div>

            </div>
        </div>
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php echo $__env->yieldContent('content'); ?>
    </div>


    <!-- Core JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Test Bootstrap JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Bootstrap version:', bootstrap ? bootstrap.Tooltip.VERSION : 'Bootstrap not loaded!');
            console.log('jQuery version:', $ ? $.fn.jquery : 'jQuery not loaded!');
        });
    </script>

    <!-- Required for pushing scripts like Leaflet/map -->
    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>

</html>
<?php /**PATH K:\Laravel\security-master\resources\views\layouts\app.blade.php ENDPATH**/ ?>