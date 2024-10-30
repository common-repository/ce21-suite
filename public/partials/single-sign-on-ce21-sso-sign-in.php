<?php
$authorizeURI_ce21 = get_option('authorizeURI_ce21');
$reqPostId_ce21 = sanitize_text_field($_REQUEST['postid']);

$current_url_ce21 = home_url("article-authentication-ce21") . '/?postid=' . $reqPostId_ce21 . '&_=' . round(microtime(true) * 1000);
$return_url_ce21 = (!empty($authorizeURI_ce21)) ? $authorizeURI_ce21.'/WPAuthorize/LoginNew?returnURL='.$current_url_ce21 : '';

$forgot_password_url = (!empty($authorizeURI_ce21)) ? $authorizeURI_ce21.'/account/forgotpassword?returnURL='.$return_url_ce21 : '';
$sign_up_url = (!empty($authorizeURI_ce21)) ? $authorizeURI_ce21.'/account/register?returnURL='.$return_url_ce21 : '';
/*$email_change_url = (!empty($authorizeURI_ce21)) ? $authorizeURI_ce21.'/account/emailchanged?returnURL='.$return_url_ce21 : '';*/
/*
if (!is_user_logged_in())
{
    if (!isset($_REQUEST['postid']) || $_REQUEST['postid'] == "")
    {
        ob_clean();
        //wp_redirect( home_url() );
        header("Location: ".home_url());
        //exit;
    }
}
*/
?>

<div id="ce21_sign_in">
    <form method="post">
        <h1>Sign In</h1>
        <div id="ce21_ss_sign_in_notification_div" class="ce21-ss-alert" style="display: none">
        </div>
        <label for="ce21Email">Email</label>
        <input id="ce21Email" placeholder="Email" type="email" name="ce21Email" required />
        <!--<a href="<?php echo esc_url($email_change_url); ?>">Email changed since last order?</a>-->
        <label for="ce21Password">Password</label>
        <input id="ce21Password" placeholder="Password" type="password" name="ce21Password" required />
        <a href="<?php echo esc_url($forgot_password_url); ?>">Forgot your password?</a>
        <input id="ce21Postid" type="hidden" name="ce21Postid" value="" />
        <button id="ce21Login" name="ce21Login" type="submit" class="ce21-login-btn">Sign In</button>
        <div class="custom-text-style">
            <span style="color:#333333;" class="">Don't have an account?</span>
        </div>
        <a href="<?php echo esc_url($sign_up_url); ?>" class="ce21-signup-btn">Sign Up</a>
        <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
    </form>
</div>

<div id="ce21_ss_public_loader_div" class="ce21-ss-public-loader" style="display: none">
    <img src="<?php echo esc_url(SINGLE_SIGN_ON_CE21__PLUGIN_URL . "public/images/loader.gif"); ?>">
</div>