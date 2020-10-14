<?php
/*
Plugin Name: Paid Memberships Pro - Register Helper Add On
Plugin URI: https://www.paidmembershipspro.com/add-ons/pmpro-register-helper-add-checkout-and-profile-fields/
Description: Add custom form fields to membership checkout and user profiles with Paid Memberships Pro.
Version: 1.7
Author: Paid Memberships Pro
Author URI: https://www.paidmembershipspro.com
Text Domain: pmpro-register-helper
*/

define('PMPRORH_DIR', dirname(__FILE__) );
define('PMPRORH_URL', WP_PLUGIN_URL . "/pmpro-register-helper");
define('PMPRORH_VERSION', '1.7');

/*
	Load plugin textdomain.
*/
function pmprorh_load_textdomain() {
  load_plugin_textdomain( 'pmpro-register-helper', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'pmprorh_load_textdomain' );

/*
	options - just defaults for now, will be in settings eventually

	Copy these lines into a custom plugin or your theme's functions.php
	and edit them to fit your needs.
*/
global $pmprorh_options;
//$pmprorh_options["register_redirect_url"] = home_url("/tools/rq/");
//$pmprorh_options["use_email_for_login"] = true;
//$pmprorh_options["directory_page"] = "/directory/";
//$pmprorh_options["profile_page"] = "/profile/";

/*
	Includes
*/
require_once(PMPRORH_DIR . "/shortcodes/pmpro_signup.php");			//[pmpro_signup ...] shortcode

/*
	Loading Modules

	You can create customizations/modifications by copying the
	register-form.php, change-password.php,	directory.php, and/or profile.php
	files and placing them in wp-content/themes/{YOUR THEME}/paid-memberships-pro/register-helper/

	The code will check if there is a custom version before using the default.
*/
$pmprorh_options['modules'] = apply_filters('pmprorh_modules', array('register-form', 'change-password', 'directory', 'profile'));
$custom_dir = get_stylesheet_directory()."/paid-memberships-pro/register-helper/";
if(!empty($pmprorh_options) && !empty($pmprorh_options['modules']))
{
	foreach($pmprorh_options['modules'] as $value)
	{
		//check folder for files,
		$custom_file = $custom_dir.$value.".php";

		if(file_exists($custom_file))
		{
			require_once($custom_file);
		}
		else
			require_once(PMPRORH_DIR . "/modules/".$value.".php");
	}
}

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
$cb->label = apply_filters("pmprorh_section_header", __('More Information','pmpro-register-helper') );
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
	if(empty($pmprorh_registration_fields[$where])) {
		$pmprorh_registration_fields[$where] = array();
	}
	if ( !empty($field) && is_a( $field, 'PMProRH_Field') ) {
		$pmprorh_registration_fields[$where][] = $field;
		return true;
	}

	return false;
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

function pmprorh_getCheckoutBoxByName($name)
{
	global $pmprorh_checkout_boxes;
	if(!empty($pmprorh_checkout_boxes))
	{
		foreach($pmprorh_checkout_boxes as $box)
		{
			if($box->name == $name)
				return $box;
		}
	}
	return false;
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
	global $pmpro_level, $pmpro_pages;
	if( !is_admin() && ( !empty( $_REQUEST['level'] ) || !empty( $pmpro_level ) || ( isset( $pmpro_pages['member_profile_edit'] ) && is_page( $pmpro_pages['member_profile_edit'] ) ) ) ) {
		if(!defined("PMPRO_VERSION"))
		{
			//load some styles that we need from PMPro (check child theme, then parent theme, then plugin folder)
			if(file_exists(get_stylesheet_directory()."/paid-memberships-pro/register-helper/css/pmpro.css"))
				wp_enqueue_style(get_stylesheet_directory_uri()."/paid-memberships-pro/register-helper/css/pmpro.css");
			elseif(file_exists(get_template_directory()."/paid-memberships-pro/register-helper/css/pmpro.css"))
				wp_enqueue_style(get_template_directory_uri()."/paid-memberships-pro/register-helper/css/pmpro.css");
			else
				wp_enqueue_style("pmprorh_pmpro", PMPRORH_URL . "/css/pmpro.css", NULL, PMPRORH_VERSION);
		}

		//load some styles that we need from PMPro (check child theme, then parent theme, then plugin folder)
		if(file_exists(get_stylesheet_directory()."/paid-memberships-pro/register-helper/css/pmprorh_frontend.css"))
			wp_enqueue_style(get_stylesheet_directory_uri()."/paid-memberships-pro/register-helper/css/pmprorh_frontend.css");
		elseif(file_exists(get_template_directory()."/paid-memberships-pro/register-helper/css/pmprorh_frontend.css"))
			wp_enqueue_style(get_template_directory_uri()."/paid-memberships-pro/register-helper/css/pmprorh_frontend.css");
		elseif(function_exists("pmpro_https_filter"))
			wp_enqueue_style("pmprorh_frontend", pmpro_https_filter(PMPRORH_URL) . "/css/pmprorh_frontend.css", NULL, "");
		else
			wp_enqueue_style("pmprorh_frontend", PMPRORH_URL . "/css/pmprorh_frontend.css", NULL, "");
	}
}
add_action( 'wp_enqueue_scripts', 'pmprorh_scripts' );

/**
 * Enqueue admin CSS
 */
function pmprorh_admin_enqueue_scripts() {
	wp_enqueue_style('pmprorh_admin', PMPRORH_URL . '/css/pmprorh_admin.css', array(), PMPRORH_VERSION, "screen");
}
add_action( 'admin_enqueue_scripts', 'pmprorh_admin_enqueue_scripts' );

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
			if( is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && ( !isset( $field->profile ) || $field->profile !== "only" && $field->profile !== "only_admin" ) ) {
				$field->displayAtCheckout();
			}
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
			if( is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && ( !isset( $field->profile ) || $field->profile !== "only" && $field->profile !== "only_admin") ) {
				$field->displayAtCheckout();
			}
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
			if(is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && ( !isset( $field->profile ) || $field->profile !== "only" && $field->profile !== "only_admin" ) ) {
				$field->displayAtCheckout();
			}
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
			if( is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && ( !isset( $field->profile ) || $field->profile !== "only" && $field->profile !== "only_admin" ) ) {
				$field->displayAtCheckout();
			}
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
			if( is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && ( !isset( $field->profile ) || $field->profile !== "only" && $field->profile !== "only_admin" ) ) {
				$field->displayAtCheckout();
			}
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
			if( is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && ( !isset( $field->profile ) || $field->profile !== "only" && $field->profile !== "only_admin" ) ) {
				$field->displayAtCheckout();
			}
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
			if( is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && ( !isset( $field->profile ) || $field->profile !== "only" && $field->profile !== "only_admin" ) ) {
				$field->displayAtCheckout();
			}
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
			if( is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && ( !isset( $field->profile ) || $field->profile !== "only" && $field->profile !== "only_admin" ) ) {
				$field->displayAtCheckout();
			}
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
		//how many fields to show at checkout?
		$n = 0;
		if(!empty($pmprorh_registration_fields[$cb->name]))
			foreach($pmprorh_registration_fields[$cb->name] as $field)
				if(is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && (!isset($field->profile) || (isset($field->profile) && $field->profile !== "only" && $field->profile !== "only_admin")))		$n++;

		if($n > 0) {
			?>
			<div id="pmpro_checkout_box-<?php echo $cb->name; ?>" class="pmpro_checkout">
				<hr />
				<h3>
					<span class="pmpro_checkout-h3-name"><?php echo $cb->label;?></span>
				</h3>
				<div class="pmpro_checkout-fields">
				<?php if(!empty($cb->description)) { ?>
					<div class="pmpro_checkout_decription"><?php echo $cb->description; ?></div>
				<?php } ?>

				<?php
					foreach($pmprorh_registration_fields[$cb->name] as $field) {
						if( is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && (!isset($field->profile) || (isset($field->profile) && $field->profile !== "only" && $field->profile !== "only_admin"))) {
							$field->displayAtCheckout();
						}
					}
				?>
				</div> <!-- end pmpro_checkout-fields -->
			</div> <!-- end pmpro_checkout_box-name -->
			<?php
		}
	}
}
add_action("pmpro_checkout_boxes", "pmprorh_pmpro_checkout_boxes");

