<?php
/*
* Get Membership data
*/
function ce21_get_membership_wp_list()
{
    $api_settings = get_ce21_ss_api_settings_data();
    $api_Access_token = (!empty($api_settings)) ?  $api_settings->AccessToken : '';
    $base_url = (!empty($api_settings)) ? $api_settings->BaseURL : '';
    $url = $base_url."/wp/membershiplist";

    return getApiCall($url,'',$api_Access_token);
}

/*
 * Create Membership shortcode
 */
function ce21_membership_list_shortcode( $atts ) {

    // Attributes
    $atts = shortcode_atts(
        array(
            'group' => '',
        ),
        $atts
    );
    ob_start();
    getMembershipListHtml($atts['group']);
    return ob_get_clean();
}
add_shortcode( 'ce21_directory', 'ce21_membership_list_shortcode' );

/*
*
*/
function getMembershipListHtml($groupID){
    $api_settings = get_ce21_ss_api_settings_data();
    $CatalogURL = (!empty($api_settings)) ? $api_settings->CatalogURL : '';
    $iframeSrc = $CatalogURL."/directory?rt=wp&groupId=".$groupID;
    ?>
    <div class="ce21_membership_list_iframe">
        <iframe src="<?php echo esc_url($iframeSrc);?>" id="wpce21directory" style="width:100%;border:0;"></iframe>
    </div>
    <div class="ce21_membership_loader" id="ce21_membership_iframe_loader">
        <div class="loader">
            <div class="text">Loading</div>
            <div class="dots">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
    <style type="text/css">
        .ce21_membership_loader{
            position: fixed;
            display: none;
            width: 100% !important;
            max-width: 100% !important;
            height: 100%;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.5);
            z-index: 2;
            font-family: "Roboto", sans-serif;
        }
        .loader {
            display: -webkit-box;
            display: flex;
            font-size: 28px;
            position: relative;
            top: 50%;
            left: 50%;
            max-width: 300px;
            transform: translate(-50%,-50%);
        }
        .loader .text{
            color: #fff;
        }
        .loader .dots {
            display: -webkit-box;
            display: flex;
            position: relative;
            top: 20px;
            left: -10px;
            width: 100px;
            -webkit-animation: dots 2s ease infinite 1s;
            animation: dots 2s ease infinite 1s;
        }
        .loader .dots div {
            position: relative;
            width: 10px;
            height: 10px;
            margin-right: 10px;
            border-radius: 100%;
            background-color: #fff;
        }
        .loader .dots div:nth-child(1) {
            width: 0px;
            height: 0px;
            margin: 5px;
            margin-right: 15px;
            -webkit-animation: show-dot 2s ease-out infinite 1s;
            animation: show-dot 2s ease-out infinite 1s;
        }
        .loader .dots div:nth-child(4) {
            background-color: transparent;
            -webkit-animation: dot-fall-left 2s linear infinite 1s;
            animation: dot-fall-left 2s linear infinite 1s;
        }
        .loader .dots div:nth-child(4):before {
            position: absolute;
            width: 10px;
            height: 10px;
            margin-right: 10px;
            border-radius: 100%;
            background-color:#fff;
            content: '';
            -webkit-animation: dot-fall-top 2s cubic-bezier(0.46, 0.02, 0.94, 0.54) infinite 1s;
            animation: dot-fall-top 2s cubic-bezier(0.46, 0.02, 0.94, 0.54) infinite 1s;
        }

        @-webkit-keyframes dots {
            0% {
                left: -10px;
            }
            20%,100% {
                left: 10px;
            }
        }

        @keyframes dots {
            0% {
                left: -10px;
            }
            20%,100% {
                left: 10px;
            }
        }
        @-webkit-keyframes show-dot {
            0%,20% {
                width: 0px;
                height: 0px;
                margin: 5px;
                margin-right: 15px;
            }
            30%,100% {
                width: 10px;
                height: 10px;
                margin: 0px;
                margin-right: 10px;
            }
        }
        @keyframes show-dot {
            0%,20% {
                width: 0px;
                height: 0px;
                margin: 5px;
                margin-right: 15px;
            }
            30%,100% {
                width: 10px;
                height: 10px;
                margin: 0px;
                margin-right: 10px;
            }
        }
        @-webkit-keyframes dot-fall-left {
            0%, 5% {
                left: 0px;
            }
            100% {
                left: 200px;
            }
        }
        @keyframes dot-fall-left {
            0%, 5% {
                left: 0px;
            }
            100% {
                left: 200px;
            }
        }
        @-webkit-keyframes dot-fall-top {
            0%, 5% {
                top: 0px;
            }
            30%,100% {
                top: 50vh;
            }
        }
        @keyframes dot-fall-top {
            0%, 5% {
                top: 0px;
            }
            30%,100% {
                top: 50vh;
            }
        }

    </style>
    <?php
        wp_enqueue_script( "CrossWindowCommunication", plugin_dir_url( __FILE__ ) . "js/CrossWindowCommunication-1.0.1.js" );
    ?>

    <script>
        var iframeChanel = new CrossWindowCommunication(document.getElementById('wpce21directory'));
        iframeChanel.on('pageHeight', function (height) {
            var iFrameID = document.getElementById('wpce21directory');
            if(iFrameID) {
                // here you can make the height, I delete it first, then I make it again
                iFrameID.height = "";
                iFrameID.height = height;
            }
            jQuery('#ce21_membership_iframe_loader').hide();
        });
        iframeChanel.on('displayProgress', function () {
            console.log('ce21_membership_iframe_loader')
            jQuery('#ce21_membership_iframe_loader').show();

        });
    </script>
    <?php
}