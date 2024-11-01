<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { die(); }
//REGISTRATION FORM SHORTCODE
add_shortcode('iusisu_signup_form', 'iusisu_user_iflair_signup_form');
function iusisu_user_iflair_signup_form()
{
	ob_start();?>
	<!--REGISTRATION FORM HTML-->
	<div class="iusisu_form-wrapper iflair-plugin">
		<?php
		$iusisu_user_logo_img = get_option('iusisu_user_logo_img');
		if (!empty($iusisu_user_logo_img))
		{?>
			<div class="iusisu_iflair-form-logo">
				<span class="iusisu_user-image" style="background-image: url('<?php echo esc_url($iusisu_user_logo_img); ?>');"></span>
			</div><?php
		}?>
		<h2><?php echo esc_html__('Sign Up','iflair-user-signin-signup');?></h2>
		<p class="iusisu_success-msg"></p>
		<form id="iusisu-user-reg" class="form-inline" method="POST" name="iusisu_registration">
			<?php wp_nonce_field('iusisu_user_registration', 'iusisu_nonce' ); ?>
			<div class="iusisu_form-field">
				<input type="text" class="form-control" name="iusisu_txtUsername" id="iusisu_txtUsername" placeholder="*Please enter user name">
			</div>
			<div class="iusisu_form-field">
				<input type="email" class="form-control" name="iusisu_txtEmail" id="iusisu_txtEmail" placeholder="*Please enter email">
			</div>
			<div class="iusisu_form-field">
				<input type="password" class="form-control" name="iusisu_txtPassword" id="iusisu_txtPassword" placeholder="*Please enter password"><i class="fa fa-eye fa-eye-slash" id="iusisu_togglePassword" ></i>
				<span class="iusisu_user_password_generate"><?php echo esc_html__('Generate','iflair-user-signin-signup');?></span>
			</div>
			<div class="iusisu_action-field">
				<input type="submit" name="iusisu_register" class="btn btn-default"/>
			</div>
			<?php $iusisu_signin_url = get_bloginfo('url').'/sign-in/'; ?>
			<?php $iusisu_forgot_pass_url = get_bloginfo('url').'/forget-password/'; ?>	<div class="iusisu_form-footer">
				<span class="iusisu_forgotPassword">
					<?php if(!empty($iusisu_signin_url)){ ?>
					<a href="<?php echo esc_url($iusisu_signin_url); ?>" ><?php echo esc_html__('Sign In','iflair-user-signin-signup'); ?></a>
					<?php } ?>
				</span>
				<?php if(!empty($iusisu_forgot_pass_url)) { ?>
				<a href="<?php echo esc_url($iusisu_forgot_pass_url); ?>"><?php echo esc_html__('Forgot Password?','iflair-user-signin-signup'); ?></a>
				<?php } ?>
			</div>
		</form>
	</div>
	<!--END REGISTRATION FORM HTML-->
	<?php
	return ob_get_clean();
}
//END REGISTRATION FORM SHORTCODE

