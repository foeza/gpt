<?php 

	$data				= $this->request->data;
	$_global_variable	= !empty($_global_variable)?$_global_variable:FALSE;
	$savePath			= Configure::read('__Site.general_folder');
	$defaultColors		= $this->Rumahku->filterEmptyField($_global_variable, 'launcher_colors');
	$btnBack			= $this->Html->link(__('Kembali'), 
		array(
			'controller' => 'settings',
			'action' => 'launcher',
			'admin' => TRUE,
		), 
		array(
			'class'=> 'btn default',
		)
	);

	$body_bg	= $this->Rumahku->filterEmptyField($data, 'UserCompanyLauncher', 'body_bg_hide');
	$bgColor	= $this->Rumahku->filterEmptyField($data, 'UserCompanyLauncher', 'body_bg_color');
	$bgType		= $bgColor ? 'color' : 'image';
	$logo		= $this->Rumahku->filterEmptyField($data, 'UserCompanyLauncher', 'logo_hide');

	$defaultOptions = array(	
		'wrapperClass'	=> FALSE,
		'frameClass'	=> FALSE,
		'labelClass'	=> FALSE,
		'class'			=> FALSE,
	);

	$this->request->data['UserCompanyLauncher']['background_type'] = $bgType;

	echo($this->Form->create('UserCompanyLauncher', array('id' => 'LauncherThemeForm', 'type' => 'file')));
