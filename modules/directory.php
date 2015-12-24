<?php
/*
	This module contains code to show a searchable directory of members
*/
function pmprorh_directory_preheader()
{
	if(!is_admin())
	{
		global $post;
		if(!empty($post->post_content) && strpos($post->post_content, "[pmpro_directory") !== false)
		{
			/*
				Preheader operations here.
			*/
			
			//enqueue the stylesheet for this (check child theme, then parent theme, then plugin folder)	
			if(file_exists(get_stylesheet_directory()."/paid-memberships-pro/register-helper/css/pmprorh_directory.css"))
				wp_enqueue_style("pmprorh_directory_user", get_stylesheet_directory_uri()."/paid-memberships-pro/register-helper/css/pmprorh_directory.css");
			elseif(file_exists(get_template_directory()."/paid-memberships-pro/register-helper/css/pmprorh_directory.css"))
				wp_enqueue_style("pmprorh_directory_user", get_template_directory_uri()."/paid-memberships-pro/register-helper/css/pmprorh_directory.css");
			else
				wp_enqueue_style("pmprorh_directory", PMPRORH_DIR . "/css/pmprorh_directory.css", NULL, PMPRORH_VERSION);	
		}
	}
}
add_action("wp", "pmprorh_directory_preheader", 1);	

function pmprorh_directory_shortcode($atts, $content=null, $code="")
{
	// $atts    ::= array of attributes
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [pmpro_directory level="1"]	directory of level 1 members

	extract(shortcode_atts(array(
		'level' => NULL
	), $atts));
	
	//some vars for the search	
	global $wpdb, $pmprorh_options;
	if(isset($_REQUEST['ps']))
		$s = $_REQUEST['ps'];
	else
		$s = "";
	
	if(isset($_REQUEST['pk']))
		$key = $_REQUEST['pk'];
	else
		$key = "";
		
	if(isset($_REQUEST['pn']))
		$pn = $_REQUEST['pn'];
	else
		$pn = 1;
		
	if(isset($_REQUEST['limit']))
		$limit = $_REQUEST['limit'];
	else
		$limit = 5;
	
	$end = $pn * $limit;
	$start = $end - $limit;				
				
	if($s)
	{
		$sqlQuery = "SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, u.user_nicename, u.display_name, UNIX_TIMESTAMP(u.user_registered) as joindate, mu.membership_id, mu.initial_payment, mu.billing_amount, mu.cycle_period, mu.cycle_number, mu.billing_limit, mu.trial_amount, mu.trial_limit, UNIX_TIMESTAMP(mu.startdate) as startdate, UNIX_TIMESTAMP(mu.enddate) as enddate, m.name as membership FROM $wpdb->users u LEFT JOIN $wpdb->usermeta um ON u.ID = um.user_id LEFT JOIN $wpdb->pmpro_memberships_users mu ON u.ID = mu.user_id LEFT JOIN $wpdb->pmpro_membership_levels m ON mu.membership_id = m.id WHERE mu.status = 'active' AND mu.membership_id > 0 AND ";
		
		if(empty($key))
			$sqlQuery .= "(u.user_login LIKE '%$s%' OR u.user_email LIKE '%$s%' OR u.display_name LIKE '%$s%' OR um.meta_value LIKE '%$s%') ";
		else
			$sqlQuery .= "(um.meta_key = '" . esc_sql($key) . "' AND um.meta_value LIKE '%$s%') ";
	
		if($level)
			$sqlQuery .= " AND mu.membership_id IN(" . $level . ") ";					
			
		$sqlQuery .= "GROUP BY u.ID ORDER BY user_registered DESC LIMIT $start, $limit";
	}
	else
	{
		$sqlQuery = "SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, u.user_nicename, u.display_name, UNIX_TIMESTAMP(u.user_registered) as joindate, mu.membership_id, mu.initial_payment, mu.billing_amount, mu.cycle_period, mu.cycle_number, mu.billing_limit, mu.trial_amount, mu.trial_limit, UNIX_TIMESTAMP(mu.startdate) as startdate, UNIX_TIMESTAMP(mu.enddate) as enddate, m.name as membership FROM $wpdb->users u LEFT JOIN $wpdb->pmpro_memberships_users mu ON u.ID = mu.user_id LEFT JOIN $wpdb->pmpro_membership_levels m ON mu.membership_id = m.id";
		$sqlQuery .= " WHERE mu.membership_id > 0  AND mu.status = 'active' ";
		if($level)
			$sqlQuery .= " AND mu.membership_id IN(" . $level . ") ";
		$sqlQuery .= "ORDER BY user_registered DESC LIMIT $start, $limit";
	}
			
	$theusers = $wpdb->get_results($sqlQuery);
	$totalrows = $wpdb->get_var("SELECT FOUND_ROWS() as found_rows");
	
	ob_start();
	
	?>
	<form class="pmpro_directory_search">		
		<input type="text" name="ps" value="<?php if(!empty($_REQUEST['ps'])) echo esc_attr($_REQUEST['ps']);?>" />
		<input type="submit" value="Search" />
	</form>
	
	<?php if(!empty($s)) { ?>
		<h3 class="pmpro_directory_subheading">
			Profiles within <em><?php echo ucwords(esc_html($s));?></em>
		</h3>
	<?php } ?>
	
	<?php
		if(!empty($theusers))
		{
			?>
			<div class="pmpro_directory_list">
				<?php
					$count = 0;			
					foreach($theusers as $auser)
					{
						$auser = get_userdata($auser->ID);
						$count++;
						?>
						<div id="profile-<?php echo $auser->user_nicename;?>" class="pmpro_profile">														
							<a class="pmpro_thumbnail pmpro_pull-left" href="<?php echo home_url($pmprorh_options["profile_page"]);?>?pu=<?php echo $auser->user_nicename;?>">
								<?php echo get_avatar($auser->ID, 64); ?>
							</a>
							<h4 class="pmpro_profile-heading">
								<a href="<?php echo home_url($pmprorh_options["profile_page"]);?>?pu=<?php echo $auser->user_nicename;?>"><?php echo $auser->display_name;?></a>
							</h4>
							<?php 
								if(strlen($auser->description) > 50)
									echo wpautop(substr($auser->description,0,50));
								else
									echo wpautop($auser->description);
							?>
							<a href="<?php echo home_url($pmprorh_options["profile_page"]);?>?pu=<?php echo $auser->user_nicename;?>"><i class="pmpro_icon-user"></i> View Profile</a>																															
							<div class="pmpro_clear"></div>
						</div>		
						<?php																			
					}
				?>
			</div>
			<?php
		}	
		else
		{	
			?>
			<div class="pmpro_message pmpro_error">No matching profiles found<?php if($s) { ?> within <em><?php echo ucwords(esc_html($s));?></em>. <a href="<?php echo home_url($pmprorh_options["directory_page"]);?>">View All Members</a><?php } else { ?>.<?php } ?></div>
			<?php					
		}
		
		//prev/next
		?>
		<div class="pmpro_pagination">
			<?php
			//prev
			if($pn > 1)
			{			
			?>
				<span class="pmpro_prev"><a href="<?php echo add_query_arg(array("ps"=>$s, "pn"=>$pn-1), home_url($pmprorh_options['directory_page']));?>">&laquo; Previous</a></span>
			<?php
			}				
			//next
			if($totalrows > $end)
			{				
			?>
				<span class="pmpro_next"><a href="<?php echo add_query_arg(array("ps"=>$s, "pn"=>$pn+1), home_url($pmprorh_options['directory_page']));?>">Next &raquo;</a></span>
			<?php
			}
			?>
		</div>
		<?php
	?>		

	<?php
	
	$temp_content = ob_get_contents();
	ob_end_clean();
		
	return $temp_content;
}
add_shortcode("pmpro_directory", "pmprorh_directory_shortcode");
