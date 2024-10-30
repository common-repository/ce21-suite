<div class="wrap">
    <h1 class="wp-heading-inline">Calender</h1>
    <a id="btn_ce21_ss_open_ance_modal" class="page-title-action">Add New</a>
    <p style="margin:0;"><i>All of the catalog programs will be listed on the calendar with the schedule details, pricing, and venue details.</i></p>
    <p style="margin:0;"><i>You can also create a custom calendar event from here and add it to the calendar.</i></p>
    <div class="ce21-ss-notice" style="margin-top:15px;">
        <h5>Please use the following shortcode to display calendar in front.</h5>
        <h5>[ce21-calendar] [ce21-mini-calendar]</h5>
    </div>

    <div id="ce21_ss_ce_notification_div" class="ce21-ss-alert" style="display: none">
    </div>

    <div class="table-responsive ce21-ss-custom-table-responsive">
        <table id="ce21_calendar_events_table" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" >
            <thead>
                <tr>
                    <th style="padding-left: 10px;padding-right: 10px;text-align: left">Title</th>
                    <th style="padding-left: 10px;padding-right: 10px;text-align: left">Start Date</th>
                    <th style="padding-left: 10px;padding-right: 10px;text-align: left">End Date</th>
                    <th style="padding-left: 10px;padding-right: 10px;text-align: left">URL</th>
                    <th style="padding-left: 10px;padding-right: 10px;text-align: left">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- The Modal -->
<div id="ce21_ss_add_new_calender_event_modal" class="ce21-ss-modal">

    <!-- Modal content -->
    <div class="ce21-ss-modal-content">
        <div class="ce21-ss-modal-header">
            <h2>Add New Calendar Event <span id="ce21_ss_ance_close_span" class="ce21-ss-close">&times;</span></h2>
            <form id="ce21_ss_add_new_calendar_event_form" name="ce21_ss_add_new_calendar_event_form">
        </div>
        <div class="ce21-ss-modal-body">
            <div id="ce21_ss_ance_notification_div" class="ce21-ss-alert" style="display: none">
            </div>
            <table class="form-table" role="presentation">
                <tbody>
                <tr>
                    <th scope="row">
                        <label for="txt_ce21_ss_ance_title">Title</label>
                    </th>
                    <td>
                        <input name="txt_ce21_ss_ance_title" type="text" id="txt_ce21_ss_ance_title" value="" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="txt_ce21_ss_ance_start_date">Start Date</label>
                    </th>
                    <td>
                        <input name="txt_ce21_ss_ance_start_date" type="text" id="txt_ce21_ss_ance_start_date" value="" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="txt_ce21_ss_ance_end_date">End Date</label>
                    </th>
                    <td>
                        <input name="txt_ce21_ss_ance_end_date" type="text" id="txt_ce21_ss_ance_end_date" value="" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="txt_ce21_ss_ance_url">URL</label>
                    </th>
                    <td>
                        <input name="txt_ce21_ss_ance_url" type="text" id="txt_ce21_ss_ance_url" value="" class="regular-text">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="ce21-ss-modal-footer">
            <p class="submit">
                <input type="button" name="btn_ce21_ss_ance" id="btn_ce21_ss_ance" class="button button-primary" value="Save Changes">
            </p>
            </form>
        </div>
    </div>

</div>

<!-- The Modal -->
<div id="ce21_ss_edit_calender_event_modal" class="ce21-ss-modal">

    <!-- Modal content -->
    <div class="ce21-ss-modal-content">
        <div class="ce21-ss-modal-header">
            <h2>Edit Calendar Event <span id="ce21_ss_ece_close_span" class="ce21-ss-close">&times;</span></h2>
            <form id="ce21_ss_edit_calendar_event_form" name="ce21_ss_edit_calendar_event_form">
        </div>
        <div class="ce21-ss-modal-body">
            <div id="ce21_ss_ece_notification_div" class="ce21-ss-alert" style="display: none">
            </div>
            <table class="form-table" role="presentation">
                <tbody>
                <tr>
                    <th scope="row">
                        <label for="txt_ce21_ss_ece_title">Title</label>
                    </th>
                    <td>
                        <input name="txt_ce21_ss_ece_title" type="text" id="txt_ce21_ss_ece_title" value="" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="txt_ce21_ss_ece_start_date">Start Date</label>
                    </th>
                    <td>
                        <input name="txt_ce21_ss_ece_start_date" type="text" id="txt_ce21_ss_ece_start_date" value="" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="txt_ce21_ss_ece_end_date">End Date</label>
                    </th>
                    <td>
                        <input name="txt_ce21_ss_ece_end_date" type="text" id="txt_ce21_ss_ece_end_date" value="" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="txt_ce21_ss_ece_url">URL</label>
                    </th>
                    <td>
                        <input name="txt_ce21_ss_ece_url" type="text" id="txt_ce21_ss_ece_url" value="" class="regular-text">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="ce21-ss-modal-footer">
            <p class="submit">
                <input type="hidden" name="hdn_ce21_calendar_event_id" id="hdn_ce21_calendar_event_id" value="">
                <input type="button" name="btn_ce21_ss_ece" id="btn_ce21_ss_ece" class="button button-primary" value="Save Changes">
            </p>
            </form>
        </div>
    </div>

</div>

<div id="ce21_ss_admin_loader_div" class="ce21-ss-admin-loader" style="display: none">
    <img src="<?php echo esc_url(SINGLE_SIGN_ON_CE21__PLUGIN_URL . "admin/images/loader.gif"); ?>">
</div>