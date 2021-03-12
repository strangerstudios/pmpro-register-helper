=== Paid Memberships Pro - Register Helper Add On ===
Contributors: strangerstudios
Tags: fields, memberships, user meta, user profile, users
Requires at least: 4.0
Tested up to: 5.7
Stable tag: 1.7

Add custom form fields to membership checkout and user profiles with Paid Memberships Pro.

== Description ==
Collect custom form fields at membership checkout and on the user profile. User fields can be added to the membership checkout page or captured on the user's frontend profile or "Edit Profile" screen in the WordPress admin.

This plugin also allows you to restrict membership registration for a list of approved email addresses or usernames.

[Read the full documentation for the Register Helper Add On](https://www.paidmembershipspro.com/add-ons/pmpro-register-helper-add-checkout-and-profile-fields/)

= Official Paid Memberships Pro Add On =

This is an official Add On for [Paid Memberships Pro](https://www.paidmembershipspro.com), the most complete member management and membership subscriptions plugin for WordPress.

= Supports Multiple Field Types =
Using Register Helper, you can add a variety of field types to capture additional information about your members. Fields can be customized by the member's selected or active membership level. Supported field types include:

* Text and Textarea
* Select and Select2 (multi-select)
* Checkbox, Grouped Checkboxes, and Radio Select
* Date
* File Upload
* Read-only
* HTML (generates any desired HTML)
* Hidden

Any registered field can be a conditional field. These fields use JavaScript to dynamically hide or show based on another field's value.

[Read the documentation on Adding Fields](https://www.paidmembershipspro.com/documentation/register-helper-documentation/adding-fields/)

= Adding Fields to Membership Checkout =
Register Helper allows you to add fields to a variety of places within the Membership Checkout page using Paid Memberships Pro. Fields can be added to existing locations including:

* after_username
* after_password
* after_email
* after_captcha
* after_billing_fields
* before_submit_button

If you would like to add fields to the profile only, specify the 'just_profile' location.

= Adding New Sections to Membership Checkout =
You can add a new box or 'section' to the Membership Checkout form using the 'checkout_boxes' feature. Your newly created box includes a title, description and specified location.

[Read the documentation on Checkout Boxes](https://www.paidmembershipspro.com/documentation/register-helper-documentation/adding-fields/#checkout-boxes)

= Restrict Membership Checkout by Email Address or Username =
Add your list of custom "approved" email addresses or usernames to the "Restrict by Email" or "Restrict by Username" field on the Memberships > Settings > Membership Levels > Edit Level admin page.

== Installation ==

1. Upload the `pmpro-register-helper` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Configure your fields using custom code. [View the full documentation on adding fields](https://www.paidmembershipspro.com/documentation/register-helper-documentation/adding-fields/) and [check out this video demo on Register Helper set up](https://www.youtube.com/watch?v=VVTHYPQpfZ4).


= Example Code for adding a Company field =
Below is a sample code that adds a "Company" field. You can add custom field code to your site by creating a custom plugin or using the Code Snippets plugin available for free in the WordPress repository. [Read this companion article for step-by-step directions on either method](https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/).

`function my_pmprorh_init( ) {
	// Don't break if Register Helper is not loaded.
	if( ! function_exists ( 'pmprorh_add_registration_field' ) ) {
		return false;
	}

	//define the fields
	$fields = array();

	$fields[] = new PMProRH_Field (
		'company',
		'text',
		array(
			'label' => 'Company',
			'profile' => true,
	));

	// Add the fields into a new checkout_boxes are of the checkout page.
	foreach ( $fields as $field ) {
		pmprorh_add_registration_field(
			'checkout_boxes', // location on checkout page
			$field            // PMProRH_Field object
		);
	}
}
add_action( 'init', 'my_pmprorh_init' );`

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the issues section of GitHub and we'll fix it as soon as we can. Thanks for helping. https://github.com/strangerstudios/pmpro-register-helper/issues

== Screenshots ==

1. A simple example of collecting text and textarea fields at membership checkout.
1. An example of using a new Checkout Box with conditional fields based on dropdown selection.
1. Using Register Helper fields in conjuction with the [Member Directory and Profile Pages Add On](https://www.paidmembershipspro.com/add-ons/pmpro-member-directory/).

== Changelog ==
= 1.7 - 2020-10-14 =
* ENHANCEMENT: Files can now be deleted by setting the "allow_delete" field attribute.
* ENHANCEMENT: Images submitted through the "file" field can now be previewed.
* ENHANCEMENT: Added function to collect information saved in the "wp_users" table.
* ENHANCEMENT: Membership managers can now see admin only fields.
* BUG FIX/ENHANCEMENT: Class "pmpro_required" now being added to required fields.
* BUG FIX/ENHANCEMENT: "Required" asterisks are now being added by the core PMPro plugin.
* BUG FIX/ENHANCEMENT: Checkout box descriptions are now being shown on frontend profile.
* BUG FIX: Fixed issue where fields would be required even if they are not visible on checkout page.
* BUG FIX: select2 and checkbox_grouped fields will now successfully save when empty.
* BUG FIX: Fields depending on checkbox_grouped value will now show/hide as expected.
* BUG FIX: Line breaks will now be preserved in textareas.
* BUG FIX: Fixed issue where "0" in text field would not pass required check.

= 1.6.1 - 2020-04-30 =
* BUG FIX: Fixed warning shown for PMPro versions < 2.3

= 1.6 - 2020-04-27 =
* BUG FIX: Fixed issue with checkbox fields readonly attribute.
* BUG FIX: Adjusted images to ensure their encoding is correct.
* BUG FIX/ENHANCEMENT: Improved CSS for custom fields at checkout.
* ENHANCEMENT: Removed "profile_only" legacy conditional check. (Thanks, @wiethkaty)
* ENHANCEMENT: Support Paid Memberships Pro v2.3+ front-end profile edit page.
* ENHANCEMENT: Update select2.js to latest distributed version. Improves integration with other plugins/themes that may also include select2.js
* ENHANCEMENT: Removed unused assets.
* ENHANCEMENT: Added classes to radio buttons, grouped check boxes and hidden fields. These now support "class" => "my-class" attribute when creating these fields.
* ENHANCEMENT: Make "The X field is required" error message translatable.
* ENHANCEMENT: Added 'pmpro-required' class to custom fields, this improves error handling and will highlight fields when there is a problem with them at checkout.

= 1.5 - 2019-11-22 =
* FEATURE: Added number fields. (Thanks, William Crandell)
* BUG FIX: Fixed issue where date fields could save values off by one month when using WP 5.3+.
* BUG FIX: Avoiding warnings when using 2Checkout.
* BUG FIX: Now properly wrapping checkoutbox list items in a ul tag.
* ENHANCEMENT: Fixed integration with the GoURL Bitcoin Payment Gateway Add On
* ENHANCEMENT: Fixed integration with the Payfast Payment Gateway Add On
* ENHANCEMENT: Added support for Multiple Memberships per User. Fields with levels parameters will show at checkout if ANY of those levels are included at checkout.
* ENHANCEMENT: Fixed placeholders when used in select2 elements.

= 1.4 =
* BUG FIX: Some required fields could be left empty at checkout.
* BUG FIX: Required File Upload was not recognized. (Thanks, contemplate on GitHub)
* BUG FIX: Slight fix for already uploaded docs. (Thanks, contemplate on GitHub)
* BUG FIX: Leading zeros were being removed from numeric values.
* BUG FIX: RH Field CSV export failure under PHP7.
* BUG FIX: Removed redundant </span> tag. (Thanks, jbruggeling on GitHub)
* BUG FIX: Fixed issue where 0 valued options were not being selected in dropdowns, multiselects, and radio fields.
* ENHANCEMENT: Improved display of field elements and checkout boxes for compatibility for 1.9.4.
* ENHANCEMENT: Improved UI of the checkbox_grouped field type.
* ENHANCEMENT: Added French translation files. (Thanks, Alfonso Sánchez Uzábal)

= 1.3.6 =
* BUG FIX: Fixed some warnings when fields are added to the Add Member Admin form.

= 1.3.5 =
* BUG FIX: Incorrect function definition (static vs non-static).
* BUG FIX: Didn't save RH fields from pmpro-add-member-admin
* ENHANCEMENT: Updated Readme, including instructions.
* ENHANCEMENT: Added logic to only load CSS and JS on the checkout and profile pages on the frontend and profile and edit user pages in the dashboard.

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
