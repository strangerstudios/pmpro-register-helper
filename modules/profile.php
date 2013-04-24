<?php
/*
	This module contains code to show public profiles.
*/
function pmprorh_profile_shortcode($atts, $content=null, $code="")
{
	// $atts    ::= array of attributes
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [pmpro_profile user_id="1"]	directory of level 1 members

	global $current_user, $wpdb;
	
	extract(shortcode_atts(array(
		'user_id' => NULL
	), $atts));
	
	if(empty($user_id) && !empty($_REQUEST['pu']))		
	{
		$user_nicename = $_REQUEST['pu'];
		$user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_nicename = '" . $wpdb->escape($user_nicename) . "' LIMIT 1");
	}
		
	if(!empty($user_id))
		$pu = get_userdata($user_id);
	elseif(empty($_REQUEST['pu']))
		$pu = get_userdata($current_user->ID);		
	else
	{
		wp_redirect(home_url("/404"));
		exit;
	}				
		
	global $pmprorh_registration_fields;
	
	//which fields are marked for the profile	
	$profile_fields = pmprorh_getProfileFields($pu->ID);
	
	//are we saving?
	if(!empty($_REQUEST['submit-profile']))
	{
		if(!current_user_can("edit_user", $pu->ID))
			die("You do not have permission to do this.");
		
		//let's edit
		foreach($profile_fields as $field)
		{
			if(isset($_POST[$field->name]))
				update_user_meta($pu->ID, $field->name, $_POST[$field->name]);
		}
		
		//reset profile fields
		$profile_fields = pmprorh_getProfileFields($pu->ID);
		
		$pmpro_msg = "Profile saved.";
		$pmpro_msgt = "success";
	}
	
	ob_start();	
	//heading
	if(!empty($pmpro_msg))
	{
	?>
		<div id="pmpro_message" class="pmpro_message <?php echo $pmpro_msgt?>"><?php echo $pmpro_msg?></div>
	<?php
	}
	
	//show the fields
	if(!empty($profile_fields))
	{	
		?>
		<form action="" method="post">
			<table class="form-table">
			<?php
			//cycle through groups
			foreach($profile_fields as $field)
			{			
				$field->displayInProfile($pu->ID);			
			}
			?>
			</table>
			<div class="pmpro_submit">
				<span id="pmpro_submit_span">
					<input type="hidden" name="submit-profile" value="1">		
					<input type="submit" class="pmpro_btn pmpro_btn-submit" value="Save Profile">				
				</span>
			</div>
		</form>
		<?php
	}	
			
	$temp_content = ob_get_contents();
	ob_end_clean();
	
	return $temp_content;
}
add_shortcode("pmpro_profile", "pmprorh_profile_shortcode");
