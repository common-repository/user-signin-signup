<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { die(); }

//REGISTRATION FORM SHORTCODE
add_shortcode('iusisu_my_account', 'iusisu_user_myaccount');
function iusisu_user_myaccount()
{
	ob_start();?>
	<div class="iusisu_my-account-wrapper iflair-signin-signup">
		<ul class="iusisu_tabs">
			<li class="iusisu_tab-link iusisu_current" data-tab="iusisu_dashboard">
				<i class="dashicons-before dashicons-dashboard" aria-hidden="true"></i><?php echo esc_html__("Dashboard", "iflair-user-signin-signup");?>
			</li>
			<li class="iusisu_tab-link" data-tab="iusisu_edit-profile">
				<i class="fa fa-user" aria-hidden="true"></i><?php echo esc_html__("Edit profile", "iflair-user-signin-signup");?>
			</li>
			<li class="iusisu_tab-link" data-tab="iusisu_change-password_sec">
				<i class="fa fa-key" aria-hidden="true"></i><?php echo esc_html__("Change password", "iflair-user-signin-signup");?>
			</li>
			<li class="iusisu_tab-link" data-tab="iusisu_logout">
				<a href="<?php echo esc_url(wp_logout_url(site_url('/sign-in/'))); ?>">
					<i class="fa fa-lock" aria-hidden="true"></i><?php echo esc_html__("Logout", "iflair-user-signin-signup");?>
				</a>
			</li>
		</ul>
		<div class="iusisu_tab-content-wrapper">
			<div id="iusisu_dashboard" class="iusisu_tab-content iusisu_current">
				<div class="iusisu_form-wrapper">
					<?php
					$iusisu_user_Details = wp_get_current_user();
					$iusisu_userID = $iusisu_user_Details->ID;
					?>
					<h2><?php echo esc_html__("WELCOME, ","iflair-user-signin-signup"). esc_html(strtoupper($iusisu_user_Details->display_name));?></h2>
					<?php
					$iusisu_ur_prof = get_user_meta($iusisu_user_Details->ID, "user_profile", true);
					$iusisu_logo = get_option('iusisu_user_logo_img');				
					if(isset($iusisu_logo) && !empty($iusisu_logo) && empty($iusisu_ur_prof))
					{  ?>
						<div class="iusisu_user-profile-image">
							<span class="iusisu_user-image" style="background-image: url('<?php if(!empty(get_option('iusisu_user_logo_img'))) { echo esc_url(get_option('iusisu_user_logo_img')); } ?>');"></span>
						</div><?php
					}
					elseif(isset($iusisu_ur_prof) && !empty($iusisu_ur_prof))
					{ ?>
						<div class="iusisu_user-profile-image">
							<span class="iusisu_user-image" style="background-image: url('<?php if(!empty($iusisu_ur_prof)) { echo esc_url($iusisu_ur_prof); } ?>');"></span>
						</div>
						<?php 
					} ?>
					<span class="iusisu_user-profile-and-logout">
						<a id='iusisu_myeditprofile' href='javascript:void(0);'><?php echo esc_html__("You can edit your profile details","iflair-user-signin-signup");?></a><?php echo esc_html__('|','iflair-user-signin-signup');?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo esc_url(wp_logout_url(site_url('/sign-in/'))); ?>"><?php echo esc_html__("Logout?", "iflair-user-signin-signup");?></a>
					</span>
				</div>
			</div>
			<!-- Start Edit Profile Section -->
			<div id="iusisu_edit-profile" class="iusisu_tab-content">
				<?php 
				global $current_user, $wp_roles;
				$successmsg = "";
				$user_ID = get_current_user_id();
				$error = array();
				//PHP UPDATE PROFILE SUBMIT FORM CODE
				if('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action']) && $_POST['action'] == 'iusisu_updates-user')
				{
					if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['_wpnonce'] ) ) , 'iusisu_update-user' ) )
					{
						wp_die(esc_html('Something went to wrong...'));
					}
					else
					{ 
						$email = sanitize_email($_POST['iusisu_email']); // Sanitize the email for security
						//UPDATE USER INFORMATION
						if (isset($email) && !empty($email))
						{
							// if (is_email($_POST['iusisu_email']) == false)
							if (is_email($email) == false) 
							{
								$error[] = esc_html__('This email you entered is not valid. Please try again','iflair-user-signin-signup');
							}				
							elseif ($user = email_exists($email)) {
							    // Email exists in the WordPress database
							    if ($user != $user_ID) {
							        $error[] = esc_html__('This email is already used by another user. Try a different one','iflair-user-signin-signup');
							    } 
							}
							else
							{ 
								wp_update_user(array('ID' => $current_user->ID, 'user_email' => sanitize_email($email)));
							}
						}
						// Ensure the file is included in WordPress
						if (isset($_FILES['iusisu_user_profile']['name']) && !empty($_FILES['iusisu_user_profile']['name']))
						{ 		       
							// Ensure the file is included in WordPress
							if (isset($_FILES['iusisu_user_profile'])) 
							{

								$iusisu_user_profile_name = sanitize_file_name(basename($_FILES['iusisu_user_profile']['name']));
								$iusisu_user_profile_full_path = sanitize_text_field($_FILES['iusisu_user_profile']['full_path']);
								$iusisu_user_profile_type = sanitize_text_field($_FILES['iusisu_user_profile']['type']);
								$iusisu_user_profile_tmp_name = sanitize_text_field($_FILES['iusisu_user_profile']['tmp_name']);
								$iusisu_user_profile_error = absint($_FILES['iusisu_user_profile']['error']);
								$iusisu_user_profile_size = absint($_FILES['iusisu_user_profile']['size']);

								$uploadedfile = array( 
									'name'      => $iusisu_user_profile_name,
									'full_path' => $iusisu_user_profile_full_path, 
									'type'      => $iusisu_user_profile_type, 
									'tmp_name'  => $iusisu_user_profile_tmp_name, 
									'error'     => $iusisu_user_profile_error,
									'size'      => $iusisu_user_profile_size,
								);
								
								$sanitized_filename = sanitize_file_name(basename($uploadedfile['name']));

								// Check for errors during upload
								if ($uploadedfile['error'] === UPLOAD_ERR_OK) 
								{
									// Check file type
									$allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
									$uploadedFileType = wp_check_filetype($sanitized_filename, null);
									$uploadedFileType = $uploadedFileType['ext'];

									if (in_array($uploadedFileType, $allowedTypes)) 
									{
										// Handle the upload using WordPress function
										$uploadOverrides = array('test_form' => false);
										require_once( IUSISU_ADMIN_DIR .'/includes/admin.php' );
										$uploadResult = wp_handle_upload($uploadedfile, $uploadOverrides);

										if ($uploadResult && !isset($uploadResult['error']))
										{
											$imageurl = $uploadResult['url'];
											update_user_meta($current_user->ID, 'user_profile', esc_url_raw($imageurl));
										}

										if (!empty($uploadResult['file'])) {
											// File uploaded successfully
											$successmsg = esc_html__('File is valid','iflair-user-signin-signup');
										} else {
											// Error handling the upload
											$error[] = esc_html__('Error handling the upload.','iflair-user-signin-signup');
										}
									} else {
									// Invalid file type
									$error[] = esc_html__('Invalid file type.','iflair-user-signin-signup');
									}
								} else {
								// Upload error occurred
								$error[] = esc_html__('Error during file upload.','iflair-user-signin-signup');
								}
							}
						}
						elseif(empty($_POST['iusisu_old_user_profile']))
						{  
							$null_values = '';
							update_user_meta($current_user->ID, 'user_profile', sanitize_text_field($null_values));
						}
						
						if (isset($_POST['iusisu_first-name']))
							update_user_meta($current_user->ID, 'first_name', sanitize_text_field($_POST['iusisu_first-name']));
						if (isset($_POST['iusisu_last-name']))
							update_user_meta($current_user->ID, 'last_name', sanitize_text_field($_POST['iusisu_last-name']));
						if (isset($_POST['iusisu_user_nicename']))
							update_user_meta($current_user->ID, 'nickname', sanitize_text_field($_POST['iusisu_user_nicename']));

						if (count($error) == 0)
						{
							//action hook for plugins and extra fields saving
							do_action('edit_user_profile_update', $current_user->ID);
							$successmsg = 'Your profile updated successfully';
						}
					}
				}
				// IS USER LOGGED IN CONDITION
				if (is_user_logged_in() == false )
				{ ?>
					<p class="warning iusisu_error"><?php echo esc_html__('You must be logged in to edit your profile.','iflair-user-signin-signup'); ?></p><?php
				}
				else
				{ ?> 
					<!--END IS USER LOGGED IN CONDITION-->
					<!--EDIT PROFILE FORM HTML-->
					<?php //ob_start(); ?>
					<div class="iusisu_form-wrapper iflair-plugin">
						<h2><?php echo esc_html__('Edit Profile','iflair-user-signin-signup'); ?></h2>
						<?php			
						if (count($error) > 0) echo wp_kses_post('<p class="iusisu_error">' . implode("<br />", $error) . '</p>');
						?>
						<p class="iusisu_success-msg"><?php echo esc_html($successmsg); ?></p>
						<?php 
						$iusisu_email = get_the_author_meta('user_email', $current_user->ID);
						$iusisu_first_name = get_the_author_meta('first_name', $current_user->ID);
						$iusisu_last_name = get_the_author_meta('last_name', $current_user->ID);
						$iusisu_user_nicename = get_the_author_meta('nickname', $current_user->ID);
						$iusisu_user_profile = get_user_meta($current_user->ID, "user_profile");
						?>
						<form method="post" name="iusisu_edit_profile" id="iusisu_adduser" enctype='multipart/form-data'>
							<div class="iusisu_form-field">
								<input class="text-input" name="iusisu_email" type="text" id="iusisu_email" placeholder="*Please enter email" value="<?php if(!empty($iusisu_email)){ echo esc_attr($iusisu_email); } ?>"/>
							</div>
							<div class="iusisu_form-field">
								<input class="text-input" name="iusisu_first-name" type="text" id="iusisu_first-name" placeholder="Please enter first name" value="<?php if(!empty($iusisu_first_name)) { echo esc_attr($iusisu_first_name); } ?>"/>
							</div>
							<div class="iusisu_form-field">
								<input class="text-input" name="iusisu_last-name" type="text" id="iusisu_last-name" placeholder="Please enter last name" value="<?php if(!empty($iusisu_last_name)) { echo esc_attr($iusisu_last_name); } ?>"/>
							</div>
							<div class="iusisu_form-field">
								<input class="text-input" name="iusisu_user_nicename" type="text" id="iusisu_user_nicename" placeholder="Please enter nicename" value="<?php if(!empty($iusisu_user_nicename)) { echo esc_attr($iusisu_user_nicename); } ?>"/>
							</div>
							<div class="iusisu_form-field iusisu_profile-picture">
								<?php 
								//if(!empty($iusisu_user_profile)){
								?>
									<i class="fa fa-cloud-upload"></i> <?php echo esc_html__('Choose profile image','iflair-user-signin-signup'); ?>
									<input type="file" class="iusisu_button-primary" value="<?php echo esc_attr(($iusisu_user_profile[0] ? $iusisu_user_profile[0] : '')); ?>" id="iusisu_user_profile" name="iusisu_user_profile"/>
								<?php //} ?>
								<?php if(empty($iusisu_user_profile[0]))
								{ ?>
									<div class="user-profile"><?php echo esc_html__('Please upload an image for your profile','iflair-user-signin-signup'); ?></div class="user-profile">
								<?php } ?>
								<div class="iusisu_image-wrap">
								<?php
								if (!empty($iusisu_user_profile[0]))
								{ ?>
									<img src='<?php echo esc_url($iusisu_user_profile[0]);?>' width='150' height='150' id='iusisu_thumb' name='iusisu_user_profile'>
										<a class='iusisu_remove-image' style='display: inline;'><img src='<?php echo esc_url(plugin_dir_url(__DIR__));?>assets/css/public/images/close.svg'></a>
										<input type='hidden' name='iusisu_old_user_profile' value='<?php echo esc_attr($iusisu_user_profile[0]);?>'>
								<?php } ?>
								</div>
							</div>
							<div class="iusisu_action-field">
								<input name="iusisu_updateuser" type="submit" id="iusisu_updateuser" class="submit button" value="<?php echo esc_html__('Update','iflair-user-signin-signup'); ?>" />
								<?php wp_nonce_field('iusisu_update-user') ?>
								<input name="action" type="hidden" id="action" value="iusisu_updates-user" />
							</div>
						</form>
					</div><?php
				} ?>
			</div>
			<!-- Start Edit Profile Section -->
			<!-- Start Change Password Section -->
			<div id="iusisu_change-password_sec" class="iusisu_tab-content">
				<?php
				iusisu_user_loggedin();
				global $current_user;
				$iusisu_user_profile = get_user_meta($current_user->ID, "user_profile",true);
				$iusisu_iflair_logo = get_option('iusisu_user_logo_img');
				?>
				<!--CHANGE PASSWORD FORM HTML-->
				<div class="iusisu_form-wrapper iflair-plugin"> 
					<?php
					if(isset($iusisu_iflair_logo) && !empty($iusisu_iflair_logo) && empty($iusisu_user_profile))
					{ ?>
						<div class="iusisu_user-profile-image">
							<span class="iusisu_user-image" style="background-image: url('<?php echo esc_url($iusisu_iflair_logo); ?>');"></span>
						</div><?php
					}
					elseif(isset($iusisu_user_profile) && !empty($iusisu_user_profile))
					{ ?>
						<div class="iusisu_user-profile-image">
							<span class="iusisu_user-image" style="background-image: url('<?php echo esc_url($iusisu_user_profile); ?>');"></span>
						</div><?php
					} ?>
					<h2><?php echo esc_html__('Change Password','iflair-user-signin-signup');?></h2>
					
					<form method="POST" id="iusisu_change-password" name="iusisu_change_password">
						<?php wp_nonce_field('iusisu_password_change',"iusisu_password-change");?>
						<div class="iusisu_change-password-message"></div>
						<div class="iusisu_form-field">
							<input id="iusisu_old-password" type="password" name="iusisu_fp_old_password" placeholder="*Please enter old password"><i class="fa fa-eye fa-eye-slash"></i>
						</div>
						<div class="iusisu_form-field">
							<input id="iusisu_new-password" type="password" name="iusisu_fp_new_password" placeholder="*Please enter new password"><i class="fa fa-eye fa-eye-slash"></i>
						</div>
						<div class="iusisu_form-field">
							<input id="iusisu_confirm-password" type="password" name="iusisu_fp_confirm_password" placeholder="*Please enter confirm password"><i class="fa fa-eye fa-eye-slash"></i>
						</div>
						<div class="iusisu_action-field">
							<input type="submit" name="iusisu_change-password" value="Save">
						</div>
					</form>
				</div>
				<!--END CHANGE PASSWORD FORM HTML-->
			</div>
			<!-- End Change Password Section -->
		</div>
	</div><!-- container -->
	<div class="hidden">
		<input type="hidden" name="iusisu_chkuser" id="iusisu_chkuser" value="<?php echo esc_html(get_current_user_id()); ?>">
		<input type="hidden" name="iusisu_site_url" id="iusisu_site_url" value="<?php echo esc_url(site_url()); ?>">
	</div>
	<?php
