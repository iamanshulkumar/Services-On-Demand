$('.icp-dd').iconpicker();
     $('.icp-dd').on('iconpickerSelected', function (e) {
    var selectedIcon = e.iconpickerValue;
    $(this).parent().parent().children('input').val(selectedIcon);
});<?php /**PATH C:\Maverick\Work\Website\XAMPP_v2\htdocs\services-on-demand\@core\resources\views/components/icon-picker.blade.php ENDPATH**/ ?>