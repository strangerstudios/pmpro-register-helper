<?php
	class PMProRH_Field
	{
		function __construct($name = NULL, $type = NULL, $attr = NULL)
		{
			if(!empty($name))
				return $this->set($name, $type, $attr);
			else
				return true;
		}
		
		/*
			setup field based on passed values
			attr is array of one or more of the following:
			- size = int (size attribute for text fieldS)
			- required = bool (require this field at registration?)
			- options = array of strings (e.g. array("value"=>"option name", "value2" = "option 2 name"))
			- profile = mixed (show field in profile page? true for both, "admins" for admins only)
			- class = string (class to add to html element)
		*/
		function set($name, $type, $attr = array())
		{
			$this->name = $name;
			$this->type = $type;
			$this->attr = $attr;
			
			//add attributes as properties of this class
			if(!empty($attr))
			{
				foreach($attr as $key=>$value)
				{
					$this->$key = $value;
				}
			}
			
			//make sure we have an id
			if(empty($this->id))
				$this->id = $this->name;
			
			//fix class
			if(empty($this->class))
				$this->class = "input";
			else
				$this->class .= " input";			
			
			//default fields						
			if($this->type == "text")
			{
				if(empty($this->size))
					$this->size = 30;
			}
			elseif($this->type == "select" || $type == "multiselect" || $type == "select2" || $type == "radio")
			{
				if(empty($this->options))
					$this->options = array("", "- choose one -");
				
				//is a non associative array is passed, set values to labels
				$repair_non_associative_options = apply_filters("pmprorh_repair_non_associative_options", true);			
				if($repair_non_associative_options && !$this->is_assoc($this->options))
				{
					$newoptions = array();
					foreach($this->options as $option)
						$newoptions[$option] = $option;
					$this->options = $newoptions;
				}				
			}
			elseif($this->type == "textarea")
			{
				if(empty($this->rows))
					$this->rows = 5;
				if(empty($this->cols))
					$this->cols = 80;
			}	
			elseif($this->type == "file")
			{
				//use the file save function
				$this->save_function = array("PMProRH_Field", "saveFile");
			}

			//default label			
			if(isset($this->label) && $this->label === false)
				$this->label = false;	//still false
			elseif(empty($this->label))
				$this->label = ucwords($this->name);
			
			return true;
		}
		
		//save function for files
		function saveFile($user_id, $name, $value)
		{
			//setup some vars
			$file = $_FILES[$name];
			$user = get_userdata($user_id);
			
			//no file?
			if(empty($file['name']))
				return;
			
			//check extension against allowed extensions
			$filetype = wp_check_filetype_and_ext($file['tmp_name'], $file['name']);									
			if((!$filetype['type'] || !$filetype['ext'] ) && !current_user_can( 'unfiltered_upload' ))
			{			
				//we throw an error earlier, but this just bails on the upload just in case
				return false;
			}
			
			/*
				save file in uploads
			*/
			//check for a register helper directory in wp-content
			$upload_dir = wp_upload_dir();
			$pmprorh_dir = $upload_dir['basedir'] . "/pmpro-register-helper/" . $user->user_login . "/";
			
			//create the dir and subdir if needed
			if(!is_dir($pmprorh_dir))
			{
				wp_mkdir_p($pmprorh_dir);
			}
						
			//if we already have a file for this field, delete it
			$old_file = get_user_meta($user->ID, $name, true);			
			if(!empty($old_file) && !empty($old_file['fullpath']) && file_exists($old_file['fullpath']))
			{				
				unlink($old_file['fullpath']);				
			}
			
			//figure out new filename
			$filename = $file['name'];
			$count = 0;
			while(file_exists($pmprorh_dir . $filename))
			{
				if($count)
					$filename = str_lreplace("-" . $count . "." . $filetype['ext'], "-" . strval($count+1) . "." . $filetype['ext'], $filename);
				else
					$filename = str_lreplace("." . $filetype['ext'], "-1." . $filetype['ext'], $filename);
								
				$count++;
				
				//let's not expect more than 50 files with the same name
				if($count > 50)
					die("Error uploading file. Too many files with the same name.");									
			}
			
			//save file
			move_uploaded_file($file['tmp_name'], $pmprorh_dir . $filename);
			
			//save filename in usermeta
			update_user_meta($user_id, $name, array("original_filename"=>$file['name'], "filename"=>$filename, "fullpath"=> $pmprorh_dir . $filename, "fullurl"=>content_url("/uploads/pmpro-register-helper/" . $user->user_login . "/" . $filename), "size"=>$file['size']));
		}
		
		//echo the HTML for the field
		function display($value = NULL)
		{
			echo $this->getHTML($value);
			return;
		}
		
		//get HTML for the field
		function getHTML($value = "")
		{
			if($this->type == "text")
			{
				$r = '<input type="text" id="' . $this->id . '" name="' . $this->name . '" value="' . esc_attr($value) . '" ';
				if(!empty($this->size))
					$r .= 'size="' . $this->size . '" ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->readonly))
					$r .= 'readonly="readonly" ';
				$r .= ' />';				
			}
			elseif($this->type == "select")
			{
				$r = '<select id="' . $this->id . '" name="' . $this->name . '" ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->readonly))
					$r .= 'readonly="readonly" ';
				$r .= ">\n";
				foreach($this->options as $ovalue => $option)
				{
					$r .= '<option value="' . esc_attr($ovalue) . '" ';
					if(!empty($ovalue) && $ovalue == $value)
						$r .= 'selected="selected" ';
					$r .= '>' . $option . "</option>\n";
				}
				$r .= '</select>';
			}
			elseif($this->type == "select2")
			{
				//value must be an array
				if(!is_array($value))
					$value = array($value);
					
				//build multi select
				$r = '<select id="' . $this->id . '" name="' . $this->name . '[]" multiple="multiple" placeholder="Choose one or more." ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->readonly))
					$r .= 'readonly="readonly" ';
				$r .= '>';
				foreach($this->options as $ovalue => $option)
				{
					$r .= '<option value="' . esc_attr($ovalue) . '" ';
					if(!empty($ovalue) && in_array($ovalue, $value))
						$r .= 'selected="selected" ';
					$r .= '>' . $option . '</option>';
				}
				$r .= '</select>';
				
				if(!empty($this->select2options))
					$r .= '<script>jQuery("#' . $this->id . '").select2({' . $this->select2options . '});</script>';
				else
					$r .= '<script>jQuery("#' . $this->id . '").select2();</script>';
			}
			elseif($this->type == "radio")
			{
				$count = 0;
				foreach($this->options as $ovalue => $option)
				{
					$count++;
					$r .= '<input type="radio" id="pmprorh_field_' . $this->name . $count . '" name="' . $this->name . '" value="' . esc_attr($ovalue) . '" ';
					if(!empty($ovalue) && $ovalue == $value)
						$r .= 'checked="checked"';
					if(!empty($this->readonly))
						$r .= 'readonly="readonly" ';
					$r .= ' /> ';
					$r .= '<label class="pmprorh_radio_label" for="pmprorh_field_' . $this->name . $count . '">' . $option . '</label> &nbsp; ';
				}
			}
			elseif($this->type == "textarea")
			{
				$r = '<textarea id="' . $this->id . '" name="' . $this->name . '" rows="' . $this->rows . '" cols="' . $this->cols . '" ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->readonly))
					$r .= 'readonly="readonly" ';
				$r .= '>' . esc_textarea($value) . '</textarea>';				
			}
			elseif($this->type == "hidden")
			{
				$r = '<input type="hidden" id="' . $this->id . '" name="' . $this->name . '" value="' . esc_attr($value) . '" ';
				if(!empty($this->readonly))
					$r .= 'readonly="readonly" ';
				$r .= '/>';						
			}
			elseif($this->type == "html")
			{
				//arbitrary html/etc
				$r = $this->html;
			}
			elseif($this->type == "file")
			{
				$r = '';
				
				//show name of existing file
				if(!empty($value))
				{
					$r .= '<div class="leftmar">Current File: <a target="_blank" href="' . $this->file['fullurl'] . '">' . basename($value) . '</a></div><div class="leftmar">';
				}
			
				//file input
				$r .= '<input type="file" id="' . $this->id . '" name="' . $this->name . '" />';								
				
				//old value
				if(is_user_logged_in())
				{
					global $current_user;
					$old_value = get_user_meta($current_user->ID, $this->name, true);
					if(!empty($old_value))
						$r .= '<input type="hidden" name="' . $this->name . '_old" value="' . esc_attr($old_value['filename']) . '" />';
				}
				
				//closing div
				if(!empty($value))
					$r .= '</div>';
				
				if(!empty($this->readonly))
					$r .= 'readonly="readonly" ';
				
				//include script to change enctype of the form
				$r .= '
				<script>
					jQuery(document).ready(function() {
						jQuery("#' . $this->id . '").closest("form").attr("enctype", "multipart/form-data");
					});
				</script>
				';
			}
			elseif($this->type == "readonly")
			{
				$r .= $this->value;
			}
			else
			{
				$r = "Unknown type <strong>" . $this->type . "</strong> for field <strong>" . $this->name . "</strong>.";
			}
			
			//show required by default
			if(!empty($this->required) && !isset($this->showrequired))
				$this->showrequired = true;
			
			if(!empty($this->required) && !empty($this->showrequired))
			{
				if(is_string($this->showrequired))
					$r .= $this->showrequired;
				else
					$r .= '<span class="pmpro_asterisk"> *</span>';
			}		
			
			return $r;
		}	
		
		function getDependenciesJS()
		{
			//dependencies
			if(!empty($this->depends))
			{					
				//build the checks
				$checks = array();
				foreach($this->depends as $check)
				{
					if(!empty($check['id']))
					{
						$checks[] = "(jQuery('#" . $check['id'] . "_div input').val() == " . json_encode($check['value']) . " || " . 
									"jQuery('#" . $check['id'] . "_div select').val() == " . json_encode($check['value']) . ")";
						$binds[] = "#" . $check['id'];
					}
				}
								
				if(!empty($checks) && !empty($binds))
				{
				?>
				<script>
					//function to check and hide/show
					function pmprorh_<?php echo $this->id;?>_hideshow()
					{						
						if(
							<?php echo implode(" && ", $checks); ?>
						)
						{
							jQuery('#<?php echo $this->id;?>_div').show();
							jQuery('#<?php echo $this->id;?>').removeAttr('disabled');
						}
						else
						{
							jQuery('#<?php echo $this->id;?>_div').hide();
							jQuery('#<?php echo $this->id;?>').attr('disabled', 'disabled');
						}
					}
					
					jQuery(document).ready(function() {											
							//run on page load
							pmprorh_<?php echo $this->id;?>_hideshow();
							
							//and run when certain fields are changed
							jQuery('<?php echo implode(',', $binds);?>').bind('click change keyup', function() {
								pmprorh_<?php echo $this->id;?>_hideshow();
							});
					});
				</script>
				<?php
				}
			}
		}
		
		function displayAtCheckout()
		{
			global $current_user;
			
			//value passed yet?
			if(isset($_REQUEST[$this->name]))
				$value = $_REQUEST[$this->name];
			elseif(isset($_SESSION[$this->name]))
				$value = $_SESSION[$this->name];
			elseif(!empty($current_user->ID) && metadata_exists("user", $current_user->ID, $this->name))
			{				
				$this->file = get_user_meta($current_user->ID, $this->name, true);			
				$value = $this->file['filename'];				
			}
			elseif(!empty($this->value))
				$value = $this->value;
			else
				$value = "";			
			?>
			<div id="<?php echo $this->id;?>_div" <?php if(!empty($this->divclass)) echo 'class="' . $this->divclass . '"';?>>
				<label for="<?php echo esc_attr($this->name);?>"><?php echo $this->label;?></label>
				<?php $this->display($value); ?>
				<?php if(!empty($this->hint)) { ?>
					<div class="leftmar"><small class="lite"><?php echo $this->hint;?></small></div>
				<?php } ?>
			</div>	
			<?php
			
			$this->getDependenciesJS();
		}
		
		function displayInProfile($user_id, $edit = NULL)
		{
			global $current_user;
			if(metadata_exists("user", $user_id, $this->name))
			{
				$meta = get_user_meta($user_id, $this->name, true);				
				if(is_array($meta) && !empty($meta['filename']))
				{
					$this->file = get_user_meta($user_id, $this->name, true);
					$value = $this->file['filename'];
				}
				else
					$value = $meta;
			}
			elseif(!empty($this->value))
				$value = $this->value;
			else
				$value = "";				
			?>
			<tr id="<?php echo $this->id;?>">
				<th><label for="<?php echo esc_attr($this->name);?>"><?php echo $this->label;?></label></th>
				<td>
					<?php 						
						if(current_user_can("edit_user", $current_user->ID) && $edit !== false)
							$this->display($value); 
						else
							echo "<div>" . $value . "</div>";
					?>	
				</td>
			</tr>			
			<?php
			
			$this->getDependenciesJS();
		}		
		
		//from: http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-numeric/4254008#4254008
		function is_assoc($array) {			
			return (bool)count(array_filter(array_keys($array), 'is_string'));
		}
	}
