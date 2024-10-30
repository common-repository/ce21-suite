<?php
/*
 * Function used to generate access token
 * */
function get_access_token()
{
    global $wpdb;
    $api_settings = get_ce21_ss_api_settings_data();

    if(!empty($api_settings))
    {
        $expiry_date = $api_settings->ExpiryDate;
        $expiry_date_stt = strtotime($expiry_date);
        $current_date_time = date('Y-m-d H:i:s');
        $current_date_time_stt = strtotime($current_date_time);

        if ($expiry_date_stt < $current_date_time_stt || $api_settings->AccessToken == '' || $expiry_date == '')
        {
            $url = $api_settings->BaseURL."/token/wp/".$api_settings->ClientId;
            $authorization = 'Basic '.base64_encode($api_settings->ClientId.':'.$api_settings->SecretKey);

            $args = array(
                'headers' => array(
                    'authorization' => $authorization,
                    'cache-control' => "no-cache"
                )
            );
            $response = wp_remote_get($url, $args);
            $err = is_wp_error($response);
            $httpcode = wp_remote_retrieve_response_code($response);

            if ( $httpcode != 200 ){
                $response_message = array(
                    'success' => false,
                    'access_token' => '',
                    'message' => 'Something went wrong! Please check API settings'
                );
                return $response_message;
            }

            if ($err)
            {
                $response_message = array(
                    'success' => false,
                    'access_token' => '',
                    'message' => 'Something went wrong while generating access token!'
                );
                return $response_message;
            }
            else
            {
                $response_obj = json_decode($response['body']);

                $expiry_date = date("Y-m-d H:i:s", strtotime("+$response_obj->expires_in sec"));

                $update_record = $wpdb->update($wpdb->prefix . 'ce21_api_settings', array(
                        'AccessToken' => $response_obj->access_token,
                        'ExpiryDate' => $expiry_date,
                        'ModifiedOn' => date('Y-m-d H:i:s')), array(
                            'Id' => $api_settings->Id
                        )
                    );

                $response_message = array(
                    'success' => true,
                    'access_token' => $response_obj->access_token,
                    'message' => 'Access Token generated successfully!'
                );
                return $response_message;
            }
        }
        else
        {
            $response_message = array(
                'success' => true,
                'access_token' => $api_settings->AccessToken,
                'message' => 'Access Token fetched successfully!'
            );
            return $response_message;
        }

    }
    else
    {
        $response_message = array(
            'success' => false,
            'access_token' => '',
            'message' => 'No api settings record found!'
        );
        return $response_message;
    }
}
?>