//after_pricing_fields
function pmprorh_pmpro_checkout_after_pricing_fields()
{
	global $pmprorh_registration_fields;

	if(!empty($pmprorh_registration_fields["after_pricing_fields"]))
	{
		foreach($pmprorh_registration_fields["after_pricing_fields"] as $field)
    {
			if( is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && ( !isset( $field->profile ) || $field->profile !== "only" && $field->profile !== "only_admin" ) ) {
				$field->displayAtCheckout();
			}
		}
	}
}
add_action("pmpro_checkout_after_pricing_fields", "pmprorh_pmpro_checkout_after_pricing_fields");

//after_billing_fields
function pmprorh_pmpro_checkout_after_billing_fields()
{
	global $pmprorh_registration_fields;

	if(!empty($pmprorh_registration_fields["after_billing_fields"]))
	{
		foreach($pmprorh_registration_fields["after_billing_fields"] as $field)
		{
			if( is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && ( !isset( $field->profile ) || $field->profile !== "only" && $field->profile !== "only_admin" ) ) {
				$field->displayAtCheckout();
			}
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
			if( is_a($field, 'PMProRH_Field') && pmprorh_checkFieldForLevel($field) && ( !isset( $field->profile ) || $field->profile !== "only" && $field->profile !== "only_admin" ) ) {
				$field->displayAtCheckout();
			}
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
				if ( !is_a($field, 'PMProRH_Field') )
					continue;

				if(!pmprorh_checkFieldForLevel($field))
					continue;

				if(!empty($field->profile) && ($field->profile === "only" || $field->profile === "only_admin"))
					continue;	//wasn't shown at checkout

				//assume no value
				$value = NULL;

				//where are we getting the value from?
				if(isset($_REQUEST[$field->name]))
				{
					//request
					$value = $_REQUEST[$field->name];
				}
				elseif(isset($_REQUEST[$field->name . '_checkbox']) && $field->type == 'checkbox')
				{
					//unchecked checkbox
					$value = 0;
				}
				elseif(!empty($_POST[$field->name . "_checkbox"]) && in_array( $field->type, array( 'checkbox', 'checkbox_grouped', 'select2' ) ) )	//handle unchecked checkboxes
				{
					//unchecked checkbox
					$value = array();
				}
				elseif(isset($_SESSION[$field->name]))
				{
					//file or value?
					if(is_array($_SESSION[$field->name]) && isset($_SESSION[$field->name]['name']))
					{
						//add to files global
						$_FILES[$field->name] = $_SESSION[$field->name];

						//set value to name
						$value = $_SESSION[$field->name]['name'];
					}
					else
					{
						//session
						$value = $_SESSION[$field->name];
					}

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
					if ( isset( $field->sanitize ) && true === $field->sanitize ) {
						$value = pmprorh_sanitize( $value, $field );
                    }

					//callback?
					if(!empty($field->save_function))
						call_user_func($field->save_function, $user_id, $field->name, $value);
					else
						update_user_meta($user_id, $field->meta_key, $value);
				}
			}
		}
	}
}
add_action('pmpro_after_checkout', 'pmprorh_pmpro_after_checkout');
add_action('pmpro_before_send_to_paypal_standard', 'pmprorh_pmpro_after_checkout');	//for paypal standard we need to do this just before sending the user to paypal
add_action('pmpro_before_send_to_twocheckout', 'pmprorh_pmpro_after_checkout', 20);	//for 2checkout we need to do this just before sending the user to 2checkout
add_action('pmpro_before_send_to_gourl', 'pmprorh_pmpro_after_checkout', 20);	//for the GoURL Bitcoin Gateway Add On
add_action('pmpro_before_send_to_payfast', 'pmprorh_pmpro_after_checkout', 20);	//for the Payfast Gateway Add On

