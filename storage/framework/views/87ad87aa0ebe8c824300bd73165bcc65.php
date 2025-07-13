<!DOCTYPE html>
<html>
<head>
    <title>Patrol Logs Report</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; background: #f8f9fa; color: #222; }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .header img {
            height: 48px;
            margin-right: 16px;
        }
        .header-title {
            font-size: 2rem;
            color: #007bff;
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
            background: #007bff;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background: #f2f6fc;
        }
        tr:nth-child(odd) {
            background: #fff;
        }
        h2 {
            color: #007bff;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?php echo e(public_path('public/assets/logo.png')); ?>" alt="Logo" onerror="this.style.display='none'">
        <span class="header-title">Patrol Logs Report</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Guard Name</th>
                <th>Checkpoint</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e($log->user_guard->name ?? '-'); ?></td>
                    <td><?php echo e($log->checkpoint->name ?? '-'); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($log->date_to_check)->format('Y-m-d')); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($log->time_to_check)->format('h:i A')); ?></td>
                    <td><?php echo e($log->status); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH K:\Laravel\security-master\resources\views\pdf\patrol_logs.blade.php ENDPATH**/ ?>