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
			elseif($this->type == "select" || $type == "multiselect" || $type == "select2")
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

			//default label			
			if(isset($this->label) && $this->label === false)
				$this->label = false;	//still false
			elseif(empty($this->label))
				$this->label = ucwords($this->name);
			
			return true;
		}
		
		//echo the HTML for the field
		function display($value = "")
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
				$r .= ' />';				
			}
			elseif($this->type == "select")
			{
				$r = '<select id="' . $this->id . '" name="' . $this->name . '" ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				$r .= '>\n';
				foreach($this->options as $ovalue => $option)
				{
					$r .= '<option value="' . esc_attr($ovalue) . '" ';
					if(!empty($ovalue) && $ovalue == $value)
						$r .= 'selected="selected" ';
					$r .= '>' . $option . '</option>\n';
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
			elseif($this->type == "textarea")
			{
				$r = '<textarea id="' . $this->id . '" name="' . $this->name . '" rows="' . $this->rows . '" cols="' . $this->cols . '" ';
				if(!empty($this->class))
					$r .= 'class="' . $this->class . '" ';
				$r .= '>' . esc_textarea($value) . '</textarea>';				
			}
			elseif($this->type == "hidden")
			{
				$r = '<input type="hidden" id="' . $this->id . '" name="' . $this->name . '" value="' . esc_attr($value) . '" />';						
			}
			elseif($this->type == "html")
			{
				//arbitrary html/etc
				$r = $this->html;
			}
			else
			{
				$r = "Unknown type for field <strong>" . $this->name . "</strong>.";
			}
			
			if(!empty($this->required) && !empty($this->showrequired))
			{
				if(is_string($this->showrequired))
					$r .= $this->showrequired;
				else
					$r .= '<span class="pmpro_asterisk"> *</span>';
			}
			return $r;
		}	

		function displayAtCheckout()
		{
			global $current_user;
			
			//value passed yet?
			if(isset($_REQUEST[$this->name]))
				$value = $_REQUEST[$this->name];
			elseif(isset($_SESSION[$this->name]))
				$value = $_SESSION[$this->name];
			elseif(!empty($current_user->ID))
				$value = get_user_meta($current_user->ID, $this->name, true);			
			else
				$value = "";
				
			?>
			<div <?php if(!empty($this->divclass)) echo 'class="' . $this->divclass . '"';?>>
				<label for="<?php echo esc_attr($this->name);?>"><?php echo $this->label;?></label>
				<?php $this->display($value); ?>	
				<?php if(!empty($this->hint)) { ?>
					<div class="leftmar"><small class="lite"><?php echo $this->hint;?></small></div>
				<?php } ?>
			</div>	
			<?php
		}
		
		function displayInProfile($user_id)
		{
			global $current_user;
			$value = get_user_meta($user_id, $this->name, true);
				
			?>
			<tr>
				<th><label for="<?php echo esc_attr($this->name);?>"><?php echo $this->label;?></label></th>
				<td>
					<?php 						
						if(current_user_can("edit_user", $current_user->ID))
							$this->display($value); 
						else
							echo "<div>" . $value . "</div>";
					?>	
				</td>
			</tr>			
			<?php
		}		
		
		//from: http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-numeric/4254008#4254008
		function is_assoc($array) {			
			return (bool)count(array_filter(array_keys($array), 'is_string'));
		}
	}
