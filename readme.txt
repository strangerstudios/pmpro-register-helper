=== PMPro Register Helper ===
Contributors: strangerstudios
Tags: users, user meta, meta, memberships, registration
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: .2.3

Add extra fields to your checkout page. Works with Paid Memberships Pro.

== Description ==
This plugin currently requires Paid Memberships Pro. 

== Installation ==

1. Upload the `pmpro-register-helper` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.

Add a sign up form to a post/widget/page using a shortcode:

[pmpro_signup level="3" short="1" intro="0" button="Signup Now"]

Adding a field to your checkout page requires two steps: (1) create a field object, (2) call pmprorh_add_registration_field() to add the field to the checkout page.

e.g.
$text = new PMProRH_Field("company", "text", array("size"=>40, "class"=>"company", "profile"=>true, "required"=>true));
pmprorh_add_registration_field("after_billing_fields", $text);

The first parameter of the pmprorh_add_registration_field designates where the field will show up. Here are the current options:
- after_username
- after_password
- after_email
- after_captcha
- checkout_boxes
- after_billing_fields
- before_submit_button
- just_profile (make sure you set the profile attr of the field to true or admins)

Here are some examples of fields:
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

In can be helpful to store the fields in an array use a loop to add the fields. e.g.

$fields = array()
$fields[] = new PMProRH_Field("company", "text", array("size"=>40, "class"=>"company", "profile"=>true, "required"=>true));
$fields[] = new PMProRH_Field("referral", "text", array("label"=>"Referral Code", "profile"=>"admins"));
$fields[] = new PMProRH_Field("gender", "select", array("options"=>array("" => "", "male"=>"Male", "female"=>"Female")));
foreach($fields as $field)
	pmprorh_add_registration_field("checkout_boxes", $field);

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the issues section of GitHub and we'll fix it as soon as we can. Thanks for helping. https://github.com/strangerstudios/pmpro-register-helper/issues

== Changelog ==
= .2.3 =
* Fixed typo in pmprorh_rf_pmpro_paypalexpress_session_vars function that was keeping session vars from being saved (important for PayPal Express)
* Updated displayAtCheckout method of the fields class to check for a value in a session var if non is set in the $_REQUEST array.
* Fixed typo in readme example
* Fixed the check for the "showrequired" parameter, which when set on a field adds "* required" to the field display.

= .2.2 =
* Added the register-form.php module as a stand alone registration form. (For use without PMPro)
* Added the "divclass" property to fields, which will add a class to the wrapping div around the fields.
* Added a span * required to required fields.
* Added a $pmprorh_options global. Will be putting these into a settings/options page eventually.
* Updated pmprorh_getProfileFields to check for manage_options instead of edit_user for "admin" setting.

= .2.1 =
* Started tracking changes.
* Added save_function as a parameter to the add field function. This function will be called with the parameters ($user_id, $field_name, $value) when the field is saved after checkout or in the profile.
