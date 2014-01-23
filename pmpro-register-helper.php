<?php
/*
Plugin Name: PMPro Register Helper
Plugin URI: http://www.paidmembershipspro.com/pmpro-register-helper/
Description: Shortcodes and other functions to help customize your registration forms.
Version: .5.6.1
Author: Stranger Studios
Author URI: http://www.strangerstudios.com
*/
//options - just defaults for now, will be in settings eventually
global $pmprorh_options;
//$pmprorh_options["register_redirect_url"] = home_url("/tools/rq/");
//$pmprorh_options["use_email_for_login"] = true;
$pmprorh_options["directory_page"] = "/directory/";
$pmprorh_options["profile_page"] = "/profile/";

//Register Form Module
/*
	If you don't have Paid Memberships Pro installed, you can use the custom registration form included with this plugin by using the [pmprorh_register_form] shortcode.
*/
require_once(dirname(__FILE__) . "/modules/register-form.php");
require_once(dirname(__FILE__) . "/modules/change-password.php");

/*
	Modules controlling the directory and profile functionality
*/
require_once(dirname(__FILE__) . "/modules/directory.php");
require_once(dirname(__FILE__) . "/modules/profile.php");

//PMProRH_Field class
/*
	Some examples of creating fields with the class.
	
	//company field is required and editable by admins and users in the profile page
	$text = new PMProRH_Field("company", "text", array("size"=>40, "class"=>"company", "profile"=>true, "required"=>true));
	
	//referral id is not required and only editable by admins. Includes an updated label.
	$referral = new PMProRH_Field("referral", "text", array("label"=>"Referral Code", "profile"=>"admins"));
	
	//dropdown, includes a blank option
	$dropdown = new PMProRH_Field("gender", "select", array("options"=>array("" => "", "male"=>"Male", "female"=>"Female")));
	
	//textarea
	$history = new PMProRH_Field("history", "textarea", array("rows"=>10, "label"=>"Tell us a little about your history."));
	
	//hidden
	$secret = new PMProRH_Field("secret", "hidden", array("value"=>"this is the secret"));
*/
require_once(dirname(__FILE__) . "/classes/class.field.php");

//global to store extra registration fields
global $pmprorh_registration_fields, $pmprorh_checkout_boxes;
$pmprorh_registration_fields = array();
$cb = new stdClass();
$cb->name = "checkout_boxes";
$cb->label = "More Information";
$cb->order = 0;
$pmprorh_checkout_boxes = array("checkout_boxes" => $cb);

/*
$text = new PMProRH_Field("company", "text", array("size"=>40, "class"=>"company", "profile"=>true, "required"=>true));
pmprorh_add_registration_field("after_billing_fields", $text);
*/

/*
	Add a field to the PMProRH regisration fields global
	
	$where refers to various hooks in the PMPro checkout page and can be:
	- after_username
	- after_password
	- after_email
	- after_captcha
	- checkout_boxes
	- after_billing_fields
	- before_submit_button
	- just_profile (make sure you set the profile attr of the field to true or admins)
*/
function pmprorh_add_registration_field($where, $field)
{
	global $pmprorh_registration_fields;
	if(empty($pmprorh_registration_fields[$where]))
		$pmprorh_registration_fields[$where] = array($field);
	else	
		$pmprorh_registration_fields[$where][] = $field;
	return true;
}

/*
	Add a new checkout box to the checkout_boxes section. You can then use this as the $where parameter to pmprorh_add_registration_field.
	
	Name must contain no spaces or special characters.
*/
function pmprorh_add_checkout_box($name, $label = NULL, $description = "", $order = NULL)
{
	global $pmprorh_checkout_boxes;
	
	$temp = new stdClass();
	$temp->name = $name;
	$temp->label = $label;
	$temp->description = $description;
	$temp->order = $order;
	
	//defaults
	if(empty($temp->label))
		$temp->label = ucwords($temp->name);
	if(!isset($order))
	{
		$lastbox = pmprorh_end($pmprorh_checkout_boxes);
		$temp->order = $lastbox->order + 1;
	}
	
	$pmprorh_checkout_boxes[$name] = $temp;
	usort($pmprorh_checkout_boxes, "pmprorh_sortByOrder");
	
	return true;
}

