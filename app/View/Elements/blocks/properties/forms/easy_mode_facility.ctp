<?php

	$record = empty($record) ? array() : $record;

	if($record){
		$facilities		= empty($facilities) ? array() : $facilities;
		$facilityList	= Hash::combine($facilities, '{n}.Facility.id', '{n}.Facility.name');

		?>
		<div class="cb-custom no-margin relative">
			<ul>
				<?php

					if($facilityList){
						$propertyFacilities = Common::hashEmptyField($record, 'PropertyFacility.facility_id', array());

						foreach($facilityList as $facilityID => $facilityName){
							$checked	= Common::hashEmptyField($propertyFacilities, $facilityID);
							$inputName	= sprintf('PropertyFacility.facility_id.%s', $facilityID);

							$label		= $this->Form->label($inputName, $facilityName);
							$input		= $this->Form->input($inputName, array(
								'type'		=> 'checkbox', 
								'div'		=> false, 
								'label'		=> false, 
								'value'		=> $facilityID, 
								'checked'	=> $checked, 
							));

							echo($this->Html->tag('li', $input . $label, array(
								'class' => 'cb-checkmark mb10', 
							)));
						}

						$checked	= Common::hashEmptyField($record, 'PropertyFacility.other_id');
						$inputName	= 'PropertyFacility.other_id';

						$label		= $this->Form->label($inputName, __('Lainnya'));
						$input		= $this->Form->input($inputName, array(
							'type'			=> 'checkbox', 
							'div'			=> false, 
							'label'			=> false, 
							'value'			=> 1, 
							'checked'		=> $checked, 
							'class'			=> 'chk-other-item', 
							'data-target'	=> '.other-facility-input',
						));

						echo($this->Html->tag('li', $input . $label, array(
							'class' => 'cb-checkmark mb10', 
						)));

						$input = $this->Form->input('PropertyFacility.other_text', array(
							'div'		=> 'form-group mb0', 
							'label'		=> false,
							'disabled'	=> empty($checked), 
							'class'		=> 'form-control input-sm other-facility-input', 
							'after'		=> $this->Html->tag('small', __('Berikan tanda koma untuk lebih dari 1 fasilitas')), 
						));

						echo($this->Html->tag('li', $input, array(
							'class' => 'mb5', 
						)));
					}

				?>
			</ul>
		</div>
		<?php

	}

?>