<?php

	$template = '';
	$template.= $this->Form->input('name', array(
		'label' => __('Nama Agen'), 
	));

	$template.= $this->Form->input('email', array(
		'label' => __('Email Agen'), 
	));

	$template.= $this->Form->input('phone', array(
		'label' => __('No. Telepon Agen'), 
	));

	echo($template);

?>