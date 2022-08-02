<?php if(!empty(get_static_option('site_gdpr_cookie_enabled'))): ?>
    <script src="<?php echo e(asset('assets/frontend/js/jquery.ihavecookies.min.js')); ?>"></script>
    <?php $gdpr_cookie_link = str_replace('{url}',url('/'),get_static_option('site_gdpr_cookie_more_info_link')) ?>
    <script>
        $(document).ready(function () {
            var delayTime = "5000";
            delayTime = delayTime ? delayTime : 4000;
            if($('body').length > 0){
                $('body').ihavecookies({
                    title: "Cookies &amp; Privacy",
                    message: `Is education residence conveying so so. Suppose shyness say ten behaved morning had. Any unsatiable assistance compliment occasional too reasonably advantages.`,
                    expires: "30",
                    link: "https://nexelit.test/p/privacy-policy",
                    delay: delayTime,
                    moreInfoLabel: "More information",
                    acceptBtnLabel: "Accept",
                    advancedBtnLabel: "Decline",
                    cookieTypes: [{"type":"Site Preferences","value":"Site Preferences","description":"These are cookies that are related to your site preferences, e.g. remembering your username, site colours, etc."},{"type":"Analytics","value":"Analytics","description":"Cookies related to site visits, browser types, etc."},{"type":"Marketing","value":"Marketing","description":"Cookies related to marketing, e.g. newsletters, social media, etc"}],
                    moreBtnLabel: "Manage",
                    cookieTypesTitle: "Manage Cookie",
                });
                $('body').on('click', '#gdpr-cookie-close', function (e) {
                    e.preventDefault();
                    $(this).parent().remove();
                });
            }

        });



























    </script>
<?php endif; ?><?php /**PATH /home/bytesed/public_html/laravel/qixer/@core/resources/views/frontend/partials/gdpr-cookie.blade.php ENDPATH**/ ?>