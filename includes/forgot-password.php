<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { die(); }

//SHORTCODE TO FORGOT FORM
add_shortcode("iusisu_forgot_password","iusisu_forgot_password_fun");
function iusisu_forgot_password_fun()
{
	ob_start();?>
	<!-- FORGOT PASSWORD FORM -->
	<div class="iusisu_form-wrapper iflair-plugin">
		<?php
		$iusisu_user_logo_img = get_option('iusisu_user_logo_img');
		if(isset($iusisu_user_logo_img) && !empty($iusisu_user_logo_img))
		{ ?>
			<div class="iusisu_iflair-form-logo">
				<span class="iusisu_user-image" style="background-image: url('<?php echo esc_url($iusisu_user_logo_img); ?>');"></span>
			</div><?php
		} ?>
		<h2><?php echo esc_html__('Forgot Password','iflair-user-signin-signup');?></h2>
		<form method="POST" id="iusisu_forgot-password" name="iusisu_forgot_password" action="">
			<?php wp_nonce_field('iusisu_forget_password', 'iusisu_wpnonce');?>
			<div class="iusisu_forgot-message"></div>
			<div class="iusisu_form-field">
				<input id="iusisu_username_email" type="text" name="iusisu_fp_email_username" placeholder="*Please enter email or user name"/>
				<label id="iusisu_username_email-error" class="iusisu_error" for="iusisu_username_email"><?php echo esc_html__('Please enter email or user name','iflair-user-signin-signup');?></label>
			</div>
			<div class="iusisu_action-field">
				<input id="iusisu_Send" type="submit" name="iusisu_Send" value="Send">
			</div>
			<?php  $iusisu_sign_in_url = get_bloginfo('url').'/sign-in/'; ?>
			<?php  $iusisu_sign_up_url = get_bloginfo('url').'/sign-up/'; ?>
			<div class="iusisu_form-footer">
				<span class="iusisu_forgotPassword">
					<?php if(!empty($iusisu_sign_in_url)){ ?>
					<a href="<?php echo esc_url($iusisu_sign_in_url); ?>"><?php echo esc_html__('Sign In','iflair-user-signin-signup');?></a>
					<?php } ?>
				</span>
				<?php if(!empty($iusisu_sign_up_url)){ ?>
				<a href="<?php echo esc_url($iusisu_sign_up_url); ?>"><?php echo esc_html__('Sign Up','iflair-user-signin-signup');?></a>
				<?php } ?>
			</div>
		</form>
	</div>
	<!--END FORGOT PASSWORD FORM -->
	<?php
	return ob_get_clean();
}
//END SHORTCODE TO FORGOT FORM 

