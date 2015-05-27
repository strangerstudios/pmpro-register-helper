<?php
function pmprorh_register_form_handler()
{		
	global $post;
	if(!empty($post->post_content) && strpos($post->post_content, "[pmprorh_register_form]") !== false)
	{
		global $current_user, $pmprorh_options;		
		if(!empty($current_user->ID))
		{
			//now redirect them
			wp_redirect($pmprorh_options['register_redirect_url']);
			exit;
		}
		
		if(!empty($_REQUEST['wp-submit']))
		{
			global $wpdb, $pmpro_msg, $pmpro_msgt;
			
			$user_login = $_REQUEST['user_login'];
			$user_email = $_REQUEST['user_email'];
			$pass1 = $_REQUEST['pass1'];
			
			$fullname = $_REQUEST['fullname'];
			
			$pmpro_checkout_confirm_password = apply_filters("pmpro_checkout_confirm_password", true);					
			if($pmpro_checkout_confirm_password)
				$pass2 = $_REQUEST['pass2'];
			else
				$pass2 = $pass1;
						
			if(!empty($pmprorh_options['use_email_for_login']))
				$user_login = $user_email;	//use email for login
			
			//require fields
			$pmpro_required_billing_fields = array();

			//filter
			$pmpro_required_billing_fields = apply_filters("pmpro_required_billing_fields", $pmpro_required_billing_fields);			

			$missing_billing_field = false;
			foreach($pmpro_required_billing_fields as $key => $field)
			{
				if(!$field)
				{																									
					$missing_billing_field = true;										
					break;
				}
			}
						
			if(!empty($missing_billing_field) || empty($user_login) || empty($pass1) || empty($pass2) || empty($user_email))
			{				
				$pmpro_msg = "Please complete all required fields.";
				$pmpro_msgt = "pmpro_error";
			}			
			elseif(isset($pass1) && $pass1 != $pass2)
			{
				$pmpro_msg = "Your passwords do not match. Please try again.";
				$pmpro_msgt = "pmpro_error";
			}
			elseif(!empty($user_email) && !is_email($user_email))
			{
				$pmpro_msg = "The email address entered is in an invalid format. Please try again.";	
				$pmpro_msgt = "pmpro_error";
			}
			elseif(!empty($fullname))
			{
				$pmpro_msg = "Please leave the full name field empty. That field is a trap for automated spammers.";	
				$pmpro_msgt = "alert-danger";
			}
			else
			{
				//user supplied requirements
				$pmpro_continue_registration = apply_filters("pmpro_registration_checks", true);

				if($pmpro_continue_registration)
				{					
					//if creating a new user, check that the email and user_login are available
					if(empty($current_user->ID))
					{
						$oldusername = $wpdb->get_var("SELECT user_login FROM $wpdb->users WHERE user_login = '" . esc_sql($user_login) . "' LIMIT 1");
						$oldemail = $wpdb->get_var("SELECT user_email FROM $wpdb->users WHERE user_email = '" . esc_sql($user_email) . "' LIMIT 1");

						//this hook can be used to allow multiple accounts with the same email address
						$oldemail = apply_filters("pmpro_checkout_oldemail", $oldemail);
					}

					if(!empty($oldusername))
					{
						$pmpro_msg = "That username is already taken. Please try another.";
						$pmpro_msgt = "pmpro_error";
					}
					elseif(!empty($oldemail))
					{
						$pmpro_msg = "That email address is already taken. Please try another.";
						$pmpro_msgt = "pmpro_error";
					}
					else
					{
						//no errors yet
						if($pmpro_msgt != "pmpro_error")
						{
							// create user
							require_once( ABSPATH . WPINC . '/registration.php');
							$user_id = wp_insert_user(array(
											"user_login" => $user_login,							
											"user_pass" => $pass1,
											"user_email" => $user_email											
											));
							if (!$user_id) {
								$pmpro_msg = "There was an error setting up your account. Please try again.";
								$pmpro_msgt = "pmpro_error";
							} else {

								//check pmpro_wp_new_user_notification filter before sending the default WP email
								if(apply_filters("pmpro_wp_new_user_notification", true, $user_id, $pmpro_level->id))
									wp_new_user_notification($user_id, $pass1);								

								$wpuser = new WP_User(0, $user_login);

								//make the user a subscriber
								$wpuser->set_role("subscriber");

								//okay, log them in to WP							
								$creds = array();
								$creds['user_login'] = $user_login;
								$creds['user_password'] = $pass1;
								$creds['remember'] = true;
								$user = wp_signon( $creds, false );		

								//now redirect them
								wp_redirect($pmprorh_options['register_redirect_url']);
								exit;
							}
						}						
					}
				}
			}
		}
	}
}
add_action("wp", "pmprorh_register_form_handler");

