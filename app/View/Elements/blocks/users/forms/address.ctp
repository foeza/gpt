<div class="locations-trigger">
	<?php 
			$inputAddress = !empty($inputAddress)?$inputAddress:false;
			$mandatory = $this->Rumahku->filterIssetField($inputAddress, 'mandatory', false, '*');

			$options = !empty($options)?$options:array();
			$modelName = !empty($modelName)?$modelName:'UserProfile';

			$regionId = isset($regionId) ? $regionId : 'regionId';
			$cityId = isset($cityId) ? $cityId : 'cityId';
			$subareaId = isset($subareaId) ? $subareaId : 'subareaId';
			$zipId = isset($zipId) ? $zipId : 'rku-zip';

			$aditionals = isset($aditionals) ? $aditionals : false;
			$use_location_picker = isset($use_location_picker) ? $use_location_picker : false;

			echo $this->Rumahku->setFormAddress( $modelName, 'areas', array(
				'aditionals' => $aditionals, 
			));

			echo $this->Rumahku->buildInputForm($modelName.'.address', array_merge($options, array(
				'label' => __('Alamat Tempat Tinggal %s', $mandatory),
			)));

			if(empty($use_location_picker)){
				echo $this->Rumahku->buildInputForm($modelName.'.region_id', array_merge($options, array(
					'inputClass' => $regionId,
					'label' => __('Provinsi %s', $mandatory),
					'empty' => __('Pilih Provinsi'),
				)));
				echo $this->Rumahku->buildInputForm($modelName.'.city_id', array_merge($options, array(
					'inputClass' => $cityId,
					'label' => __('Kota %s', $mandatory),
					'empty' => __('Pilih Kota'),
				)));
				echo $this->Rumahku->buildInputForm($modelName.'.subarea_id', array_merge($options, array(
					'inputClass' => $subareaId,
					'label' => __('Area %s', $mandatory),
					'empty' => __('Pilih Area'),
				)));
			}
			else{
				echo($this->element('blocks/properties/forms/location_picker', array(
					'options' => array_merge($options, array(
						'model'			=> $modelName, 
						'mandatory'		=> $mandatory, 
						'set_address'	=> false, 
					)), 
				)));
			}
			
			echo $this->Rumahku->buildInputForm($modelName.'.zip', array_merge($options, array(
				'inputClass' => $zipId,
				'label' => __('Kode Pos %s', $mandatory),
				'class' => 'relative  col-sm-3 col-xl-5',
			)));
	?>
</div>