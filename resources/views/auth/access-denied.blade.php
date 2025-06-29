<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied | Patrol Sync</title>
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
            background-image: url('{{ asset('assets/background.png') }}');
            background-size: cover;
            background-position: center;
            filter: brightness(0.85);
        }

        .access-denied-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
        }

        .access-denied-panel {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 3rem;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .logo {
            height: 60px;
            margin-bottom: 1rem;
        }

        .brand-text {
            color: #2196f3;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #2196f3;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #0d8bf2;
        }

        .icon-large {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="background-image"></div>

    <div class="container-fluid access-denied-container">
        <div class="access-denied-panel">
            <div class="text-center mb-4">
                <img src="{{ asset('assets/logo.png') }}" alt="Patrol Sync Logo" class="logo">
            </div>

            <div class="icon-large">
                <i class="fas fa-ban"></i>
            </div>

            <h2 class="text-danger mb-3">Access Denied</h2>
            <h4 class="mb-4">Welcome to <span class="brand-text">QR - Patrol Sync</span></h4>

            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Access Restricted:</strong> Only administrators can access the dashboard.
            </div>

            <p class="text-muted mb-4">
                If you are a guard or client, please use the mobile application to access your assigned features.
                Contact your administrator if you believe this is an error.
            </p>

            <div class="d-grid gap-2 d-md-block">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Back to Login
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