/**
 * Sanitizes the passed value.
 *
 * @param array|int|null|string|stdClass $value The value to sanitize
 *
 * @return array|int|string|object     Sanitized value
 */
function pmprorh_sanitize( $value, $field = null ) {

	if ( is_array( $value ) ) {

		foreach ( $value as $key => $val ) {
			$value[ $key ] = pmprorh_sanitize( $val );
		}
	}

	if ( is_object( $value ) ) {

		foreach ( $value as $key => $val ) {
			$value->{$key} = pmprorh_sanitize( $val );
		}
	}

	if ( ! empty( $field ) && ! empty( $field->type ) && $field->type === 'textarea' ) {
		$value = sanitize_textarea_field( $value );
	} elseif ( ( ! is_array( $value ) ) && ctype_alpha( $value ) ||
	     ( ( ! is_array( $value ) ) && strtotime( $value ) ) ||
	     ( ( ! is_array( $value ) ) && is_string( $value ) ) ||
	     ( ( ! is_array( $value ) ) && is_numeric( $value) )
	) {

		$value = sanitize_text_field( $value );
	}

	return $value;
}

/*
	Require required fields.
*/
//require the fields
function pmprorh_rf_pmpro_registration_checks($okay)
{
	global $current_user;

	//arrays to store fields that were required and missed
	$required = array();
    $required_labels = array();

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
                //handle arrays
                $field->name = preg_replace('/\[\]$/', '', $field->name);

				//if the field is not for this level, skip it
				if(!is_a($field, 'PMProRH_Field') || !pmprorh_checkFieldForLevel($field))
					continue;

				if(!empty($field->profile) && ($field->profile === "only" || $field->profile === "only_admin"))
					continue;	//wasn't shown at checkout

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
							if($okay)	//only want to update message if there is no previous error
								pmpro_setMessage(sprintf(__("Sorry, the file type for %s is not permitted for security reasons.", "pmprorh"), $_FILES[$field->name]['name']), "pmpro_error");
							return false;
						}
						else
						{
							//check for specific extensions anyway
							if(!empty($field->ext) && !in_array($filetype['ext'], $field->ext))
							{
								if($okay)	//only want to update message if there is no previous error
									pmpro_setMessage(sprintf(__("Sorry, the file type for %s is not permitted for security reasons.", "pmprorh"), $_FILES[$field->name]['name']), "pmpro_error");
								return false;
							}
						}
					}
				}
				else
					$value = false;

				if( ! $field->was_filled_if_needed() ) {
					$required[] = $field->name;
                    $required_labels[] = $field->label;
				}
			}
		}
	}

	if(!empty($required))
	{
		$required = array_unique($required);

		//add them to error fields
		global $pmpro_error_fields;
		$pmpro_error_fields = array_merge((array)$pmpro_error_fields, $required);

		if( count( $required ) == 1 ) {
			$pmpro_msg = sprintf( __( 'The %s field is required.', 'pmpro-register-helper' ),  implode(", ", $required_labels) );
			$pmpro_msgt = 'pmpro_error';
		} else {
			$pmpro_msg = sprintf( __( 'The %s fields are required.', 'pmpro-register-helper' ),  implode(", ", $required_labels) );
			$pmpro_msgt = 'pmpro_error';
		}

		if($okay)
			pmpro_setMessage($pmpro_msg, $pmpro_msgt);

		return false;
	}

	//return whatever status was before
	return $okay;
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
                if( !is_a($field, 'PMProRH_Field') || !pmprorh_checkFieldForLevel($field))
					continue;

                if(isset($_REQUEST[$field->name]))
					$_SESSION[$field->name] = $_REQUEST[$field->name];
				elseif(isset($_FILES[$field->name]))
				{
					/*
						We need to save the file somewhere and save values in $_SESSION
					*/

					//check for a register helper directory in wp-content
					$upload_dir = wp_upload_dir();
					$pmprorh_dir = $upload_dir['basedir'] . "/pmpro-register-helper/tmp/";

					//create the dir and subdir if needed
					if(!is_dir($pmprorh_dir))
					{
						wp_mkdir_p($pmprorh_dir);
					}

					//move file
					$new_filename = $pmprorh_dir . basename($_FILES[$field->name]['tmp_name']);
					move_uploaded_file($_FILES[$field->name]['tmp_name'], $new_filename);

					//update location of file
					$_FILES[$field->name]['tmp_name'] = $new_filename;

					//save file info in session
					$_SESSION[$field->name] = $_FILES[$field->name];
				}
			}
		}
	}
}
add_action("pmpro_paypalexpress_session_vars", "pmprorh_rf_pmpro_paypalexpress_session_vars");
add_action("pmpro_before_send_to_twocheckout", "pmprorh_rf_pmpro_paypalexpress_session_vars", 10, 0);