//from: http://www.php.net/manual/en/function.end.php#107733
function pmprorh_end($array) { return end($array); }

function pmprorh_sortByOrder($a, $b)
{
	if ($a->order == $b->order) {
        return 0;
    }
    return ($a->order < $b->order) ? -1 : 1;
}

/*
	Load CSS, JS files
*/
function pmprorh_scripts()
{
	if(!is_admin())
	{
		if(!defined("PMPRO_VERSION"))
		{
			//load some styles that we need from PMPro
			wp_enqueue_style("pmprorh_pmpro", plugins_url('css/pmpro.css',__FILE__ ));
		}
		
		wp_enqueue_style("pmprorh_frontend", plugins_url('css/pmprorh_frontend.css',__FILE__ ));
	}
}
add_action("init", "pmprorh_scripts");

/*
	Cycle through extra fields. Show them at checkout.			
*/
//default register_form (if PMPro is not installed)
function pmprorh_default_register_form()
{
	global $pmprorh_registration_fields;	
	
	if(!empty($pmprorh_registration_fields["register_form"]))
	{
		foreach($pmprorh_registration_fields["register_form"] as $field)
		{					
			if(pmprorh_checkFieldForLevel($field))
				$field->displayAtCheckout();		
		}
	}
}
add_action("register_form", "pmprorh_default_register_form");

//pmprorh register_form after_email
function pmprorh_register_form_after_email()
{
	global $pmprorh_registration_fields;	
	
	if(!empty($pmprorh_registration_fields["pmprorh_after_email"]))
	{		
		foreach($pmprorh_registration_fields["pmprorh_after_email"] as $field)
		{			
			if(pmprorh_checkFieldForLevel($field))
				$field->displayAtCheckout();		
		}
	}
}
add_action("pmprorh_after_email", "pmprorh_register_form_after_email");

//pmprorh register_form after_password
function pmprorh_register_form_after_password()
{
	global $pmprorh_registration_fields;	
	
	if(!empty($pmprorh_registration_fields["pmprorh_after_password"]))
	{
		foreach($pmprorh_registration_fields["pmprorh_after_password"] as $field)
		{			
			if(pmprorh_checkFieldForLevel($field))
				$field->displayAtCheckout();		
		}
	}
}
add_action("pmprorh_after_password", "pmprorh_register_form_after_password");

//pmprorh register_form after_email
function pmprorh_register_form()
{
	global $pmprorh_registration_fields;	
	
	if(!empty($pmprorh_registration_fields["pmprorh_register_form"]))
	{
		foreach($pmprorh_registration_fields["pmprorh_register_form"] as $field)
		{			
			if(pmprorh_checkFieldForLevel($field))
				$field->displayAtCheckout();		
		}
	}
}
add_action("pmprorh_register_form", "pmprorh_register_form");

/*
	Cycle through extra fields. Show them at checkout.			
*/
//after_username
function pmprorh_pmpro_checkout_after_username()
{
	global $pmprorh_registration_fields;	
	
	if(!empty($pmprorh_registration_fields["after_username"]))
	{
		foreach($pmprorh_registration_fields["after_username"] as $field)
		{						
			if(pmprorh_checkFieldForLevel($field))
				$field->displayAtCheckout();		
		}
	}
}
add_action("pmpro_checkout_after_username", "pmprorh_pmpro_checkout_after_username");

//after_password
function pmprorh_pmpro_checkout_after_password()
{
	global $pmprorh_registration_fields;	
	
	if(!empty($pmprorh_registration_fields["after_password"]))
	{
		foreach($pmprorh_registration_fields["after_password"] as $field)
		{			
			if(pmprorh_checkFieldForLevel($field))
				$field->displayAtCheckout();		
		}
	}
}
add_action("pmpro_checkout_after_password", "pmprorh_pmpro_checkout_after_password");

