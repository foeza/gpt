<?php 
        $rowClass = isset($rowClass)?$rowClass:'row';
        $groupClass = isset($groupClass)?$groupClass:'form-group';
        $frameClass = !empty($frameClass)?$frameClass:false;
        $inputClass = !empty($inputClass)?$inputClass:false;
        $inputClass2 = !empty($inputClass2)?$inputClass2:$inputClass;
        $separator = isset($separator)?$separator:true;

        $type = !empty($type)?$type:false;
        $label = !empty($label)?$label:false;
        $labelDivClass = !empty($labelDivClass)?$labelDivClass:false;
        $labelClass = isset($labelClass)?$labelClass:'control-label';

        $class = !empty($class)?$class:false;
        $fieldName1 = !empty($fieldName1)?$fieldName1:false;
        $fieldName2 = !empty($fieldName2)?$fieldName2:false;

        $placeholder1 = !empty($placeholder1)?$placeholder1:false;
        $placeholder2 = !empty($placeholder2)?$placeholder2:false;

        $divider = !empty($divider)?$divider:false;
        $dividerClass = !empty($dividerClass)?$dividerClass:false;
        $classGroup = !empty($classGroup)?$classGroup:false;

        $attributes = !empty($attributes)?$attributes:false;

        if( !empty($textGroup) ) {
	        $groupLabel = $this->Html->tag('div', $textGroup, array(
	        	'class' => 'input-group-addon at-right '.$classGroup,
	    	));
	    	$inputClass .= ' has-side-control';
	    	$inputClass2 .= ' has-side-control';
	    	$class .= ' input-group';
        } else {
        	$groupLabel = false;
        }
?>
<div class="<?php echo $groupClass; ?> input-multiple">
	<div class="<?php echo $rowClass; ?>">
		<div class="<?php echo $frameClass; ?>">
			<div class="row">
				<?php 
						echo $this->Html->tag('div', $this->Form->label($fieldName1, $label, array(
                            'class' => $labelClass,
                        )), array(
							'class' => $labelDivClass,
						));
				?>
				<div class="<?php echo $class; ?> mb0">
					<?php 
                            $options = array(
                                'label' => false,
                                'required' => false,
                                'div' => false,
                                'class' => 'form-control at-right '.$inputClass,
                                'placeholder' => $placeholder1,
                            );

                            if( !empty($attributes) ) {
                                $options = array_merge($options, $attributes);
                            }

                            echo $this->Form->input($fieldName1, $options);
                            echo $groupLabel;
                    ?>
				</div>
				<?php 
                        if( !empty($separator) ) {
    						echo $this->Html->tag('div', $this->Rumahku->icon($divider), array(
    							'class' => 'sign '.$dividerClass,
    						));
                        }
				?>
				<div class="<?php echo $class; ?> ml0">
					<?php 
                            $options = array(
                                'label' => false,
                                'required' => false,
                                'div' => false,
                                'class' => 'form-control at-right '.$inputClass2,
                                'placeholder' => $placeholder2,
                            );

                            if( !empty($attributes) ) {
                                $options = array_merge($options, $attributes);
                            }
                            
                            echo $this->Form->input($fieldName2, $options);
                            echo $groupLabel;
                    ?>
				</div>
			</div>
		</div>
	</div>
</div>