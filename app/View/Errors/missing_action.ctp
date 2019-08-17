<?php

	$message = empty($message) ? NULL : $message;
	$content = '';
	$content.= $this->Html->tag('h2', $message);
	$content.= $this->Html->tag('p', __('Ups, Halaman tidak ditemukan. (404)'), array('class' => 'error'));

	echo($content);

	$debugMode = Configure::read('debug');
	if($debugMode){
		echo($this->element('exception_stack_trace'));
	}

?>