//after_email
function pmprorh_pmpro_checkout_after_email()
{
	global $pmprorh_registration_fields;	
	
	if(!empty($pmprorh_registration_fields["after_email"]))
	{
		foreach($pmprorh_registration_fields["after_email"] as $field)
		{			
			if(pmprorh_checkFieldForLevel($field))
				$field->displayAtCheckout();		
		}
	}
}
add_action("pmpro_checkout_after_email", "pmprorh_pmpro_checkout_after_email");

//after captcha
function pmprorh_pmpro_checkout_after_captcha()
{
	global $pmprorh_registration_fields;	
	
	if(!empty($pmprorh_registration_fields["after_captcha"]))
	{
		foreach($pmprorh_registration_fields["after_captcha"] as $field)
		{			
			if(pmprorh_checkFieldForLevel($field))
				$field->displayAtCheckout();		
		}
	}
}
add_action("pmpro_checkout_after_captcha", "pmprorh_pmpro_checkout_after_captcha");

//checkout boxes
function pmprorh_pmpro_checkout_boxes()
{
	global $pmprorh_registration_fields, $pmprorh_checkout_boxes;	
	
	foreach($pmprorh_checkout_boxes as $cb)
	{
		if(!empty($pmprorh_registration_fields[$cb->name]))
		{
			?>
			<table id="pmpro_checkout_box-<?php echo $cb->name; ?>" class="pmpro_checkout" width="100%" cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
					<th>
						<?php echo $cb->label;?>
					</th>						
				</tr>
			</thead>
			<tbody>  
				<tr><td>
				<?php if(!empty($cb->description)) {  ?><div class="pmpro_checkout_decription"><?php echo $cb->description; ?></div><?php } ?>
				<?php
				foreach($pmprorh_registration_fields[$cb->name] as $field)
				{			
					if(pmprorh_checkFieldForLevel($field))
						$field->displayAtCheckout();		
				}
				?>
				</td></tr>
			</tbody>
			</table>
			<?php
		}
	}
}
add_action("pmpro_checkout_boxes", "pmprorh_pmpro_checkout_boxes");

//after_billing_fields
function pmprorh_pmpro_checkout_after_billing_fields()
{
	global $pmprorh_registration_fields;	
		
	if(!empty($pmprorh_registration_fields["after_billing_fields"]))
	{
		foreach($pmprorh_registration_fields["after_billing_fields"] as $field)
		{			
			if(pmprorh_checkFieldForLevel($field))
				$field->displayAtCheckout();		
		}
	}
}
add_action("pmpro_checkout_after_billing_fields", "pmprorh_pmpro_checkout_after_billing_fields");

//before submit button
function pmprorh_pmpro_checkout_before_submit_button()
{
	global $pmprorh_registration_fields;	
	
	if(!empty($pmprorh_registration_fields["before_submit_button"]))
	{
		foreach($pmprorh_registration_fields["before_submit_button"] as $field)
		{			
			if(pmprorh_checkFieldForLevel($field))
				$field->displayAtCheckout();		
		}
	}
}
add_action("pmpro_checkout_before_submit_button", "pmprorh_pmpro_checkout_before_submit_button");

/*
	Update the fields at checkout.
*/
function pmprorh_pmpro_after_checkout($user_id)
{
	global $pmprorh_registration_fields;	
	
	//any fields?
	if(!empty($pmprorh_registration_fields))
	{		
		//cycle through groups
		foreach($pmprorh_registration_fields as $where => $fields)
		{						
			//cycle through fields
			foreach($fields as $field)
			{				
				if(!pmprorh_checkFieldForLevel($field))
					continue;
				
				//assume no value
				$value = NULL;
				
				//where are we getting the value from?
				if(isset($_REQUEST[$field->name]))
				{
					//request
					$value = $_REQUEST[$field->name];
				}
				elseif(isset($_SESSION[$field->name]))
				{
					//session
					$value = $_SESSION[$field->name];
					
					//unset
					unset($_SESSION[$field->name]);
				}
				elseif(isset($_FILES[$field->name]))
				{
					//file
					$value = $_FILES[$field->name]['name'];
				}
												
				//update user meta
				if(isset($value))	
				{					
					//callback?
					if(!empty($field->save_function))
						call_user_func($field->save_function, $user_id, $field->name, $value);
					else
						update_user_meta($user_id, $field->name, $value);
				}
			}			
		}
	}
}
add_action('pmpro_after_checkout', 'pmprorh_pmpro_after_checkout');
add_action('pmpro_before_send_to_paypal_standard', 'pmprorh_pmpro_after_checkout');	//for paypal standard we need to do this just before sending the user to paypal

