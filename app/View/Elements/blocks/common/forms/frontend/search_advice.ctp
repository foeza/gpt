<?php

	$adviceCategories = empty($adviceCategories) ? array() : $adviceCategories;

	$template = '';
	$template.= $this->Form->input('keyword', array(
		'placeholder' => __('Judul atau konten...'),
	));

	$template.= $this->Form->input('category', array(
		'label'		=> __('Kategori'), 
		'empty'		=> __('Pilih Kategori'),
		'options'	=> $adviceCategories,
	));

	echo($template);

?>