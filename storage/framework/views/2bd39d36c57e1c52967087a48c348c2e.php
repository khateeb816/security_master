<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Patrol Sync</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f6fa;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        background-image: url('<?php echo e(asset('assets/background.png')); ?>');
            background-size: cover;
            background-position: center;
            filter: brightness(0.85);
        }
        
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
        }
        
        .form-panel {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 2.5rem;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
        }
        
        .btn-primary {
            background-color: #2196f3;
            border: none;
            padding: 10px;
            border-radius: 8px;
        }
        
        .btn-primary:hover {
            background-color: #0d8bf2;
        }
        
        .logo {
            height: 60px;
        }
        
        .login-heading {
            font-weight: 700;
        }
        
        .brand-text {
            color: #2196f3;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="background-image"></div>
    
    <div class="container-fluid login-container">
        <div class="form-panel">
            <div class="text-center mb-4">
                <img src="<?php echo e(asset('assets/logo.png')); ?>" alt="Patrol Sync Logo" class="logo mb-2">
            </div>
            <h4 class="text-center login-heading">Welcome to <span class="brand-text">QR - Patrol Sync</span></h4>
            <p class="text-center mb-4 text-muted">A cloud guard tour monitoring system to manage your patrols</p>
            
            <form action="<?php echo e(route('login')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label for="email" class="form-label">User Name*</label>
                    <input type="text" class="form-control" name="email" placeholder="Type here" required>
                </div>
                <div class="mb-3 position-relative">
                    <label for="password" class="form-label">Password*</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter Your Password" required>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember">
                        <label class="form-check-label">Remember Me</label>
                    </div>
                    <a href="#" class="text-decoration-none small text-primary">Forgot Password?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">Log In</button>
            </form>
        </div>
    </div>
</body>
</html><?php /**PATH K:\Laravel\security-master\resources\views\login.blade.php ENDPATH**/ ?>