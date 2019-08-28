<?php

	$controller	= strtolower($this->params->controller);
	$action		= strtolower($this->params->action);

	if($controller == 'pages' && $action == 'developers'){
		$controller = 'properties';
	}

	$panels = array(
		'properties' => array(
			'title'	=> 'Pencarian Properti', 
			'path'	=> 'search_property', 
			'url'	=> array(
				'admin'			=> false, 
				'controller'	=> 'properties',
				'action' 		=> 'search',
				'find',
			), 
		), 
		'users' => array(
			'title'	=> 'Pencarian Agen', 
			'path'	=> 'search_agent', 
			'url'	=> array(
				'admin'			=> false, 
				'controller'	=> 'users',
				'action' 		=> 'search',
				'agents',
			), 
		), 
		'advices' => array(
			'title'	=> 'Pencarian Artikel', 
			'path'	=> 'search_advice',
			'url'	=> array(
				'admin'			=> false, 
				'controller'	=> 'blogs',
				'action' 		=> 'search',
				'index',
			),  
		), 
		'ebrosurs' => array(
			'title'	=> 'Pencarian Ebrosur', 
			'path'	=> 'search_ebrochure', 
			'url'	=> array(
				'admin'			=> false, 
				'controller'	=> 'ebrosurs',
				'action' 		=> 'search',
				'index',
			), 
		), 
	);

	if(in_array($controller, array_keys($panels))){
		$title	= Hash::get($panels, $controller.'.title', 'Pencarian Cepat');
		$path	= Hash::get($panels, $controller.'.path', false);
		$url	= Hash::get($panels, $controller.'.url', false);

		if($path && $url){

			?>
			<div class="mobile-search-trigger">
				<?php

					echo($this->Form->button(__('Tampilkan Pencarian'), array(
						'type'			=> 'button', 
						'class'			=> 'btn btn-primary btn-block',
						'role'			=> 'search-trigger',
						'data-value'	=> 'show', 
					)));

				?>
			</div>
			<div id="mobile-search-wrapper" class="mobile-search locations-trigger">
				<div class="mobile-search-title">
					<?php

						echo($this->Html->tag('h3', __($title)));

					?>
				</div>
				<div class="mobile-search-body">
					<?php

						$options			= empty($options) ? array() : $options;
						$modelName			= Common::hashEmptyField($options, 'model_name', 'Search');
						$formOptions		= Common::hashEmptyField($options, 'form', array());
						$defaultOptions		= array(
							'url'			=> $url, 
							'role'			=> 'form', 
							'class'			=> 'form', 
							'inputDefaults'	=> array(
								'class'		=> 'form-control clearit', 
								'required'	=> false, 
								'div'		=> array(
									'class' => 'form-group', 
								), 
							),
						);

						if($formOptions && is_array($formOptions)){
							$defaultOptions = array_replace($defaultOptions, $formOptions);
						}

						$template = '';

						if(in_array($controller, array('properties', 'ebrosurs'))){
							$template = $this->Rumahku->setFormAddress($modelName);
						}

						$template.= $this->Form->create($modelName, $defaultOptions);
						$template.= $this->element(sprintf('blocks/common/forms/frontend/%s', $path));

					//	buttons
						$buttons = $this->Form->button(__('Cari'), array(
							'type'	=> 'submit', 
							'class'	=> 'btn btn-primary btn-block',
						));

						$buttons.= $this->Form->button(__('Batal'), array(
							'type'			=> 'button', 
							'class'			=> 'btn btn-default btn-block',
							'role'			=> 'search-trigger',
							'data-value'	=> 'close', 
						));

						$template.= $this->Html->tag('div', $buttons, array('class' => 'form-actions'));
						$template.= $this->Form->end();

						echo($template);

					?>
				</div>
			</div>
			<?php

		}
	}

?>