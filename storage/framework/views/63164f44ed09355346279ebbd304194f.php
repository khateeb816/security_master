<?php $__env->startSection('content'); ?>
<div class="container py-5 text-center">
    <h4 class="mb-4">QR Code for Checkpoint: <strong><?php echo e($checkpoint->name); ?></strong></h4>
    <div class="d-flex justify-content-center mb-4">
        <?php echo $qr; ?>

    </div>
    <p class="lead">Scan this code at the checkpoint.</p>
    <p class="lead">NFC UID: <?php echo e($checkpoint->nfc_uid ?? 'N/A'); ?></p>
    <p class="lead">Check Point ID: <?php echo e($checkpoint->id ?? 'N/A'); ?></p>
    <div class="mt-3">
        <a href="<?php echo e(route('clients.branches.checkpoints.qrcode.download', [
            'client' => $checkpoint->client_id,
            'branch' => $checkpoint->branch_id,
            'checkpoint' => $checkpoint->id
        ])); ?>" class="btn btn-primary me-2">
            <i class="fas fa-download"></i> Download QR Code
        </a>
        <a href="<?php echo e(url()->previous()); ?>" class="btn btn-secondary">Back</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH K:\Laravel\security-master\resources\views\checkpoints\qrcode.blade.php ENDPATH**/ ?>