return ob_get_clean();
}
//END REGISTRATION FORM SHORTCODE

//CALL THE CHANGE PASSWORD FORM AJAX ACTION
add_action( 'wp_ajax_iusisu_change_password_ajax', 'iusisu_change_password_ajax_fun' );
add_action( 'wp_ajax_nopriv_iusisu_change_password_ajax', 'iusisu_change_password_ajax_fun' );
function iusisu_change_password_ajax_fun()
{
	if(!isset($_POST['iusisu_password_change']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['iusisu_password_change'])),'iusisu_password_change'))
	{
		wp_die(esc_html('Something went to wrong...'));
	}
	else
	{ 
		if(($_POST['iusisu_oldPass'] && $_POST['iusisu_newPass'] && $_POST['iusisu_confPass']))
		{
			$iusisu_old_password = sanitize_text_field($_POST['iusisu_oldPass']);
			$iusisu_new_password = sanitize_text_field($_POST['iusisu_newPass']);
			$iusisu_confirm_password = sanitize_text_field($_POST['iusisu_confPass']);
			require_once(IUSISU_INCLUDES_DIR.'/class-phpass.php');
			$wp_hasher = new PasswordHash(8, TRUE);
			$wp_hasher = new PasswordHash(8, TRUE);
			$user = wp_get_current_user();
			$user_id = get_current_user_id();
			$iusisu_user_old_pass = $user->data->user_pass;
			if($wp_hasher->CheckPassword($iusisu_old_password, $iusisu_user_old_pass)) 
			{
				if($iusisu_old_password === $iusisu_new_password && $iusisu_new_password === $iusisu_confirm_password)
				{
					echo wp_kses_post("<p class='iusisu_error'>". esc_html__("You can enter your new password is different then old password" , "iflair-user-signin-signup")."</p>");
				}
				elseif($iusisu_old_password !== $iusisu_new_password && $iusisu_new_password === $iusisu_confirm_password)
				{
					wp_set_password( $iusisu_new_password , $user_id );
					$user_detail = get_user_by( 'id' , $user_id );
					$user_email=$user_detail->data->user_email;
					$user_login=$user_detail->data->user_login;
					if(!empty($user_email))
					{
						$iusisu_change_pass_msg = "Your password changed successfully";
						$headers[] = wp_kses_post('Content-Type: text/html; charset=UTF-8');
						$headers[] = wp_kses_post("From: " . (get_option('iusisu_user_from_email') ? get_option('iusisu_user_from_email') : get_option('admin_email')) . " \r\n");
						$to = esc_html($user_email);
						$subject = (get_option('iusisu_userchange_subject') ? esc_html(get_option('iusisu_userchange_subject')) : esc_html($iusisu_change_pass_msg) );
						if(!empty(get_option('iusisu_user_password_change_email_body')))
						{
							$html_format_message_display = htmlentities(wpautop(stripslashes(get_option('iusisu_user_password_change_email_body'))));
							$message = wp_kses_post(html_entity_decode($html_format_message_display));
							$message = wp_kses_post(str_replace('{user_name}', $user_login,  $message));
							$message = wp_kses_post(str_replace('{user_email}', $user_email,  $message));
							$message = wp_kses_post(str_replace('{user_password}', $iusisu_new_password,  $message));
						}
						else
						{
							$message  .= wp_kses_post("Hello ".$user_login."," . "\r\n");
							$message .= wp_kses_post("<br>");
							$message  .= wp_kses_post("\r\n");
							$message  .= wp_kses_post("Your username is : ".$user_login."" . "\r\n");
							$message .= wp_kses_post("<br>");
							$message  .= wp_kses_post("Your email address is : ".$user_email."" . "\r\n");
							$message .= wp_kses_post("<br>");
							$message  .= wp_kses_post("Your new password is : "."<br>".$iusisu_new_password."" . "\r\n");
							$message  .= wp_kses_post("\r\n");
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
						$iusisu_send_email = wp_mail($to, $subject, $message, $headers);
						$iusisu_pass_changed_success = "Password has been changed successfully";
						if(empty($iusisu_send_email))
						{
							echo wp_kses_post("<p class='iusisu_error'>". esc_html__("Error" , "iflair-user-signin-signup")."</p>");
						}
						else
						{
							echo wp_kses_post("<p class='iusisu_success-msg'>".(get_option('iusisu_change_pass_msg') ? esc_html(get_option('iusisu_change_pass_msg'))."</p>" : "<p class='iusisu_success-msg'>".esc_html($iusisu_pass_changed_success)."</p>"));
							wp_set_password($iusisu_new_password, $user_id);
						}
					}
				}
				else
				{
					echo wp_kses_post("<p class='iusisu_error'>". esc_html__("Confirm password not matching with new password" , "iflair-user-signin-signup")."</p>");
				}
			}
			else
			{
				echo wp_kses_post("<p class='iusisu_error'>". esc_html__("Please enter your old password correctly" , "iflair-user-signin-signup")."</p>");
			}
		}
	}
	wp_die();
}
//END THE CHANGE PASSWORD FORM AJAX ACTION