/*
	Require required fields.
*/
//require the fields
function pmprorh_rf_pmpro_registration_checks($okay)
{
	global $pmpro_msg, $pmpro_msgt, $current_user;
	
	//if there is an earlier error, just return that
	if(!$okay)
		return $okay;
	
	//array to store fields that were required and missed
	$required = array();
	
	//any fields?
	global $pmprorh_registration_fields;
	if(!empty($pmprorh_registration_fields))
	{
		//cycle through groups
		foreach($pmprorh_registration_fields as $where => $fields)
		{			
			//cycle through fields
			foreach($fields as $field)
			{
				//if the field is not for this level, skip it
				if(!pmprorh_checkFieldForLevel($field))
					continue;
					
				if(isset($_REQUEST[$field->name]))
					$value = $_REQUEST[$field->name];	
				elseif(isset($_FILES[$field->name]))
				{
					$value = $_FILES[$field->name]['name'];
					
					//handle empty file but the user already has a file
					if(empty($value) && !empty($_REQUEST[$field->name . "_old"]))
						$value = $_REQUEST[$field->name . "_old"];
					elseif(!empty($value))
					{
						//check extension against allowed extensions
						$filetype = wp_check_filetype_and_ext($_FILES[$field->name]['tmp_name'], $_FILES[$field->name]['name']);						
						if((!$filetype['type'] || !$filetype['ext'] ) && !current_user_can( 'unfiltered_upload' ))
						{			
							pmpro_setMessage(sprintf(__("Sorry, the file type for %s is not permitted for security reasons.", "pmpro"), $_FILES[$field->name]['name']), "pmpro_error");
							return false;
						}
					}
				}
				else
					$value = false;
			 
				if(!empty($field->required) && empty($value))
				{
					$required[] = $field->name;		
				}
			}
		}
	}
	
	if(!empty($required))
	{
		if(count($required) > 1)
			$pmpro_msg = "The " . implode(", ", $required) . " field is required.";
		else
			$pmpro_msg = "The " . implode(", ", $required) . " fields are required.";
		$pmpro_msgt = "pmpro_error";
		return false;
	}
	
	//all good
	return true;
}
add_filter("pmpro_registration_checks", "pmprorh_rf_pmpro_registration_checks");

/*
	Sessions vars for PayPal Express
*/
function pmprorh_rf_pmpro_paypalexpress_session_vars()
{
	global $pmprorh_registration_fields;
	
	//save our added fields in session while the user goes off to PayPal	
	if(!empty($pmprorh_registration_fields))
	{
		//cycle through groups
		foreach($pmprorh_registration_fields as $where => $fields)
		{			
			//cycle through fields
			foreach($fields as $field)
			{
				if(!pmprorh_checkFieldForLevel($field))
					continue;
					
				if(isset($_REQUEST[$field->name]))
					$_SESSION[$field->name] = $_REQUEST[$field->name];
			}
		}
	}
}
add_action("pmpro_paypalexpress_session_vars", "pmprorh_rf_pmpro_paypalexpress_session_vars");