/*
	Show profile fields.
*/
function pmprorh_rf_show_extra_profile_fields($user, $withlocations = false)
{
	global $pmprorh_registration_fields;

	//which fields are marked for the profile
	$profile_fields = pmprorh_getProfileFields($user->ID, $withlocations);

	//show the fields
	if(!empty($profile_fields) && $withlocations)
	{
		foreach($profile_fields as $where => $fields)
		{
			$box = pmprorh_getCheckoutBoxByName($where);

			if ( !empty($box->label) )
			{ ?>
				<h3><?php echo $box->label; ?></h3><?php
			} ?>

			<table class="form-table">
			<?php
			//cycle through groups
			foreach($fields as $field)
			{
				if ( is_a($field, 'PMProRH_Field') )
					$field->displayInProfile($user->ID);
			}
			?>
			</table>
			<?php
		}
	}
	elseif(!empty($profile_fields))
	{
		?>
		<table class="form-table">
		<?php
		//cycle through groups
		foreach($profile_fields as $field)
		{
			if ( is_a($field, 'PMProRH_Field') )
				$field->displayInProfile($user->ID);
		}
		?>
		</table>
		<?php
	}
}
function pmprorh_rf_show_extra_profile_fields_withlocations($user)
{
	pmprorh_rf_show_extra_profile_fields($user, true);
}
add_action( 'show_user_profile', 'pmprorh_rf_show_extra_profile_fields_withlocations' );
add_action( 'edit_user_profile', 'pmprorh_rf_show_extra_profile_fields_withlocations' );

/**
 * Show Profile fields on the frontend "Member Profile Edit" page.
 *
 * @since 2.3
 */
