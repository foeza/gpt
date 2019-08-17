<?php
 
	$model		= empty($model) ? 'Property' : $model;
	$field		= empty($field) ? 'price' : $field;
	$freetext	= isset($freetext) ? $freetext : true;
	$empty		= isset($empty) ? $empty : true;
	$options	= empty($options) ? array() : $options;

	$inputName	= array_filter(array($model, $field));
	$inputName	= implode('.', $inputName);
	$inputValue	= Common::hashEmptyField($this->data, $inputName);

	if(empty($options)){
		$globalData	= Configure::read('Global.Data');
		$options	= Common::hashEmptyField($globalData, 'price_options', array());
	}

	$inputText		= '';
	$inputOptions	= array();

//	text input
	if($freetext){
		$textInputName = array_filter(array($model, 'min_price'));
		$textInputName = implode('.', $textInputName);

		$freetextInputs = $this->Form->text($textInputName, array(
			'placeholder'		=> __('Minimum'), 
			'class'				=> 'form-control input_price', 
			'data-allow-null'	=> 'true', 
			'role'				=> 'min-value', 
		));

		$freetextInputs.= $this->Form->label(false, '-', array('class' => 'normal'));

		$textInputName = array_filter(array($model, 'max_price'));
		$textInputName = implode('.', $textInputName);

		$freetextInputs.= $this->Form->text($textInputName, array(
			'placeholder'		=> __('Maksimum'), 
			'class'				=> 'form-control input_price', 
			'data-allow-null'	=> 'true', 
			'role'				=> 'max-value', 
		));		

		$inputOptions[]	= $this->Html->tag('li', $freetextInputs, array(
			'class' => 'dropdown-text-input', 
		));
	}

//	select options
	if($empty){
		$empty		= is_bool($empty) ? 'Semua' : $empty;
		$inputText	= $empty;

		$inputOptions[]	= $this->Html->tag('li', $this->Html->link($this->Html->tag('strong', __($empty)), 'javascript:void(0);', array(
			'escape'	=> false, 
			'class'		=> $inputValue ? '' : 'selected', 
		)));
	}
	else if($options){
		$empty		= array_slice($options, 0, 1);
		$inputValue	= key($empty);
		$inputText	= array_shift($empty);
	}

	foreach($options as $optionValue => $optionText){
		$isSelected	= $inputValue == $optionValue;
		$inputText	= $isSelected ? $optionText : $inputText;

		$inputOptions[] = $this->Html->tag('li', $this->Html->link(__($optionText), 'javascript:void(0);', array(
			'escape'		=> false, 
			'data-value'	=> $optionValue, 
			'class'			=> $isSelected ? 'selected' : '', 
		)));
	}

//	render
	$inputText = $this->Html->tag('span', __($inputText), array('class' => 'title'));
	$inputText.= $this->Html->tag('span', $this->Rumahku->icon('rv4-angle-down'), array('class' => 'icon'));

	$content = $this->Html->link($inputText, 'javascript:void(0);', array(
		'escape'			=> false, 
		'class'				=> 'dropdown-toggle', 
		'data-toggle'		=> 'dropdown', 
		'aria-expanded'		=> 'false', 
		'aria-hashpopup'	=> 'true', 
		'data-empty'		=> $empty, 
	));

	$content.= $this->Html->tag('ul', implode(null, $inputOptions), array(
		'class' => 'dropdown-menu dropdown-menu-select', 
	));

	$content.= $this->Form->hidden($inputName, array(
		'class'	=> 'input-dropdown', 
		'value'	=> $inputValue, 
	));

//	wrap all
	$wrapper = empty($wrapper) ? array() : $wrapper;
	$wrapper = is_string($wrapper) ? array('class' => $wrapper) : array_replace_recursive(array(
		'class' => 'form-control dropdown-group', 
	), $wrapper);

	$wrapperTag	= Common::hashEmptyField($wrapper, 'tag', 'div');
	$wrapper	= Hash::remove($wrapper, 'tag');

	echo($this->Html->tag($wrapperTag, $content, $wrapper));

?>