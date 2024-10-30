(function( $ ) {
    'use strict';

    jQuery( document ).ready(function() {

        /*
        * Script used for add new calendar event modal.
        * */
        var ce21_ss_ance_modal = document.getElementById("ce21_ss_add_new_calender_event_modal");

        var ce21_ss_ance_modal_btn = document.getElementById("btn_ce21_ss_open_ance_modal");

        var ce21_ss_ance_modal_close_span = document.getElementById("ce21_ss_ance_close_span");

        ce21_ss_ance_modal_btn.onclick = function() {
            ce21_ss_ance_modal.style.display = "block";
        }

        ce21_ss_ance_modal_close_span.onclick = function() {
            ce21_ss_ance_modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == ce21_ss_ance_modal) {
                ce21_ss_ance_modal.style.display = "none";
            }
        }


        /*
        * Script used for edit calendar event modal.
        * */
        var ce21_ss_ece_modal = document.getElementById("ce21_ss_edit_calender_event_modal");

        var ce21_ss_ece_modal_close_span = document.getElementById("ce21_ss_ece_close_span");

        ce21_ss_ece_modal_close_span.onclick = function() {
            ce21_ss_ece_modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == ce21_ss_ece_modal) {
                ce21_ss_ece_modal.style.display = "none";
            }
        }


        /*
        * Function used to initialize datetimepicker for start-date located in add-new-calendar-event-modal in calender page.
        * */
        jQuery('#txt_ce21_ss_ance_start_date').datetimepicker({
            format:'Y-m-d H:i:s',
        });


        /*
        * Function used to initialize datetimepicker for end-date located in add-new-calendar-event-modal in calender page.
        * */
        jQuery('#txt_ce21_ss_ance_end_date').datetimepicker({
            format:'Y-m-d H:i:s',
        });


        /*
        * Function used to initialize datetimepicker for start-date located in edit-calendar-event-modal in calender page.
        * */
        jQuery('#txt_ce21_ss_ece_start_date').datetimepicker({
            format:'Y-m-d H:i:s',
        });


        /*
        * Function used to initialize datetimepicker for end-date located in edit-calendar-event-modal in calender page.
        * */
        jQuery('#txt_ce21_ss_ece_end_date').datetimepicker({
            format:'Y-m-d H:i:s',
        });


        /*
        * Function used to initialize datatable
        * */
        var dataTable=$('#ce21_calendar_events_table').DataTable({
            "processing": true,
            "serverSide":true,
            "ajax":{
                url:ajax_login_object.ajaxurl,
                type:"post",
                data: {action: 'load_ce21_single_sign_on_calendar_events'}
            }
        });
    });


    /*
    * Function used to save new calendar event
    * */
    jQuery(document).on('click', '#btn_ce21_ss_ance',function() {
        jQuery('#ce21_ss_admin_loader_div').show();

        var title = jQuery('#txt_ce21_ss_ance_title').val();
        var start_date = jQuery('#txt_ce21_ss_ance_start_date').val();
        var end_date = jQuery('#txt_ce21_ss_ance_end_date').val();
        var url = jQuery('#txt_ce21_ss_ance_url').val();

        if (title == '')
        {
            jQuery('#ce21_ss_ance_notification_div').text("Please enter Title!");
            jQuery('#ce21_ss_ance_notification_div').removeClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ance_notification_div').removeClass("ce21-ss-alert-success");
            jQuery('#ce21_ss_ance_notification_div').addClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ance_notification_div').show();
            jQuery('#ce21_ss_admin_loader_div').hide();
            return false;
        }
        if (start_date == '')
        {
            jQuery('#ce21_ss_ance_notification_div').text("Please select Start Date!");
            jQuery('#ce21_ss_ance_notification_div').removeClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ance_notification_div').removeClass("ce21-ss-alert-success");
            jQuery('#ce21_ss_ance_notification_div').addClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ance_notification_div').show();
            jQuery('#ce21_ss_admin_loader_div').hide();
            return false;
        }
        if (end_date == '')
        {
            jQuery('#ce21_ss_ance_notification_div').text("Please select End Date!");
            jQuery('#ce21_ss_ance_notification_div').removeClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ance_notification_div').removeClass("ce21-ss-alert-success");
            jQuery('#ce21_ss_ance_notification_div').addClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ance_notification_div').show();
            jQuery('#ce21_ss_admin_loader_div').hide();
            return false;
        }
        if (end_date < start_date)
        {
            jQuery('#ce21_ss_ance_notification_div').text("Please select End Date which is grater than Start Date!");
            jQuery('#ce21_ss_ance_notification_div').removeClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ance_notification_div').removeClass("ce21-ss-alert-success");
            jQuery('#ce21_ss_ance_notification_div').addClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ance_notification_div').show();
            jQuery('#ce21_ss_admin_loader_div').hide();
            return false;
        }

        var $params = {action: 'ce21_ss_add_new_calendar_event', title: title, start_date: start_date, end_date: end_date, url: url};
        jQuery.ajax({
            type: "POST",
            url: ajax_login_object.ajaxurl,
            dataType: 'json',
            data: $params,
            success: function (response) {
                if (response)
                {
                    if (response.success == true)
                    {
                        jQuery('#txt_ce21_ss_ance_title').val('');
                        jQuery('#txt_ce21_ss_ance_start_date').val('');
                        jQuery('#txt_ce21_ss_ance_end_date').val('');
                        jQuery('#txt_ce21_ss_ance_url').val('');
                        jQuery('#ce21_ss_add_new_calender_event_modal').hide();

                        jQuery('#ce21_ss_ce_notification_div').text(response.message);
                        jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-danger");
                        jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-success");
                        jQuery('#ce21_ss_ce_notification_div').addClass("ce21-ss-alert-success");
                        jQuery('#ce21_ss_ce_notification_div').show();
                        jQuery('#ce21_ss_admin_loader_div').hide();
                        jQuery("#ce21_calendar_events_table").DataTable().ajax.reload();
                    }
                    else
                    {
                        jQuery('#ce21_ss_ce_notification_div').text(response.message);
                        jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-danger");
                        jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-success");
                        jQuery('#ce21_ss_ce_notification_div').addClass("ce21-ss-alert-danger");
                        jQuery('#ce21_ss_ce_notification_div').show();
                        jQuery('#ce21_ss_add_new_calender_event_modal').hide();
                        jQuery('#ce21_ss_admin_loader_div').hide();
                    }
                }
            },
            error: function (response) {
                jQuery('#ce21_ss_ce_notification_div').text("Something went wrong!");
                jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-danger");
                jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-success");
                jQuery('#ce21_ss_ce_notification_div').addClass("ce21-ss-alert-danger");
                jQuery('#ce21_ss_ce_notification_div').show();
                jQuery('#ce21_ss_add_new_calender_event_modal').hide();
                jQuery('#ce21_ss_admin_loader_div').hide();
            }
        });
    });


    /*
    * Function used to update calendar event
    * */
   jQuery(document).on('click', '#btn_ce21_ss_ece' ,function() {
        jQuery('#ce21_ss_admin_loader_div').show();

        var id = jQuery('#hdn_ce21_calendar_event_id').val();
        var title = jQuery('#txt_ce21_ss_ece_title').val();
        var start_date = jQuery('#txt_ce21_ss_ece_start_date').val();
        var end_date = jQuery('#txt_ce21_ss_ece_end_date').val();
        var url = jQuery('#txt_ce21_ss_ece_url').val();

        if (title == '')
        {
            jQuery('#ce21_ss_ece_notification_div').text("Please enter Title!");
            jQuery('#ce21_ss_ece_notification_div').removeClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ece_notification_div').removeClass("ce21-ss-alert-success");
            jQuery('#ce21_ss_ece_notification_div').addClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ece_notification_div').show();
            jQuery('#ce21_ss_admin_loader_div').hide();
            return false;
        }
        if (start_date == '')
        {
            jQuery('#ce21_ss_ece_notification_div').text("Please select Start Date!");
            jQuery('#ce21_ss_ece_notification_div').removeClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ece_notification_div').removeClass("ce21-ss-alert-success");
            jQuery('#ce21_ss_ece_notification_div').addClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ece_notification_div').show();
            jQuery('#ce21_ss_admin_loader_div').hide();
            return false;
        }
        if (end_date == '')
        {
            jQuery('#ce21_ss_ece_notification_div').text("Please select End Date!");
            jQuery('#ce21_ss_ece_notification_div').removeClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ece_notification_div').removeClass("ce21-ss-alert-success");
            jQuery('#ce21_ss_ece_notification_div').addClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ece_notification_div').show();
            jQuery('#ce21_ss_admin_loader_div').hide();
            return false;
        }
        if (end_date < start_date)
        {
            jQuery('#ce21_ss_ece_notification_div').text("Please select End Date which is grater than Start Date!");
            jQuery('#ce21_ss_ece_notification_div').removeClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ece_notification_div').removeClass("ce21-ss-alert-success");
            jQuery('#ce21_ss_ece_notification_div').addClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ece_notification_div').show();
            jQuery('#ce21_ss_admin_loader_div').hide();
            return false;
        }

        var $params = {action: 'ce21_ss_edit_calendar_event', id: id, title: title, start_date: start_date, end_date: end_date, url: url};
        jQuery.ajax({
            type: "POST",
            url: ajax_login_object.ajaxurl,
            dataType: 'json',
            data: $params,
            success: function (response) {
                if (response)
                {
                    if (response.success == true)
                    {
                        jQuery('#ce21_ss_edit_calender_event_modal').hide();
                        jQuery('#ce21_ss_ce_notification_div').text(response.message);
                        jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-danger");
                        jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-success");
                        jQuery('#ce21_ss_ce_notification_div').addClass("ce21-ss-alert-success");
                        jQuery('#ce21_ss_ce_notification_div').show();
                        jQuery('#ce21_ss_admin_loader_div').hide();
                        jQuery("#ce21_calendar_events_table").DataTable().ajax.reload();
                    }
                    else
                    {
                        jQuery('#ce21_ss_ce_notification_div').text(response.message);
                        jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-danger");
                        jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-success");
                        jQuery('#ce21_ss_ce_notification_div').addClass("ce21-ss-alert-danger");
                        jQuery('#ce21_ss_ce_notification_div').show();
                        jQuery('#ce21_ss_edit_calender_event_modal').hide();
                        jQuery('#ce21_ss_admin_loader_div').hide();
                    }
                }
            },
            error: function (response) {
                jQuery('#ce21_ss_ce_notification_div').text("Something went wrong!");
                jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-danger");
                jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-success");
                jQuery('#ce21_ss_ce_notification_div').addClass("ce21-ss-alert-danger");
                jQuery('#ce21_ss_ce_notification_div').show();
                jQuery('#ce21_ss_edit_calender_event_modal').hide();
                jQuery('#ce21_ss_admin_loader_div').hide();
            }
        });

    });

})( jQuery );