function pmprorh_rf_show_extra_frontend_profile_fields( $user, $withlocations = false ) {
	global $pmprorh_registration_fields;

	//which fields are marked for the profile
	$profile_fields = pmprorh_getProfileFields($user->ID, $withlocations);

	//show the fields
	if ( ! empty( $profile_fields ) && $withlocations ) {
		foreach( $profile_fields as $where => $fields ) {
			$box = pmprorh_getCheckoutBoxByName( $where );

			// Only show on front-end if there are fields to be shown.
			$show_fields = false;
			foreach( $fields as $key => $field ) {
				if ( $field->profile !== 'only_admin' ) {
					$show_fields = true;
				}
			}

			// Bail if there are no fields to show on the front-end profile.
			if ( ! $show_fields ) {
				return;
			}
			?>

			<div class="pmpro_checkout_box-<?php echo $where; ?>">
				<?php if ( ! empty( $box->label ) ) { ?>
					<h3><?php echo $box->label; ?></h3>
				<?php } ?>

				<div class="pmpro_member_profile_edit-fields">
					<?php if ( ! empty( $box->description ) ) { ?>
						<div class="pmpro_checkout_description"><?php echo $box->description; ?></div>
					<?php } ?>

					<?php
						 // Cycle through groups.
						foreach( $fields as $field ) {
							if ( is_a( $field, 'PMProRH_Field' ) && $field->profile !== 'only_admin' ) {
								$field->displayAtCheckout( $user->ID );
							}
						}
					?>
				</div> <!-- end pmpro_member_profile_edit-fields -->
			</div> <!-- end pmpro_checkout_box_$where -->
			<?php
		}
	} elseif ( ! empty( $profile_fields ) ) { ?>
		<div class="pmpro_member_profile_edit-fields">
			<?php
				 // Cycle through groups.
				foreach( $fields as $field ) {
					if ( is_a( $field, 'PMProRH_Field' ) && $field->profile !== 'only_admin' ) {
						$field->displayAtCheckout( $user->ID );
					}
				}
			?>
		</div> <!-- end pmpro_member_profile_edit-fields -->
		<?php
	}
}
function pmprorh_rf_show_extra_frontend_profile_fields_withlocations( $user ) {
	pmprorh_rf_show_extra_frontend_profile_fields($user, true);
}
add_action( 'pmpro_show_user_profile', 'pmprorh_rf_show_extra_frontend_profile_fields_withlocations' );

/*
    Integrate with PMPro Add Member Admin addon
 */
function pmprorh_pmpro_add_member_fields( $user = null, $user_id = null)
{
    global $pmprorh_registration_fields;

    $addmember_fields = array();
    if(!empty($pmprorh_registration_fields))
    {
        //cycle through groups
        foreach($pmprorh_registration_fields as $where => $fields)
        {
            //cycle through fields
            foreach($fields as $field)
            {
	            if(is_a($field, 'PMProRH_Field') && isset($field->addmember) && !empty($field->addmember) && ( in_array( strtolower( $field->addmember ), array( 'true', 'yes' ) ) || true == $field->addmember ) )
                {
                        $addmember_fields[] = $field;
                }
            }
        }
    }


    //show the fields
    if(!empty($addmember_fields))
    {
        ?>
            <?php
            //cycle through groups
            foreach($addmember_fields as $field)
            {
				if(empty($user_id) && !empty($user) && !empty($user->ID)) {
					$user_id = $user->ID;
				}

		    		if (is_a($field, 'PMProRH_Field'))
					$field->displayInProfile($user_id);
            }
            ?>
    <?php
    }
}
add_action( 'pmpro_add_member_fields', 'pmprorh_pmpro_add_member_fields', 10, 2 );

