<!DOCTYPE html>
<html>
<head>
    <title>Incident Reports</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; background: #f8f9fa; color: #222; }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
        }
        .header img {
            height: 48px;
            margin-right: 16px;
        }
        .header-title {
            font-size: 2rem;
            color: #dc3545;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        th, td {
            padding: 10px 8px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        th {
            background: #dc3545;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background: #f8d7da;
        }
        tr:nth-child(odd) {
            background: #fff;
        }
        h2 {
            color: #dc3545;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?php echo e(public_path('public/assets/logo.png')); ?>" alt="Logo" onerror="this.style.display='none'">
        <span class="header-title">Incident Reports</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Guard Name</th>
                <th>Incident Type</th>
                <th>Reported At</th>
                <th>Status</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e($incident->user->name ?? '-'); ?></td>
                    <td><?php echo e($incident->type); ?></td>
                    <td><?php echo e($incident->created_at->format('Y-m-d h:i A')); ?></td>
                    <td><?php echo e($incident->status ?? '-'); ?></td>
                    <td><?php echo e($incident->message); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH K:\Laravel\security-master\resources\views\pdf\incidents.blade.php ENDPATH**/ ?>