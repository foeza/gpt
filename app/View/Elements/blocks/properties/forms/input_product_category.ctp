<?php
		$options			= empty($options) ? array() : $options;
		$wrapperClass		= Common::hashEmptyField($options, 'wrapper_class', 'col-sm-12');
		$frameLabelClass	= Common::hashEmptyField($options, 'frame_label_class', 'col-xl-2 taright col-sm-3');
		$frameInputClass	= Common::hashEmptyField($options, 'frame_input_class', 'relative col-sm-5 col-xl-3');

	//	search category product
		$autoUrlAgent = $this->Html->url(array(
			'controller' => 'ajax',
			'action' => 'list_data',
			'product_ctg', 
			'admin' => false,
		));

		echo $this->Rumahku->buildInputForm('name_category', array(
			'label' => __('Kategori Produk *'),
			'data_url' => $autoUrlAgent,
			'id' => 'autocomplete',
			'type' => 'text',
			'autocomplete' => 'off',
			'labelClass' => $frameLabelClass,
			'class' => $frameInputClass,
			'frameClass' => $wrapperClass,
		));
?>