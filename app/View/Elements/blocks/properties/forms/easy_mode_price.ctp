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
		<div class="price-list relative mt20">
			<div id="sell-price-placeholder" class="<?php echo($actionID == 2 ? 'hide' : ''); ?>">
				<ul>
					<?php

						$editableInput = $this->Html->link($currencySymbol, '#', array(
							'data-value'	=> $currencyID, 
							'data-name'		=> 'data[Property][currency_id]', 
							'data-type'		=> 'select', 
							'data-mode'		=> 'inline', 
							'data-source'	=> str_replace('"', '\'', json_encode($currencyList)), 
							'class'			=> 'editable editable-click', 
						));

						if($price){
							$price = $this->Number->currency($price, false, array('places' => 0));
						}

						$editableInput.= $this->Html->link($price, '#', array(
							'data-value'		=> $price, 
							'data-name'			=> 'data[Property][price]', 
							'data-type'			=> 'text', 
							'data-mode'			=> 'inline', 
							'data-placeholder'	=> __('Masukkan harga properti Anda disini'), 
							'class'				=> 'editable editable-click', 
							'data-tpl'			=> '<input type="text" class="input_price">', 
						));

					//	$editableInput = $this->Html->tag('h1', $editableInput);

						echo($this->Html->tag('li', $editableInput, array('class' => 'mb5')));

					?>
				</ul>
			</div>
			<div id="rent-price-placeholder" class="<?php echo($actionID == 1 ? 'hide' : ''); ?>">
				<ul>
					<?php

						if($periodList){
							$pricePeriods	= Common::hashEmptyField($record, 'PropertyPrice.period_id', array());
							$propertyPrices	= array();

							if($pricePeriods){
								foreach($pricePeriods as $key => $periodID){
									$propertyPrices[$periodID] = array(
										'currency_id'	=> Common::hashEmptyField($record, sprintf('PropertyPrice.currency_id.%s', $key), 1), 
										'price'			=> Common::hashEmptyField($record, sprintf('PropertyPrice.price.%s', $key), 0), 
									);
								}
							}

							$counter = 0;
							foreach($periodList as $periodID => $periodName){
								$currencyID	= Common::hashEmptyField($propertyPrices, sprintf('%s.currency_id', $periodID), 1);
								$price		= Common::hashEmptyField($propertyPrices, sprintf('%s.price', $periodID));

								$editableInput = $this->Html->link($currencySymbol, '#', array(
									'data-value'	=> $currencyID, 
									'data-name'		=> 'data[PropertyPrice][currency_id]['.$counter.']', 
									'data-type'		=> 'select', 
									'data-mode'		=> 'inline', 
									'data-source'	=> str_replace('"', '\'', json_encode($currencyList)), 
									'class'			=> 'editable editable-click ' . ($currencySymbol ? '' : 'editable-empty'), 
								));

								if($price){
									$price = $this->Number->currency($price, false, array('places' => 0));
								}

								$editableInput.= $this->Html->link($price, '#', array(
									'data-value'		=> $price, 
									'data-name'			=> 'data[PropertyPrice][price]['.$counter.']', 
									'data-type'			=> 'text', 
									'data-mode'			=> 'inline', 
									'data-placeholder'	=> __('Masukkan harga properti Anda disini'), 
									'class'				=> 'editable editable-click', 
									'data-tpl'			=> '<input type="text" class="input_price">', 
								));

								$editableInput.= $this->Html->link($periodName, '#');
								$editableInput.= $this->Form->hidden('PropertyPrice.period_id', array(
									'name'	=> 'data[PropertyPrice][period_id]['.$counter.']', 
									'value'	=> $periodID, 
								));

								echo($this->Html->tag('li', $editableInput, array('class' => 'mb5')));

								$counter++;
							}
						}

					?>
				</ul>
			</div>
		</div>
		<?php

	}

?>