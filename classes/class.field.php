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
			foreach($attr as $key=>$value)
			{
				$this->$key = $value;
			}
			
			//make sure we have an id
			if(empty($this->id))
				$this->id = $this->name;
				
			//default fields						
			if($this->type == "text")
			{
				if(empty($this->size))
					$this->size = 30;
			}
			elseif($this->type == "select")
			{
				if(empty($this->options))
					$this->options = array("", "- choose one -");
			}
			elseif($this->type == "textarea")
			{
				if(empty($this->rows))
					$this->rows = 5;
				if(empty($this->cols))
					$this->cols = 80;
			}	

			//default label
			if(empty($this->label))
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
			else
			{
				$r = "Unknown type for field <strong>" . $this->name . "</strong>.";
			}
			
			return $r;
		}	

		function displayAtCheckout()
		{
			global $current_user;
			
			//value passed yet?
			if(isset($_REQUEST[$this->name]))
				$value = $_REQUEST[$this->name];
			elseif(!empty($current_user->ID))
				$value = get_user_meta($current_user->ID, $this->name, true);
			else
				$value = "";
				
			?>
			<div>
				<label for="<?php echo esc_attr($this->name);?>"><?php echo $this->label;?></label>
				<?php $this->display($value); ?>				
			</div>	
			<?php
		}
		
		function displayInProfile($user_id)
		{
			$value = get_user_meta($user_id, $this->name, true);
				
			?>
			<tr>
				<th><label for="<?php echo esc_attr($this->name);?>"><?php echo $this->label;?></label></th>
				<td>
					<?php 						
						$this->display($value); 
					?>	
				</td>
			</tr>			
			<?php
		}		
	}