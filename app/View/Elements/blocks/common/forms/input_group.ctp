<?php 
		$type = !empty($type)?$type:'text';
		$id = !empty($id)?$id:false;
		$inputClass = !empty($inputClass)?$inputClass:false;
		$labelClass = !empty($labelClass)?$labelClass:false;
		$divClass = !empty($divClass)?$divClass:false;
		$disabled = !empty($disabled)?$disabled:false;
		$label = !empty($label)?$label:false;
		$fieldName = !empty($fieldName)?$fieldName:false;
		$errorFieldName = !empty($errorFieldName)?$errorFieldName:$fieldName;
		$placeholder = !empty($placeholder)?$placeholder:false;
		$options = !empty($options)?$options:false;
		$attributes = !empty($attributes)?$attributes:array();
		$labelAttributes = !empty($labelAttributes)?$labelAttributes:array();
		$readonly = !empty($readonly)?$readonly:false;
		$error = isset($error)?$error:true;
		$title = !empty($title)?$title:false;

		if( !empty($disabled) || !empty($readonly) ) {
			$groupClass = 'disable';
		} else {
			$groupClass = '';
		}

		if($error){
			$errorMsg = $this->Form->error($errorFieldName);
		} else {
			$errorMsg = $this->Form->error($errorFieldName);

			if( !empty($errorMsg) ) {
				$title = $this->Rumahku->safeTagPrint($errorMsg);

				if( $fieldName != $errorFieldName ) {
					$inputClass .= ' form-error';
				}
			}
		}

		$attributes = array_merge(array(
			'type' => $type,
			'id' => $id,
            'label' => false,
            'div' => false,
            'required' => false,
            'error' => false,
            'title' => $title,
            'class' => $inputClass,
            'placeholder' => $placeholder,
            'disabled' => $disabled,
            'readonly' => $readonly
        ), $attributes);

        if( !empty($options) ) {
        	$attributes['options'] = $options;
        }

        if($labelClass){
        	$labelAttributes['class'] = $labelClass;
        }
?>
<div class="<?php echo $divClass; ?>">
	<div class="input-group <?php echo $groupClass; ?>">
		<?php 
				echo $this->Html->tag('label', $label, $labelAttributes);

				switch ($type) {
					case 'select':
						echo $this->Html->tag('div', $this->Form->input($fieldName, $attributes).$this->Rumahku->icon('rv4-angle-down', false, 'span'), array(
							'class' => 'select',
						));
						break;
					
					default:
						echo $this->Form->input($fieldName, $attributes);
						break;
				}

				if( !empty($error) && !empty($errorMsg) ){
					echo $this->Form->error($errorFieldName);
				}
		?>
	</div>
	<?php
			if( !empty($after) ){
				echo $after;
			}
	?>
</div>