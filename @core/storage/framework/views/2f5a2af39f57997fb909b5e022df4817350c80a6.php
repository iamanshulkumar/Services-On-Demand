<ul class="user-profile-chat margin-top-30" id="users">
    <?php $__currentLoopData = $sellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li>
            <a href="javascript:void(0);" class="chat-toggle"data-id="<?php echo e(optional($seller->seller)->id); ?>"data-user="<?php echo e(optional($seller->seller)->name); ?>">
                <div class="chat-bg bg-image" <?php echo render_background_image_markup_by_attachment_id(optional($seller->seller)->image); ?>> <span class="notification-dot active"></span> </div>
                <h4 class="chat-author-title"> <?php echo e(optional($seller->seller)->name); ?> </h4>
            </a>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
<?php /**PATH /home/bytesed/public_html/laravel/qixer/@core/Modules/LiveChat/Resources/views/frontend/buyer/seller_container.blade.php ENDPATH**/ ?>