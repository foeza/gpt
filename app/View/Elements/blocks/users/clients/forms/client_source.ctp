<?php
		$data = $this->request->data;

		$value_added = isset($value_added)?$value_added:false;
		$value_event_added = isset($value_event_added)?$value_event_added:false;
		// init data match for client source
		$dataMatch = array(
			array('.address-reference', array('1','2','3'), 'slide'),
			array('.event-client-reference', array('2'), 'slide'),
			array('.sosmed-id-placeholder', array('4'), 'slide'),
			array('.another-option-placeholder', array('6','7','8','9','10','11','12'), 'slide'),
			array('.walkin-option-placeholder', array('5'), 'slide'),
			array('.add-sosmed-reference', array('4'), 'slide'),
		);
		$dataMatch = json_encode($dataMatch);

?>

<div class="content-with-annotation">
	<?php
	        echo $this->Html->tag('h2', __('Referensi Klien'), array(
	        	'class' => 'sub-heading'
	        ));
			echo($this->Rumahku->buildInputForm('UserClient.client_ref_id', array(
				'label' => __('Sumber data klien'),
				'options' => $clientMasterReference,
				'empty' => __('Pilih Referensi'),
				'inputClass' => 'handle-toggle', 
                'attributes' => array(
					'data-match' => $dataMatch, 
					'data-reset-target' => 'true', 
				),
			)));
	?>
	<div class="content-input mt30">
		<?php
				echo $this->Html->tag('div', $this->Rumahku->buildInputForm('UserClient.content_event', array(
					'type' => 'text', 
					'label' => __('Nama Event'),
					'fieldError' => 'UserClient.content_event',
				)), array(
					'class' => 'event-client-reference', 
				));

            	echo $this->element('blocks/users/clients/forms/sosmed');

				//	if choose walk in or indirect
				echo $this->Html->tag('div', $this->Rumahku->buildInputForm('UserClient.client_ref_walk_in', array(
					'type' => 'textarea', 
					'label' => __('Keterangan'),
				)), array(
					'class' => 'walkin-option-placeholder', 
				));

				echo $this->Html->tag('div', $this->Rumahku->buildInputForm('UserClient.client_ref_another_option', array(
					'type' => 'textarea', 
					'label' => __('Keterangan'),
				)), array(
					'class' => 'another-option-placeholder', 
				));

				$region_id = Common::hashEmptyField($data, 'UserClient.additional_region_id');
				$city_id = Common::hashEmptyField($data, 'UserClient.additional_city_id');
				$subareas_additional = !empty($subareas_additional)?$subareas_additional:false;
		?>
		<div class="content-input locations-trigger address-reference">
			<?php
				/*
					echo $this->Rumahku->setFormAddress('UserClient', 'areas', array(
						'region_id' => $region_id,
						'city_id' => $city_id,
					));

					echo $this->Rumahku->buildInputForm('UserClient.additional_address', array(
						'label' => __('Alamat (Optional)'),
						'autocomplete' => 'off',
					));
					echo $this->Rumahku->buildInputForm('UserClient.additional_region_id', array(
						'empty' => __('Pilih Provinsi'),
	                    'inputClass' => 'regionId',
						'label' => __('Provinsi'),
						'autocomplete' => 'off',
					));
					echo $this->Rumahku->buildInputForm('UserClient.additional_city_id', array(
						'empty' => __('Pilih Kota'),
	                    'inputClass' => 'cityId',
						'label' => __('Kota'),
						'autocomplete' => 'off',
					));
					echo $this->Rumahku->buildInputForm('UserClient.additional_subarea_id', array(
						'empty' => __('Pilih Area'),
	                    'inputClass' => 'subareaId',
						'label' => __('Area'),
						'autocomplete' => 'off',
						'options' => $subareas_additional,
					));
					echo $this->Rumahku->buildInputForm('UserClient.additional_zip', array(
	                    'inputClass' => 'rku-zip',
						'label' => __('Kode Pos'),
						'autocomplete' => 'off',
					));
				*/

					$modelName		= 'UserClient';
					$mandatory		= false;
					$inputOptions	= array(
						'frameClass'	=> 'col-sm-12 col-md-8',
						'labelClass'	=> 'col-xl-2 col-sm-4 control-label taright',
						'class'			=> 'relative col-sm-8 col-xl-4',
					);

					echo($this->Rumahku->buildInputForm($modelName.'.additional_address', array_replace($inputOptions, array(
						'label' => __('Alamat (Optional)'),
					))));

					echo($this->element('blocks/properties/forms/location_picker', array(
						'options' => array_replace($inputOptions, array(
							'mandatory'		=> false, 
							'model'			=> $modelName, 
							'field_prefix'	=> 'additional_', 
						)), 
					)));

					echo($this->Rumahku->buildInputForm($modelName.'.additional_zip', array(
						'label'			=> __('Kode Pos'),
						'inputClass'	=> 'rku-zip',
					)));
			?>
		</div>
	</div>
</div>