function pmprorh_pmpro_add_member_added( $uid = null, $user = null )
{
	/**
	 * BUG: Incorrectly assumed that the user_login $_REQUEST[] variable exists
	 *
	 * @since 1.3
	 */
	if ( ! empty( $user ) && is_object( $user ) ) {
		$user_id = $user->ID;
	}

	if ( !empty( $uid ) && ( empty( $user ) || !is_object( $user ) ) ) {
		$user_id = $uid;
	}

	if ( empty($uid) && ( empty( $user ) || !is_object( $user ) ) ) {

		$user_login = isset( $_REQUEST['user_login'] ) ? $_REQUEST['user_login'] : null;

		if (!empty($user_login)) {
			$user_id = get_user_by('login', $_REQUEST['user_login'])->ID;
		}

	}

	// check whether the user login variable contains something useful
	if (empty($user_id)) {

		global $pmpro_msgt;
		global $pmpro_msg;

		$pmpro_msg = __("Unable to add/update PMPro Register Helper registration fields for this member", "pmprorh");
		$pmpro_msgt = "pmpro_error";

		return false;
	}

    global $pmprorh_registration_fields;

    $addmember_fields = array();
    if(!empty($pmprorh_registration_fields))
    {
        //cycle through groups
        foreach($pmprorh_registration_fields as $where => $fields)
        {
            //cycle through fields
            foreach($fields as $field)
            {
	            if(is_a($field, 'PMProRH_Field') && isset($field->addmember) && !empty($field->addmember) && ( in_array( strtolower( $field->addmember ), array( 'true', 'yes' ) ) || true == $field->addmember ) )
                {
                        $addmember_fields[] = $field;
                }
            }
        }
    }

    //save our added fields in session while the user goes off to PayPal
    if(!empty($addmember_fields))
    {
        //cycle through fields
        foreach($addmember_fields as $field)
        {
            if(is_a($field, 'PMProRH_Field') && isset($_POST[$field->name]) || isset($_FILES[$field->name]))
            {
	            if ( isset( $field->sanitize ) && true === $field->sanitize ) {

		            $value = pmprorh_sanitize( $_POST[ $field->name ], $field );
	            } elseif( isset($_POST[$field->name]) ) {
	                $value = $_POST[ $field->name ];
                } else {
                	$value = $_FILES[$field->name];
                }

                //callback?
                if(!empty($field->save_function))
                    call_user_func($field->save_function, $user_id, $field->name, $value );
                else
                    update_user_meta($user_id, $field->meta_key, $value );
            }
            elseif(is_a($field, 'PMProRH_Field') && !empty($_POST[$field->name . "_checkbox"]) && $field->type == 'checkbox')	//handle unchecked checkboxes
            {
                //callback?
                if(!empty($field->save_function))
                    call_user_func($field->save_function, $user_id, $field->name, 0);
                else
                    update_user_meta($user_id, $field->meta_key, 0);
			}
			elseif(!empty($_POST[$field->name . "_checkbox"]) && in_array( $field->type, array( 'checkbox', 'checkbox_grouped', 'select2' ) ) )	//handle unchecked checkboxes
			{
				//callback?
				if(!empty($field->save_function))
					call_user_func($field->save_function, $user_id, $field->name, array());
				else
					update_user_meta($user_id, $field->meta_key, array());
			}
        }
    }

}
add_action( 'pmpro_add_member_added', 'pmprorh_pmpro_add_member_added', 10, 2 );

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
				if(is_a($field, 'PMProRH_Field') && !empty($field->memberslistcsv) && ($field->memberslistcsv == "true"))
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
function pmprorh_getProfileFields($user_id, $withlocations = false)
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
				if(!is_a($field, 'PMProRH_Field') || !pmprorh_checkFieldForLevel($field, "profile", $user_id))
					continue;

				if(!empty($field->profile) && ($field->profile === "admins" || $field->profile === "admin" || $field->profile === "only_admin"))
				{
					if( current_user_can( 'manage_options' ) || current_user_can( 'pmpro_membership_manager' ) )
					{
						if($withlocations)
							$profile_fields[$where][] = $field;
						else
							$profile_fields[] = $field;
					}
				}
				elseif(!empty($field->profile))
				{
					if($withlocations)
						$profile_fields[$where][] = $field;
					else
						$profile_fields[] = $field;
				}
			}
		}
	}

	return $profile_fields;
}