/*
* Function used to delete ce21-single-sign-on-calendar-event
* */
function ce21_ss_delete_calendar_event(id)
{
    var ans = confirm("Are you sure want to delete this calendar event?");

    if (ans)
    {
        jQuery('#ce21_ss_admin_loader_div').show();
        var $params = {action: 'ce21_ss_delete_calendar_event', id: id};
        jQuery.ajax({
            type: "POST",
            url: ajax_login_object.ajaxurl,
            dataType: 'json',
            data: $params,
            success: function (response) {
                if (response)
                {
                    if (response.success == true)
                    {
                        jQuery('#ce21_ss_ce_notification_div').text(response.message);
                        jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-danger");
                        jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-success");
                        jQuery('#ce21_ss_ce_notification_div').addClass("ce21-ss-alert-success");
                        jQuery('#ce21_ss_ce_notification_div').show();
                        jQuery('#ce21_ss_admin_loader_div').hide();
                        jQuery("#ce21_calendar_events_table").DataTable().ajax.reload();
                    }
                    else
                    {
                        jQuery('#ce21_ss_ce_notification_div').text(response.message);
                        jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-danger");
                        jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-success");
                        jQuery('#ce21_ss_ce_notification_div').addClass("ce21-ss-alert-danger");
                        jQuery('#ce21_ss_ce_notification_div').show();
                        jQuery('#ce21_ss_admin_loader_div').hide();
                    }
                }
            },
            error: function (response) {
                jQuery('#ce21_ss_ce_notification_div').text("Something went wrong!");
                jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-danger");
                jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-success");
                jQuery('#ce21_ss_ce_notification_div').addClass("ce21-ss-alert-danger");
                jQuery('#ce21_ss_ce_notification_div').show();
                jQuery('#ce21_ss_admin_loader_div').hide();
            }
        });
    }
}