?>
<div class="container-fluid bg-custom-template">
	<div class="row">
		<div class="col-sm-3 col-md-3 no-pleft no-pright form-placeholder">
			<?php
				echo($this->Form->create('UserCompanyLauncher', array(
					'url'			=> $this->Html->url(NULL, TRUE), 
					'inputDefaults'	=> array('div' => FALSE),
					'type'			=> 'file',
				))); 
			?>
			<div class="bg-grey">
				<?php
					echo($this->Html->link($this->Rumahku->icon('rv4-cross'), 
						array(
							'controller' => 'settings',
							'action' => 'launcher',
							'admin' => TRUE,
						),
						array(
							'escape' => FALSE,
							'class' => 'btn-close-theme'
						)
					));

					echo($this->Html->div('clear', ''));
				?>
			</div>
			<ul id="form-custom-template">
				<?php

					echo($this->Html->tag('h1', __('Kustomisasi Launcher')));

					$activeMenu	= $active_menu;
					$childOpts	= array('class' => 'list-form-custom-template');
					$caretIcon	= $this->Rumahku->icon('rv4-angle-right', FALSE, 'i', 'pull-right');

				//	BACKGROUND LAUNCHER ===============================================================================
					$template	= '';
					$label		= __('Background Launcher').$caretIcon;

				//	background type
					$field = $this->Rumahku->buildInputForm('background_type', array_merge($defaultOptions, array(
						'id'		=> 'selBackgroundType',
						'label'		=> FALSE,
		                'options'	=> array('color' => __('Background Warna'), 'image' => __('Background Gambar')),
		            )));
					$template.= $this->Html->div('form-group', $field);

				//	background color
					$field = $this->Rumahku->fieldColorPicker('body_bg_color', NULL, array(
						'class'			=> 'relative col-md-9 col-xs-12',
						'defaultClass'	=> 'col-md-3 col-xs-12',
						'dataField'		=> 'body_bg_color',
						'dataDefault' 	=> $defaultColors,
					));
					$field.= $this->Html->tag('small', $this->Html->tag('span', 'Warna background untuk Launcher.'), array('escape' => FALSE));
					$template.= $this->Html->div('form-group '.($bgType == 'color' ? '' : 'hide'), $field, array('id' => 'bg-color-placeholder'));

				//	background image
					$field = $this->Rumahku->buildInputForm('body_bg_img', array_merge(
						$defaultOptions, 
						array(
							'type'		=> 'file',
							'label'		=> FALSE,
							'infoText'	=> __('Gambar background untuk Launcher.'),
							'infoClass'	=> FALSE,
							'preview'	=> array(
								'photo'		=> $body_bg,
								'save_path'	=> $savePath,
								'size'		=> 'm',
							),
						)
					));
					$template.= $this->Html->div('form-group '.($bgType == 'color' ? 'hide' : ''), $field, array('id' => 'bg-img-placeholder'));

					$template = $this->Html->tag('li', $template);
					echo($this->Rumahku->_generateMenuSide($label, FALSE, FALSE, $activeMenu, 'setting-background', $template, $childOpts));

				//	HEADER ============================================================================================
					$template	= '';
					$label		= __('Header').$caretIcon;

				//	logo launcher
					$field = $this->Rumahku->buildInputForm('logo', array_merge(
						$defaultOptions, 
						array(
							'type'		=> 'file',
							'label'		=> __('Logo Launcher'),
							'preview'	=> array(
								'photo'		=> $logo,
								'save_path'	=> $savePath,
								'size'		=> 'm',
							),
						)
					));
					$template.= $this->Html->div('form-group', $field);

				//	header bg color
					$field = $this->Rumahku->fieldColorPicker('header_bg', __('Warna Header'), array(
						'class'			=> 'relative col-md-9 col-xs-12',
						'defaultClass'	=> 'col-md-3 col-xs-12',
						'dataField'		=> 'header_bg',
						'dataDefault' 	=> $defaultColors,
					));
					$template.= $this->Html->div('form-group', $field);

					$template = $this->Html->tag('li', $template);
					echo($this->Rumahku->_generateMenuSide($label, FALSE, FALSE, $activeMenu, 'setting-header', $template, $childOpts));

				//	BUTTON ============================================================================================
					$template	= '';
					$label		= __('Button').$caretIcon;

				//	active button background color
					$field = $this->Rumahku->fieldColorPicker('button_active_bg', __('Warna Button Active'), array(
						'class'			=> 'relative col-md-9 col-xs-12',
						'defaultClass'	=> 'col-md-3 col-xs-12',
						'dataField'		=> 'button_active_bg',
						'dataDefault' 	=> $defaultColors,
					));
					$template.= $this->Html->div('form-group', $field);

				//	active button font color
					$field = $this->Rumahku->fieldColorPicker('button_active_color', __('Warna Font Button Active'), array(
						'class'			=> 'relative col-md-9 col-xs-12',
						'defaultClass'	=> 'col-md-3 col-xs-12',
						'dataField'		=> 'button_active_color',
						'dataDefault' 	=> $defaultColors,
					));
					$template.= $this->Html->div('form-group', $field);

				//	default button background color
					$field = $this->Rumahku->fieldColorPicker('button_bg', __('Warna Button Default'), array(
						'class'			=> 'relative col-md-9 col-xs-12',
						'defaultClass'	=> 'col-md-3 col-xs-12',
						'dataField'		=> 'button_bg',
						'dataDefault' 	=> $defaultColors,
					));
					$template.= $this->Html->div('form-group', $field);

				//	default button font color
					$field = $this->Rumahku->fieldColorPicker('button_color', __('Warna Font Button Default'), array(
						'class'			=> 'relative col-md-9 col-xs-12',
						'defaultClass'	=> 'col-md-3 col-xs-12',
						'dataField'		=> 'button_color',
						'dataDefault' 	=> $defaultColors,
					));
					$template.= $this->Html->div('form-group', $field);

				//	posisi button
					$field = $this->Rumahku->buildInputForm('button_top', array_merge($defaultOptions, array(
						'id'		=> 'selButtonTop',
						'label'		=> __('Posisi Button'),
		                'options'	=> array('top' => __('Top'), 'middle' => __('Middle'), 'bottom' => __('Bottom')),
		            )));
					$template.= $this->Html->div('form-group', $field);

					$template = $this->Html->tag('li', $template);
					echo($this->Rumahku->_generateMenuSide($label, FALSE, FALSE, $activeMenu, 'setting-button', $template, $childOpts));

				//	FOOTER ============================================================================================
					$template	= '';
					$label		= __('Footer').$caretIcon;

				//	footer background color
					$field = $this->Rumahku->fieldColorPicker('footer_bg', __('Warna Background Footer'), array(
						'class'			=> 'relative col-md-9 col-xs-12',
						'defaultClass'	=> 'col-md-3 col-xs-12',
						'dataField'		=> 'footer_bg',
						'dataDefault' 	=> $defaultColors,
					));
					$template.= $this->Html->div('form-group', $field);

				//	footer font color
					$field = $this->Rumahku->fieldColorPicker('footer_color', __('Warna Font Footer'), array(
						'class'			=> 'relative col-md-9 col-xs-12',
						'defaultClass'	=> 'col-md-3 col-xs-12',
						'dataField'		=> 'footer_color',
						'dataDefault' 	=> $defaultColors,
					));
					$template.= $this->Html->div('form-group', $field);

					$template = $this->Html->tag('li', $template);
					echo($this->Rumahku->_generateMenuSide($label, FALSE, FALSE, $activeMenu, 'setting-footer', $template, $childOpts));
					echo($this->Html->tag('li', $this->Html->link('Reset', 'javascript:void(0);', array(
			        	'class' => 'btn red reset-form'
			        )).$this->Form->button(__('Simpan dan Tayangkan'), array(
			       		'type' => 'submit',
			       		'class' => 'btn blue save-custom-template'
			       	)), array(
			        	'class' => 'li-reset'
			        )));
				?>
			</ul>
			<?php
				echo($this->Form->end());
			?>
		</div>
		<div class="col-sm-9 col-md-9 no-pleft no-pright iframe-box">
			<?php
				$frameSource = FULL_BASE_URL.'/pages/apps/'.$theme_id;
			?>
			<div class="frame-placeholder">
				<iframe id="launcher-preview" name="launcher-preview" class="frame-preview" src="<?php echo($frameSource); ?>"></iframe>
			</div>
		</div>
		<?php 
			echo($this->Html->tag('span', FULL_BASE_URL, array('id' => 'base_url', 'class' => 'hide')));
			echo($this->Html->tag('span', $theme_id, array('id' => 'theme_id','class' => 'hide')));
		?>
	</div>
</div>