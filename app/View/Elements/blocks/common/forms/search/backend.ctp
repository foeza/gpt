<?php 
		$url = !empty($url)?$url:false;
		$new_action_button = !empty($new_action_button)?$new_action_button:false;
		$fieldInputName = !empty($fieldInputName)?$fieldInputName:'keyword';
		$with_text_box = isset($with_text_box)?$with_text_box:true;
 		$placeholder = !empty($placeholder)?$placeholder:false;
		$autocomplete = !empty($autocomplete)?$autocomplete:'on';
		$_advanced = !empty($_advanced)?$_advanced:false;
		$btnSearchClass = !empty($btnSearchClass)?$btnSearchClass:'btn-search';
		$addClass = '';
		$_seachButtonType = !empty($_seachButtonType)?$_seachButtonType:'submit';
		$_form = isset($_form)?$_form:true;

		$sorting = !empty($sorting)?$sorting:false;
		$buttonDelete = !empty($sorting['buttonDelete'])?$sorting['buttonDelete']:false;
		$overflowDelete = !empty($sorting['overflowDelete'])?$sorting['overflowDelete']:false;
		$buttonAdd = !empty($sorting['buttonAdd'])?$sorting['buttonAdd']:false;
		$buttonCustom = !empty($sorting['buttonCustom'])?$sorting['buttonCustom']:false;
		$options = !empty($sorting['options'])?$sorting['options']:false;

		$buttons		= Common::hashEmptyField($sorting, 'buttons', array());
		$buttonDivider	= Common::hashEmptyField($sorting, 'buttonDivider');

		$datePicker = !empty($datePicker)?$datePicker:false;

		if( empty($_advanced) ) {
			$addClass = 'no-side-left';
		}

		if( !empty($_form) ) {
			echo $this->Form->create('Search', array(
	    		'url' => $url,
			));
		}

		$with_action_button = isset($with_action_button) ? $with_action_button : true;

		if(!empty($with_action_button)){
?>
<div class="search-style-1">
	<div class="row">
		<div class="col-sm-12">
		    <div class="input-group <?php echo $addClass; ?>">
				<?php 
					if(!empty($with_text_box)){
						echo $this->Html->link(__('Pencarian'), 'javascript:void(0);', array(
							'escape' => false,
                            'class'=> 'input-group-addon at-left',
                            'role' => 'button',
                        ));
						echo $this->Form->input($fieldInputName, array(
                            'type' => 'text', 
							'label' => false,
                            'div' => false,
                            'class'=> 'form-control has-side-control at-left refine-keyword',
                            'placeholder' => $placeholder,
                            'autocomplete' => $autocomplete,
                        ));
						echo $this->Form->button($this->Rumahku->icon('rv4-magnify'), array(
                            'type' => $_seachButtonType, 
                            'class'=> $btnSearchClass,
                        ));
                    }

	                    if( !empty($advanced_content) ) {
							echo $this->Html->link(__('Detail Pencarian').$this->Rumahku->icon('caret'), 'javascript:void(0);', array(
								'escape' => false,
	                            'class'=> 'input-group-addon at-right toggle-display advanced-search',
	                            'role' => 'button',
	                            'data-display' => '.search-box',
	                        ));
						}
				?>
			</div>
			<?php 
                    if( !empty($advanced_content) ) {
                    	$advanced_content_option = isset($advanced_content_option) ? $advanced_content_option : array();
                    	
                		echo $this->element($advanced_content, $advanced_content_option);
					}
			?>
		</div>
	</div>
</div>
<div class="form-type">
	<div class="row">
		<?php 
				if( !empty($sorting) ) {
			        echo $this->element('blocks/common/forms/sorting/backend', array(
				        'sorting' => $options,
			    	));
				}

				if( !empty($exportExcel) ) {
			        echo $this->element('blocks/common/forms/excel/backend');
				}

				if( !empty($buttonDelete) ) {
					$class = isset( $buttonDelete['class'] ) ? $buttonDelete['class'] : 'btn red';
					$column_class = isset( $buttonDelete['column_class'] ) ? $buttonDelete['column_class'] : 'col-sm-2';
					echo $this->Rumahku->buildButton($buttonDelete, $column_class.' button-type button-style-1', $class.' hide');

					if( !empty($overflowDelete) ) {
		?>
		<div class="delete-overflow clear">
			<div class="counter floleft">
				<?php 

						$text = $this->Rumahku->filterEmptyField($buttonDelete, 'text', null, 'Hapus');
						echo $this->Html->tag('span', 0);
						echo __(' Data di%s', strtolower($text));
				?>
			</div>
			<div class="action-delete floright">
				<?php 
						$buttonDelete['text'] = $this->Rumahku->icon('rv4-cross').__($text);
						echo $this->Rumahku->buildButton($buttonDelete);
				?>
			</div>
		</div>
		<?php
					}
				}

				if( !empty($buttonAdd) ) {
					echo $this->Rumahku->buildButton($buttonAdd, 'col-sm-2 pull-right btn-add-full', 'btn blue');
				}
				if( !empty($buttonCustom) ) {
					echo $this->Rumahku->buildButton($buttonCustom, 'col-sm-2 pull-right btn-add-full', 'btn green');
				}

				if($buttons){
					$isMultiple	= Hash::numeric(array_keys($buttons));
					$buttons	= $isMultiple ? $buttons : array($buttons);
					$counter	= 1;

					foreach($buttons as $button){
					//	extract yang bukan option button
						$divOpts	= Common::hashEmptyField($button, 'div');
						$divClass	= !is_array($divOpts) ? $divOpts : Common::hashEmptyField($divOpts, 'class', 'col-sm-2 pull-right btn-add-full');

					//	remove yang bukan option button
						$button = Hash::remove($button, 'div');

						echo($this->Rumahku->buildButton($button, $divClass));

						if($counter < count($buttons) && $buttonDivider){
							echo($this->Rumahku->_callTableDivider());
						}
					}
				}

				if( !empty($datePicker) ) {
			        echo $this->element('blocks/common/forms/datepicker/backend');
				}

				if( !empty($customElement) ) {
			        echo $this->element($customElement);
				}

				if( !empty($_form) ) {
					echo $this->Form->end(); 
				}
		?>
	</div>
</div>
<?php
		} else if( !empty($new_action_button) ) {
	        echo $this->element('headers/tables/header', array(
	        	'buttonDelete' => $buttonDelete,
	        	'buttonAdd' => $buttonAdd,
	        	'buttonCustom' => $buttonCustom,
		        'sorting' => $options,
		        'overflowDelete' => $overflowDelete,
		        'buttons' => $buttons, 
		        'buttonDivider' => $buttonDivider, 
        	));
		}
?>