=== Paid Memberships Pro - Register Helper Add On ===
Contributors: strangerstudios
Tags: users, user meta, meta, memberships, registration
Requires at least: 3.5
Tested up to: 4.6.1
Stable tag: 1.3.4

Add extra fields to your checkout page. Works with Paid Memberships Pro.

== Description ==
This plugin currently requires Paid Memberships Pro. 

== Installation ==

1. Upload the `pmpro-register-helper` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.

Add a sign up form to a post/widget/page using a shortcode:

[pmpro_signup level="3" short="1" title="Sign Up for Gold Membership" intro="0" button="Signup Now"]

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

//checkbox
$checkbox = new PMProRH_Field("agree", "checkbox", array("profile"=>true));

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

//date fields
$date = new PMProRH_Field("date", "date", array("profile"=>true));

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

= 1.3.4 =
* BUG: Fixed bug where checkbox values weren't updated if they were changed from checked to unchecked during a renewal checkout. (Thanks, stevep2000)
* BUG: Fixed display of fields with multiple values in Members List CSV.
* BUG: Fixed JS logic for conditional checkbox fields. (Thanks, jslootbeek)
* BUG: Fixes for date fields.
* ENHANCEMENT: Updated select2 to the latest version and using minimized files.
* ENHANCEMENT: Now loading select2 on front end pages and user profiles.
* ENHANCEMENT: Made the multiselect "Choose one or more" instruction translatable.

= 1.3.3 =
* BUG: Still fixing the bug with dependency fields with labels with quotes in them.

= 1.3.2=
* BUG: Fixed a bug with dependency fields.

= 1.3.1 =
* ENHANCEMENT: Added an option html_attributes to fields that can be used to add arbitrary attributes to the HTML elements. e.g. set it to => array('placeholder'=>'Your Company') to set the placeholder attribute.

= 1.3 =
* BUG: Fixed warnings. (Thanks, Harsha and Thomas)
* BUG: Now using disabled=disabled on select fields instead of readonly=readonly.
* BUG: Fixed issues when the "profile" option of a field was set to "profile_only".
* BUG/ENHANCEMENT: Now only loading the select2 CSS and JS on the frontend to avoid conflicts with other plugins using select2 in the backend. (Thanks, Justin/defunctl on GitHub)
* BUG/ENHANCEMENT: Added handle to wp_enqueue_style() calls for templates to use.
* ENHANCEMENT: Added CSS classes to file fields. (Thanks, Ted Barnett)

= 1.2.1 =
* BUG: Now using $pmpro_level global to check for level fields in case the site uses default level post meta or otherwise filters the level.
* BUG: Fixed bug with select2 and multiselect fields when checking out with PayPal Express. (Thanks, samkam)

= 1.2 =
* BUG: Fixing conflicts that arise when field names overlap with public query vars. For example in WP 4.4 "title" was added as a public query var, which was often used as a field/usermeta name.
* BUG: Fixed bug where fields were sometimes showing up for levels they weren't set for.
* BUG: Fixed warnings.
* ENHANCEMENT: Added "depends" support for radio button fields.
* ENHANCEMENT: Added "pmprorh_section_header" filter to change the title of the default checkout box heading.

= 1.1 =
* BUG: Fixed display issues with file fields.
* BUG: Fixed issue where the "levels" option was only accepting arrays. You can now pass integers as well to check for a single level. (Thanks, Andy Schaff)
* ENHANCEMENT: No longer showing required asterisks for fields on the user profile page. We weren't forcing requirement on the profile page anyway and the asterisks were breaking some theme designs.
* ENHANCEMENT: Added the pmprorh_get_html filter. First parameter $r is the HTML about to be returned by the getHTML method. The second parameter $field is the field object.

= 1.0.2 =
* BUG: Fixed bug where all fields with profile=>true were acting as if they were profile=>only. (Thanks, Merry Eisner)

= 1.0.1 =
* BUG/ENHANCEMENT: Fixed the "only" setting for the profile options, so you can use "profile"=>"only" to have fields that only show up in the profile and don't show up (or get checked for requirements) at checkout. You can also use "only_admin" to add profile fields that can only be seen/edited by admins.
* ENHANCEMENT: Added "password" as a field type.
* ENHANCEMENT: Added an "intro" attribute to the "pmpro_signup" shortcode. Pass any text into the attribute to have that text shown above the signup form.
* ENHANCEMENT: Added a "login" attribute to the "pmpro_signup" shortcode. If set to 1 or true, a link to login will be shown below the signup button.

= 1.0 =
* No update from previous version, but setting to 1.0 since inclusion in the WordPress.org repository.
* Please backup you version of PMPro Register Helper if you have made any changes to modules or other parts of the code before upgrading to 1.0+.

= .6.2.2 =
* No only doing the bemail redirect to login if the user is not logged in.

= .6.2.1 =
* Fixed bug with readonly fields not showing up in the profile.
* You can now use "false" or "true" when setting attributes in the pmprorh_signup shortcode. (Thanks, Kim)
* Fixed bug where custom fields wouldn't be saved in 2checkout.