function pmprorh_register_form_shortcode($atts, $content=null, $code="")
{
	// $atts    ::= array of attributes
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [pmprorh_register_form]

	/*
	extract(shortcode_atts(array(
		'level' => NULL
	), $atts));
	*/
	
	if(!empty($_REQUEST['wp-submit']))
	{
		$user_login = $_REQUEST['user_login'];
		$user_email = $_REQUEST['user_email'];		
	}
	else
	{
		$user_login = "";
		$user_email = "";
	}
		
	global $current_user, $pmpro_msg, $pmpro_msgt, $pmprorh_options;
	ob_start();
	?>	
<form name="registerform" id="registerform" action="" method="post">
	<?php if($pmpro_msg) 
		{
	?>
		<div id="pmpro_message" class="pmpro_message <?php echo $pmpro_msgt?>"><?php echo $pmpro_msg?></div>
	<?php
		}
		else
		{
	?>
		<div id="pmpro_message" class="pmpro_message" style="display: none;"></div>
	<?php
		}
	?>
	
	<?php		
		if(empty($pmprorh_options['use_email_for_login']))
		{
	?>
	<div id="div_user_login">
		<label>Username</label>
		<input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr($user_login); ?>" size="25" /> <span class="required">* Required</span>
	</div>
	<?php
		}
	?>
	<div id="div_user_email">
		<label>E-mail</label>
		<input type="text" name="user_email" id="user_email" class="input" value="<?php echo esc_attr($user_email); ?>" size="25" /> <span class="required">* Required</span>
	</div>
	<?php do_action("pmprorh_after_email"); ?>
	<div class="pmpro_hidden">
        	<label for="fullname"><?php _e('Full Name', 'pmpro');?></label>
        	<input id="fullname" name="fullname" type="text" class="input <?php echo pmpro_getClassForField("fullname");?>" size="30" value="" /> <strong><?php _e('LEAVE THIS BLANK', 'pmpro');?></strong>
    	</div>
	<div id="div_pass1">
		<label>Password</label>
		<input autocomplete="off" name="pass1" id="pass1" size="25" class="input" value="" type="password" /> <span class="required">* Required</span>
	</div>
	<?php
		$pmpro_checkout_confirm_password = apply_filters("pmpro_checkout_confirm_password", true);					
		if($pmpro_checkout_confirm_password)
		{
		?>
		<div id="div_pass2">
			<label>Confirm Password</label>
			<input autocomplete="off" name="pass2" id="pass2" size="25" class="input" value="" type="password" /> <span class="required">* Required</span>
		</div>   
		<?php
		}
	?>
	
	<?php do_action("pmprorh_after_password"); ?>    
    <?php do_action("pmprorh_register_form"); ?>
	
	<br class="clear" />
	<div class="submit">
		<label>&nbsp;</label>
		<input type="submit" name="wp-submit" id="wp-submit" value="Register" tabindex="100" />
	</div>
</form>

<p id="nav">
<a href="<?php echo home_url("wp-login.php");?>">Log in</a> |
<a href="<?php echo home_url("/wp-login.php?action=lostpassword");?>" title="Password Lost and Found">Lost your password?</a>
</p>

<p class="top1em required">* Required Field</p>
	<?php
	$temp_content = ob_get_contents();
	ob_end_clean();

	return $temp_content;
}
add_shortcode("pmprorh_register_form", "pmprorh_register_form_shortcode");
