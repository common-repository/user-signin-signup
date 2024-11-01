<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { die(); }
// Shortcode for signing form
add_shortcode( 'iusisu_signin_form', 'iusisu_user_iflair_signin_form' );
function iusisu_user_iflair_signin_form()
{
	ob_start();?>
		<!--LOGIN FORM HTML-->
		<div class="iusisu_form-wrapper iflair-plugin">
			<?php
			global $current_user;
			$iusisu_user_profile = get_user_meta($current_user->ID, "user_profile",true);
			$iusisu_user_logo_img = get_option('iusisu_user_logo_img');
			if(!isset($iusisu_user_profile[0]) && !empty($iusisu_user_logo_img))
			{ ?>
				<div class="iusisu_iflair-form-logo">
					<span class="iusisu_user-image" style="background-image: url('<?php echo esc_url($iusisu_user_logo_img); ?>');"></span>
				</div><?php
			} ?>
			<h2><?php echo esc_html__("Sign In",'iflair-user-signin-signup');?></h2>
			<p class='iusisu_login-message-con'></p>
			<form id="iusisu_login" name="form" method="post" action=""> 
				<?php wp_nonce_field('iusisu_user_login', 'iusisu_user_login');?>
				<div class="iusisu_form-field">
					<input id="iusisu_email" type="text" placeholder="*Please enter email or user name" name="iusisu_email">
				</div>
				<div class="iusisu_form-field">
					<input id="iusisu_password" type="password" placeholder="*Please enter password" name="iusisu_password"><i class="fa fa-eye fa-eye-slash" id="iusisu_togglePassword" ></i> <!--onclick="myFununction()"-->
				</div>
				<?php $remember_forever_val = 'forever'; ?>
				<div class="iusisu_form-field remember-me">
					<input type="checkbox" name="iusisu_rememberme" value="<?php if(!empty($remember_forever_val)){ echo esc_attr($remember_forever_val); } ?>"><?php echo esc_html__(" Remember Me",'iflair-user-signin-signup');?>
				</div>
				<div class="iusisu_action-field">
					<input id="iusisu_submit" type="submit" name="iusisu_submit" value="Submit">
					<input type="hidden" name="redirect_to" value="<?php echo esc_url(site_url());?>">
				</div>
				<?php
				$iusisu_sign_up_url = get_bloginfo('url').'/sign-up/'; 
				$iusisu_forgot_pass_url = get_bloginfo('url').'/forget-password/';
				?>
				<div class="iusisu_form-footer">
					<span class="iusisu_forgotPassword">
						<a href="<?php echo esc_url($iusisu_sign_up_url); ?>"><?php echo esc_html__("Sign Up",'iflair-user-signin-signup');?></a>
					</span>
					<a href="<?php echo esc_url($iusisu_forgot_pass_url);?>"><?php echo esc_html__("Forgot Password?",'iflair-user-signin-signup');?></a>
				</div>
			</form>
		</div>
		<!--END LOGIN FORM HTML-->
		<?php 
	return ob_get_clean();
}
//END LOGIN FORM SHORTCODE

//CALL THE FORM AJAX ACTION
add_action( 'wp_ajax_iusisu_userValidateFunc', 'iusisu_userValidateFunc_fun' );
add_action( 'wp_ajax_nopriv_iusisu_userValidateFunc', 'iusisu_userValidateFunc_fun' );
function iusisu_userValidateFunc_fun()
{
	if (isset($_REQUEST['iusisu_nonce']) || wp_verify_nonce(sanitize_text_field( wp_unslash($_REQUEST['iusisu_nonce'])),'iusisu_user_login' ) )
		{ 
			$iusisu_email = sanitize_text_field($_REQUEST['iusisu_email']);
			$iusisu_password = sanitize_text_field($_REQUEST['iusisu_password']);
			$iusisu_rememberme = sanitize_text_field($_REQUEST['iusisu_rememberme']);
				
			global $wpdb;
			$iusisu_email = $wpdb->escape(esc_attr($iusisu_email));
			$iusisu_password = $wpdb->escape(esc_attr($iusisu_password));  
			$iusisu_remember = $wpdb->escape(esc_attr($iusisu_rememberme)); 

			if($iusisu_remember)
			{
				$iusisu_remember = true;
			}
			else
			{
				$iusisu_remember = ""; 
			}

			$iusisu_creds = array();
			$iusisu_creds['user_login'] = $iusisu_email;
			$iusisu_creds['user_password'] = $iusisu_password;
			$iusisu_creds['remember'] = $iusisu_remember;
			$iusisu_user = wp_signon( $iusisu_creds , is_ssl() );

			$iusisu_userID = $iusisu_user->ID;

			if(!empty($iusisu_userID) && $iusisu_creds['remember'] == true) {
				wp_set_current_user( $iusisu_userID, $iusisu_email );
				wp_set_auth_cookie( $iusisu_userID, true, false );
			}

			if ( is_wp_error( $iusisu_user ) ) 
			{   
				$errormsg = esc_html__("Invalid email/user name or password","iflair-user-signin-signup");
				$response['message'] = $errormsg;
				$response['code'] = intval(0);
				echo wp_json_encode($response);
				wp_die();
			}
			else
			{
				$sucessmsg = esc_html__("User Sign In successfully","iflair-user-signin-signup");
				$sucessmsg = esc_html(get_option('iusisu_login_msg') ? get_option('iusisu_login_msg') : $sucessmsg );
				$response['redirect_url'] = esc_url(site_url('/user-account/'));
				$response['code'] = intval(1);
				$response['message'] = $sucessmsg;
				echo wp_json_encode($response);
				wp_die();
			}			
		}
		else
		{
			wp_die(esc_html('Something went to wrong'));
		}	
	wp_die();
}
//END CALL THE FORM AJAX ACTION