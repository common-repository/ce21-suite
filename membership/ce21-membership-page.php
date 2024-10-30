<?php
$messagesArr = '';
$access_token_response = get_access_token();

if ($access_token_response['success'])
{
    $membershipLists = ce21_get_membership_wp_list();
}
else{
    $message = $access_token_response['message'];
    $messageClassName = 'error';
    $messagesArr = array( 'message' => $message,  'messageClassName' => $messageClassName );
}

if(!empty($messagesArr['message']) && !empty($messagesArr['messageClassName']))
{
    echo '<div id="setting-error-settings_updated" class="'.esc_attr($messagesArr['messageClassName']).' settings-error notice is-dismissible"> 
                                <p><strong> '.esc_html($messagesArr['message']).'</strong></p>
                                <button type="button" class="notice-dismiss">
                                    <span class="screen-reader-text">Dismiss this notice.</span>
                                </button>
                            </div>';
}

if ($access_token_response['success']) {
?>
    <div class="container-fluid">
        <div class="ce21-membership row">
            <div class="col-md-12 margin-top-15">
                <h2>Directory Settings</h2>
                <p style="margin:0;"><i>Use the below tool to generate a shortcode for a particular group to show Directory details of that group.</i></p>
                <p style="margin:0;"><i>Directory is a great tool to search for members within the group. Various search filters do ease the process a lot.</i></p>
                <form id="ce21_membership_list_settings_form" name="ce21_membership_list_settings_form">
                    <div class="margin-top-15">
                        <label for="ce21_membership" class="d-block">Select Group</label>
                        <?php if(!empty($membershipLists)){ ?>
                            <select name="ce21_membership" id="ce21_membership" class="activeMembership">
                                <option value="0">All</option>
                                <?php
                                    foreach ($membershipLists as $membershipList)
                                    {
                                        echo '<option value="'.esc_attr($membershipList->membershipTypeId).'">'.esc_html($membershipList->name).'</option>';
                                    }
                                ?>
                            </select>
                        <?php } ?>
                    </div>
                    <input type="button" id="btn_save_ce21_membership_settings" class="btn btn-success margin-top-15" value="Generate Shortcode"/>
                </form>

                <div id="ce21_ss_api_settings_notification_div" class="ce21-ss-alert alert alert-success" style="display: none">
                </div>

                <div class="row mt-4" id="ce21_div_generated_shortcode" style="display: none">
                    <div class="col-md-2">
                        <label for="ce21_generated_shortcode">Shortcode</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" id="ce21_generated_shortcode" class="w-100" value="" />
                    </div>
                    <div class="col-md-2">
                        <button id="copy_short_code" class="btn btn-success" onclick="copyShortcode()">
                            Copy Shortcode
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php } ?>