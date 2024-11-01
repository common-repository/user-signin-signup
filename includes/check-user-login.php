<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { die(); }

add_action("wp_enqueue_scripts", "iusisu_user_loggedin_scripts");
function iusisu_user_loggedin_scripts()
{
    global $post;

    // Pages
    $iusisu_login_page = get_option('iusisu_user_login_page');
    $iusisu_reg_page = get_option('iusisu_user_registration_page');
    $iusisu_myaccount_page = get_option('iusisu_user_my_account_page');
    $iusisu_forgotpass_page = get_option('iusisu_user_forgot_pass_page');
    $iusisu_current_page = get_the_ID();

    // Check if the user is not logged in and on the my account page
    if (is_user_logged_in() == false && $iusisu_current_page == $iusisu_myaccount_page) {
        $script = "window.location='" . esc_url(site_url('/sign-in/')) . "';";
        wp_add_inline_script('iusisu-custom-script', $script, 'after');
    } elseif ((is_user_logged_in() == true && $iusisu_current_page == $iusisu_login_page) || (is_user_logged_in() == true && $iusisu_current_page == $iusisu_reg_page) || (is_user_logged_in() == true && $iusisu_current_page == $iusisu_forgotpass_page)) {
        $script = "window.location.href ='" . esc_url(get_permalink($iusisu_myaccount_page)) . "';";
        wp_add_inline_script('iusisu-custom-script', $script, 'after');
    }
}

add_action("wp_head", "iusisu_user_loggedin");
function iusisu_user_loggedin()
{
    ?>
    <div id="iusisu_user_loader" style="display: none;"></div>
    <?php
}