<?php

	$record = empty($record) ? array() : $record;

	if($record){
		$_global_variable	= empty($_global_variable) ? array() : $_global_variable;
		$propertyActions	= empty($propertyActions) ? array() : $propertyActions;
		$propertyTypes		= empty($propertyTypes) ? array() : $propertyTypes;
		$lotUnits			= empty($lotUnits) ? array() : $lotUnits;
		$currencies			= empty($currencies) ? array() : $currencies;
		$facilities			= empty($facilities) ? array() : $facilities;
		$periods			= empty($periods) ? array() : $periods;

		$actionID	= Common::hashEmptyField($record, 'Property.property_action_id');
		$typeID		= Common::hashEmptyField($record, 'Property.property_type_id');
		$price		= Common::hashEmptyField($record, 'Property.price', 0);

		$currencyList	= Hash::combine($currencies, '{n}.Currency.id', '{n}.Currency.symbol');
		$periodList		= Hash::combine($periods, '{n}.Period.id', '{n}.Period.name');

		$currencyID		= Common::hashEmptyField($record, 'Property.currency_id', 1);
		$periodID		= Common::hashEmptyField($record, 'Property.period_id');
		$currencySymbol	= Common::hashEmptyField($currencyList, $currencyID);
		$periodName		= Common::hashEmptyField($periodList, $periodID);

		?>
		<div class="form-added numeric-list relative">
			<ul>
				<?php

					$inputCount = Common::hashEmptyField($record, 'PropertyPointPlus.name', array());
					$inputCount = $inputCount ? count($inputCount) : 1;

					for($index = 0; $index < $inputCount; $index++){
						$pointPlusName = Common::hashEmptyField($record, sprintf('PropertyPointPlus.name.%s', $index));
					//	$editableInput = $this->Form->input('PropertyPointPlus.name.', array(
					//		'div'	=> 'form-group mb0', 
					//		'label'	=> false, 
					//		'error'	=> false, 
					//		'value'	=> $pointPlusName, 
					//		'class'	=> 'form-control input-sm editable-input', 
					//		'placeholder'	=> __('Masukkan nilai lebih properti disini'), 
					//	));

						$editableInput = $this->Html->link($pointPlusName, '#', array(
							'data-value'		=> $pointPlusName, 
							'data-name'			=> 'data[PropertyPointPlus][name]['.$index.']', 
							'data-type'			=> 'text', 
							'data-mode'			=> 'inline', 
							'data-placeholder'	=> __('Masukkan nilai lebih properti disini'), 
							'class'				=> 'editable editable-click editable-fullwidth', 
						));

						echo($this->Html->tag('li', $editableInput, array('class' => 'mb5')));
					}

				?>
			</ul>
			<div class="form-group mb0">
				<?php 

					echo($this->Html->link(__('%s Tambah', $this->Rumahku->icon('rv4-bold-plus')), '#', array(
						'escape'	=> false,
						'role'		=> 'button',
						'class'		=> 'field-added',
					)));

				?>
			</div>
		</div>
		<?php

	}

?>