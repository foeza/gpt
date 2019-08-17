<?php
		$bankName = $this->Rumahku->filterEmptyField($bank_exclusive, 'Bank', 'name');
		$slugTheme = $this->Rumahku->filterEmptyField($dataCompany, 'Theme', 'slug');

		$mls_id		= $this->Rumahku->filterEmptyField($property, 'Property', 'mls_id');

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
?>
<div id="content-wrapper" class="detail-banks">
	<?php

			echo $this->Form->create('Kpr', array(
	            'class' => 'bank-kpr-form',
			));
			echo $this->element('blocks/kpr/detailBank/calculator_header');
			echo $this->element('blocks/kpr/detailBank/calculator_tab_menu');
			echo $this->element('blocks/kpr/detailBank/calculator_content');
			echo $this->Form->end();
	?>
</div>