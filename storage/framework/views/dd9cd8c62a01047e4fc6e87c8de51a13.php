<?php $__env->startSection('content'); ?>
<div class="container py-5 text-center">
    <h4 class="mb-4">QR Code for Checkpoint: <strong><?php echo e($checkpoint->name); ?></strong></h4>
    <div class="d-flex justify-content-center mb-4">
        <?php echo $qr; ?>

    </div>
    <p class="lead">Scan this code at the checkpoint.</p>
    <div class="mt-3">
        <a href="<?php echo e(url()->previous()); ?>" class="btn btn-secondary">Back</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH K:\Laravel\security-master\resources\views/checkpoints/qrcode.blade.php ENDPATH**/ ?>