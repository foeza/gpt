<?php
		$bankName = $this->Rumahku->filterEmptyField($bank_exclusive, 'Bank', 'name');
		$named = $this->Rumahku->filterEmptyField($this->params, 'named');
		$slugTheme = $this->Rumahku->filterEmptyField($dataCompany, 'Theme', 'slug');
		$mls_id		= $this->Rumahku->filterEmptyField($property, 'Property', 'mls_id');

		$classSection = $this->Kpr->widgetClass('section', $dataCompany);

		$label	= $this->Property->getNameCustom($property);
		$slug		= $this->Rumahku->toSlug($label);

		$urlProperty = array(
			'controller' => 'properties', 
			'action'	 => 'detail',
			'mlsid'		 => $mls_id,
			'slug'		 => $slug, 
			'admin'		 => FALSE,
		);

		echo $this->Kpr->addCrumb($slugTheme, array(
			'label' => $label,
			'bankName' => $bankName,
			'urlProperty' => $urlProperty,
		));

		$this->Html->addCrumb(__('Form Aplikasi'));
?>
<div id="content-wrapper" class= "application-banks <?php echo $classSection; ?>">
	<?php
			echo $this->element('blocks/kpr/detailBank/calculator_header');
			echo $this->element('blocks/kpr/detailBank/calculator_tab_menu');
			echo $this->element('blocks/kpr/detailBank/content_application');
	?>
</div>