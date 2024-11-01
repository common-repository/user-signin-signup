jQuery(document).ready(function ($) {         
    //Plugin Form Logo Remove
    jQuery(document).on('click',".input_img .iusisu_remove-image",function(){ 
        $(this).parents('.input_img').find('input[type=hidden]').val('');
        $(this).parents('.input_img').find('.iusisu_image-wrap').html('');
    }); 
    //My Account Page Profile Picture Remove
    jQuery(document).on('click',".iusisu_profile-picture .iusisu_remove-image",function(){
        $(this).parents('.iusisu_profile-picture').find('.iusisu_image-wrap').html('');
    });     
   
    //nav tabs for theme options
    $('ul.iusisu_tabs li').click(function () {
        var tab_id = $(this).attr('data-tab');

        $('ul.iusisu_tabs li').removeClass('iusisu_current');
        $('.iusisu_tab-content').removeClass('iusisu_current');

        $(this).addClass('iusisu_current');
        $("#" + tab_id).addClass('iusisu_current');
    });

    jQuery.validator.addMethod('passwordvalidation', function(value, element, param) 
    {
        var nameRegex = /^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
        return nameRegex.test(value);
    }, 'You need to create your password with minimum 8 characters and including alphnumeric and add one special character like !@#$%^&*()');
            
    jQuery("form[name='iusisu_registration']").validate(
    {
        rules: {
            iusisu_txtUsername: "required",
            iusisu_txtEmail: { 
                required: true,
            },
            iusisu_txtPassword: {
                required: true,
                passwordvalidation: true
            }
        },
        messages: {
            iusisu_txtUsername: "Please enter user name",
            iusisu_txtPassword: {
                required: "Please enter password",
            },
            iusisu_txtEmail: {
                required: "Please enter email",
                iusisu_txtEmail: "Please enter a valid email address"
            },
        },
        submitHandler: function (form) {
            var iusisu_txtUsername = jQuery(form).find('#iusisu_txtUsername').val();
            var iusisu_txtEmail = jQuery(form).find('#iusisu_txtEmail').val();
            var iusisu_txtPassword = jQuery(form).find('#iusisu_txtPassword').val();
            var ajax_url= admin_ajaxObj.ajax_url;
            var iusisu_nonce = jQuery(form).find('#iusisu_wpnonce').val();
            var data =  {
                iusisu_txtUsername : iusisu_txtUsername,
                iusisu_txtEmail : iusisu_txtEmail,
                iusisu_txtPassword : iusisu_txtPassword,
                iusisu_nonce : iusisu_nonce,
                action : "iusisu_userregisterFunc"
            };
            jQuery.ajax({
                url: ajax_url,
                type:'POST',
                data: data,
                beforeSend: function()
                {
                    jQuery("#iusisu_user_loader").fadeIn(500);
                },
                success: function(response)
                {                       
                    var iusisu_res = JSON.parse(response);              
                    if(iusisu_res.iusisu_success_msg){
                        jQuery(".iusisu_success-msg").text(iusisu_res.iusisu_success_msg);
                    }
                    if(iusisu_res.iusisu_username_space){
                        jQuery(iusisu_res.iusisu_username_space).insertAfter('.iusisu_form-wrapper.iflair-plugin h2');
                        setTimeout(function() { jQuery(".iusisu_form-wrapper.iflair-plugin p.iusisu_user-sign-up.iusisu_error").remove(); }, 5000);
                    } else if(iusisu_res.iusisu_username_empty){
                        jQuery(iusisu_res.iusisu_username_empty).insertAfter('.iusisu_form-wrapper.iflair-plugin h2');
                        setTimeout(function() { jQuery(".iusisu_form-wrapper.iflair-plugin p.iusisu_user-sign-up.iusisu_error").remove(); }, 5000);                             
                    } else if(iusisu_res.iusisu_username_exists){
                        jQuery(iusisu_res.iusisu_username_exists).insertAfter('.iusisu_form-wrapper.iflair-plugin h2');
                        setTimeout(function() { jQuery(".iusisu_form-wrapper.iflair-plugin p.iusisu_user-sign-up.iusisu_error").remove(); }, 5000);                              
                    } else if(iusisu_res.iusisu_email_valid){
                        jQuery(iusisu_res.iusisu_email_valid).insertAfter('.iusisu_form-wrapper.iflair-plugin h2');
                        setTimeout(function() { jQuery(".iusisu_form-wrapper.iflair-plugin p.iusisu_user-sign-up.iusisu_error").remove(); }, 5000);                             
                    } else if(iusisu_res.iusisu_email_existence){
                        jQuery(iusisu_res.iusisu_email_existence).insertAfter('.iusisu_form-wrapper.iflair-plugin h2');
                        setTimeout(function() { jQuery(".iusisu_form-wrapper.iflair-plugin p.iusisu_user-sign-up.iusisu_error").remove(); }, 5000);                             
                    } else if(iusisu_res.iusisu_password){
                        jQuery(iusisu_res.iusisu_password).insertAfter('.iusisu_form-wrapper.iflair-plugin h2');    
                        setTimeout(function() { jQuery(".iusisu_form-wrapper.iflair-plugin p.iusisu_user-sign-up.iusisu_error").remove(); }, 5000);     
                    }                   
                },
                complete:function(data)
                {
                    jQuery("#iusisu_user_loader").fadeOut(500);
                }
            });
            
        }
    });
            
    //SHOW HIDE PASSWORD FIELD
    const iusisu_togglePassword = document.querySelector('#iusisu_togglePassword');
    const iusisu_password = document.querySelector('#iusisu_txtPassword');
    if(iusisu_togglePassword) {
        iusisu_togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const iusisu_type = iusisu_password.getAttribute('type') === 'password' ? 'text' : 'password';
            iusisu_password.setAttribute('type', iusisu_type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });
    }
    //END SHOW HIDE PASSWORD FIELD

    jQuery(document).on("click",".iusisu_user_password_generate",function(){
        iusisu_generatePassword();
    });
    
    function iusisu_generatePassword(length = 20) {
        let iusisu_generatedPassword = "";
        const iusisu_validChars = "0123456789" +
        "abcdefghijklmnopqrstuvwxyz" +
        "ABCDEFGHIJKLMNOPQRSTUVWXYZ" +
        ",.-{}+!\"#$%/()=?" + "!@#$%^&*";
        for (let i = 0; i < length; i++) {
            let iusisu_randomNumber = crypto.getRandomValues(new Uint32Array(1))[0];
            iusisu_randomNumber = iusisu_randomNumber / 0x100000000;
            iusisu_randomNumber = Math.floor(iusisu_randomNumber * iusisu_validChars.length);
            iusisu_generatedPassword += iusisu_validChars[iusisu_randomNumber];
        }
        jQuery("#iusisu_txtPassword").val(iusisu_generatedPassword);
    }

    // User account page jquery
    setTimeout(function ()
    {
     var iusisu_currentTab = localStorage.getItem('current');
     var iusisu_currentur = localStorage.getItem('currentusr');
     var iusisu_chkur = jQuery('#iusisu_chkuser').val();

        if (iusisu_currentTab && iusisu_currentur == iusisu_chkur) {
            jQuery('.iusisu_tabs li[data-tab="' + iusisu_currentTab + '"]').trigger('click');
        }
    }, 200);

    jQuery('#iusisu_myeditprofile').click(function ()
    {   
        // DASHBOARD EDIT PROFILE LINK
        jQuery('.iusisu_tabs li[data-tab="iusisu_edit-profile"]').click();
    });

    jQuery('ul.iusisu_tabs li.iusisu_tab-link').click(function (e)
    {   
        var iusisu_chkur = jQuery('#iusisu_chkuser').val();
        localStorage.setItem('current', jQuery(e.target).attr('data-tab'));
        localStorage.setItem('currentusr', iusisu_chkur);
        var tab_id = jQuery(this).attr('data-tab');

        jQuery('ul.iusisu_tabs li.iusisu_tab-link').removeClass('iusisu_current');
        jQuery('.iusisu_tab-content').removeClass('iusisu_current');

        jQuery(this).addClass('iusisu_current');
        jQuery("#" + tab_id).addClass('iusisu_current');
    });

    // Start Change Password Jquery
    jQuery(document).on('click','#iusisu_change-password .iusisu_form-field i',function()
        {
            var iusisu_type = jQuery(this).siblings('input').attr('type') === 'password' ? 'text' : 'password';
            jQuery(this).siblings('input').attr('type',iusisu_type);
            this.classList.toggle('fa-eye-slash');
        });
                
        jQuery.validator.addMethod('passwordvalidation', function(value, element, param)
        {
            var iusisu_nameRegex = /^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
            return value.match(iusisu_nameRegex);
        }, 'You need to create your password with minimum 8 character and including alphnumeri and add one special character like !@#$%^&*()');

        jQuery("form[name='iusisu_change_password']").validate(
        {
            rules: {
              iusisu_fp_old_password: "required",
              iusisu_fp_new_password:  {
                    required: true,
                    passwordvalidation: true
                },
              iusisu_fp_confirm_password : {
                    required: true,
                    equalTo : '[name="iusisu_fp_new_password"]'
                },
            },
            messages: {
              iusisu_fp_old_password: "Please enter your old password",
              iusisu_fp_new_password: {
                    required: "Please enter your new password",
                    //minlength: "Your password must be at least 5 characters long"
                    },
              iusisu_fp_confirm_password : {
                    required: "Please enter your confirm password",
                    equalTo : 'New password and confirm password not match'
                },
            },
            submitHandler: function(form) {
                var iusisu_redirect_home_page = jQuery("#iusisu_site_url").val();
                var iusisu_oldPass = jQuery("#iusisu_old-password").val();
                var iusisu_newPass = jQuery("#iusisu_new-password").val();
                var iusisu_confPass = jQuery("#iusisu_confirm-password").val();
                var iusisu_password_change = jQuery("#iusisu_password-change").val();

                var ajax_url = admin_ajaxObj.ajax_url;
                var data =  {
                    iusisu_oldPass:iusisu_oldPass,
                    iusisu_newPass:iusisu_newPass,
                    iusisu_confPass:iusisu_confPass,
                    iusisu_password_change:iusisu_password_change,
                    action:"iusisu_change_password_ajax"
                };                  
                jQuery.ajax({
                    url: ajax_url,
                    type:'POST',
                    data: data,
                    beforeSend: function()
                    {
                        // Show image container
                        jQuery("#iusisu_user_loader").fadeIn(500);
                    },
                    success: function(html){
                        jQuery("#iusisu_username_email").css("border", "");
                        jQuery(".iusisu_change-password-message").html(html);
                        setTimeout(function() { jQuery(".iusisu_change-password-message").hide(); }, 5000);
                    },
                    complete:function(data)
                    {
                        // Hide image container
                        jQuery("#iusisu_user_loader").fadeOut(500);
                    }
                });
            }
        });
        // End Change Password Jquery

        // Start edit profile jquery
        jQuery("form[name='iusisu_edit_profile']").validate({
            rules: {
                iusisu_email: {
                    required: true,
                    email: true
                },
            },
            messages: {
                iusisu_email: {
                    required: "Please enter your email",
                    email: "Please enter a valid email address"
                },
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
        // End edit profile jquery
           
        // Start jquery login form
        jQuery('form[id="iusisu_login"]').validate(
        {
            rules: {
                iusisu_email: {
                    required: true,
                },
                iusisu_password: {
                    required: true,
                    // minlength: 5,
                }
            },
            messages: {
                iusisu_email: {
                    required: "Please enter email or user name",
                },
                iusisu_password: {
                    required: "Please enter password",
                    // minlength: 'Password must be at least 5 characters long'
                }
            },
            submitHandler: function(form)
            {
                var iusisu_email = jQuery(form).find('#iusisu_email').val();
                var iusisu_password = jQuery(form).find('#iusisu_password').val();
                var iusisu_rememberme = jQuery(form).find('#iusisu_rememberme').val();

                var ajax_url = admin_ajaxObj.ajax_url;
                var iusisu_nonce = jQuery(form).find('#iusisu_user_login').val();
                var data =  {
                    iusisu_email : iusisu_email,
                    iusisu_password : iusisu_password,
                    iusisu_rememberme : iusisu_rememberme,
                    iusisu_nonce : iusisu_nonce,
                    action : "iusisu_userValidateFunc"
                };
                jQuery.ajax({
                    url: ajax_url,
                    type:'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function()
                    {
                        // Show image container
                        jQuery("#iusisu_user_loader").fadeIn(500);
                    },
                    success: function(response)
                    {   
                        if(response.code == 1)
                        {
                            jQuery(".iusisu_login-message-con").html(response.message);
                            jQuery(".iusisu_login-message-con").addClass("success-msg");
                            setTimeout(function(){
                                window.location = response.redirect_url;
                            }, 2000);
                        }
                        else if(response.code == 0){
                            jQuery(".iusisu_login-message-con").html(response.message);
                            jQuery(".iusisu_login-message-con").addClass("iusisu_error");
                            
                        }else {
                            jQuery(".iusisu_login-message-con").html('Please activate your account first');
                            jQuery(".iusisu_login-message-con").addClass("iusisu_error");
                        }                                                                    
                        
                    },
                    complete:function(data)
                    {
                        // Hide image container
                        jQuery("#iusisu_user_loader").fadeOut(500);
                    }
                });
            }
        });

}); 