/*
	Change the enctype of the edit user form in case files need to be uploaded.
*/
function pmprorh_user_edit_form_tag()
{
	echo ' enctype="multipart/form-data"';
}
add_action('user_edit_form_tag', 'pmprorh_user_edit_form_tag');

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
			if (!is_a($field, 'PMProRH_Field'))
				continue;

			if(isset($_POST[$field->name]) || isset($_FILES[$field->name]))
			{
				if ( isset( $_POST[ $field->name ] ) && isset( $field->sanitize ) && true === $field->sanitize ) {
					$value = pmprorh_sanitize( $_POST[ $field->name ], $field );
				} elseif( isset($_POST[$field->name]) ) {
				    $value = $_POST[ $field->name ];
                } else {
                	$value = $_FILES[$field->name];
                }

				//callback?
				if(!empty($field->save_function))
					call_user_func($field->save_function, $user_id, $field->name, $value);
				else
					update_user_meta($user_id, $field->meta_key, $value);
			}
			elseif(!empty($_POST[$field->name . "_checkbox"]) && $field->type == 'checkbox')	//handle unchecked checkboxes
			{
				//callback?
				if(!empty($field->save_function))
					call_user_func($field->save_function, $user_id, $field->name, 0);
				else
					update_user_meta($user_id, $field->meta_key, 0);
			}
			elseif(!empty($_POST[$field->name . "_checkbox"]) && in_array( $field->type, array( 'checkbox', 'checkbox_grouped', 'select2' ) ) )	//handle unchecked checkboxes
			{
				//callback?
				if(!empty($field->save_function))
					call_user_func($field->save_function, $user_id, $field->name, array());
				else
					update_user_meta($user_id, $field->meta_key, array());
			}
		}
	}
}
add_action( 'personal_options_update', 'pmprorh_rf_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'pmprorh_rf_save_extra_profile_fields' );
add_action( 'pmpro_personal_options_update', 'pmprorh_rf_save_extra_profile_fields' );

/*
	This code can be used to restrict level signups by email address or username
*/
//text area for emails and user names on edit level page
function pmprorh_pmpro_membership_level_after_other_settings()
{
	$level = $_REQUEST['edit'];
	$restrict_emails = pmpro_getOption("level_" . $level . "_restrict_emails");
	?>
	<h3 class="topborder">Restrict by Email</h3>
	<p>To restrict signups to specific email addresses, enter those email addresses below, one per line. If blank, signups will not be restricted.</p>
	<textarea rows="10" cols="80" name="restrict_emails" id="restrict_emails"><?php echo str_replace("\"", "&quot;", stripslashes($restrict_emails))?></textarea>
	<?php

	$restrict_usernames = pmpro_getOption("level_" . $level . "_restrict_usernames");
	?>
	<h3 class="topborder">Restrict by Username</h3>
	<p>To restrict signups to specific users or usernames, enter those usernames below, one per line. If blank, signups will not be restricted.</p>
	<textarea rows="10" cols="80" name="restrict_usernames" id="restrict_usernames"><?php echo str_replace("\"", "&quot;", stripslashes($restrict_usernames))?></textarea>
	<?php
}
add_action("pmpro_membership_level_after_other_settings", "pmprorh_pmpro_membership_level_after_other_settings");

//update the emails and usernames on save
function pmprorh_pmpro_save_membership_level($saveid)
{
	$restrict_emails = $_REQUEST['restrict_emails'];
	pmpro_setOption("level_" . $saveid . "_restrict_emails", $restrict_emails);

	$restrict_emails = $_REQUEST['restrict_usernames'];
	pmpro_setOption("level_" . $saveid . "_restrict_usernames", $restrict_emails);
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
			array_walk( $haystack, function( &$val ) { 
				$val = trim($val);
				return $val;
			});
			if(!in_array($needle, $haystack))
			{
				global $pmpro_msg, $pmpro_msgt;
				$pmpro_msg = "This membership level is restricted to certain users only. Make sure you've entered your email address correctly.";
				$pmpro_msgt = "pmpro_error";
				$okay = false;

				//no further checks here
				return $okay;
			}
		}

		//are we restricting user names for this level
		$restrict_usernames = pmpro_getOption("level_" . $pmpro_level->id . "_restrict_usernames");

		if(!empty($restrict_usernames))
		{
			$restrict_usernames = strtolower(str_replace(array(";", ",", " "), "\n", $restrict_usernames));
			if(!empty($current_user->user_login))
				$needle = strtolower($current_user->user_login);
			else
				$needle = strtolower($_REQUEST['username']);
			$haystack = explode("\n", $restrict_usernames);
			array_walk( $haystack, function( &$val ) { 
				$val = trim($val);
				return $val;
			});
			if(!in_array($needle, $haystack))
			{
				global $pmpro_msg, $pmpro_msgt;
				$pmpro_msg = "This membership level is restricted to certain users only. Make sure you are logged into your existing account and using the proper username.";
				$pmpro_msgt = "pmpro_error";
				$okay = false;
			}
		}
	}

	return $okay;
}
add_filter("pmpro_registration_checks", "pmprorh_pmpro_registration_checks");

