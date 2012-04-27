<?php
/*
Plugin Name: PMPro Register Helper
Plugin URI: http://www.paidmembershipspro.com/pmpro-register-helper/
Description: Shortcodes and other functions to help customize your registration forms.
Version: .1
Author: Stranger Studios
Author URI: http://www.strangerstudios.com
*/

/*
	This shortcode will show a signup form. It will only show user account fields.
	If the level is not free, the user will have to enter the billing information on the checkout page.	
*/
function pmprorh_signup_shortcode($atts, $content=null, $code="")
{
	// $atts    ::= array of attributes
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [pmpro_signup level="3" short="1" intro="0" button="Signup Now"]

	extract(shortcode_atts(array(
		'level' => NULL,		
		'short' => NULL,
		'intro' => true,
		'button' => "Signup &raquo;"
	), $atts));
	
	//turn 0's into falses
	if($short == "0")
		$short = false;
	if($intro == "0")
		$intro = false;
	
	global $current_user, $membership_levels;

	ob_start();	
	?>
		<?php if(!empty($current_user->ID)) { ?>
			<p>You are logged in as <?php echo $current_user->user_login; ?>.</p>
		<?php } else { ?>
		<form class="pmpro_form" action="<?php echo pmpro_url("checkout"); ?>" method="pot">
			<?php
				if($intro)
				{
					echo wpautop("Register for " . $membership_levels[$level]->name . ".");
				}
			?>
			
			<input type="hidden" id="level" name="level" value="<?php echo $level; ?>" />	
			<div>
				<label for="username">Username</label>
				<input id="username" name="username" type="text" class="input" size="30" value="" /> 
			</div>										
			<div>
				<label for="password">Password</label>
				<input id="password" name="password" type="password" class="input" size="30" value="" /> 
			</div>
			<?php if($short) { ?>
				<input type="hidden" name="password2_copy" value="1" />
			<?php } else { ?>
				<div>
					<label for="password2">Confirm Password</label>
					<input id="password2" name="password2" type="password" class="input" size="30" value="" /> 
				</div>			
			<?php } ?>
			<div>
				<label for="bemail">E-mail Address</label>
				<input id="bemail" name="bemail" type="text" class="input" size="30" value="" /> 
			</div>
			<?php if($short) { ?>
				<input type="hidden" name="bconfirmemail_copy" value="1" />
			<?php } else { ?>
				<div>
					<label for="bconfirmemail">Confirm E-mail</label>
					<input id="bconfirmemail" name="bconfirmemail" type="text" class="input" size="30" value="" /> 
				</div>	         
			<?php } ?>
			<div class="pmpro_hidden">
				<label for="fullname">Full Name</label>
				<input id="fullname" name="fullname" type="text" class="input" size="30" value="" /> <strong>LEAVE THIS BLANK</strong>
			</div>	
			<div>
				<span id="pmpro_submit_span" >
					<input type="hidden" name="submit-checkout" value="1" />		
					<input type="submit" class="pmpro_btn pmpro_btn-submit-checkout" value="<?php echo $button; ?>" />
				</span>
			</div>	
		</form>
		<?php } ?>
	<?php
	$temp_content = ob_get_contents();
	ob_end_clean();
	return $temp_content;
}
add_shortcode("pmpro_signup", "pmprorh_signup_shortcode");

/*
	This code can be used to restrict level signups by email address.
*/
//text area for emails on edit level page
function pmprorh_pmpro_membership_level_after_other_settings()
{
	$level = $_REQUEST['edit'];	
	$restrict_emails = pmpro_getOption("level_" . $level . "_restrict_emails");
	?>
	<h3 class="topborder">Restrict by Email</h3>
	<p>To restrict signups to specific email addresses, enter those email addresses below, one per line. If blank, signups will not be restricted.</p>
	<textarea rows="10" cols="80" name="restrict_emails" id="restrict_emails"><?php echo str_replace("\"", "&quot;", stripslashes($restrict_emails))?></textarea>
	<?php
}
add_action("pmpro_membership_level_after_other_settings", "pmprorh_pmpro_membership_level_after_other_settings");

//update the emails on save
function pmprorh_pmpro_save_membership_level($saveid)
{
	$restrict_emails = $_REQUEST['restrict_emails'];
	pmpro_setOption("level_" . $saveid . "_restrict_emails", $restrict_emails);
}
add_action("pmpro_save_membership_level", "pmprorh_pmpro_save_membership_level");

//check emails when registering
function pmprorh_pmpro_registration_checks($okay)
{
	global $current_user;
	
	//only check if we're okay so far and there is an email to check
	if($okay && (!empty($_REQUEST['bemail']) || !empty($current_user->user_email)))
	{
		//are we restricting emails for this level
		global $pmpro_level;
		$restrict_emails = pmpro_getOption("level_" . $pmpro_level->id . "_restrict_emails");		
		if(!empty($restrict_emails))
		{
			$restrict_emails = str_replace(array(";", ",", " "), "\n", $restrict_emails);
			if(!empty($current_user->user_email))
				$needle = $current_user->user_email;
			else
				$needle = $_REQUEST['bemail'];
			$haystack = explode("\n", $restrict_emails);
			array_walk($haystack, create_function('&$val', '$val = trim($val);')); 			
			if(!in_array($needle, $haystack))
			{
				global $pmpro_msg, $pmpro_msgt;
				$pmpro_msg = "This membership level is restricted to certain users only. Make sure you've entered your email address correctly.";
				$pmpro_msgt = "pmpro_error";
				$okay = false;
			}
		}
	}
	
	return $okay;
}
add_filter("pmpro_registration_checks", "pmprorh_pmpro_registration_checks");