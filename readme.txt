=== PMPro Register Helper ===
Contributors: strangerstudios
Tags: users, user meta, meta, memberships, registration
Requires at least: 3.0
Tested up to: 3.6
Stable tag: .5.4

Add extra fields to your checkout page. Works with Paid Memberships Pro.

== Description ==
This plugin currently requires Paid Memberships Pro. 

== Installation ==

1. Upload the `pmpro-register-helper` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.

Add a sign up form to a post/widget/page using a shortcode:

[pmpro_signup level="3" short="1" intro="0" button="Signup Now"]

Adding a field to your checkout page requires two steps: (1) create a field object, (2) call pmprorh_add_registration_field() to add the field to the checkout page. Optionally, you can create your own "checkout_box" or fieldset to the checkout page using pmprorh_add_checkout_box().

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

NOTE: The first parameter of the PMProRH_Field function must contain no spaces or special characters other than _ or -.

Here are some examples of fields:
//company field is required and editable by admins and users in the profile page
$text = new PMProRH_Field("company", "text", array("size"=>40, "class"=>"company", "profile"=>true, "required"=>true));
	
//referral id is not required and only editable by admins. Includes an updated label.
$referral = new PMProRH_Field("referral", "text", array("label"=>"Referral Code", "profile"=>"admins"));
	
//dropdown, includes a blank option
$dropdown = new PMProRH_Field("gender", "select", array("options"=>array("" => "", "male"=>"Male", "female"=>"Female")));
	
//select2 dropdown
$select2 = new PMProRH_Field("category", "select2", array("profile"=>"only", "required"=>true, "options"=>array("cat1"=>"Category 1", "cat2"=>"Category 2", "cat3"=>"Category 3"), "select2options"=>"maximumSelectionSize: 2"));  

//radio
$radio = new PMProRH_Field("gender", "radio", array("options"=>array("male"=>"Male", "female"=>"Female"))); 
	
//textarea
$history = new PMProRH_Field("history", "textarea", array("rows"=>10, "label"=>"Tell us a little about your history."));
	
//hidden
$secret = new PMProRH_Field("secret", "hidden", array("value"=>"this is the secret"));

//any html
$html = new PMProRH_Field("htmlsection", "html", array("html"=>"<p>You can put any HTML here, and it will be <strong>added</strong> to your form.</p>"));

//readonly field
$readonly = new PMProRH_Field("r1", "readonly", array("value"=>"Readonly value"));

//readonly property on other field type
$readonly_text = new PMProRH_Field("r2", "text", array("value"=>"Readonly value", "readonly"=>true));

//file uploads
$resume = new PMProRH_Field("resume", "file", array("profile"=>true, "options"=>array()));

//Dependent fields. Add a "depends" value to the params array. Value should be an array of arrays. The inner array should be of the form array("id"=>{field id}, "value"=>{field value})
$category = new PMProRH_Field("category", "select", array("options"=>array("cat1"=>"Category 1", "cat2"=>"Category 2")));  
$subcat1 = new PMProRH_Field("subcat", "select", array("options"=>array(1=>"Subcat 1.1", 2=>"Subcat 1.2", 3=>"Subcat 1.3"), "depends"=>array(array("id"=>"category", "value"=>"cat1"))));  
$subcat2 = new PMProRH_Field("subcat", "select", array("options"=>array(1=>"Subcat 2.1", 2=>"Subcat 2.2", 3=>"Subcat 2.3"), "depends"=>array(array("id"=>"category", "value"=>"cat2"))));

In can be helpful to store the fields in an array use a loop to add the fields. e.g.

$fields = array();
$fields[] = new PMProRH_Field("company", "text", array("size"=>40, "class"=>"company", "profile"=>true, "required"=>true));
$fields[] = new PMProRH_Field("referral", "text", array("label"=>"Referral Code", "profile"=>"admins"));
$fields[] = new PMProRH_Field("gender", "select", array("options"=>array("" => "", "male"=>"Male", "female"=>"Female")));
foreach($fields as $field)
	pmprorh_add_registration_field("checkout_boxes", $field);

Adding a checkout box.

pmprorh_add_checkout_box("personal", "Personal Information");	//order parameter defaults to one more than the last checkout box
pmprorh_add_checkout_box("business", "Business Information", "Fields below are optional but will help us in verifying your account.");

Then add fields to these boxes.
$field = new PMProRH_Field("gender", "select", array("options"=>array("" => "", "male"=>"Male", "female"=>"Female")));
pmprorh_add_registration_field("personal", $field);

$field = PMProRH_Field("company", "text", array("size"=>40, "class"=>"company", "profile"=>true, "required"=>true));
pmprorh_add_registration_field("business", $field);

Note that the "checkout_boxes" location is now just the first checkout_box in the list with order = 0.
	
== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the issues section of GitHub and we'll fix it as soon as we can. Thanks for helping. https://github.com/strangerstudios/pmpro-register-helper/issues

== Changelog ==
= .5.4 =
* Added readonly field type (displays a label and static text) and readonly property (sets the readonly property of the html element). Examples in readme.
* Changed ids on the checkout form. The inputs will now have id = to the one set. The wrapping div will have an id like id_div.
* Fixing up dependencies.

= .5.3 =
* Added "radio" as a field type.
* Added dependencies. Add something like "depends"=>array(array("otherfieldname", "otherfieldvalue")) to the arguments array and the field specified will only show up if the other field has that value.

= .5.2.1 =
* Fixed typo in pmprorh_after_password checkout box check.

= .5.2 =
* Supports sending false as a label to keep the label from being shown.
* Fixed capability check in $field->displayInProfile to be "edit_user" instead of "edit_users".

= .5.1 =
* Includes meta fields and values in checkout confirmation emails sent to admins. (But not to members. Feel free to copy the code at the bottom of pmpro-register-helper.php and tweak to send to members as well. May add a way to do this in the future with an option to turn on/off.)

= .5 =
* Fixed bug where required fields weren't really required.
* Added change password page module
* Added profile page module. (Note this code needs added security, including XSS checks in the post data and wp nonce support.)
* Added directory page module.
* displayInProfile method for fields will show a dump of the value if the current user does not have the authority to edit the profile user.

= .4 =
* Added the select2 and html options types.

= .3.1 =
* Now setting $value to NULL while looping through fields in pmprorh_pmpro_after_checkout. This will keep user meta values from bleeding into other meta keys.
* PMPro 1.5.7.1 Added the pmpro_before_send_to_paypal_standard hook. This is executed at checkout before calling the sendToPayPal method on the order. The register helper plugin has been updated to update user meta fields during this hook in addition to the pmpro_after_checkout hook. (Because for PayPal Standard, when pmpro_after_checkout is called, the $_SESSION vars are unavailable to it. So other plugins relying on the pmpro_after_checkout hook may have issues with PayPal Standard.)

= .3 =
* Added id="pmpro_checkout_box-NAME" to pmpro_checkout tables.
* Added pmprorh_add_checkout_box($name, $label = NULL, $description = "", $order = NULL) which will add a new section and optional description to the checkout page that you can then use in as the $where parameter in pmprorh_add_registration_field(). Updated instructions.

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
