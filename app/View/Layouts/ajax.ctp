<?php

	$_wrapper_ajax	= isset($_wrapper_ajax) ? $_wrapper_ajax : 'wrapper-write';
	$_flash			= isset($_flash) ? $_flash : true;
	$content		= false;

	if(!empty($layout_css)){
		$content.= $this->Html->css($layout_css);
	}

	if(!empty($_flash)){
		$flash = $this->Html->tag('div', $this->element('blocks/common/template_flash'), array(
			'class' => 'wrapper-ajax-alert',
		));
	}
	else{
		$flash = null;
	}
	
	$content.= $this->fetch('content');

	if(!empty($layout_js)){
		$content.= $this->Html->script($layout_js, array(
			'defer' => 'defer',
		));
	}

	$content = $flash.$content;

	if(!empty($_wrapper_ajax)){
		$content = $this->Html->tag('div', $content, array(
			'id' => $_wrapper_ajax,
		));
	}
	
	if(!empty($_breadcrumb)){
        echo $this->element('headers/breadcrumb');
	}
	echo($content);

?>
<?php
//		echo $this->element('blocks/common/template_flash');
//		echo $this->Html->tag('div', $this->fetch('content'), array(
//			'id' => 'wrapper-write',
//		));
?>
