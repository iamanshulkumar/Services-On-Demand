<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo e(get_static_option('site_title')); ?> - <?php echo e(get_static_option('site_tag_line')); ?></title>

    <?php echo render_site_meta(); ?>


    <!-- favicon -->
    <?php echo render_favicon_by_id(get_static_option('site_favicon')); ?>


    <link rel="stylesheet" href="<?php echo e(asset('assets/frontend/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/frontend/css/line-awesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/frontend/css/style.css')); ?>">

</head>

<body>
    <div class="overlays"></div>
    <!-- Header area end -->
    <!-- Error Area starts -->
    <div class="error-area padding-top-100 padding-bottom-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="error-wrapper">
                        <div class="thumb">
                            <?php echo render_image_markup_by_attachment_id(get_static_option('error_image')); ?>

                        </div>
                        <div class="contents margin-top-60">
                            <h2 class="title"><?php echo e(get_static_option('error_404_page_title')); ?></h2>
                            <h4 class="sub-title mt-4" style="font-size: 24px"><?php echo e(get_static_option('error_404_page_subtitle')); ?></h4>
                            <p class="my-2"><?php echo e(get_static_option('error_404_page_paragraph')); ?></p>
                            <div class="btn-wrapper margin-top-50">
                                <a href="<?php echo e(route('homepage')); ?>" class="cmn-btn btn-bg-1"><?php echo e(get_static_option('error_404_page_button_text')); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Error Area ends -->
</body>
</html>
<?php /**PATH C:\Maverick\Work\Website\XAMPP_v2\htdocs\services-on-demand\@core\resources\views/frontend/pages/404.blade.php ENDPATH**/ ?>