function pmprorh_checkFieldForLevel( $field, $scope = 'default', $args = NULL ) {
	global $pmpro_level, $pmpro_checkout_level_ids;
	if ( ! empty( $field->levels ) ) {
		if ( 'profile' === $scope ) {
			// Expecting the args to be the user id.
			if ( pmpro_hasMembershipLevel( $field->levels, $args ) ) {
				return true;
			} else {
				return false;
			}
		} else {			
			if ( empty( $pmpro_checkout_level_ids ) && ! empty( $pmpro_level ) && ! empty( $pmpro_level->id ) ) {
				$pmpro_checkout_level_ids = array( $pmpro_level->id );
			}
			if ( ! is_array( $field->levels ) ) {
				$field_levels = array( $field->levels );
			} else {
				$field_levels = $field->levels;
			}
			if ( ! empty( $pmpro_checkout_level_ids ) ) {
				// Check against $_REQUEST.
				return ( ! empty( array_intersect( $field_levels, $pmpro_checkout_level_ids ) ) );
			}
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
	if(!empty($_GET['bemail']) && !is_user_logged_in())
	{
		//make sure the email is available
		$oldemail = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_email = '" . esc_sql($_REQUEST['bemail']) . "' LIMIT 1");
		if(!$oldemail || !apply_filters('pmpro_checkout_oldemail', true) )
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
function pmprorh_enqueue_select2($hook) {
	global $pmpro_pages;

	// only include on front end and user profiles
	if( ( !is_admin() && (
			!empty( $_REQUEST['level'] ) ||
			!empty( $pmpro_level ) ||
			class_exists("Theme_My_Login") && method_exists('Theme_My_Login', 'is_tml_page') && Theme_My_Login::is_tml_page("profile") ) ||
		( isset( $pmpro_pages['member_profile_edit'] ) && is_page( $pmpro_pages['member_profile_edit'] ) ) ) ||
		$hook == 'profile.php' ||
		$hook == 'user-edit.php' ) {
		wp_enqueue_style('select2', plugins_url('css/select2.min.css', __FILE__), '', '4.0.3', 'screen');
		wp_enqueue_script('select2', plugins_url('js/select2.min.js', __FILE__), array( 'jquery' ), '4.0.3' );
	}
}
add_action("wp_enqueue_scripts", "pmprorh_enqueue_select2");
add_action("admin_enqueue_scripts", "pmprorh_enqueue_select2");


/*
	adding meta fields to confirmation email
*/
function pmprorh_pmpro_email_filter($email)
{
	global $wpdb;

	//only update admin confirmation emails
	if(!empty($email) && strpos($email->template, "checkout") !== false && strpos($email->template, "admin") !== false)
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
					if ( !is_a($field, 'PMProRH_Field') )
						continue;

					$email->body .= "- " . $field->label . ": ";

					$value = get_user_meta($user_id, $field->meta_key, true);
					if($field->type == "file" && is_array($value) && !empty($value['fullurl']))
						$email->body .= $value['fullurl'];
					elseif(is_array($value))
						$email->body .= implode(", ", $value);
					else
						$email->body .= $value;

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
	$csv_cols = pmprorh_getCSVFields();
	foreach($csv_cols as $key => $value)
	{
		$columns[$value->meta_key] = "pmprorh_csv_columns";
	}

	return $columns;
}
add_filter("pmpro_members_list_csv_extra_columns", "pmprorh_pmpro_members_list_csv_extra_columns", 10);

/*
	Activation/Deactivation
*/
function pmprorh_activation()
{
	wp_schedule_event(time(), 'daily', 'pmprorh_cron_delete_tmp');
}
function pmprorh_deactivation()
{
	wp_clear_scheduled_hook('pmprorh_cron_delete_tmp');
}
register_activation_hook(__FILE__, 'pmprorh_activation');
register_deactivation_hook(__FILE__, 'pmprorh_deactivation');

/*
	Delete old files in wp-content/uploads/pmpro-register-helper/tmp every day.
*/
function pmprorh_cron_delete_tmp()
{
	$upload_dir = wp_upload_dir();
	$pmprorh_dir = $upload_dir['basedir'] . "/pmpro-register-helper/tmp/";

	if(file_exists($pmprorh_dir) && $handle = opendir($pmprorh_dir))
	{
		while(false !== ($file = readdir($handle)))
		{
			$file = $pmprorh_dir . $file;
			$filelastmodified = filemtime($file);
			if(is_file($file) && (time() - $filelastmodified) > 3600)
			{
				unlink($file);
			}
		}

		closedir($handle);
	}

	exit;
}
add_action('pmprorh_cron_delete_tmp', 'pmprorh_cron_delete_tmp');

//function to pull meta for the added CSV columns
function pmprorh_csv_columns($user, $column)
{
	if(!empty($user->metavalues->{$column}))
	{
		// check for multiple values
		$value = maybe_unserialize($user->metavalues->{$column});
		if(is_array($value))
			$value = join(',', $value);

		return $value;
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

/*
Function to add links to the plugin row meta
*/
function pmprorh_plugin_row_meta($links, $file) {
	if(strpos($file, 'pmpro-register-helper.php') !== false)
	{
		$new_links = array(
			'<a href="' . esc_url('https://www.paidmembershipspro.com/add-ons/pmpro-register-helper-add-checkout-and-profile-fields/')  . '" title="' . esc_attr( __( 'View Documentation', 'pmpro-register-helper' ) ) . '">' . __( 'Docs', 'pmpro-register-helper' ) . '</a>',
			'<a href="' . esc_url('https://www.paidmembershipspro.com/support/') . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro-register-helper' ) ) . '">' . __( 'Support', 'pmpro-register-helper' ) . '</a>',
		);
		$links = array_merge($links, $new_links);
	}
	return $links;
}
add_filter('plugin_row_meta', 'pmprorh_plugin_row_meta', 10, 2);