//CALL THE FORM AJAX ACTION
add_action( 'wp_ajax_iusisu_userregisterFunc', 'iusisu_userregisterFunc_fun' );
add_action( 'wp_ajax_nopriv_iusisu_userregisterFunc', 'iusisu_userregisterFunc_fun' );
function iusisu_userregisterFunc_fun()
{ 
	if(isset($_POST['iusisu_nonce']))
	{
		$iusisu_nonce = sanitize_text_field($_POST['iusisu_nonce']);
	}

	if ( isset( $iusisu_nonce ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $iusisu_nonce ) ) , 'iusisu_user_registration' ) )
	{	
		$iusisu_username = sanitize_text_field($_POST['iusisu_txtUsername']);
		$iusisu_email = sanitize_text_field($_POST['iusisu_txtEmail']);
		$iusisu_password = sanitize_text_field($_POST['iusisu_txtPassword']); 
	} else {
		wp_die(esc_html('Nonce is invalid'));
	}
	//user role from plugin management
	$iusisu_user_role = (get_option('iusisu_role') ? get_option('iusisu_role') : get_option('default_role'));
	$error = array();
	if (strpos($iusisu_username, ' ') !== FALSE)
	{
		$error['iusisu_username_space'] = "<p class='iusisu_user-sign-up iusisu_error'>". esc_html__('User name has space','iflair-user-signin-signup') ."</p>";
		echo wp_json_encode($error);
		wp_die();
	}
	if (empty($iusisu_username))
	{
		$error['iusisu_username_empty'] = "<p class='iusisu_user-sign-up iusisu_error'>". esc_html__('Needed user name must','iflair-user-signin-signup') ."</p>";
		echo wp_json_encode($error);
		wp_die();
	}
	if (username_exists($iusisu_username))
	{
		$error['iusisu_username_exists'] = "<p class='iusisu_user-sign-up iusisu_error'>". esc_html__('User name already exists','iflair-user-signin-signup') ."</p>";
		echo wp_json_encode($error);
		wp_die();
	}
	if (is_email($iusisu_email) == false)
	{
		$error['iusisu_email_valid'] = "<p class='iusisu_user-sign-up iusisu_error'>".  esc_html__('Email has no valid value','iflair-user-signin-signup') ."</p>";
		echo wp_json_encode($error);
		wp_die();
	}
	if (email_exists($iusisu_email))
	{
		$error['iusisu_email_existence'] = "<p class='iusisu_user-sign-up iusisu_error'>".  esc_html__('Email already exists','iflair-user-signin-signup') ."</p>";
		echo wp_json_encode($error);
		wp_die();
	}
	if (empty($iusisu_password))
	{
		$error['iusisu_password'] = "<p class='iusisu_user-sign-up iusisu_error'>".  esc_html__('Needed password must','iflair-user-signin-signup') ."</p>";
		echo wp_json_encode($error);
		wp_die();
	}
	if (count($error) == 0)
	{
		//combining in one header the From and content-type
		$iusisu_userdata = array(
			'user_login' => sanitize_text_field($iusisu_username),
			'user_pass' => sanitize_text_field($iusisu_password),
			'user_email' => sanitize_text_field($iusisu_email),
		);
		$user_id = wp_insert_user($iusisu_userdata);
		if (!empty($user_id))
		{
			$user = new WP_User($user_id);
			$user->set_role($iusisu_user_role);
			$headers[] = wp_kses_post('Content-Type: text/html; charset=UTF-8');
			$headers[] = wp_kses_post("From: " . (get_option('iusisu_user_from_email') ? get_option('iusisu_user_from_email') : get_option('admin_email')) . " \r\n");
			// create md5 code to verify later
			$code = md5(time());
			// make it into a code to send it to user via email
			$string = array('id' => $user_id, 'code' => $code);
			// create the activation code and activation status
			$null_val = 0;
			add_user_meta($user_id, 'is_activated', sanitize_text_field($null_val));
			add_user_meta($user_id, 'activation_code', sanitize_text_field($code));
			// create the url
			//$url = site_url() . '/?act=' . base64_encode(serialize($string));
			$url = site_url() . '/?act=' . urlencode(wp_json_encode($string));

			$iusisu_static_wlcm_msg = "Welcome ! you have registered successfully";

			$activate_urls = "<a href=".esc_url($url).">".esc_html__('Please activate your account','iflair-user-signin-signup')."</a>";

			$subject = (get_option('iusisu_user_subject') !== '' ? esc_html(get_option('iusisu_user_subject')) : esc_html($iusisu_static_wlcm_msg . get_bloginfo("name")));

			if(!empty(get_option('iusisu_user_registration_email_body')))
			{
				$activate_url = wp_kses($activate_urls,
					array( 'a'=> array(
						'href' => array(),
						'title' => array(),
					),
			    	'br'     => array(),
			    	'em'     => array(),
			    	'strong' => array(),
				));
				$html_format_message_display = htmlentities(wpautop(stripslashes(get_option('iusisu_user_registration_email_body'))));
				$message = wp_kses_post(html_entity_decode($html_format_message_display));
				$site_name = wp_kses_post(get_bloginfo( 'name' ));
				$message =  wp_kses_post(str_replace('{user_name}', $iusisu_username,  $message));
				$message =  wp_kses_post(str_replace('{site_name}', $site_name,  $message));
				$message =  wp_kses_post(str_replace('{Please activate your account}', $activate_url,  $message));
			}
			else
			{	
				$activate_urls = "<a href='" . esc_url($url) . "'> ".esc_html__('Please activate your account','iflair-user-signin-signup')."</a>";

				$activate_url = wp_kses($activate_urls,
					array( 'a'=> array(
						'href' => array(),
						'title' => array(),
					),
			    	'br'     => array(),
			    	'em'     => array(),
			    	'strong' => array(),
				));

				$message .= wp_kses_post("Hello " . $iusisu_username . "," . "<br>");
				$message .= wp_kses_post("<br>");
				$message .= wp_kses_post("Welcome to our site : " . get_bloginfo("name") . "<br>");
				$message .= wp_kses_post("Please click the following link to activate your account");
				$message .= wp_kses_post("<br>");
				$message .= $activate_url;
				$message .= wp_kses_post("<br>");
				$message .= wp_kses_post("Note : After click on above link, you need to Sign In, so your account will be activate"."<br>");
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
			wp_mail($iusisu_email, $subject, $message,$headers);

			if (!empty(get_option('admin_email')))
			{	
				$iusisu_reg_msg = 'New user registered on our site';
				$headers[] = wp_kses_post('Content-Type: text/html; charset=UTF-8');
				$headers[] = wp_kses_post("From: " . (get_option('iusisu_user_from_email') ? get_option('iusisu_user_from_email') : get_option('admin_email')) . " \r\n");
				$subject = (get_option('admin_subject') !== '' ? esc_html(get_option('admin_subject')) : esc_html($iusisu_reg_msg));

				if(!empty(get_option('iusisu_user_registration_email_body_admin')))
				{
					$html_format_message_display = htmlentities(wpautop(stripslashes(get_option('iusisu_user_registration_email_body_admin'))));
					$message = wp_kses_post(html_entity_decode($html_format_message_display));
					$message =  wp_kses_post(str_replace('{user_name}', $iusisu_username,  $message));
					$message =  wp_kses_post(str_replace('{user_email}', $iusisu_email, $message));
				}
				else
				{
					$message = wp_kses_post("Hello Admin," . "<br>");
					$message .= wp_kses_post("\r\n");
					$message .= wp_kses_post("New user is registered on our site" . "<br>");
					$message .= wp_kses_post("<br>");
					$message .= wp_kses_post("Here are details :" . "<br>");
					$message .= wp_kses_post("Name : " . $iusisu_username  . "<br>");
					$message .= wp_kses_post("Email : " . $iusisu_email . "<br>");
					$message .= wp_kses_post("<br>");
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
				wp_mail(get_option('admin_email'), $subject, $message,$headers);
			}
				
			$iusisu_resultdata['iusisu_success_msg'] = html_entity_decode(get_option('iusisu_registration_msg') ? esc_html(get_option('iusisu_registration_msg')) : esc_html__("User sign Up successfully, You will be get email. Please activate your account from it"));

			echo wp_json_encode($iusisu_resultdata);
			wp_die();
		}				
	}
	
}