<?php

	$data			= $this->request->data;
	$isEbrochure	= Common::hashEmptyField($data, 'UserCompanyConfig.is_brochure');
	$options		= array(
		'frameClass'	=> 'col-sm-12',
		'labelClass'	=> 'col-xl-1 col-sm-4 col-md-3 control-label taright',
		'class'			=> 'relative col-sm-8 col-xl-4',
	);

	$save_path_ebrosur = Configure::read('__Site.ebrosurs_photo');
	$photoSizeEbrosur = $this->Rumahku->_rulesDimensionImage($save_path_ebrosur, 'large', 'size');

	$brochure_custom_sell = $this->Rumahku->filterEmptyField($data, 'UserCompanyConfig', 'brochure_custom_sell');
	$brochure_custom_rent = $this->Rumahku->filterEmptyField($data, 'UserCompanyConfig', 'brochure_custom_rent');
	$type_custom_ebrochure = $this->Rumahku->filterEmptyField($data, 'UserCompanyConfig', 'type_custom_ebrochure');

	$ebrosur_colors = $this->Rumahku->filterEmptyField($_global_variable, 'ebrosur_colors', $type_custom_ebrochure, 'landscape');

	if(!empty($type_custom_ebrochure) && $type_custom_ebrochure == 'potrait'){
            $photoSizeEbrosur = '724x1024';
	}

	$ebrosurs_type = array(
		'landscape'	=> 'Landscape',
		'potrait'	=> 'Potrait'
	);

	echo($this->Rumahku->buildInputToggle('is_brochure', array_merge($options, array(
		'label' => __('E-Brosur'),
		'attributes' => array(
			'class' => 'handle-toggle-content',
			'data-target' => '.brochure-box'
		), 
	))));

?>
<div class="brochure-box" style="<?php echo($isEbrochure ? '' : 'display:none;'); ?>">
	<?php

		echo($this->Rumahku->buildInputToggle('is_ebrosur_frontend', array_merge($options, array(
			'label' => __('Tampilkan E-Brosur di FrontEnd?'),
		))));

		echo($this->Rumahku->buildInputToggle('auto_create_ebrochure', array_merge($options, array(
			'label' => __('Auto Create E-Brosur'),
		))));

		$dataMatch = str_replace('"', "'", json_encode(array(
			array('.brochure-box-old', array('0'), 'slide'), 
			array('.brochure-box-builder', array('1'), 'slide'), 
		)));

		echo($this->Rumahku->buildInputToggle('is_ebrochure_builder', array_merge($options, array(
			'label'			=> __('Gunakan E-Brosur Studio'),
			'attributes'	=> array(
				'class'			=> 'handle-toggle',
				'data-match'	=> $dataMatch, 
			), 
		))));

	?>
	<div class="brochure-box-builder">
		<?php

			$ebrochureTemplates = empty($ebrochureTemplates) ? array() : $ebrochureTemplates;

			echo($this->element('blocks/ebrosurs/forms/template', array(
				'templates' => $ebrochureTemplates, 
			)));

		?>
	</div>
	<?php
			echo($this->Rumahku->buildInputForm('brochure_custom_sell', array_merge($options, array(
				'type' => 'file',
				'label' => sprintf(__('Upload E-Brosur Dijual ( <span class="resolution-broschure">%s</span> )'), $photoSizeEbrosur),
				'preview' => array(
					'photo' => $brochure_custom_sell,
					'save_path' => $save_path_ebrosur,
					'size' => 's',
				),
				'delete_photo' => array(
					'url' => array(
						'controller' => 'settings',
						'action' => 'delete_template_ebrosur',
						'brochure_custom_sell',
						'admin' => true
					),
					'confirm' => __('Apakah Anda yakin ingin menghapus template eBrosur Dijual?')
				)
			))));

			echo($this->Rumahku->buildInputForm('brochure_custom_rent', array_merge($options, array(
				'type' => 'file',
				'label' => sprintf(__('Upload E-Brosur Disewakan ( <span class="resolution-broschure">%s</span> )'), $photoSizeEbrosur),
				'preview' => array(
					'photo' => $brochure_custom_rent,
					'save_path' => $save_path_ebrosur,
					'size' => 's',
				),
				'delete_photo' => array(
					'url' => array(
						'controller' => 'settings',
						'action' => 'delete_template_ebrosur',
						'brochure_custom_rent',
						'admin' => true
					),
					'confirm' => __('Apakah Anda yakin ingin menghapus template eBrosur Disewakan?')
				)
			))));
	?>
	<div class="brochure-box-old">
		<?php

			echo($this->Rumahku->buildInputToggle('is_description_ebrochure', array_merge($options, array(
				'label' => __('Tampilkan deskripsi properti?'),
			))));

			echo($this->Rumahku->buildInputToggle('is_specification_ebrochure', array_merge($options, array(
				'label' => __('Tampilkan spesifikasi properti?'),
			))));

			echo($this->Rumahku->buildInputForm('type_custom_ebrochure', array_merge($options, array(
				'label' => __('Tipe E-Brosur *'),
				'options' => $ebrosurs_type,
				'empty' => __('Pilih Tipe E-Brosur'),
				'attributes' => array(
					'class' => 'type-broschure-handle'
				), 
			))));

			echo($this->Rumahku->fieldColorPicker('brochure_content_color', __('Warna Konten'), array_merge($options, array(
				'dataField' => 'content_color',
				'dataDefault' => $ebrosur_colors,
				'defaultClass' => 'col-sm-3 col-xs-12',
				'class' => 'relative col-sm-5 col-xs-12'
			))));

			echo($this->Rumahku->fieldColorPicker('brochure_footer_color', __('Warna Footer'), array_merge($options, array(
				'dataField' => 'footer_color',
				'dataDefault' => $ebrosur_colors,
				'defaultClass' => 'col-sm-3 col-xs-12',
				'class' => 'relative col-sm-5 col-xs-12'
			))));

			echo($this->element('blocks/ebrosurs/forms/kordinat'));
			echo($this->element('blocks/ebrosurs/forms/kordinat', array(
				'field_x' => 'delta_x_created',
				'field_y' => 'delta_y_created',
				'label' => __('Koordinat Tanggal dibuat')
			)));

			echo($this->Rumahku->buildInputToggle('with_mls_id', array_merge($options, array(
				'label' => __('MLS ID'),
				'attributes' => array(
					'class' => 'handle-toggle-content',
					'data-target' => '.kordinat-mlsid-box'
				)
			))));

			echo($this->Html->div('kordinat-mlsid-box', $this->element('blocks/ebrosurs/forms/kordinat', array(
				'field_x' => 'delta_x_mlsid',
				'field_y' => 'delta_y_mlsid',
				'label' => __('Koordinat MLS ID')
			)), array(
				'style' => 'display:'.(!empty($data['UserCompanyConfig']['with_mls_id']) ? 'block' : 'none')
			)));

		?>
	</div>
</div>