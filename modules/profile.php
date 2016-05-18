<?php
/*
	This module contains code to show public profiles.
*/
function pmprorh_profile_preheader()
{
	if(!is_admin())
	{
		global $post;
		if(!empty($post->post_content) && strpos($post->post_content, "[pmpro_profile") !== false)
		{
			/*
				Preheader operations here.
			*/			
			global $main_post_id;
			$main_post_id = $post->ID;
			
			//enqueue the stylesheet for this (check child theme, then parent theme, then plugin folder)	
			if(file_exists(get_stylesheet_directory()."/paid-memberships-pro/register-helper/css/pmprorh_profile.css"))
				wp_enqueue_style("pmprorh_profile_user", get_stylesheet_directory_uri()."/paid-memberships-pro/register-helper/css/pmprorh_profile.css");
			elseif(file_exists(get_template_directory()."/paid-memberships-pro/register-helper/css/pmprorh_profile.css"))
				wp_enqueue_style("pmprorh_profile_user", get_template_directory_uri()."/paid-memberships-pro/register-helper/css/pmprorh_profile.css");
			else
				wp_enqueue_style("pmprorh_profile", PMPRORH_DIR . "/css/pmprorh_profile.css", NULL, PMPRORH_VERSION);
			
			function pmprorh_post_title($title, $post_id = NULL)
			{				
				global $main_post_id;
				if(!empty($_REQUEST['pu']) && $post_id == $main_post_id)
				{
					global $wpdb;
					
					$user_nicename = $_REQUEST['pu'];
					$display_name = $wpdb->get_var("SELECT display_name FROM $wpdb->users WHERE user_nicename = '" . esc_sql($user_nicename) . "' LIMIT 1");					
					
					if(!empty($display_name))
					{
						$title = $display_name;
						
						//member lite theme uses this
						global $longtitle;
						$longtitle = $display_name;						
					}
				}
				
				return $title;
			}
			add_filter("wp_title", "pmprorh_post_title");
			add_filter("the_title", "pmprorh_post_title", 10, 2);
		}
	}
}
add_action("wp", "pmprorh_profile_preheader", 1);	

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
		$user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_nicename = '" . esc_sql($user_nicename) . "' LIMIT 1");
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
	
	//show bio
	if(!empty($pu->description))
		echo wpautop($pu->description);
	
	//show users be able to edit their own profile?
	$edit_own_profile = false;	//can just send them to the WP profile page to edit if you want
	
	//show the fields
	if(!empty($profile_fields))
	{	
		?>
		<div class="pmpro_directory_profile">
		<form action="" method="post">
			<table class="form-table">
			<?php			
			foreach($profile_fields as $field)
			{			
				$field->displayInProfile($pu->ID, $edit_own_profile);			
			}
			?>
			</table>
			
			<?php if(current_user_can("edit_users", $current_user->ID) && $edit_own_profile) { ?>
			<div class="pmpro_submit">
				<span id="pmpro_submit_span">
					<input type="hidden" name="submit-profile" value="1">		
					<input type="submit" class="pmpro_btn pmpro_btn-submit" value="Save Profile">				
				</span>
			</div>
			<?php } ?>
		</form>
		</div>
		<?php
	}	
			
	$temp_content = ob_get_contents();
	ob_end_clean();
	
	return $temp_content;
}
add_shortcode("pmpro_profile", "pmprorh_profile_shortcode");
