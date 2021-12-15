<?php
	class PMProRH_Field
	{
		function __construct($name = NULL, $type = NULL, $attr = NULL)
		{
			$this->defaults();

			if(!empty($name))
				return $this->set($name, $type, $attr);
			else
				return true;
		}

		function defaults() {

			// create default setting variables
			$this->addmember = false;
			$this->id = null;
			$this->label = null;
			$this->levels = null;
			$this->memberslistcsv = false;
			$this->readonly = false;
			$this->depends = array();
			$this->showrequired = true;
			$this->showmainlabel = true;
			$this->divclass = null;
			$this->hint = null;
			$this->size = null;
			$this->rows = 5;
			$this->cols = 80;
			$this->required = false;
			$this->options = array();
			$this->multiple = false;
			$this->text = null;
			$this->file = null;
			$this->html = null;
			$this->profile = null;
			$this->just_profile = false;
			$this->class = null;
			$this->sanitize = true;
		}

		/*
			setup field based on passed values
			attr is array of one or more of the following:
			- size = int (size attribute for text fieldS)
			- required = bool (require this field at registration?)
			- options = array of strings (e.g. array("value"=>"option name", "value2" = "option 2 name"))
			- profile = mixed (show field in profile page? true for both, "admins" for admins only)
			- just_profile = bool (not required. true means only show field in profile)
			- class = string (class to add to html element)
		*/
		function set($name, $type, $attr = array())
		{
			$this->name = $name;
			$this->type = $type;
			$this->attr = $attr;
			
			//set meta key
			$this->meta_key = $this->name;

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
			
			//default label			
			if(isset($this->label) && $this->label === false)
				$this->label = false;	//still false
			elseif(empty($this->label))
				$this->label = ucwords($this->name);
			
			if(!isset($this->showmainlabel))
				$this->showmainlabel = true;
			
			//add prefix to the name if equal to a query var
			$public_query_vars = array('m', 'p', 'posts', 'w', 'cat', 'withcomments', 'withoutcomments', 's', 'search', 'exact', 'sentence', 'calendar', 'page', 'paged', 'more', 'tb', 'pb', 'author', 'order', 'orderby', 'year', 'monthnum', 'day', 'hour', 'minute', 'second', 'name', 'category_name', 'tag', 'feed', 'author_name', 'static', 'pagename', 'page_id', 'error', 'attachment', 'attachment_id', 'subpost', 'subpost_id', 'preview', 'robots', 'taxonomy', 'term', 'cpage', 'post_type', 'title', 'embed' );
			if(in_array($this->name, $public_query_vars))
				$this->name = "pmprorhprefix_" . $this->name;

			// Save wp_users table fields to the WP_User, not usermeta.
			$user_table_fields = apply_filters( 'pmprorh_user_table_fields', array( 'user_url' ) );
			if ( in_array( $this->name, $user_table_fields ) ) {
				//use the save date function
				$this->save_function = array( $this, 'saveUsersTable' );
			}

			//default fields						
			if($this->type == "text")
			{
				if(empty($this->size))
					$this->size = 30;
			}			
			elseif($this->type == "number")
			{
				if(empty($this->size))
					$this->size = 5;
			}			
			elseif($this->type == "select" || $type == "multiselect" || $type == "select2" || $type == "radio")
			{
				//default option
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
				// Default to file preview if image type.
				if ( ! isset( $this->preview ) ) {
					$this->preview = true;
				}
				// Default to allow file delete if full source known.
				if ( ! isset( $this->allow_delete ) ) {
					$this->allow_delete = 'only_admin';
				}
				//use the file save function
				$this->save_function = array($this, "saveFile");
			}
			elseif($this->type == "checkbox")
			{
				if(!isset($this->text))
				{
					$this->text = $this->label;
					$this->showmainlabel = false;
				}
			}
            elseif($this->type == "date")
            {
                //use the save date function
                $this->save_function = array($this, "saveDate");
            }
			
			return true;
		}

		// Save function for users table field.
		function saveUsersTable( $user_id, $name, $value ) {
			// Special sanitization needed for certain user fields.
			if ( $name === 'user_url' ) {
				$value = esc_url_raw( $value );
			}
			if ( $name === 'user_nicename' ) {
				$value = sanitize_title( $value );
			}

			// Save updated profile fields.
			wp_update_user( array( 'ID' => $user_id, $name => $value ) );
		}

		//save function for files
		function saveFile($user_id, $name, $value)
		{			
			//setup some vars
			$file = $_FILES[$name];
			$user = get_userdata($user_id);
			$meta_key = str_replace("pmprorhprefix_", "", $name);

			// deleting?
			if( isset( $_REQUEST['pmprorh_delete_file_' . $name . '_field'] ) ) {
				$delete_old_file_name = $_REQUEST['pmprorh_delete_file_' . $name . '_field'];
				if ( ! empty( $delete_old_file_name ) ) {
					$old_file_meta = get_user_meta( $user->ID, $meta_key, true );					
					if ( 
						! empty( $old_file_meta ) && 
						! empty( $old_file_meta['fullpath'] ) && 
						file_exists( $old_file_meta['fullpath'] ) &&
						$old_file_meta['filename'] ==  $delete_old_file_name
					) {				
						unlink( $old_file_meta['fullpath'] );
						if ( ! empty( $old_file_meta['previewpath'] ) ) {
							unlink( $old_file_meta['previewpath'] );
						}
						delete_user_meta( $user->ID, $meta_key );
					}
				}
			}

			//no file?
			if(empty($file['name'])) {
				return;
			}

			//check extension against allowed extensions
			$filetype = wp_check_filetype_and_ext($file['tmp_name'], $file['name']);

			if((!$filetype['type'] || !$filetype['ext'] ) && !current_user_can( 'unfiltered_upload' ))
			{			
				//we throw an error earlier, but this just bails on the upload just in case
				return false;
			}
			else
			{
				//need to check this in case we are in class context or not
				if(!empty($this))
				{
					if(!empty($this->ext))
						$ext = $this->ext;
					else
						$ext = false;
				}
				else
				{
					global $pmprorh_registration_fields;					
					foreach($pmprorh_registration_fields as $checkout_box)
					{
						foreach($checkout_box as $field)
						{
							if($field->name == $name)
							{
								if(!empty($field->ext))
									$ext = $field->ext;
								else
									$ext = false;
									
								break 2;
							}
						}
					}
				}
				
				//check for specific extensions anyway
				if(!empty($ext) && !in_array($filetype['ext'], $ext))
				{
					pmpro_setMessage(sprintf(__("Sorry, the file type for %s is not permitted for security reasons.", "pmpro"), $file['name']), "pmpro_error");
					return false;
				}
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
			$old_file = get_user_meta($user->ID, $meta_key, true);
			if(!empty($old_file) && !empty($old_file['fullpath']) && file_exists($old_file['fullpath']))
			{				
				unlink($old_file['fullpath']);
			}
			
			//figure out new filename
			$filename = sanitize_file_name($file['name']);
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
			if(strpos($file['tmp_name'], $upload_dir['basedir']) !== false)
			{
				//was uploaded and saved to $_SESSION
				rename($file['tmp_name'], $pmprorh_dir . $filename);			
			}
			else
			{
				//it was just uploaded
				move_uploaded_file($file['tmp_name'], $pmprorh_dir . $filename);				
			}
			
			// If file is an image, save a preview thumbnail.
			if ( $filetype && 0 === strpos( $filetype['type'], 'image/' ) ) {
				$preview_file = wp_get_image_editor( $pmprorh_dir . $filename );
				if ( ! is_wp_error( $preview_file ) ) {
					$preview_file->resize( 200, NULL, false );
					$preview_file->generate_filename( 'pmprorh_preview' );
					$preview_file = $preview_file->save();
				}
			}

			// Our folder in the uploads directory that we want to save files to.
			$upload_dir = '/pmpro-register-helper/' . $user->user_login . '/';

			// If multisite, prefix the directory with the current blog ID specific folder.
			if ( is_multisite() ) {
				$upload_dir = '/sites/' . get_current_blog_id() . $upload_dir;
			}

			// Get the full uploads directory URL we want to save files to.
			$upload_path = content_url( '/uploads' . $upload_dir );

			$file_meta_value_array = array(
				'original_filename'	=> $file['name'],
				'filename'			=> $filename,
				'fullpath'			=> $pmprorh_dir . $filename,
				'fullurl'			=> $upload_path . $filename,
				'size'				=> $file['size'],
			);

			if ( ! empty( $preview_file ) && ! is_wp_error( $preview_file ) ) {
				$file_meta_value_array['previewpath'] = $preview_file['path'];
				$file_meta_value_array['previewurl'] = $upload_path . $preview_file['file'];
			}

			//save filename in usermeta
			update_user_meta($user_id, $meta_key, $file_meta_value_array );			
		}

        //fix date then update user meta
        function saveDate($user_id, $name, $value)
        {
	        if ( isset( $this->sanitize ) && true === $this->sanitize ) {

		        $value = pmprorh_sanitize( $value, $this );
	        }

        	$meta_key = str_replace("pmprorhprefix_", "", $name);
            $date = date('Y-m-d', strtotime(date($value['y'] . '-' . $value['m'] . '-' . $value['d'])));
        	update_user_meta($user_id, $meta_key, $date);
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
			//vars to store HTML to be added to the beginning or end
			$r_beginning = "";
			$r_end = "";

			if($this->type == "text")
			{
				$r = '<input type="text" id="' . $this->id . '" name="' . $this->name . '" value="' . esc_attr(wp_unslash($value)) . '" ';
				if(!empty($this->size))
					$r .= 'size="' . $this->size . '" ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->readonly))
					$r .= 'readonly="readonly" ';
				if(!empty($this->html_attributes))
					$r .= $this->getHTMLAttributes();
				$r .= ' />';				
			}
			elseif($this->type == "number")
			{
				$r = '<input type="number" pattern="\d+" id="' . $this->id . '" name="' . $this->name . '" value="' . esc_attr($value) . '" ';
				if(!empty($this->size))
					$r .= 'size="' . $this->size . '" ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->readonly))
					$r .= 'readonly="readonly" ';
				if ( ! empty( $this->html_attributes ) ) {
					// If custom values not available set the defaults
					if ( ! array_key_exists( 'min', $this->html_attributes ) ) {
						$this->html_attributes['min'] = '0';
					}
					if ( ! array_key_exists( 'step', $this->html_attributes ) ) {
						$this->html_attributes['step'] = '1';
					}
					$r .= $this->getHTMLAttributes();
				} else {
					// Set the defaults
					$this->html_attributes['min'] = '0';
					$this->html_attributes['step'] = '1';
					$r .= $this->getHTMLAttributes();
				}
				$r .= ' />';
			}
			elseif($this->type == "password")
			{
				$r = '<input type="password" id="' . $this->id . '" name="' . $this->name . '" value="' . esc_attr(wp_unslash($value)) . '" ';
				if(!empty($this->size))
					$r .= 'size="' . $this->size . '" ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->readonly))
					$r .= 'readonly="readonly" ';
				if(!empty($this->html_attributes))
					$r .= $this->getHTMLAttributes();
				$r .= ' />';				
			}
			elseif($this->type == "select")
			{
				//if multiple is set, value must be an array
				if(!empty($this->multiple) && !is_array($value))				
					$value = array($value);
								
				if(!empty($this->multiple))
					$r = '<select id="' . $this->id . '" name="' . $this->name . '[]" ';	//multiselect
				else
					$r = '<select id="' . $this->id . '" name="' . $this->name . '" ';		//regular
					
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->readonly))
					$r .= 'disabled="disabled" ';
				if(!empty($this->multiple))
					$r .= 'multiple="multiple" ';
				if(!empty($this->html_attributes))
					$r .= $this->getHTMLAttributes();
				$r .= ">\n";
				foreach($this->options as $ovalue => $option)
				{
					$r .= '<option value="' . 
						
						
						($ovalue) . '" ';
					if(!empty($this->multiple) && in_array($ovalue, $value))
						$r .= 'selected="selected" ';
					elseif($ovalue == $value)
						$r .= 'selected="selected" ';
					$r .= '>' . $option . "</option>\n";
				}
				$r .= '</select>';
			}
			elseif($this->type == "multiselect")
			{
				//value must be an array
				if(!is_array($value))
					$value = array($value);
				
				$r = '<select id="' . $this->id . '" name="' . $this->name . '[]" multiple="multiple" ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->readonly))
					$r .= 'disabled="disabled" ';
				if(!empty($this->html_attributes))
					$r .= $this->getHTMLAttributes();
				$r .= ">\n";
				foreach($this->options as $ovalue => $option)
				{
					$r .= '<option value="' . esc_attr($ovalue) . '" ';
					if(in_array($ovalue, $value))
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
				$r = '<select id="' . $this->id . '" name="' . $this->name . '[]" multiple="multiple" ';
				if(isset($this->placeholder)) {
					$r .= 'placeholder="' . esc_attr($this->placeholder) . '" ';
					if(empty($this->select2options)) {
						$this->select2options = 'placeholder: "' . esc_attr($this->placeholder) . '"';
					}
				} else {
					$r .= 'placeholder="' . __('Choose one or more.', 'pmpro-register-helper') . '" ';
				}				
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->readonly))
					$r .= 'disabled="disabled" ';
				if(!empty($this->html_attributes))
					$r .= $this->getHTMLAttributes();
				$r .= '>';
				foreach($this->options as $ovalue => $option)
				{
					$r .= '<option value="' . esc_attr($ovalue) . '" ';
					if(in_array($ovalue, $value))
						$r .= 'selected="selected" ';
					$r .= '>' . $option . '</option>';
				}
				$r .= '</select>';
				$r .= '<input type="hidden" name="'.$this->name.'_checkbox" value="1" />';	// Extra field so we can track unchecked boxes. Naming just for consistency.
				
				if(!empty($this->select2options))
					$r .= '<script>jQuery(document).ready(function($){ $("#' . $this->id . '").select2({' . $this->select2options . '}); });</script>';
				else
					$r .= '<script>jQuery(document).ready(function($){ $("#' . $this->id . '").select2(); });</script>';
			}
			elseif($this->type == "radio")
			{
				$count = 0;
				$r = '';
				foreach($this->options as $ovalue => $option)
				{
					$count++;
					$r .= '<div class="pmpro_checkout-field-radio-item">';
					$r .= '<input type="radio" id="pmprorh_field_' . $this->name . $count . '" name="' . $this->name . '" value="' . esc_attr($ovalue) . '" ';
					if(!empty($ovalue) && $ovalue == $value)
						$r .= 'checked="checked"';
					if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
					if(!empty($this->readonly))
						$r .= 'disabled="disabled" ';
					if(!empty($this->html_attributes))
						$r .= $this->getHTMLAttributes();
					$r .= ' /> ';
					$r .= '<label class="pmprorh_radio_label" for="pmprorh_field_' . $this->name . $count . '">' . $option . '</label> &nbsp; ';
					$r .= '</div> <!-- end pmpro_checkout-field-radio-item -->';
				}
			}
			elseif($this->type == "checkbox")
			{
				$r = '<input name="'.$this->name.'"' .' type="checkbox" value="1"'.' id="'.$this->id.'"';
				$r.=checked( $value, 1,false);
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';		
				if(!empty($this->readonly))
					$r .= 'disabled="disabled" ';
				if(!empty($this->html_attributes))
					$r .= $this->getHTMLAttributes();		
				$r .= ' /> ';
				$r .= '<label class="pmprorh_checkbox_label" for="' . $this->name . '">' . $this->text . '</label> &nbsp; ';
				$r .= '<input type="hidden" name="'.$this->name.'_checkbox" value="1" />';	//extra field so we can track unchecked boxes
			}
			
			elseif($this->type == "checkbox_grouped")
			{
				//value must be an array
				if(!is_array($value))
					$value = array($value);

				$r = sprintf( '<div class="pmprorh_grouped_checkboxes"><ul>' );
				$counter = 1;
				foreach($this->options as $ovalue => $option)
				{
					if ( ! empty( $this->class ) ) {
						$class = $this->class;
					}

				    $r .= sprintf( '<li style="list-style: none;"><span class="pmprorh_checkbox_span">' );
					$r .= sprintf(
                        '<input name="%1$s[]" type="checkbox" value="%2$s" id="%3$s" class="%4$s" %5$s %6$s %7$s />',
                         $this->name,
                        $ovalue,
						"{$this->id}_{$counter}",
                        $this->id . ' ' . str_replace( 'pmpro_required pmpro-required', '', $class ), // Don't show every option as required.
                        ( in_array($ovalue, $value) ? 'checked="checked"' : null ),
                        ( !empty( $this->readonly ) ? 'readonly="readonly"' : null ),
                        $this->getHTMLAttributes()
					);     
					
					$r .= sprintf( ' <label class="pmprorh_checkbox_label pmpro_label-inline pmpro_clickable" for="%1$s">%2$s</label>', "{$this->id}_{$counter}",$option );
					$r .= sprintf( '<input type="hidden" name="%1$s_checkbox[]" value="%2$s" />', $this->name, $ovalue );	//extra field so we can track unchecked boxes
                    $counter++;
					$r .= sprintf( '</span></li>' );
				}
				
				$r .= sprintf( '</ul></div>' );
				
			}
			
			elseif($this->type == "textarea")
			{
				$r = '<textarea id="' . $this->id . '" name="' . $this->name . '" rows="' . $this->rows . '" cols="' . $this->cols . '" ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->readonly))
					$r .= 'readonly="readonly" ';
				if(!empty($this->html_attributes))
					$r .= $this->getHTMLAttributes();
				$r .= '>' . esc_textarea(wp_unslash($value)) . '</textarea>';				
			}
			elseif($this->type == "hidden")
			{
				$r = '<input type="hidden" id="' . $this->id . '" name="' . $this->name . '" value="' . esc_attr(wp_unslash($value)) . '" ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->readonly))
					$r .= 'readonly="readonly" ';
				if(!empty($this->html_attributes))
					$r .= $this->getHTMLAttributes();
				$r .= '/>';						
			}
			elseif($this->type == "html")
			{
				//arbitrary html/etc
				if(!empty($this->html))
					$r = $this->html;
				else
					$r = "";
			}
			elseif($this->type == "file")
			{
				$r = '';
								
				//file input
				$r .= '<input type="file" id="' . $this->id . '" ';
				if(!empty($this->accept))
					$r .= 'accept="' . esc_attr($this->accept) . '" ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				if(!empty($this->html_attributes))
					$r .= $this->getHTMLAttributes();
				if(!empty($this->readonly))
					$r .= 'disabled="disabled" ';
				$r .= 'name="' . $this->name . '" />';


				//old value
				if(is_user_logged_in())
				{
					global $current_user;
					$old_value = get_user_meta($current_user->ID, $this->meta_key, true);
					if(!empty($old_value))
						$r .= '<input type="hidden" name="' . $this->name . '_old" value="' . esc_attr($old_value['filename']) . '" />';
				}

				// Show a preview of existing file if image type.
				if ( ( ! empty( $this->preview ) ) && ! empty( $value ) && ! empty( $this->file['previewurl'] ) ) {
					$filetype = wp_check_filetype( basename( $this->file['previewurl'] ), null );
					if ( $filetype && 0 === strpos( $filetype['type'], 'image/' ) ) {
						$r_end .= '<div class="pmprorh_file_preview"><img src="' . $this->file['previewurl'] . '" alt="' . basename($value) . '" /></div>';
					}
				}

				//show name of existing file
				if(!empty($value))
				{
					if ( ! empty( $this->file['fullurl'] ) ) {
						$r_end .= '<span class="pmprorh_file_' . $this->name . '_name">' . sprintf(__('Current File: %s', 'pmpro-register-helper' ), '<a target="_blank" href="' . $this->file['fullurl'] . '">' . basename($value) . '</a>' ) . '</span>';
					} else {
						$r_end .= sprintf(__('Current File: %s', 'pmpro-register-helper' ), basename($value) );
					}

					// Allow user to delete the uploaded file if we know the full location. 
					if ( ( ! empty( $this->allow_delete ) ) && ! empty( $this->file['fullurl'] ) ) {
						// Check whether the current user can delete the uploaded file based on the field attribute 'allow_delete'.
						if ( $this->allow_delete === true || 
							( $this->allow_delete === 'admins' || $this->allow_delete === 'only_admin' && current_user_can( 'manage_options', $current_user->ID ) )
						) {
 						$r_end .= '&nbsp;&nbsp;<button class="pmprorh_delete_restore_file" id="pmprorh_delete_file_' . $this->name . '_button" onclick="return false;">' . __( '[delete]', 'pmpro-register-helper' ) . '</button>';
						$r_end .= '<button class="pmprorh_delete_restore_file" id="pmprorh_cancel_delete_file_' . $this->name . '_button" style="display: none;" onclick="return false;">' . __( '[restore]', 'pmpro-register-helper' ) . '</button>';
						$r_end .= '<input id="pmprorh_delete_file_' . $this->name . '_field" name="pmprorh_delete_file_' . $this->name . '_field" type="hidden" value="0" />';
						}
					}
				}
				
				//include script to change enctype of the form and allow deletion
				$r .= '
				<script>
					jQuery(document).ready(function() {
						jQuery("#' . $this->id . '").closest("form").attr("enctype", "multipart/form-data");

						jQuery("#pmprorh_delete_file_' . $this->name . '_button").click(function(){
							jQuery("#pmprorh_delete_file_' . $this->name . '_field").val("' . basename($value) . '");
							jQuery(".pmprorh_file_' . $this->name . '_name").css("text-decoration", "line-through");
							jQuery("#pmprorh_cancel_delete_file_' . $this->name . '_button").show();
							jQuery("#pmprorh_delete_file_' . $this->name . '_button").hide();
						});

						jQuery("#pmprorh_cancel_delete_file_' . $this->name . '_button").click(function(){
							jQuery("#pmprorh_delete_file_' . $this->name . '_field").val(0);
							jQuery(".pmprorh_file_' . $this->name . '_name").css("text-decoration", "none");
							jQuery("#pmprorh_delete_file_' . $this->name . '_button").show();
							jQuery("#pmprorh_cancel_delete_file_' . $this->name . '_button").hide();
						});
					});
				</script>
				';
			}
            elseif($this->type == "date")
            {
                $r = '<select id="' . $this->id . '_m" name="' . $this->name . '[m]"';

                if(!empty($this->class))
                    $r .= ' class="' . $this->class . '"';

                if(!empty($this->readonly))
                    $r .= 'disabled="disabled"';

                if(!empty($this->html_attributes))
					$r .= $this->getHTMLAttributes();

                $r .= ' >';

                //setup date vars
                if(is_array($value) && !empty($value)){
		    $value = strtotime(implode("/", $value), current_time('timestamp'));
		}elseif(!is_array($value) && !empty($value)){
		    $value = strtotime($value, current_time('timestamp'));
		}else{
		    $value = strtotime(date('Y-m-d'), current_time('timestamp'));
		}

                $year = date("Y", $value);
                $month = date("n", $value);
                $day = date("j", $value);

                for($i = 1; $i < 13; $i++)
                {
                    $r .= '<option value="' . $i . '" ';
                    if($i == $month)
                        $r .= 'selected="selected"';

                    $r .= '>' . date("M", strtotime($i . "/15/" . $year, current_time("timestamp"))) . '</option>';
                }

                $r .= '</select><input id="' . $this->id . '_d" name="' . $this->name . '[d]" type="text" size="2" value="' . $day . '" ';

                if(!empty($this->readonly))
                    $r .= 'readonly="readonly" ';

                $r .= '/><input id="' . $this->id . '_y" name="' . $this->name . '[y]" type="text" size="4" value="' . $year . '" ';

                if(!empty($this->readonly))
                    $r .= 'readonly="readonly" ';

                $r .= '/>';
            }
            elseif($this->type == "readonly")
			{				
				$r = $value;
			}
			else
			{
				$r = "Unknown type <strong>" . $this->type . "</strong> for field <strong>" . $this->name . "</strong>.";
			}
			
			//show required by default
			if(!empty($this->required) && !isset($this->showrequired))
				$this->showrequired = true;
			
			//but don't show required on the profile page unless overridden.
			if(defined('IS_PROFILE_PAGE') && IS_PROFILE_PAGE && !apply_filters('pmprorh_show_required_on_profile', false, $this))
				$this->showrequired = false;

			if ( ! empty( $this->required ) && ! empty( $this->showrequired ) && is_string( $this->showrequired ) && $this->showrequired !== 'label' ) {
					$r .= $this->showrequired;
			}

			//anything meant to be added to the beginning or end?
			$r = $r_beginning . $r . $r_end;

			//filter
			$r = apply_filters('pmprorh_get_html', $r, $this);

			return $r;
		}

		function getHTMLAttributes() {
			$astring = "";
			if(!empty($this->html_attributes)) {
				foreach($this->html_attributes as $name => $value)
					$astring .= "{$name}=\"{$value}\"";
			}

			return $astring;
		}	
		
		function getDependenciesJS()
		{
			global $pmprorh_registration_fields;
			//dependencies
			if(!empty($this->depends))
			{					
				//build the checks
				$checks = array();
				foreach($this->depends as $check)
				{
					if(!empty($check['id']))
					{
						// If checking grouped_checkbox, need to update the $check['id'] with index of option.
						$field_id = $check['id'];
						$depends_checkout_box = PMProRH_Field::get_checkout_box_name_for_field( $field_id );
						if ( empty( $depends_checkout_box ) ) {
							continue;
						}
						foreach ( $pmprorh_registration_fields[ $depends_checkout_box ] as $field ) {
							if ( $field->type === 'checkbox_grouped' && $field->name === $field_id && ! empty( $field->options ) ) {
								$field_id = $field_id . '_' . intval( array_search( $check['value'], array_keys( $field->options ) )+1 );
							}
						}

						$checks[] = "((jQuery('#" . $field_id ."')".".is(':checkbox')) "
						 ."? jQuery('#" . $field_id . ":checked').length > 0"
						 .":(jQuery('#" . $field_id . "').val() == " . json_encode($check['value']) . " || jQuery.inArray( jQuery('#" . $field_id . "').val(), " . json_encode($check['value']) . ") > -1)) ||"."(jQuery(\"input:radio[name='".$check['id']."']:checked\").val() == ".json_encode($check['value'])." || jQuery.inArray(".json_encode($check['value']).", jQuery(\"input:radio[name='".$field_id."']:checked\").val()) > -1)";
					
						$binds[] = "#" . $field_id .",input:radio[name=". $field_id ."]";
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
							jQuery('#<?php echo $this->id;?>_tr').show();
							jQuery('#<?php echo $this->id;?>_div').show();
							jQuery('#<?php echo $this->id;?>').removeAttr('disabled');
						}
						else
						{
							jQuery('#<?php echo $this->id;?>_tr').hide();
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
			if($this->type == "date") {
				if(isset($_REQUEST[$this->name])) {
					$tempstr = intval($_REQUEST[$this->name]["m"])."/";
					$tempstr .= intval($_REQUEST[$this->name]["d"])."/";
					$tempstr .= intval($_REQUEST[$this->name]["y"]);
					$value = $tempstr; // will be modified by strtotime later.
				} elseif(isset($_SESSION[$this->name."[m]"])) {
					$tempstr = intval($_SESSION[$this->name]["m"])."/";
					$tempstr .= intval($_SESSION[$this->name]["d"])."/";
					$tempstr .= intval($_SESSION[$this->name]["y"]);
					$value = $tempstr; // will be modified by strtotime later.
				} elseif(!empty($current_user->ID) && metadata_exists("user", $current_user->ID, $this->meta_key)) {
					$meta = get_user_meta($current_user->ID, $this->meta_key, true);
					$value = $meta;
				} elseif(!empty($this->value))
					$value = $this->value;
				else
					$value = "";
			} elseif(isset($_REQUEST[$this->name]))
				$value = $_REQUEST[$this->name];
			elseif(isset($_SESSION[$this->name]))
			{
				//file or value?
				if(is_array($_SESSION[$this->name]) && !empty($_SESSION[$this->name]['name']))
				{
					$_FILES[$this->name] = $_SESSION[$this->name];
					$this->file = $_SESSION[$this->name]['name'];
					$value = $_SESSION[$this->name]['name'];
				}
				else
					$value = $_SESSION[$this->name];
			}
			elseif(!empty($current_user->ID) && metadata_exists("user", $current_user->ID, $this->meta_key))
			{				
				$meta = get_user_meta($current_user->ID, $this->meta_key, true);				
				if(is_array($meta) && !empty($meta['filename']))
				{
					$this->file = get_user_meta($current_user->ID, $this->meta_key, true);
					$value = $this->file['filename'];
				}
				else
					$value = $meta;									
			} elseif ( ! empty( $current_user->ID ) ) {
				$userdata = get_userdata( $current_user->ID );
				if ( ! empty( $userdata->{$this->name} ) ) {
					$value = $userdata->{$this->name};
				} else {
					$value = '';
				}
			}
			elseif(!empty($this->value))
				$value = $this->value;
			else
				$value = "";

			//update class value for div and field element
			$this->class .= " " . pmpro_getClassForField($this->name);
			
			// add default pmpro_required class to field.
			if ( ! empty( $this->required ) ) {
				$this->class .= " pmpro_required pmpro-required"; // pmpro-required was in a previous version. Keeping it in case someone is using it.
				$this->divclass .= " pmpro_checkout-field-required";
			}

			if ( empty( $this->showrequired ) || is_string( $this->showrequired ) ) {
				$this->divclass .= " pmpro_checkout-field-hide-required";
			}
			
			$this->divclass .= " pmpro_checkout-field-" . $this->type;
			?>
			<div id="<?php echo $this->id;?>_div" class="pmpro_checkout-field<?php if(!empty($this->divclass)) echo ' ' . $this->divclass; ?>">
				<?php if(!empty($this->showmainlabel)) { ?>
					<label for="<?php echo esc_attr($this->name);?>"><?php echo $this->label;?></label>
					<?php 
						if(!empty($this->required) && !empty($this->showrequired) && $this->showrequired === 'label')
						{
						?><span class="pmprorh_asterisk"> <abbr title="Required Field">*</abbr></span><?php
						}
					?>
					<?php $this->display($value); ?>
				<?php } else { ?>
					<?php $this->display($value); ?>
				<?php } ?>
				
				<?php if(!empty($this->hint)) { ?>
					<p><small class="lite"><?php echo $this->hint;?></small></p>
				<?php } ?>
			</div>	
			<?php
			
			$this->getDependenciesJS();
		}
		
		function displayInProfile($user_id, $edit = NULL)
		{
			global $current_user;
			if(metadata_exists("user", $user_id, $this->meta_key))
			{
				$meta = get_user_meta($user_id, $this->name, true);				
				if(is_array($meta) && !empty($meta['filename']))
				{
					$this->file = get_user_meta($user_id, $this->meta_key, true);
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
			<tr id="<?php echo $this->id;?>_tr">
				<th>
					<?php if(!empty($this->showmainlabel)) { ?>
						<label for="<?php echo esc_attr($this->name);?>"><?php echo $this->label;?></label>
					<?php } ?>
				</th>
				<td>
					<?php 						
						if(current_user_can("edit_user", $current_user->ID) && $edit !== false)
							$this->display($value); 
						else
							echo "<div>" . $this->displayValue($value) . "</div>";						
					?>
					<?php if(!empty($this->hint)) { ?>
						<small class="lite"><?php echo $this->hint;?></small>
					<?php } ?>
				</td>
			</tr>			
			<?php
			
			$this->getDependenciesJS();
		}		
		
		//checks for array values and values from fields with options
		function displayValue($value)
		{
			if(is_array($value) && !empty($this->options))
			{							
				$labels = array();
				foreach($value as $item)
				{
					$labels[] = $this->options[$item];
				}
				
				echo implode(", ", $labels);
			}
			elseif(is_array($value))
				echo implode(", ", $value);
			elseif(!empty($this->options))
				echo $this->options[$value];
			else
				echo $value;
		}
		
		//from: http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-numeric/4254008#4254008
		function is_assoc($array) {			
			return (bool)count(array_filter(array_keys($array), 'is_string'));
		}

		static function get_checkout_box_name_for_field( $field_name ) {
			global $pmprorh_registration_fields;
			foreach( $pmprorh_registration_fields as $checkout_box_name => $fields ) {
				foreach($fields as $field) {
					if( $field->name == $field_name ) {
						return $checkout_box_name;
					}
				}
			}
			return '';
		}

		function was_present_on_checkout_page() {
			// Check if checkout box that field is in is on page.
			$checkout_box = PMProRH_Field::get_checkout_box_name_for_field( $this->name );
			if ( empty( $checkout_box ) ) {
				// Checkout box does not exist.
				return false;
			}

			$user_fields_locations = array(
				'after_username',
				'after_password',
				'after_email',
				'after_captcha',
			);
			if ( is_user_logged_in() && in_array( $checkout_box, $user_fields_locations ) ) {
				// User is logged in and field is only for new users.
				return false;
			}
			
			// Check if field is hidden because of "depends" option.
			if ( ! empty( $this->depends ) ) {					
				//build the checks
				$checks = array();
				foreach($this->depends as $check) {
					if( ! empty( $check['id'] ) && isset( $check['value'] ) ) {
						// We have a valid depends statement.
						if ( isset( $_REQUEST[ $check['id'] . '_checkbox' ] ) ) {
							// This fields depends on a checkbox or checkbox_grouped input.
							if ( isset( $_REQUEST[ $check['id'] ] ) && is_array( $_REQUEST[ $check['id'] ] ) ) {
								// checkbox_grouped input.
								if ( ! in_array( $check['value'], $_REQUEST[ $check['id'] ] ) ) {
									return false;
								}
							} elseif ( isset( $_REQUEST[ $check['id'] ] ) && '1' === $_REQUEST[ $check['id'] ] ) {
								// Single checkbox that is checked.
								if ( empty( $check['value'] ) ) {
									// Set to show field only if checkbox is unchecked.
									return false;
								}
							} else {
								// Single checkbox that is unchecked. Set to show field only if checkbox is checked.
								if ( ! empty( $check['value'] ) ) {
									return false;
								}
							}
						} elseif ( isset( $_REQUEST[ $check['id'] ] ) && $check['value'] != $_REQUEST[ $check['id'] ] ) {
							// This fields depends on another field type.
							return false;
						}
					}				
				}
			}

			// Field should have been showed at checkout.
			return true;
		}

		function was_filled_if_needed() {
			// If field is never required or is not present on checkout page, return true.
			if ( ! $this->required || ! $this->was_present_on_checkout_page() ) {
				return true;
			}

			// Return whether the field is filled.
			switch ( $this->type ) {
				case 'text':
				case 'textarea':
				case 'number':
					$filled = ( isset( $_REQUEST[$this->name] ) && '' !== trim( $_REQUEST[$this->name] ) );
					break;
				default:
					$filled = ! ( empty( $_REQUEST[$this->name] ) && empty( $_FILES[$this->name]['name'] ) && empty( $_REQUEST[$this->name.'_old'] ) );
			}
			return $filled;
		}
	}
