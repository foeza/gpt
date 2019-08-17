<?php

	$youtubeText = $this->Html->tag('strong', 'YouTube');

	$template = $this->Html->tag('div', $this->Rumahku->icon('rv4-image-2'), array(
		'class' => 'pict',
	));
	$template.= $this->Html->div('form-youtube', 
		$this->element('blocks/common/multiple_forms', array(
			'modelName'		=> 'PropertyVideos',
			'labelName'		=> __(sprintf('URL %s', $youtubeText)),
			'placeholder'	=> __('Masukkan URL video yang diunggah melalui YouTube'),
			'infoTop'		=> __(sprintf('Anda dapat menambahkan video mengenai properti yang diiklankan dengan menggunakan URL video yang diunggah melalui %s', $youtubeText)),
			'divClassTop'	=> FALSE,
			'limit'			=> 1,
		))
	);
	$template.= $this->Html->div('action-upload tacenter', 
		$this->Form->button(__('Simpan Video'), array(
			'type'	=> 'submit',
			'class'	=> 'btn background dark'
		))
	);

	$isAjax		= isset($isAjax) ? $isAjax : false;
	$options	= array();

	if($isAjax){
		$options = array(
			'class'					=> 'ajax-form', 
			'data-type'				=> 'content', 
			'data-wrapper-write'	=> '#property_media_wrapper', 
		);
	}

	echo($this->Form->create('PropertyVideos', $options));
	echo($this->Html->div('info-upload-photo text-center', $template));
	echo($this->Form->end());

?>