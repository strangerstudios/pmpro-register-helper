<?php
function pmprorh_change_password_form_handler()
{		
	global $post;
	if(!empty($post->post_content) && strpos($post->post_content, "[pmprorh_change_password_form]") !== false)
	{
		global $current_user, $pmprorh_options;		
		if(empty($current_user->ID))
		{
			//now redirect them
			wp_redirect(wp_login_url());
			exit;
		}
		
		if(!empty($_REQUEST['wp-submit']))
		{
			global $wpdb, $pmpro_msg, $pmpro_msgt;
						
			$pass0 = $_REQUEST['pass0'];
			$pass1 = $_REQUEST['pass1'];
			$pass2 = $_REQUEST['pass2'];
															
			if(empty($pass0) || empty($pass1) || empty($pass2))
			{
				$pmpro_msg = "Please complete all fields.";
				$pmpro_msgt = "pmpro_error";
			}
			elseif(isset($pass1) && $pass1 != $pass2)
			{
				$pmpro_msg = "Your passwords do not match. Please try again.";
				$pmpro_msgt = "pmpro_error";
			}			
			else
			{
				//check that the original password is correct
				if(!wp_check_password($_REQUEST['pass0'], $current_user->data->user_pass, $current_user->ID))
				{
					$pmpro_msg = "The current password entered was incorrect.";
					$pmpro_msgt = "pmpro_error";
				}
				else
				{
					//update users password
					$user_data = array("ID" => $current_user->ID, "user_pass" => $_REQUEST['pass1']);
					
					if(wp_update_user($user_data) !== false)
					{
						//messages
						$pmpro_msg = "Your password has been updated.";
						$pmpro_msgt = "pmpro_success";
					}
				}								
			}
		}
	}
}
add_action("wp", "pmprorh_change_password_form_handler");

function pmprorh_change_password_form_shortcode($atts, $content=null, $code="")
{
	// $atts    ::= array of attributes
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [pmprorh_change_password_form]

	/*
	extract(shortcode_atts(array(
		'level' => NULL
	), $atts));
	*/		
		
	global $current_user, $pmpro_msg, $pmpro_msgt, $pmprorh_options;
	ob_start();
	?>	
<form class="pmpro_form" name="changepasswordform" id="changepasswordform" action="" method="post">
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
		
	<div id="div_pass0">
		<label>Current Password</label>
		<input type="password" name="pass0" id="pass0" class="input" value="" size="25" /> <span class="pmpro_asterisk">*</span>
	</div>
	<div id="div_pass1">
		<label>New Password</label>
		<input type="password" name="pass1" id="pass1" class="input" value="" size="25" /> <span class="pmpro_asterisk">*</span>
	</div>
	<div id="div_pass2">
		<label>Confirm Password</label>
		<input type="password" name="pass2" id="pass2" class="input" value="" size="25" /> <span class="pmpro_asterisk">*</span>
	</div>
	<div class="pmpro_submit">
		<input class="pmpro_btn" type="submit" name="wp-submit" id="wp-submit" value="Change Password" tabindex="100" />
	</div>
</form>

<p class="top1em"><span class="pmpro_asterisk">* Required Field</span></p>
	<?php
	$temp_content = ob_get_contents();
	ob_end_clean();

	return $temp_content;
}
add_shortcode("pmprorh_change_password_form", "pmprorh_change_password_form_shortcode");