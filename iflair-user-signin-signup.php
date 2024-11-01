<?php
/**
 * Plugin Name: 	User Sign In / Sign Up
 * Plugin URI: 		https://profiles.wordpress.org/iflairwebtechnologies
 * Description: 	We're provide functionality like user can able to Sign Up, Sign In, Edit profile, Forget password, Change password. This plugin will be add user as a wordpress user. We have given many customisation settings for colors, messages, email configuration
 * Version: 		1.1.1
 * License: 		GPLv2 or later
 * Author: 			iFlair - Wordpress Team
 * Author URI:		https://www.iflair.com/
 * Text Domain:		iflair-user-signin-signup
 * Domain Path:		/languages 
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { die(); }

//ENQUEUE JQUERY AND CSS FILE FOR FRONTEND
add_action('wp_enqueue_scripts', 'iusisu_enqueue_style_and_script');
function iusisu_enqueue_style_and_script()
{ 
	?>
	<style>
		:root {
			--primary: <?php echo esc_html(get_option('iusisu_primary_color')); ?>;
			--secondary: <?php echo esc_html(get_option('iusisu_secondary_color')); ?>;
			--pre-secondary: <?php echo esc_html(get_option('iusisu_pre_secondary_color')); ?>;
			--btn-hover: <?php echo esc_html(get_option('iusisu_pre_secondary_hover_color')); ?>;
		}
	</style>
	<?php
	wp_enqueue_script('jquery');
	wp_enqueue_style('iusisu-style', plugin_dir_url(__FILE__) . 'assets/css/public/style.css', array(), '1.1.1', false);
	wp_enqueue_script('iusisu-script-validate', plugin_dir_url(__FILE__) . 'assets/js/public/jquery.validate.min.js', array('jquery'), '1.1.1', false);
	wp_enqueue_script('iusisu-custom-script', plugin_dir_url(__FILE__) . 'assets/js/public/scripts.js', array('jquery'), '1.1.1', false);
	wp_localize_script('iusisu-custom-script', 'admin_ajaxObj', array('ajax_url' => esc_url(admin_url('admin-ajax.php')), 'curr_user' => get_current_user_id()));
	wp_enqueue_style('iusisu-font-awesome',plugin_dir_url(__FILE__) . 'assets/css/public/font-awesome/css/all.css');
}

// ENQUEUE JQUERY AND CSS FILE FOR BACKEND
add_action('admin_enqueue_scripts', 'iusisu_admin_enqueue_style_and_script');
function iusisu_admin_enqueue_style_and_script()
{
	wp_enqueue_media();
	wp_enqueue_script('iusisu-admin', plugin_dir_url(__FILE__) . 'assets/js/admin/admin-script.js', array('jquery'), false, time());
	wp_localize_script('iusisu-admin', 'admin_ajaxObj', array('ajax_url' => esc_url(admin_url('admin-ajax.php')), 'curr_user' => get_current_user_id()));	
	wp_enqueue_style('iusisu-plugin-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin/plugin-admin-style.css', array(), '1.1.1', false);
	wp_enqueue_script('iusisu-script-validate', plugin_dir_url(__FILE__) . 'assets/js/admin/jquery.validate.min.js', array('jquery'), '1.1.1', false);
}

// DEFINE PLUGIN DIRECTORY
if (!defined('IUSISU_DIR'))
{
	define('IUSISU_DIR', dirname(__FILE__)); 
}
if (!defined('IUSISU_INCLUDES_DIR'))
{
	// Parse the URL
	$includes_parsed_url = wp_parse_url(esc_url(includes_url()));
	// Get the path
	$includes_path = trim($includes_parsed_url['path'], '/');
	// Split the path into segments
	$includes_path_segments = explode('/', $includes_path);
	// Get the last segment
	$includes_last_segment = end($includes_path_segments);
	define('IUSISU_INCLUDES_DIR', ABSPATH.$includes_last_segment);	
}
if (!defined('IUSISU_ADMIN_DIR'))
{	
	// Parse the URL
	$admin_parsed_url = wp_parse_url(esc_url(admin_url()));
	// Get the path
	$admin_path = trim($admin_parsed_url['path'], '/');
	// Split the path into segments
	$admin_path_segments = explode('/', $admin_path);
	// Get the last segment
	$admin_last_segment = end($admin_path_segments);	
	define('IUSISU_ADMIN_DIR', ABSPATH.$admin_last_segment);	
}

define( 'IUSISU_PLUGIN_FILE', __FILE__ );

// APPLY HOOK WHEN PLUGIN ACTIVE
register_activation_hook( IUSISU_PLUGIN_FILE, 'iusisu_plugin_activation' );
function iusisu_plugin_activation()
{
	// BY DEFAULT WHEN ACTIVE PLUGIN THEN FILL DEFAULT MESSAGE
	$iusisu_user_form_success_msg = array(
		'iusisu_registration_form' => '[iusisu_signup_form]',
		'iusisu_login_form' => '[iusisu_signin_form]',
		'iusisu_my_account' => '[iusisu_my_account]',
		'iusisu_forgot_password' => '[iusisu_forgot_password]',
		'iusisu_change_password' => '[iusisu_change_password]',
		'iusisu_primary_color' =>  '#000000',
		'iusisu_secondary_color' =>  '#17b4eb',
		'iusisu_pre_secondary_color' =>  '#ffffff',
		'iusisu_pre_secondary_hover_color' =>  '#000000',
		'iusisu_login_msg' => 'User Sign In successfully',
		'iusisu_registration_msg' => 'User sign Up successfully, You will be get email. Please activate your account from it',
		'iusisu_forget_pass_msg' => 'We have successfully sent new password on your email address',
		'iusisu_change_pass_msg' => 'Password has been change successfully',
		'iusisu_role' => 'subscriber',
		'iusisu_user_subject' => 'Welcome ! you have registered successfully',
		'iusisu_admin_subject' => 'New user registered on our site',
		'iusisu_userforgot_subject' => 'Forget password',
		'iusisu_userchange_subject' => 'Your password has been change successfully',
		'iusisu_user_from_email' => get_option('admin_email'),
		'iusisu_user_signature' => '-'."<br>".'Thanks,'."<br>".get_bloginfo( 'name' )." Team",
		'iusisu_user_registration_email_body' => "Hello {user_name},"."<br><br>"."Welcome to our site : {site_name}"."<br><br>"."Please click the following link to activate your account"."<br>"."{Please activate your account}"."<br><br>"."<strong>"."Note : After click on above link, you need to Sign In, so your account will be activate"."</strong>"."<br><br>",
		'iusisu_user_registration_email_body_admin' => "Hello Admin,"."<br><br>"."New user is registered on our site"."<br><br>"."Here are details :"."<br>"."User name : {user_name}"."<br>"."Email : {user_email}"."<br><br>",
		'iusisu_user_forget_password_email_body' => "Hello {user_name},"."<br><br>"."Your user name is : {user_name}"."<br>"."Your email address is : {user_email}"."<br><br>"."Your new password is :"."<br>"."{user_password}"."<br><br>"."<a href='".esc_url(site_url()).'/sign-in/'."'>Click here to login</a>"."<br><br>",
		'iusisu_user_password_change_email_body' => "Hello {user_name},"."<br><br>"."Your user name is : {user_name}"."<br>"."Your email address is : {user_email}"."<br><br>"."Your new password is :"."<br>"."{user_password}"."<br><br>",
	);
	foreach ($iusisu_user_form_success_msg as $iusisu_key => $iusisu_success_value)
	{
		update_option($iusisu_key, wp_kses_post($iusisu_success_value));
	}
	// END, BY DEFAULT WHEN ACTIVE PLUGIN THEN FILL DEFAULT MESSAGE

	if ( ! current_user_can( 'activate_plugins' ) ) return;
	global $wpdb;
	$iusisu_pages = array(
		'login'=>array('title'=>'Sign In','content'=>'[iusisu_signin_form]','option_page'=>'iusisu_user_login_page'),
		'registration'=>array('title'=>'Sign Up','content'=>'[iusisu_signup_form]','option_page'=>'iusisu_user_registration_page'),
		'forget-password'=>array('title'=>'Forget Password','content'=>'[iusisu_forgot_password]','option_page'=>'iusisu_user_forgot_pass_page'),
		'user-my-account'=>array('title'=>'User Account','content'=>'[iusisu_my_account]','option_page'=>'iusisu_user_my_account_page')
	);

	foreach ($iusisu_pages as $iusisu_pages_key => $iusisu_page_value)
	{		
		$iusisu_query = $wpdb->prepare("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = %s",$iusisu_pages_key);

		if ( null === $wpdb->get_row( $iusisu_query ) ) {
			$iusisu_current_user = wp_get_current_user();
			// create post object
			$iusisu_cnt = 'publish';
			$iusisu_page = 'page';
			$iusisu_page = array(
				'post_title'  => sanitize_text_field($iusisu_page_value['title']),
				'post_status' => sanitize_text_field($iusisu_cnt),
				'post_content'=> sanitize_textarea_field($iusisu_page_value['content']),
				'post_author' => (int) $iusisu_current_user->ID,
				'post_type'   => sanitize_text_field($iusisu_page),
			);

			if (!get_page_by_path( $iusisu_page_value['title'], OBJECT, 'page'))
			{   // Check If Page Not Exits
				// insert the post into the database
				$iusisu_postID = wp_insert_post($iusisu_page);
				update_option($iusisu_page_value['option_page'], (int) $iusisu_postID);
			}
		}
	}
}

//INCLUDE REQUIRED PAGES
require_once(IUSISU_INCLUDES_DIR . '/pluggable.php');
require_once(IUSISU_DIR . '/includes/check-user-login.php');
require_once(IUSISU_DIR . '/includes/user-login.php');
require_once(IUSISU_DIR . '/includes/user-registration.php');
require_once(IUSISU_DIR . '/includes/forgot-password.php');
require_once(IUSISU_DIR . '/includes/user-account.php');
//END INCLUDE REQUIRED PAGES

//REMOVE THE ADMIN BAR FROM THE FRONT END
if (!current_user_can('administrator'))
{
	add_filter('show_admin_bar', '__return_false');
}
//END REMOVE THE ADMIN BAR FROM THE FRONT END

add_action('wp_authenticate', 'iusisu_isUserActivated');
function iusisu_isUserActivated($username)
{ 
	// First need to get the user object
	$iusisu_user = get_user_by('login', $username);
	if (isset($iusisu_user) && !empty($iusisu_user))
	{
		$iusisu_user = get_user_by('email', $username);
		if (isset($iusisu_user) && !empty($iusisu_user))
		{
			return $username;
		}
	}

	$iusisu_userStatus = get_user_meta($iusisu_user->ID, 'is_activated', 1);

	if ( in_array( 'administrator', (array) $iusisu_user->roles ) ) {
    	//The user has the "author" role
		$iusisu_login_page = esc_url(home_url('/sign-in/'));
		//if ($iusisu_userStatus == 0)
		if (empty($iusisu_userStatus))
		{
			wp_redirect($iusisu_login_page . "?login=failed");
			exit;
		}
	}
}

add_action('wp', 'iusisu_redirect');
function iusisu_redirect()
{
	if (isset($_GET['act']) && !empty($_GET['act']))
	{
		$encoded_data = sanitize_text_field($_GET['act']);
		$iusisu_data = json_decode(urldecode($encoded_data), true);
		//$iusisu_data = unserialize(base64_decode(sanitize_text_field($_GET['act'])));
		$iusisu_code = get_user_meta($iusisu_data['id'], 'activation_code', true);

		// verify whether the code given is the same as ours
		if ($iusisu_code == $iusisu_data['code'])
		{ 
			// update the user meta
			$iusisu_true_val = 1;
			update_user_meta($iusisu_data['id'], 'is_activated', (int) $iusisu_true_val);
			wp_safe_redirect(esc_url(home_url('/sign-in/')));
			exit;
		}
	}
}
//END CUSTOM ROLE CREATE FUNCTION

// ADD ADMIN MENU
add_action('admin_menu', 'iusisu_signin_signup_plugin_create_menu');
function iusisu_signin_signup_plugin_create_menu()
{	
	$plugin_icon_url = plugin_dir_url( __FILE__ ). 'assets/css/admin/images/menu-icon.svg';
	// CREATE CUSTOM IFLAIR SIGN IN / SIGN UP SETTING MENU
	add_menu_page('User Sign In / Sign Up', 'User Sign In / Sign Up', 8, IUSISU_DIR, 'iusisu_admin_menu_page',''.esc_url($plugin_icon_url).'', 5);

	// CALL REGISTER SETTING FUNCTION
	add_action( 'admin_init', 'iusisu_register_iflair_signin_signup_plugin_settings' );
}
//END ADD ADMIN MENU

// REGISTER SETTING
function iusisu_register_iflair_signin_signup_plugin_settings()
{
	register_setting( 'iusisu-user-signin-signup', 'iusisu_registration_form' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_login_form' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_my_account' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_forgot_password' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_change_password' );

	register_setting( 'iusisu-user-signin-signup', 'iusisu_role' );

	register_setting( 'iusisu-user-signin-signup', 'iusisu_primary_color' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_secondary_color' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_pre_secondary_color' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_pre_secondary_hover_color' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_login_msg' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_registration_msg' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_forget_pass_msg' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_change_pass_msg' );

	register_setting( 'iusisu-user-signin-signup', 'iusisu_user_from_email' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_user_signature');
	register_setting( 'iusisu-user-signin-signup', 'iusisu_user_subject' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_admin_subject' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_userforgot_subject' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_userchange_subject' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_user_registration_email_body' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_user_registration_email_body_admin' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_user_forget_password_email_body' );
	register_setting( 'iusisu-user-signin-signup', 'iusisu_user_password_change_email_body' );
}
// END, REGISTER SETTING

// CALLBACK FUNCTION FOR IFLAIR SIGN IN / SIGN UP SETTING
function iusisu_admin_menu_page()
{
	?>
	<div class="wrap iusisu_a_admin">
		<h1><?php echo esc_html__('User Sign In / Sign Up','iflair-user-signin-signup');?></h1>
		<form method="post" action="options.php">
		    <?php settings_fields( 'iusisu-user-signin-signup' ); ?>
		    <?php do_settings_sections( 'iusisu-user-signin-signup' ); ?>
		    <div class="iusisu_a_tabing-wrapper iusisu_a_admin-tabing">
				<!-- Nav tabs -->
				<ul class="iusisu_a_tabs">
					<li class="iusisu_a_tab-link iusisu_a_shortcode_info iusisu_a_current" data-tab="iusisu_site_shortcodes"><?php echo esc_html__('Shortcode information','iflair-user-signin-signup');?></li>
					<li class="iusisu_a_tab-link iusisu_a_user_role_setting" data-tab="iusisu_user_section"><?php echo esc_html__('User role settings','iflair-user-signin-signup');?></li>
					<li class="iusisu_a_tab-link iusisu_a_user_page_setting" data-tab="iusisu_page_customization"><?php echo esc_html__('Sign Up / Sign In form settings','iflair-user-signin-signup');?></li>
					<li class="iusisu_a_tab-link iusisu_a_email_setting" data-tab="iusisu_mail_settings"><?php echo esc_html__('Email settings','iflair-user-signin-signup');?></li>
				</ul>
				<div class="iusisu_a_tab-content iusisu_a_current" id="iusisu_site_shortcodes">
					<div class="iusisu_a_shortcode-copy">
						<h2><?php echo esc_html__('Copy below shortcode and paste in any page','iflair-user-signin-signup');?></h2>
						<div class="iusisu_a_click">
							<div class="iusisu_a_shortcode-frm">				
								<label><?php echo esc_html__('Sign Up form :','iflair-user-signin-signup');?></label>
								<input type="text" name="iusisu_registration_form" onfocus="this.select();" value="<?php if(!empty(get_option('iusisu_registration_form'))) { echo esc_attr( get_option('iusisu_registration_form') ); } ?>" class="iusisu_a_copy" readonly>
								<div class="iusisu-copied-txt" style="display: none;"><h4><?php echo esc_html__('Copied !','iflair-user-signin-signup');?></h4></div><br>
							</div>
							<div class="iusisu_a_shortcode-frm">
								<label><?php echo esc_html__('Sign In form :','iflair-user-signin-signup');?></label>	
								<input type="text" name="iusisu_login_form" onfocus="this.select();" value="<?php if(!empty(get_option('iusisu_login_form'))) { echo esc_attr( get_option('iusisu_login_form') ); } ?>" class="iusisu_a_copy" readonly><div class="iusisu-copied-txt" style="display: none;"><h4><?php echo esc_html__('Copied !','iflair-user-signin-signup');?></h4></div><br>
							</div>
							<div class="iusisu_a_shortcode-frm">
								<label><?php echo esc_html__('My account page :','iflair-user-signin-signup');?></label>		
								<input type="text" name="iusisu_my_account" onfocus="this.select();" value="<?php if(!empty(get_option('iusisu_my_account'))) { echo esc_attr( get_option('iusisu_my_account') ); } ?>" class="iusisu_a_copy" readonly><div class="iusisu-copied-txt" style="display: none;"><h4><?php echo esc_html__('Copied !','iflair-user-signin-signup');?></h4></div><br>
							</div>
							<div class="iusisu_a_shortcode-frm">			
								<label><?php echo esc_html__('Forgot password form :','iflair-user-signin-signup');?></label>
								<input type="text" name="iusisu_forgot_password" onfocus="this.select();" value="<?php if(!empty(get_option('iusisu_forgot_password'))) { echo esc_attr( get_option('iusisu_forgot_password') ); } ?>" class="iusisu_a_copy" readonly><div class="iusisu-copied-txt" style="display: none;"><h4><?php echo esc_html__('Copied !','iflair-user-signin-signup');?></h4></div><br>
							</div>
						</div>
					</div>
				</div>

				<div class="iusisu_a_tab-content" id="iusisu_user_section">
					<div class="iusisu_a_user_roles_select">
						<h2><?php echo esc_html__('Please choose role for new user Sign Up','iflair-user-signin-signup');?></h2>
						<?php global $wp_roles; ?>
						<select name="iusisu_role">
							<option value="" disabled="disabled"><?php echo esc_html__('Choose User Role','iflair-user-signin-signup');?></option>
							<?php foreach ($wp_roles->roles as $key => $user_role_value)
							{ ?>
								<option value="<?php echo esc_attr($key);?>" <?php selected(esc_attr(get_option('iusisu_role')) , $key);?>><?php echo esc_html($user_role_value['name']);?></option>
								<?php
							} ?>
						</select>
					</div>
				</div>

				<div class="iusisu_a_tab-content" id="iusisu_page_customization">
					<div class="iusisu_a_inner-column-wrapper">
						<div class="iusisu_a_form_colors iusisu_a_col-6">
							<h2><?php echo esc_html__('Set colors','iflair-user-signin-signup');?></h2>
							<div class="iusisu_a_add_color"> 
								<div class="iusisu_a_input">
									<label for="iusisu_primary_color"><?php echo esc_html__('Select background color (Primary color)','iflair-user-signin-signup');?></label>
									<input type="color" name="iusisu_primary_color" id="iusisu_primary_color" value="<?php if(!empty(get_option('iusisu_primary_color'))) { echo esc_attr( get_option('iusisu_primary_color') ); } ?>">
								</div>
								<div  class="iusisu_a_input">
									<label for="iusisu_secondary_color"><?php echo esc_html__('Select text color (Primary color)','iflair-user-signin-signup');?></label>
									<input type="color" name="iusisu_secondary_color" id="iusisu_secondary_color" value="<?php if(!empty(get_option('iusisu_secondary_color'))) { echo esc_attr( get_option('iusisu_secondary_color') ); } ?>">
								</div>
								<div  class="iusisu_a_input">
									<label for="iusisu_pre_secondary_color"><?php echo esc_html__('Select hover text color (Secondary color)','iflair-user-signin-signup');?></label>
									<input type="color" name="iusisu_pre_secondary_color" id="iusisu_pre_secondary_color" value="<?php if(!empty(get_option('iusisu_pre_secondary_color'))) { echo esc_attr( get_option('iusisu_pre_secondary_color') ); } ?>">
								</div>
								<div  class="iusisu_a_input">
									<label for="iusisu_pre_secondary_hover_color"><?php echo esc_html__('Select hover button background color (Secondary color)','iflair-user-signin-signup');?></label>
									<input type="color" name="iusisu_pre_secondary_hover_color" id="iusisu_pre_secondary_hover_color" value="<?php if(!empty(get_option('iusisu_pre_secondary_hover_color'))) { echo esc_attr( get_option('iusisu_pre_secondary_hover_color') ); } ?>">
								</div>
							</div>
						</div>
						<div class="iusisu_a_form_custom_message iusisu_a_col-6">
							<h2><?php echo esc_html__('For every forms :: Messages','iflair-user-signin-signup');?></h2>
							<p class="iusisu_a_form_success_message"><?php echo esc_html__('You can edit messages used in various situations here','iflair-user-signin-signup');?></p>
							<div id="iusisu_logo"> 
								<div class="iusisu_a_input">
									<label for="iusisu_login_msg"><?php echo esc_html__('Sign In success message','iflair-user-signin-signup');?></label>
									<input type="text" name="iusisu_login_msg" id="iusisu_login_msg" class="regular-text" value="<?php if(!empty(get_option('iusisu_login_msg'))) { echo esc_attr( get_option('iusisu_login_msg') ); } ?>">
								</div>
								<div class="iusisu_a_input">
									<label for="iusisu_registration_msg"><?php echo esc_html__('Sign Up success message','iflair-user-signin-signup');?></label>
									<input type="text" name="iusisu_registration_msg" id="iusisu_registration_msg" class="regular-text" value="<?php if(!empty(get_option('iusisu_registration_msg'))) { echo esc_attr( get_option('iusisu_registration_msg') ); } ?>">
								</div>
								<div class="iusisu_a_input">
									<label for="iusisu_forget_pass_msg"><?php echo esc_html__('Forget password success message','iflair-user-signin-signup');?></label>
									<input type="text" name="iusisu_forget_pass_msg" id="iusisu_forget_pass_msg" class="regular-text" value="<?php if(!empty(get_option('iusisu_forget_pass_msg'))) { echo esc_attr( get_option('iusisu_forget_pass_msg') ); } ?>">
								</div>
								<div class="iusisu_a_input">
									<label for="iusisu_change_pass_msg"><?php echo esc_html__('Change password success message','iflair-user-signin-signup');?></label>
									<input type="text" name="iusisu_change_pass_msg" id="iusisu_change_pass_msg" class="regular-text" value="<?php if(!empty(get_option('iusisu_change_pass_msg'))) { echo esc_attr( get_option('iusisu_change_pass_msg') ); } ?>">
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="iusisu_a_tab-content" id="iusisu_mail_settings">
					<div class="iusisu_a_inner-column-wrapper">
						<div class='iusisu_a_user-mail iusisu_a_col-6'>
							<h2><?php echo esc_html__('All mail settings','iflair-user-signin-signup');?></h2>
							<div class='iusisu_a_input'>
								<label><?php echo esc_html__('From email','iflair-user-signin-signup');?> </label>
								<input type="text" name="iusisu_user_from_email" id="iusisu_user_from_email" class="regular-text" value="<?php if(!empty(get_option('iusisu_user_from_email'))) {echo esc_attr( get_option('iusisu_user_from_email') ); } ?>">
							</div>

							<div class='iusisu_a_input'>
								<label><?php echo esc_html__('Email signature','iflair-user-signin-signup');?></label>
								<?php  wp_kses_post(wp_editor( html_entity_decode(get_option('iusisu_user_signature')  ), 'iusisu_user_signature', array('editor_height' => 120)));  ?>
							</div>
						</div>
						<hr>
						<div class='iusisu_a_all-mail iusisu_a_col-6'>
							<h2><?php echo esc_html__('Email subjects','iflair-user-signin-signup');?></h2>
							<div class='iusisu_a_input'>
								<label><?php echo esc_html__("Sign Up email 'Subject' for user",'iflair-user-signin-signup');?> </label> 
								<input type="text" name="iusisu_user_subject" id="iusisu_user_subject" class="regular-text" value="<?php if(!empty(get_option('iusisu_user_subject'))) { echo esc_attr( get_option('iusisu_user_subject') ); } ?>">
							</div>
							<div class='iusisu_a_input'> 
								<label><?php echo esc_html__("Sign Up email 'Subject' for admin",'iflair-user-signin-signup');?> </label>
								<input type="text" name="iusisu_admin_subject" id="iusisu_admin_subject" class="regular-text" value="<?php if(!empty(get_option('iusisu_admin_subject'))) { echo esc_attr( get_option('iusisu_admin_subject') ); } ?>">
							</div>
							<div class='iusisu_a_input'>
								<label><?php echo esc_html__("Forgot password email 'Subject'",'iflair-user-signin-signup');?> </label>
								<input type="text" name="iusisu_userforgot_subject" id="iusisu_userforgot_subject" class="regular-text" value="<?php if(!empty(get_option('iusisu_userforgot_subject'))) { echo esc_attr( get_option('iusisu_userforgot_subject') ); } ?>">
							</div>
							<div class='iusisu_a_input'>
								<label><?php echo esc_html__("Change password email 'Subject'",'iflair-user-signin-signup');?> </label>
								<input type="text" name="iusisu_userchange_subject" id="iusisu_userchange_subject" class="regular-text" value="<?php if(!empty(get_option('iusisu_userchange_subject'))) { echo esc_attr( get_option('iusisu_userchange_subject') ); } ?>">
							</div>
						</div>
					</div>
					<hr>
					<div><?php echo esc_html__("In the following fields, you can use these mail-tags : {user_name} {site_name} {user_email} {user_password} {Please activate your account}", "iflair-user-signin-signup"); ?></div>
					<div class="iusisu_a_inner-column-wrapper">
						<div class='user-mail-body iusisu_a_col-6'>
							<h2><?php echo esc_html__('Sign Up email body for : User email','iflair-user-signin-signup');?></h2>
							<div class='iusisu_a_input'>
								<?php wp_kses_post(wp_editor( html_entity_decode(get_option('iusisu_user_registration_email_body')), 'iusisu_user_registration_email_body', array('editor_height' => 300)));  ?>
							</div>
						</div>
						<div class='user-mail-body iusisu_a_col-6'>
							<h2><?php echo esc_html__('Sign Up email body for : Admin email','iflair-user-signin-signup');?></h2>
							<div class='iusisu_a_input'>
								<?php  wp_kses_post(wp_editor( html_entity_decode(get_option('iusisu_user_registration_email_body_admin')), 'iusisu_user_registration_email_body_admin', array('editor_height' => 300))); ?>
							</div>
						</div>
						<div class='user-mail-body iusisu_a_col-6'>
							<h2><?php echo esc_html__('Forget password email body','iflair-user-signin-signup');?></h2>
							<div class='iusisu_a_input'>
								<?php wp_kses_post(wp_editor( html_entity_decode(get_option('iusisu_user_forget_password_email_body')), 'iusisu_user_forget_password_email_body', array('editor_height' => 300)));  ?>
							</div>
						</div>
						<div class='user-mail-body iusisu_a_col-6'>
							<h2><?php echo esc_html__('Change password email body','iflair-user-signin-signup');?></h2>
							<div class='iusisu_a_input'>
								<?php  wp_kses_post(wp_editor( html_entity_decode(get_option('iusisu_user_password_change_email_body')), 'iusisu_user_password_change_email_body', array('editor_height' => 300))); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		    <?php submit_button(); ?>
		</form>
	</div><?php
}
// CALLBACK FUNCTION FOR IFLAIR SIGN IN / SIGN UP SETTING

// IF USER NOT LOGIN THEN REDIRECT SIGNIN PAGE
add_action("get_header","iusisu_redirect_sign_in_page_not_logged_in",1);
function iusisu_redirect_sign_in_page_not_logged_in()
{
	if (is_user_logged_in() == false && is_page('home'))
	{
		wp_redirect(esc_url(home_url('/sign-in/')));
	}
}
// END, IF USER NOT LOGIN THEN REDIRECT SIGNIN PAGE

// Replace default Gravatar Image used in WordPress
add_filter( 'get_avatar', 'iusisu_filter_get_avatar', 10, 5 );
function iusisu_filter_get_avatar( $avatar, $id_or_email, $size, $default, $alt )
{
	// Get attachment id
   	$attachment_id = get_user_meta( $id_or_email, 'user_profile', true );  
	// NOT empty
	if (isset($attachment_id) && !empty($attachment_id))
	{
		$profile_image = "<img alt='img' src='".esc_url($attachment_id)."' class='avatar avatar-32 photo' height='32' width='32' loading='lazy' decoding='async'>";
		return $profile_image;
	}
	return $avatar;
}

function iusisu_inline_jquery_script() {
    $iusisu_inline_script = '
        jQuery(document).ready(function($) {
            var iusisu_username = $("#iusisu_email").val();
        });
    ';
    wp_add_inline_script('iusisu-custom-script', $iusisu_inline_script);
}
add_action('wp_enqueue_scripts', 'iusisu_inline_jquery_script');

// Display settings field when plugin acive
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'iusisu_user_plugin_action_links' );
function iusisu_user_plugin_action_links( $links ) { 
   $iusisu_user_settings = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=iflair-user-signin-signup') ) .'">'.esc_html__('Settings','iflair-user-signin-signup').'</a>';  
   array_unshift($links , $iusisu_user_settings);
   return $links; 
}

// Load plugin textdomain
add_action( 'init', 'iusisu_user_load_textdomain' );
function iusisu_user_load_textdomain() {
    load_plugin_textdomain( 'iflair-user-signin-signup', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}