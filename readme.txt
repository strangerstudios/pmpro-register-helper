=== PMPro Register Helper ===
Contributors: strangerstudios
Tags: users, user meta, meta, memberships, registration
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: .2.2

Add extra fields to your checkout page. Works with Paid Memberships Pro.

== Description ==
This plugin currently requires Paid Memberships Pro. 

== Installation ==

1. Upload the `pmpro-register-helper` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.

Add a sign up form to a post/widget/page using a shortcode:

[pmpro_signup level="3" short="1" intro="0" button="Signup Now"]

Use functions like the following to add fields to your check out page.

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

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the issues section of GitHub and we'll fix it as soon as we can. Thanks for helping. https://github.com/strangerstudios/pmpro-register-helper/issues

== Changelog ==
= .2.2 =
* Added the register-form.php module as a stand alone registration form. (For use without PMPro)
* Added the "divclass" property to fields, which will add a class to the wrapping div around the fields.
* Added a span * required to required fields.
* Added a $pmprorh_options global. Will be putting these into a settings/options page eventually.

= .2.1 =
* Started tracking changes.
* Added save_function as a parameter to the add field function. This function will be called with the parameters ($user_id, $field_name, $value) when the field is saved after checkout or in the profile.