/*
	Show profile fields.
*/
function pmprorh_rf_show_extra_profile_fields($user)
{
	global $pmprorh_registration_fields;

	//which fields are marked for the profile	
	$profile_fields = pmprorh_getProfileFields($user->ID);			
	
	//show the fields
	if(!empty($profile_fields))
	{		
		?>
		<h3>Extra profile information</h3>
		<table class="form-table">
		<?php
		//cycle through groups
		foreach($profile_fields as $field)
		{			
			$field->displayInProfile($user->ID);			
		}
		?>
		</table>
		<?php
	}
}
add_action( 'show_user_profile', 'pmprorh_rf_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'pmprorh_rf_show_extra_profile_fields' );

/*
	Get RH fields which are set to showup in the Members List CSV Export.	
*/
function pmprorh_getCSVFields()
{
	global $pmprorh_registration_fields;
	
	$csv_fields = array();
	if(!empty($pmprorh_registration_fields))
	{
		//cycle through groups
		foreach($pmprorh_registration_fields as $where => $fields)
		{			
			//cycle through fields
			foreach($fields as $field)
			{				
				if(!empty($field->memberslistcsv) && ($field->memberslistcsv == "true"))
				{	
					$csv_fields[] = $field;
				}
	
			}
		}
	}
	
	return $csv_fields;
}

/*
	Get the RH fields which are marked to show in the profile.
	If a $user_id is passed in, get fields based on the user's level.
*/
function pmprorh_getProfileFields($user_id)
{
	global $pmprorh_registration_fields;
	
	$profile_fields = array();
	if(!empty($pmprorh_registration_fields))
	{
		//cycle through groups
		foreach($pmprorh_registration_fields as $where => $fields)
		{			
			//cycle through fields
			foreach($fields as $field)
			{				
				if(!pmprorh_checkFieldForLevel($field, "profile", $user_id))
					continue;				
				
				if(!empty($field->profile) && ($field->profile === "admins" || $field->profile === "admin"))
				{
					if(current_user_can("manage_options", $user_id))
						$profile_fields[] = $field;
				}
				elseif(!empty($field->profile))
				{
					$profile_fields[] = $field;
				}
			}
		}
	}
	
	return $profile_fields;
}

/*
	Save profile fields.
*/
function pmprorh_rf_save_extra_profile_fields( $user_id ) 
{
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	$profile_fields = pmprorh_getProfileFields($user_id);
		
	//save our added fields in session while the user goes off to PayPal	
	if(!empty($profile_fields))
	{		
		//cycle through fields
		foreach($profile_fields as $field)
		{
			if(isset($_POST[$field->name]) || isset($_FILES[$field->name]))
			{
				//callback?
				if(!empty($field->save_function))
					call_user_func($field->save_function, $user_id, $field->name, $_POST[$field->name]);
				else
					update_user_meta($user_id, $field->name, $_POST[$field->name]);				
			}			
		}
	}
}
add_action( 'personal_options_update', 'pmprorh_rf_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'pmprorh_rf_save_extra_profile_fields' );

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
		<form class="pmpro_form" action="<?php echo pmpro_url("checkout"); ?>" method="post">
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
			<?php do_action("pmpro_checkout_after_username");?>
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
			<?php do_action("pmpro_checkout_after_password");?>
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
			<?php do_action("pmpro_checkout_after_email");?>
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
			$restrict_emails = strtolower(str_replace(array(";", ",", " "), "\n", $restrict_emails));
			if(!empty($current_user->user_email))
				$needle = strtolower($current_user->user_email);
			else
				$needle = strtolower($_REQUEST['bemail']);
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

function pmprorh_checkFieldForLevel($field, $scope = "default", $args = NULL)
{	
	if(!empty($field->levels))
	{
		if($scope == "profile")
		{
			//expecting the args to be the user id
			if(pmpro_hasMembershipLevel($field->levels, $args))
				return true;
			else
				return false;
		}
		else
		{
			//check against $_REQUEST
			if(!empty($_REQUEST['level']))
			{
				if(in_array($_REQUEST['level'], $field->levels))
					return true;
				else
					return false;
			}
			else
				return false;
		}
	}
	
	return true;
}

/*
	Slim signup form conversion.
*/
function pmprorh_email_passed($level)
{		
	global $wpdb, $pmpro_msg, $pmpro_msgt;
	
	//confirm email
	if(!empty($_GET['bemail']))
	{
		//make sure the email is available
		$oldemail = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_email = '" . $wpdb->escape($_REQUEST['bemail']) . "' LIMIT 1");
		if(!$oldemail)
		{			
			//confirm email
			global $bemail, $bconfirmemail;
			$bemail = str_replace(" ", "+", $_REQUEST['bemail']);
			$bconfirmemail = $bemail;
			$_REQUEST['bconfirmemail'] = $_REQUEST['bemail'];

			//make sure bemail is still set later
			add_action("pmpro_checkout_after_password", "pmproh_pmpro_checkout_after_password");
			
			//don't show the confirm email
			add_filter("pmpro_checkout_confirm_email", "pmproh_pmpro_checkout_confirm_email");
		}
		else
		{
			//email is in use
			wp_redirect(home_url("/login/?redirect_to=" . pmpro_url("checkout", "?level=" . $_REQUEST['level'])));
			exit;
		}
	}	

	return $level;	
}
add_action("init", "pmprorh_email_passed");

//prepopulate email address if passed (setup via pmprorh_email_passed)
function pmproh_pmpro_checkout_after_password()
{
	global $bemail;
	$bemail = str_replace(" ", "+", $_REQUEST['bemail']);	
}
//don't confirm email (setup via pmprorh_email_passed)
function pmproh_pmpro_checkout_confirm_email($show)
{
	return false;
}

/*
	Enqueue Select2 JS
*/
function pmprorh_enqueue_select2()
{
	//should check for cases when this is needed instead of always including.
	wp_enqueue_style('select2', plugins_url('css/select2.css', __FILE__), '', '3.1', 'screen');
	wp_enqueue_script('select2', plugins_url('js/select2.js', __FILE__), array( 'jquery' ), '3.1' );
}
add_action("init", "pmprorh_enqueue_select2");


/*
	adding meta fields to confirmation email
*/
function pmprorh_pmpro_email_filter($email)
{
	global $wpdb;
 
	//only update admin confirmation emails
	if(strpos($email->template, "checkout") !== false && strpos($email->template, "admin") !== false)
	{ 
		//get the user_id from the email
		$user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_email = '" . $email->data['user_email'] . "' LIMIT 1");

		if(!empty($user_id))
		{
			//get meta fields
			$fields = pmprorh_getProfileFields($user_id);
			
			//add to bottom of email
			if(!empty($fields))
			{
				$email->body .= "<p>Extra Fields:<br />";
				foreach($fields as $field)
				{
					$meta = get_user_meta($user_id, $field->name, true);
					
					$email->body .= "- " . $field->label . ": ";
					
					if(is_array($meta)) $email->body .= implode(', ', $meta);
					
					else $email->body .= $meta;
					
					$email->body .= "<br />";
					
				}				
				$email->body .= "</p>";
			}			
		}
	}
 
	return $email;
}
add_filter("pmpro_email_filter", "pmprorh_pmpro_email_filter", 10, 2);

/*
	Add CSV fields to the Member's List CSV Export.
*/
function pmprorh_pmpro_members_list_csv_extra_columns($columns)
{	
	$csv_cols = pmprorh_getCSVFields($user_id);		
	foreach($csv_cols as $key => $value)
	{		
		$columns[$value->name] = "pmprorh_csv_columns";
	}
	
	return $columns;
}
add_filter("pmpro_members_list_csv_extra_columns", "pmprorh_pmpro_members_list_csv_extra_columns", 10);

//function to pull meta for the added CSV columns
function pmprorh_csv_columns($user, $column)
{
	if(!empty($user->metavalues->$column))
	{		
		return $user->metavalues->$column;
	}
	else
	{
		return "";
	}
}




/*
	Replace last occurence of a string.
	From: http://stackoverflow.com/a/3835653/1154321
*/
if(!function_exists("str_lreplace"))
{
	function str_lreplace($search, $replace, $subject)
	{
		$pos = strrpos($subject, $search);

		if($pos !== false)
		{
			$subject = substr_replace($subject, $replace, $pos, strlen($search));
		}

		return $subject;
	}
}
