<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { font-weight: bold; }
        .company-info { margin-bottom: 10px; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin: 20px 0 10px 0; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .meta-table td { padding: 4px 8px; border: 1px solid #ddd; background: #f6f6f6; }
        .meta-table td.label { font-weight: bold; background: #eaeaea; width: 120px; }
        .events-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .events-table th { background: #222; color: #fff; font-weight: bold; padding: 6px 4px; border: 1px solid #222; font-size: 12px; }
        .events-table td { border: 1px solid #ddd; padding: 4px 4px; font-size: 12px; }
        .footer { font-size: 10px; color: #888; margin-top: 20px; text-align: left; }
        hr { border: none; border-top: 2px solid #222; margin: 10px 0 15px 0; }
    </style>
</head>
<body>
    <div class="company-info header">
        <?php echo e(config('app.company_name', 'Your Company Name')); ?><br>
        <?php echo e(config('app.company_address', 'Your Company Address')); ?><br>
        <?php echo e(config('app.company_phone', 'Your Company Phone')); ?><br>
        <?php echo e(config('app.company_country', 'Your Country')); ?>

    </div>
    <hr>
    <div class="title">Incident Reports</div>
    <table class="meta-table">
        <tr><td class="label">Client</td><td><?php echo e($filters['client'] ?? 'All'); ?></td></tr>
        <tr><td class="label">Branch</td><td><?php echo e($filters['branch'] ?? 'All'); ?></td></tr>
        <tr><td class="label">Guard</td><td><?php echo e($filters['guard'] ?? 'All'); ?></td></tr>
        <tr><td class="label">Date Range</td><td><?php echo e($filters['date_range'] ?? (isset($from) && isset($to) ? $from.' - '.$to : 'All')); ?></td></tr>
    </table>
    <table class="events-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Message</th>
                <th>Checkpoint Name</th>
                <th>Guard Name</th>
            </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e(\Carbon\Carbon::parse($incident->created_at)->format('d-m-Y H:i:s')); ?></td>
                <td><?php echo e($incident->type ?? '-'); ?></td>
                <td><?php echo e($incident->message ?? '-'); ?></td>
                <td><?php echo e($incident->checkpoint->name ?? '-'); ?></td>
                <td><?php echo e($incident->user->name ?? '-'); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <div class="footer">
        Page: {PAGE_NUM} / {PAGE_COUNT}<br>
        Generated date: <?php echo e(now()->format('d-m-Y H:i:s')); ?>

    </div>
</body>
</html>
<?php /**PATH K:\Laravel\security-master\resources\views/pdf/incidents.blade.php ENDPATH**/ ?>