= .6.2 =
* Added after_pricing_fields location linked to the pmpro_checkout_after_pricing_fields action in the checkout template.

= .6.1 =
* Now respecting checkout boxes as sections when displaying profile fields at checkout and in the profile. Some of this was in the documentation, but not in the actual plugin until now.
* Fixed bug which prevented multiple select fields user metadata from being saved when getting back from payment gateway. (Thanks, Andrea "toomuchdesign" Carraro on GitHub)
* Fixed bug with required asterisks showing in the wrong places.

= .6 =
* Updated code to add profile fields into the admin confirmation email to handle multiselect and file types better. (Thanks, sweettea)
* Added support for integration with PMMPro Add Member addon. Use "addmember"=>true in your field options.
* "readonly" option now gives select fields disabled="disabled" attribute
* Added date as a field option.

= .5.20 =
* Fixed bug which prevented multiple select fields user metadata from being saved when getting back from payment gateway.
* Now allowing duplicate emails with pmpro_checkout_oldemail filter

= .5.19 =
* Can now set the "showrequired" option to "label" (all lower) and the required asterisk will be rendered between the label and the input field. Useful for some themes/designs.
* Fixed warning in pmprorh_cron_delete_tmp(). (Thanks, nozzljohn)

= .5.18 =
* Added code to set the enctype on the edit user page so file uploads work in the admin.

= .5.17.3 =
* Fixed warning on radio and readonly fields when shown in the profile. (Thanks, MarkG)

= .5.17.2 =
* The "depends" functionality now supports checking select2 and multiselect fields. The depending field will show up if any of the selected values in the multiselect equals the value given. e.g. "depends"=>array(array("id"=>"category", "value"=>"category1")) will show the depending field if category1 is one of the options selected in the #category multiselect field. (Thanks, Erik Bertrand)

= .5.17.1 =
* Fixed fatal error that would occur sometimes if Paid Memberships Pro was not active. (Thanks, Karmyn Tyler Cobb)

= .5.17 =
* Fixed bugs with multiselect fields. Now both using the field type "multiselect" or adding "multiple"=>true to the attributes for a select field will turn it into a multi select field.

= .5.16 =
* Fixed bug with required select fields with "multiple" enabled.

= .5.15 =
* Added the displayValue($value) method to the field class and using it in the displayInProfile method. This handles values that are arrays or indexes in a field's options.
* Now using field labels instead of names when displaying list of fields in the required fields error message.

= .5.14 =
* Now adding pmpro_error class to required fields if they are empty. (Thanks, Adrian)

= .5.13 =
* Added PMPRORH_DIR constant. Now using constant to enqueue stylesheets.
* Will now look in /themes/{YOUR THEME}/paid-memberships-pro/register-helper/css/ for copies of the CSS files, which will be used in place of the default CSS files if found.
* Fixed required field error so it will not show duplicate warnings for fields with the same name and will use proper grammar when 1 field is missing.

= .5.12 =
* Now looking in /themes/{YOUR THEME}/paid-memberships-pro/register-helper/ for copies of the modules .php files, which will be used in place of the default modules if found.
* Will no longer show a checkout box on the checkout page if there are no fields in it.
* Added fullname honey pot field to the register form module.

= .5.11.1 =
* Added pmpro_checkout_confirm_password filter. You can disable/hide the "confirm password" field on registration by adding `add_filter("pmpro_checkout_confirm_password", "__return_false");` to your active theme's functi)ons.php/etc.

= .5.11 =
* Fixed bug where only first letter of a meta value was showing up on checkout form.

= .5.10 =
* Fixed issue with file uploads. (Thanks, rwilki)

= .5.9 =
* Added checkbox as a field option.

= .5.8 =
* Now saving file info in a temp folder and $_SESSION so you can upload files using offsite gateways like PayPal Express.
* Using single quotes so \n in HTML output echos properly. (Thanks, joshlevinson on GitHub)
* If a "multiple" value is set on a select field, it will add the multiple property to the select element. (Thanks, joshlevinson on GitHub)
* Added "ext" option for file fields. If set to an array of extensions, it will check that the file uploaded matches one of those extensions.
* Added "accept" option for file fields. Sets the "accept" property of the minetype(s) given. e.g. use "image/*" to filter the file input to images.

= .5.7 =
* Added ability to restrict checkouts by username.
* Added hints to text fields.
* Updated query in directory module so you can specify a comma separated list of level ids in [pmpro_directory level="1,2"]

= .5.6.1 =
* Fixed dependency fields to work on edit user page in the admin as well.

= .5.6 =
* Requires version 1.7.6.1 of PMPro or later.
* Added a "memberslistcsv" option for fields. When set to true, the field will show up as a column in the Members List CSV export. (Thanks, Harsha.)
* Fixed bug where restrict by email was case sensitive.

= .5.5.1 =
* Fixed bug where only the first letter of a value was shown on the edit profile page.

= .5.5 =
* Properly handling required fields of type file.
* Checking file type and extension against WP's list for security.
* "showrequired" defaults to true now if "required" is set.

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
