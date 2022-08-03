<?php if(session()->has('msg')): ?>
    <div class="alert alert-danger alert-<?php echo e(session('type')); ?>">
        <?php echo session('msg'); ?>

    </div>
<?php endif; ?>
<?php /**PATH C:\Maverick\Work\Website\XAMPP_v2\htdocs\services-on-demand\@core\resources\views/components/flash-msg.blade.php ENDPATH**/ ?>