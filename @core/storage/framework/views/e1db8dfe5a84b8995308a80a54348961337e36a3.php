
<script>
  (function($){
    "use strict";

    $(document).ready(function(){

        $(document).on('change','#search_by_category,#search_by_subcategory,#search_by_rating,#search_by_sorting',function(e){
            e.preventDefault();
            $('#search_service_list_form').trigger('submit');
        })
        var oldSearchQ = '';
        $(document).on('keyup','#search_by_query',function(e){
            e.preventDefault();
            let qVal = $(this).val().trim();

            if(oldSearchQ !== qVal){
                setTimeout(function (){
                    oldSearchQ = qVal.trim();
                    if(qVal.length > 2){
                        $('#search_service_list_form').trigger('submit');
                    }
                },2000);
            }

        })

    });
})(jQuery);
</script>

<?php /**PATH C:\Maverick\Work\Website\XAMPP_v2\htdocs\qixer\@core\resources\views/frontend/pages/services/partials/service-search.blade.php ENDPATH**/ ?>