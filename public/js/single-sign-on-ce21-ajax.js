var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};
jQuery(document).ready(function($) {

    // Perform AJAX login on form submit
    $('#ce21_sign_in form').on('submit', function(e){
        var postid = getUrlParameter('postid');
        $('#ce21_sign_in form p.status').removeClass('error');
        $('#ce21_sign_in form p.status').show().text(ajax_login_object.loadingmessage);
        $("#ce21Login").css({"opacity":"0.6"});
        $('#ce21Login').html('Processing...');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_login_object.ajaxurl,
            data: {
                'action': 'ce21_sign_in_ajax_api', //calls wp_ajax_nopriv_ajaxlogin
                'username': $('#ce21_sign_in form #ce21Email').val(),
                'password': $('#ce21_sign_in form #ce21Password').val(),
                'postid': postid,
                'security': $('#ce21_sign_in form #security').val()
            },
            success: function(data){				
                if( !data.isSuccess){
                    $('#ce21_ss_sign_in_notification_div').addClass('error');
                    $('#ce21_ss_sign_in_notification_div').html(data.message);
                    $('#ce21_ss_sign_in_notification_div').show();
                    $("#ce21Login").css({"opacity":"1"});
                    $('#ce21Login').html('Sign In');
                }
                $('#ce21_sign_in form p.status').text(data.message);
                if (data.isSuccess == true && data.redirectURL){
					document.location.href = data.redirectURL;
                    $("#ce21Login").css({"opacity":"1"});
                    $('#ce21Login').html('Sign In');
                }
            },
            error: function() {
                $("#ce21Login").css({"opacity":"1"});
                $('#ce21Login').html('Sign In');
            }
        });
        e.preventDefault();
    });

    $(document).on('click','.ce21_calendar_nav',function(){
        var month = $(this).attr("data-month");
        var year  = $(this).attr("data-year");
       $('#ce21MiniCalendarLoader').show();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_login_object.ajaxurl,
            data: {
                'action': 'get_ce21_mini_calendar', //calls wp_ajax_nopriv_ajaxlogin
                'month': month,
                'year': year
            },
            success: function(data){
                if (data.success == true){
                    $('#ce21MiniCalendarContainer').html(data.content);
                    $('#ce21MiniCalendarLoader').hide();
                }
            },
            error: function() {
                $('#ce21MiniCalendarLoader').hide();
            }
        });
    });

});
