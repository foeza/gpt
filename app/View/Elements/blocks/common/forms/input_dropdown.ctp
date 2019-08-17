<?php 
		if( !empty($options) ) {
			$data = $this->request->data;
			$fieldName = isset($fieldName)?$fieldName:false;
			$attributes = !empty($attributes)?$attributes:array();
			$value = Hash::get($data, $fieldName);

			if( empty($value) ) {
				$value = '';
			}

			$frameClass = isset($frameClass)?$frameClass:'col-sm-12 mb30';
			$empty = isset($empty)?$empty:__('Semua');
			$_checkbox = isset($_checkbox)?$_checkbox:false;
			$_checkbox_action = isset($_checkbox_action)?$_checkbox_action:'dropdown-menu-form';

			$label = isset($label)?$label:false;
			$labelClass = isset($labelClass)?$labelClass:false;
			$checkboxAttr = !empty($checkboxAttr)?$checkboxAttr:array();

			$default_value = '';
			$default_title = isset($default_title)?$default_title:false;
			$dataTitle = false;

			if( !empty($_checkbox) ) {
				$dropdownClass = $_checkbox_action;
			} else {
				$dropdownClass = 'dropdown-menu-select';
			}

			if( empty($default_title) ) {
				if( !empty($value) ) {
					if( is_array($value) ) {
						$default_title = array();

						foreach ($value as $id => $name) {
							if( !empty($options[$id]) ) {
								$default_title[] = $options[$id];
							}
						}
						
						$dataTitle = implode(', ', $default_title);
						$dataCurrent = $this->Rumahku->getTitleCheckBox($dataTitle);
					} else {
						$dataCurrent = $this->Rumahku->filterEmptyField($options, $value);
					}

					$default_title = $dataCurrent;
				} else if( !empty($empty) ) {
					$default_title = $empty;
				} else if( !empty($options) ) {
					$default_value = key($options);
					$default_title = !empty($options[$default_value])?$options[$default_value]:false;
				}
			}
			
			$titleHeader = $this->Html->tag('span', $default_title, array(
				'class' => 'title',
			));
?>
<div class="<?php echo $frameClass; ?>">
	<?php 
			if( !empty($label) ) {
				echo $this->Html->tag('label', $label, array(
					'class' => $labelClass,
				));
			}
	?>
	<div class="dropdown-group">
		<?php 
				echo $this->Html->link(sprintf(__('%s %s'), $titleHeader, $this->Html->tag('span', $this->Rumahku->icon('rv4-angle-down'), array(
					'class' => 'icon',
				))), '#', array(
					'escape' => false,
					'class' => 'dropdown-toggle',
					'data-value' => $default_value,
					'data-empty' => $empty,
					'data-toggle' => 'dropdown',
					'aria-expanded' => 'false',
					'aria-hashpopup' => 'true',
					'title' => $dataTitle,
				));
		?>
		<ul class="dropdown-menu <?php echo $dropdownClass; ?>">
			<?php 
					if( !empty($empty) ) {
						if( !empty($_checkbox) ) {
							$content = $this->Form->input($fieldName.'.0', array(
					            'type' => 'checkbox',
					            'label' => false,
					            'div' => false,
					            'required' => false,
					            'error' => false,
					            'rel' => '',
					        ));
				        	$content .= $this->Form->label($fieldName.'.0', $empty);

					        echo $this->Html->tag('li', $this->Html->tag('div', $this->Html->tag('div', $content, array(
					            'class' => 'cb-checkmark',
					        )), array(
					            'class' => 'cb-custom',
					        )));
						} else {
					        echo $this->Html->tag('li', $this->Html->link($empty, '#', array(
					        	'escape' => false,
					            'data-value' => '',
					        )));
						}
				    }

					foreach ($options as $id => $name) {
						if( !empty($_checkbox) ) {
							$content = $this->Form->input($fieldName.'.'.$id, array_merge(array(
					            'type' => 'checkbox',
					            'label' => false,
					            'div' => false,
					            'required' => false,
					            'error' => false,
					            'value' => true,
					            'options' => false,
					            'rel' => $id,
					        ), $checkboxAttr));
					        $content .= $this->Form->label($fieldName.'.'.$id, $name);

					        echo $this->Html->tag('li', $this->Html->tag('div', $this->Html->tag('div', $content, array(
					            'class' => 'cb-checkmark',
					        )), array(
					            'class' => 'cb-custom',
					        )));
				        } else {
					        echo $this->Html->tag('li', $this->Html->link($name, '#', array(
					        	'escape' => false,
					            'data-value' => $id,
					        )));
						}
					}
			?>
		</ul>
		<?php 
				if( empty($_checkbox) ) {
					echo $this->Form->hidden($fieldName, array_merge($attributes, array(
						'class'=> 'input-dropdown', 
					)));
				}
		?>
	</div>
	<?php
			echo $this->Rumahku->errorField($fieldName);
	?>
</div>
<?php 
		}
?>