/*
* Function used to open edit-calendar-event-modal
* */
function ce21_ss_open_edit_calendar_event_modal(id)
{
    jQuery('#ce21_ss_admin_loader_div').show();
    jQuery('#hdn_ce21_calendar_event_id').val(id);

    var $params = {action: 'get_ce21_single_sign_on_calendar_event', id: id};
    jQuery.ajax({
        type: "POST",
        url: ajax_login_object.ajaxurl,
        dataType: 'json',
        data: $params,
        success: function (response) {
            if (response)
            {
                if (response.success == true)
                {
                    jQuery('#txt_ce21_ss_ece_title').val(response.event.title);
                    jQuery('#txt_ce21_ss_ece_start_date').val(response.event.start_date);
                    jQuery('#txt_ce21_ss_ece_end_date').val(response.event.end_date);
                    jQuery('#txt_ce21_ss_ece_url').val(response.event.url);
                    jQuery('#ce21_ss_admin_loader_div').hide();
                    var ce21_ss_ece_modal = document.getElementById("ce21_ss_edit_calender_event_modal");
                    ce21_ss_ece_modal.style.display = "block";
                }
                else
                {
                    jQuery('#ce21_ss_ce_notification_div').text(response.message);
                    jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-danger");
                    jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-success");
                    jQuery('#ce21_ss_ce_notification_div').addClass("ce21-ss-alert-danger");
                    jQuery('#ce21_ss_ce_notification_div').show();
                    jQuery('#ce21_ss_admin_loader_div').hide();
                }
            }
        },
        error: function (response) {
            jQuery('#ce21_ss_ce_notification_div').text("Something went wrong!");
            jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ce_notification_div').removeClass("ce21-ss-alert-success");
            jQuery('#ce21_ss_ce_notification_div').addClass("ce21-ss-alert-danger");
            jQuery('#ce21_ss_ce_notification_div').show();
            jQuery('#ce21_ss_admin_loader_div').hide();
        }
    });
}