//FORGOT PASSWORD AJAX ACTION
add_action( 'wp_ajax_iusisu_forgot_password', 'iusisu_forgot_password_ajax_fun' );
add_action( 'wp_ajax_nopriv_iusisu_forgot_password', 'iusisu_forgot_password_ajax_fun' );
function iusisu_forgot_password_ajax_fun()
{
	if(isset($_POST['iusisu_username_email']) || isset($_POST['iusisu_wpnonce']) || !empty($_POST['iusisu_username_email']))
	{
		if ( ! isset( $_POST['iusisu_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['iusisu_wpnonce'] ) ) , 'iusisu_forget_password' ) )
		{
    		wp_die(esc_html('Something went to wrong...') );
		}
		else
		{
			$iusisu_fp_email_username = sanitize_text_field($_POST['iusisu_username_email']);			
			
			$iusisu_detailByusername = get_user_by( 'login' , trim( $iusisu_fp_email_username ) );
			
			$iusisu_detailByemail = get_user_by( 'email' , trim( $iusisu_fp_email_username ) );
			
			if($iusisu_detailByusername)
			{
				$user_detail = $iusisu_detailByusername;
			}
			elseif($iusisu_detailByemail)
			{
				$user_detail = $iusisu_detailByemail;
			}
			$user_id = $user_detail->ID;
			$iusisu_user_email = $user_detail->data->user_email;
			$iusisu_user_login = $user_detail->data->user_login;
			
			if(!empty($iusisu_user_email) || !empty($iusisu_user_login))
			{	
				$forgot_pass_static_msg = "Forget password";
				$iusisu_new_password=wp_generate_password( 12, true );
				$email = get_option('iusisu_user_from_email') ? esc_html(get_option('iusisu_user_from_email')) : esc_html(get_option('admin_email'));
				$to = esc_html($iusisu_user_email);
				$subject = (get_option('iusisu_userforgot_subject') ? esc_html(get_option('iusisu_userforgot_subject')) : esc_html($forgot_pass_static_msg));				
				$url = site_url().'/sign-in/';
				$url = esc_url($url);
				if(!empty(get_option('iusisu_user_forget_password_email_body')))
				{
					$html_format_message_display = htmlentities(wpautop(stripslashes(get_option('iusisu_user_forget_password_email_body'))));
					$message = wp_kses_post(html_entity_decode($html_format_message_display));
					$message =  wp_kses_post(str_replace('{user_name}', $iusisu_user_login, $message));
					$message =  wp_kses_post(str_replace('{user_email}', $iusisu_user_email, $message));
					$message =  wp_kses_post(str_replace('{user_password}', $iusisu_new_password, $message));
				}
				else
				{
					$click_url = '<a href="'.esc_url($url).'">'.esc_html__('Click here to login','iflair-user-signin-signup').'</a>';
					$cl_url = wp_kses($click_url,
						array( 'a'=> array(
							'href' => array(),
							'title' => array(),
						),
				    	'br'     => array(),
				    	'em'     => array(),
				    	'strong' => array(),
					));
					$message  .= wp_kses_post("Hello ".$iusisu_user_login."," . "<br>");
					$message  .= wp_kses_post("<br>");
					$message  .= wp_kses_post("Your username is : ".$iusisu_user_login . "<br>");
					$message  .= wp_kses_post("Your email address is : ".$iusisu_user_email."" . "<br>");
					$message  .= wp_kses_post("Your new password is : "."<br>".$iusisu_new_password."" . "<br>");
					$message  .= wp_kses_post("<br>");
					$message  .= $cl_url;
					$message  .= wp_kses_post("<br>");

				}				
				if (!empty(get_option('iusisu_user_signature')))
				{
					$html_format_message_display = htmlentities(wpautop(stripslashes(get_option('iusisu_user_signature'))));
					$message .= wp_kses_post(html_entity_decode($html_format_message_display));
				}
				else
				{
					$message .= wp_kses_post("-" . "<br>");
					$message .= wp_kses_post("Thanks," . "<br>");
					$message .= wp_kses_post(get_bloginfo('name') . " Team" . "<br>");
				}
				$headers[] = wp_kses_post('Content-Type: text/html; charset=UTF-8');
				$headers[] = wp_kses_post('From: '. $email);				
				$iusisu_send_email = wp_mail($to, $subject, $message, $headers);
				if(empty($iusisu_send_email))
				{
					echo wp_kses_post("<div class='iusisu_error iusisu_forgot-message'>".esc_html__("Error" , "iflair-user-signin-signup")."</div>");
				}
				else
				{
					echo wp_kses_post("<div class='iusisu_success-msg iusisu_forgot-message'>".(get_option('iusisu_forget_pass_msg') ? esc_html(get_option('iusisu_forget_pass_msg'))."</div>" : "<div class='iusisu_success-msg iusisu_forgot-message'>". esc_html__("We have successfully sent new password on your email address" , "iflair-user-signin-signup")."</div>"));
					wp_set_password($iusisu_new_password, $user_id);
				}
			}
			else
			{
				echo wp_kses_post("<div class='iusisu_error iusisu_forgot-message'>".esc_html__("Invalid email or user name" , "iflair-user-signin-signup")."</div>");
			}
		}
	}
	wp_die();
}
//END FORGOT PASSWORD AJAX ACTION   
?>