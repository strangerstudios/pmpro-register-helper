=== Custom User Profile Fields for User Registration & Member Frontend Profiles with Paid Memberships Pro ===
Contributors: strangerstudios, paidmembershipspro
Tags: user profile, user fields, memberships, user meta, user profile, users
Requires at least: 5.4
Tested up to: 6.2
Requires PHP: 6.1.1
Stable tag: 1.8.3

Create custom user profile fields collected at registration or membership checkout for your WordPress users, members, and site admins.

== Description ==

### IMPORTANT UPDATE

Since PMPro version 2.9 was released on July 18, 2022, this plugin is no longer needed to manage user fields. With PMPro 2.9+, you can manage user fields from the Memberships > Settings > User Fields page in the WP admin dashboard. Any custom code written to work with Register Helper will still work as intended with only PMPro 2.9 installed.

*This plugin will no longer be maintained.*

If you were using the member directory features, you should use the [PMPro Member Directory plugin](https://www.paidmembershipspro.com/add-ons/member-directory/).

If you were using the Register Form module with PMPro, you should consider using the [PMPro Sign Up Shortcode plugin](https://www.paidmembershipspro.com/add-ons/pmpro-signup-shortcode/)).

If you were using the Register Form without PMPro, you can continue to use this plugin, but note that it will no longer be maintained.

If you were using the Restrict by Email/Username feature, you should use [this code snippet instead](https://github.com/strangerstudios/pmpro-snippets-library/blob/dev/checkout/restrict-checkout-by-email-or-username.php).

### The most popular WordPress plugin for custom user fields and member profiles.

Create custom user profile fields and collect additional user information at registration or membership checkout for your WordPress users, members, and site admins.

User fields can be added in many locations including:

* The membership checkout page in Paid Memberships Pro
* Captured on the user's frontend profile page.
* Managed in the WordPress admin area on the "Your Profile" screen (for users).
* Edited by site admins only on the "Edit User Profile" screen in the WordPress admin.

This plugin also allows you to restrict membership registration for a list of approved email addresses or usernames.

[Learn more about custom user profile fields and member registration fields in our documentation site](https://www.paidmembershipspro.com/add-ons/pmpro-register-helper-add-checkout-and-profile-fields/?utm_source=wordpress-org&utm_medium=readme&utm_campaign=pmpro-regiser-helper).

### Supports Multiple Field Types
Choose from a variety of popular user field types to capture additional information about your members. Fields can be customized by the member's selected or active membership level. The most popular field types include:

* Text and Textarea
* Select and Select2 (multi-select)
* Checkbox, Grouped Checkboxes, and Radio Select
* Date
* File Upload
* Read-only
* HTML (generates any desired HTML)
* Hidden

= Adding Fields For Specific Membership Levels =
This plugin is built for Paid Memberships Pro, the top WordPress membership plugin that's 100% free. You can add your fields for all members, or choose to show a field for specific members only. For example, allow your Premium level members to add a full length bio and upload a resume, while your Starter members can only add a brief bio and no file upload.

Custom user fields are a way to gather more information about your members and create a more tailored, premium experience.

= Conditional User Field Logic =
Any registered field can be a conditional field. These fields use JavaScript to dynamically hide or show based on another field's value.

= Create a Public or Private Member Directory =
Many sites use these custom fields to build a member directory for site visitors or members. You can customize which fields are displayed based on membership level. Read the [Member Directory and Profiles for PMPro](https://www.paidmembershipspro.com/add-ons/member-directory/?utm_source=wordpress-org&utm_medium=readme&utm_campaign=pmpro-register-helper) documentation page for more information on this feature.

= Restrict Membership Checkout by Email Address or Username =
Add your list of custom "approved" email addresses or usernames to the "Restrict by Email" or "Restrict by Username" field on the Memberships > Settings > Membership Levels > Edit Level admin page.

https://www.youtube.com/watch?v=VVTHYPQpfZ4

### About Paid Memberships Pro

[Paid Memberships Pro is a WordPress membership plugin](https://www.paidmembershipspro.com/?utm_source=wordpress-org&utm_medium=readme&utm_campaign=pmpro-register-helper) that puts you in control. Create what you want and release in whatever format works best for your business.

* Courses & E-Learning
* Private podcasts
* Premium Newsletters
* Private Communities
* Sell physical & digital goods

Paid Memberships Pro allows anyone to build a membership site—for free. Restrict content, accept payment, and manage subscriptions right from your WordPress admin.

Paid Memberships Pro is built "the WordPress way" with a lean core plugin and over 75 Add Ons to enhance every aspect of your membership site. Each business is different and we encourage customization. For our members we have a library of 300+ recipes to personalize your membership site.

Paid Memberships Pro is the flagship product of Stranger Studios. We are a bootstrapped company which grows when membership sites like yours grow. That means we focus our entire company towards helping you succeed.

[Try Paid Memberships Pro entirely for free on WordPress.org](https://wordpress.org/plugins/paid-memberships-pro/) and see why 100,000+ sites trust us to help them #GetPaid.

### Read More

Want more information on private forums, premium discussion boards, and WordPress membership sites? Have a look at:

* The [Paid Memberships Pro](https://www.paidmembershipspro.com/?utm_source=wordpress-org&utm_medium=readme&utm_campaign=pmpro-register-helper) official homepage.
* The [Custom Fields Register Helper for PMPro documentation page](https://www.paidmembershipspro.com/add-ons/pmpro-register-helper-add-checkout-and-profile-fields/?utm_source=wordpress-org&utm_medium=readme&utm_campaign=pmpro-register-helper).
* Also follow PMPro on [Twitter](https://twitter.com/pmproplugin), [YouTube](https://www.youtube.com/channel/UCFtMIeYJ4_YVidi1aq9kl5g) & [Facebook](https://www.facebook.com/PaidMembershipsPro/).

== Installation ==

Note: This plugin doesn't require [Paid Memberships Pro](https://wordpress.org/plugins/paid-memberships-pro/), but it is strongly recommended for sites that want to build a membership site and capture user profile information as part of free or paid member registration.

### Install PMPro Register Helper from within WordPress

1. Visit the plugins page within your dashboard and select "Add New"
1. Search for "PMPro Register Helper"
1. Locate this plugin and click "Install"
1. Activate "Paid Memberships Pro - Register Helper Add On" through the "Plugins" menu in WordPress
1. Go to "after activation" below.

### Install PMPro Register Helper Manually

1. Upload the `pmpro-register-helper` folder to the `/wp-content/plugins/` directory
1. Activate "Paid Memberships Pro - Register Helper" through the "Plugins" menu in WordPress
1. Go to "after activation" below.

### After Activation: Set Up Your Custom Fields

= Adding Fields to Membership Checkout =
Register Helper allows you to add fields to a variety of places within the Membership Checkout page using Paid Memberships Pro. Fields can be added to existing locations including:

* after_username
* after_password
* after_email
* after_captcha
* after_billing_fields
* before_submit_button

If you would like to add fields to the profile only, specify the 'just_profile' location.

Fields must be configured using custom code. We offer support for creating up to 5 fields as part of your [premium membership](https://www.paidmembershipspro.com/pricing/?utm_source=wordpress-org&utm_medium=readme&utm_campaign=pmpro-register-helper).

[View the full documentation on adding fields](https://www.paidmembershipspro.com/documentation/register-helper-documentation/adding-fields/?utm_source=wordpress-org&utm_medium=readme&utm_campaign=pmpro-register-helper) and [check out this video demo on Register Helper set up](https://www.youtube.com/watch?v=VVTHYPQpfZ4).

= Adding New Sections to Membership Checkout =
You can add a new box or 'section' to the Membership Checkout form using the 'checkout_boxes' feature. Your newly created box includes a title, description, and loads in the specified location.

[Read the documentation on Custom Checkout Boxes](https://www.paidmembershipspro.com/documentation/register-helper-documentation/adding-fields/#checkout-boxes?utm_source=wordpress-org&utm_medium=readme&utm_campaign=pmpro-register-helper)

= Restrict Membership Checkout by Email Address or Username =
Add your list of custom "approved" email addresses or usernames to the "Restrict by Email" or "Restrict by Username" field on the Memberships > Settings > Membership Levels > Edit Level admin page.

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the issues section of GitHub and we'll fix it as soon as we can. Thanks for helping. https://github.com/strangerstudios/pmpro-register-helper/issues

== Screenshots ==

1. A simple example of collecting text and textarea fields at membership checkout.
1. An example of using a new Checkout Box with conditional fields based on dropdown selection.
1. Using Register Helper fields in conjunction with the [Member Directory and Profile Pages Add On](https://www.paidmembershipspro.com/add-ons/pmpro-member-directory/).

== Changelog ==
= 1.8.3 - 2023-03-17 =
* BUG FIX: Reverting v1.8.2 changes which were not needed and potentially problematic.

= 1.8.2 - 2023-03-15 =
* BUG FIX: Resolved PHP 8+ compatibility issue by making some class methods static.

= 1.8.1 - 2023-01-04 =
* SECURITY: Better escaping of the signup shortcode parameters and other outputs.
* NOTICE: This plugin is still deprecated, but we have pushed out this security release to avoid potential security issues on sites using the old plugin.

= 1.8 - 2022-07-22 =
* FEATURE: Can set fields to be saved into a user taxonomy.
* ENHANCEMENT/BUG FIX: Sanitizing the upload file name. (Thanks, @benholdmen)
* ENHANCEMENT/BUG FIX: Removed unused select2 images.
* BUG FIX: Fixed fatal error when activating RH with PMPro 2.9+ active.
* BUG FIX: Fix mixed content errors in wp-admin when using HTTPS. (Thanks, @ZebulanStanphill)
* BUG FIX: Fixed issue with file uploads on multisite.

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
