@if(!empty(get_static_option('site_gdpr_cookie_enabled')))
    <script src="{{asset('assets/frontend/js/jquery.ihavecookies.min.js')}}"></script>
    @php $gdpr_cookie_link = str_replace('{url}',url('/'),get_static_option('site_gdpr_cookie_more_info_link')) @endphp
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
{{--        $(document).ready(function () {--}}
{{--            var delayTime = "{{get_static_option('site_gdpr_cookie_delay')}}";--}}
{{--            delayTime = delayTime ? delayTime : 4000;--}}
{{--            @php--}}
{{--                $all_title_fields = get_static_option('site_gdpr_cookie_manage_item_title');--}}
{{--                $all_title_fields = !empty($all_title_fields) ? unserialize($all_title_fields,['class' => false]) : [''];--}}
{{--                $all_description_fields = get_static_option('site_gdpr_cookie_manage_item_description');--}}
{{--                $all_description_fields = !empty($all_description_fields) ? unserialize($all_description_fields,['class' => false]) : [];--}}
{{--                $cookie_mange_data = [];--}}
{{--            @endphp--}}
{{--            @foreach($all_title_fields as $index => $title)--}}
{{--            @php--}}
{{--                $cookie_mange_data[] = [--}}
{{--                    'type' => $title,--}}
{{--                    'value' => $title,--}}
{{--                    'description' => $all_description_fields[$index] ?? '',--}}
{{--                ];--}}
{{--            @endphp--}}
{{--            @endforeach--}}
{{--                // $.gdprcookie/--}}
{{--            $('body').ihavecookies();--}}
{{--            --}}{{--$('body').ihavecookies({--}}
{{--            --}}{{--    title: "{{get_static_option('site_gdpr_cookie_title')}}",--}}
{{--            --}}{{--    message: `{{get_static_option('site_gdpr_cookie_message')}}`,--}}
{{--            --}}{{--    expires: "{{get_static_option('site_gdpr_cookie_expire')}}",--}}
{{--            --}}{{--    link: "{{$gdpr_cookie_link}}",--}}
{{--            --}}{{--    delay: delayTime,--}}
{{--            --}}{{--    moreInfoLabel: "{{get_static_option('site_gdpr_cookie_more_info_label')}}",--}}
{{--            --}}{{--    acceptBtnLabel: "{{get_static_option('site_gdpr_cookie_accept_button_label')}}",--}}
{{--            --}}{{--    advancedBtnLabel: "{{get_static_option('site_gdpr_cookie_decline_button_label')}}",--}}
{{--            --}}{{--    cookieTypes: {!!   json_encode($cookie_mange_data) !!},--}}
{{--            --}}{{--    moreBtnLabel: "{{get_static_option('site_gdpr_cookie_manage_button_label',"Manage")}}",--}}
{{--            --}}{{--    cookieTypesTitle: "{{get_static_option('site_gdpr_cookie_manage_title',"Manage Cookies")}}",--}}
{{--            --}}{{--});--}}
{{--            $('body').on('click', '#gdpr-cookie-close', function (e) {--}}
{{--                e.preventDefault();--}}
{{--                $(this).parent().remove();--}}
{{--            });--}}
{{--        });--}}
    </script>
@endif