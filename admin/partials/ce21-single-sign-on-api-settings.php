<?php
$api_settings = get_ce21_ss_api_settings_data();
$api_settings_id = (!empty($api_settings)) ? $api_settings->Id : '';
$base_url = (!empty($api_settings)) ? $api_settings->BaseURL : '';
$client_id = (!empty($api_settings)) ? $api_settings->ClientId : '';
$secret_key = (!empty($api_settings)) ? $api_settings->SecretKey : '';
$catalog_url = (!empty($api_settings)) ? $api_settings->CatalogURL : '';
?>
<div class="wrap">
    <h1>API Settings</h1>   
    <p style="margin:0;"><i>API keys are required for the Program List and Calendar widgets. The credentials can be found in Manager, <a href="https://manager.ce21.com/" target="_blank">https://manager.ce21.com/</a> > Settings > API Settings.</i></p>
    <p style="margin:0;"><i>The Base URL, Client ID, and Secret Key are listed there. The Catalog URL is the frontend URL for the catalog.</i></p>
    <form id="ce21_ss_api_settings_form" name="ce21_ss_api_settings_form">
        <div id="ce21_ss_api_settings_notification_div" class="ce21-ss-alert" style="display: none">
        </div>
        <table class="form-table" role="presentation">
            <input type="hidden" name="api_settings_id" id="api_settings_id" value="<?php echo esc_attr($api_settings_id); ?>">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="txt_ce21_ss_base_url">Base URL</label>
                    </th>
                    <td>
                        <input name="txt_ce21_ss_base_url" type="text" id="txt_ce21_ss_base_url" value="<?php echo esc_attr($base_url); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="txt_ce21_ss_client_id">Client Id</label>
                    </th>
                    <td>
                        <input name="txt_ce21_ss_client_id" type="text" id="txt_ce21_ss_client_id" value="<?php echo esc_attr($client_id); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="txt_ce21_ss_secret_key">Secret Key</label>
                    </th>
                    <td>
                        <input name="txt_ce21_ss_secret_key" type="text" id="txt_ce21_ss_secret_key" value="<?php echo esc_attr($secret_key); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="txt_ce21_ss_catalog_url">Catalog URL</label>
                    </th>
                    <td>
                        <input name="txt_ce21_ss_catalog_url" type="text" id="txt_ce21_ss_catalog_url" value="<?php echo esc_attr($catalog_url); ?>" class="regular-text">
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="button" name="btn_save_ce21_ss_api_settings" id="btn_save_ce21_ss_api_settings" class="button button-primary" value="Save Changes">
        </p>
    </form>
</div>
<div id="ce21_ss_admin_loader_div" class="ce21-ss-admin-loader" style="display: none">
    <img src="<?php echo esc_url(SINGLE_SIGN_ON_CE21__PLUGIN_URL . "admin/images/loader.gif"); ?>">
</div>