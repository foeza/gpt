<?php
		$is_SA = Configure::read('User.Admin.Rumahku');
		
		$data = $this->request->data;
		$value = !empty($value)?$value:array();
		$_global_variable = !empty($_global_variable)?$_global_variable:array();

		$theme_slug = $this->Rumahku->filterEmptyField($value, 'Theme', 'slug');
		$owner_type = $this->Rumahku->filterEmptyField($value, 'Theme', 'owner_type');
		$is_bg_image = $this->Rumahku->filterEmptyField($value, 'Theme', 'is_bg_image');
		$is_bg_header = $this->Rumahku->filterEmptyField($value, 'Theme', 'is_bg_header');
		$is_img_header = $this->Rumahku->filterEmptyField($value, 'Theme', 'is_img_header');

		$default_theme_settings = $this->Rumahku->filterEmptyField($_global_variable, 'theme_colors', strtolower($theme_slug));

		$font_types = $this->Rumahku->filterEmptyField($_global_variable, 'font_type');
		$is_bg_image_footer = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_bg_footer_easyliving');
		$is_autoslideshow = $this->Rumahku->filterEmptyField($value, 'UserCompanySetting', 'is_autoslideshow');

		$themeConfigs = Common::hashEmptyField($value, 'ThemeConfig', array());

		$authGroupID	= Configure::read('User.group_id');
		$isAgent		= Common::validateRole('agent', $authGroupID);

		$optionsForm = array(	
			'wrapperClass' => false,
			'frameClass' => false,
			'labelClass' => false,
			'class' => false,
		);

		$slideinterval = array();
		$font_size = array();

		for ($i = 1; $i <= 10; $i++) { 
			$slideinterval[$i] = sprintf(__('%s Detik'), $i);
		}
		for ($i = 8; $i <= 45; $i++) { 
			$font_size[$i] = sprintf('%spx', $i);
		}
?>
<div class="container-fluid bg-custom-template">
	<div class="row">
		<div class="col-sm-3 col-md-3 no-pleft no-pright">
			<?php
					echo $this->Form->create('UserCompanySetting', array(
						'url'=> $this->Html->url( null, true ), 
						'inputDefaults' => array('div' => false),
						'type' => 'file',
					)); 
			?>
			<div class="bg-grey">
				<?php
						$action = $isAgent ? 'personal_theme_selection' : 'theme_selection';

						echo $this->Html->link($this->Rumahku->icon('rv4-cross'), array(
							'controller' => 'settings',
							'action' => $action,
							'admin' => true,
						), array(
							'escape' => false,
							'class' => 'btn-close-theme'
					   	));
						
						echo $this->Html->div('clear', '');
				?>
			</div>
			<ul id="form-custom-template">
				<?php 
						echo $this->Html->tag('h1', __('Kustomisasi Web'));

						$optionli = array(
							'class' => 'list-form-custom-template'
						);

						$caret_down = $this->Rumahku->icon('rv4-angle-right', false, 'i', 'pull-right');

						$dashboardLimits = Configure::read('Global.Data.limit_dashboard');

						$label		= __('Beranda').$caret_down;
						$content	= '';

						foreach($dashboardLimits as $field => $text){
							$fieldValue		= Common::hashEmptyField($value, sprintf('UserCompanySetting.%s', $field));
							$themeConfig	= Hash::Extract($themeConfigs, sprintf('{n}.ThemeConfig[slug=%s]', $field));
							$themeConfig	= $themeConfig && is_array($themeConfig) ? array_shift($themeConfig) : array();

							if($fieldValue && $themeConfig){
								$dataOptions = $this->Rumahku->setOPtions($themeConfig);
								$content.= $this->Rumahku->buildInputForm( $field, array_merge($optionsForm , array(
									'id'		=> $field,
									'label'		=> $text,
									'empty'		=> sprintf('Pilih %s', $text),
									'options'	=> $dataOptions,
									'type'		=> 'select',
								)));
							}
						}


						/* S: SETTING PROPERTY HIGHLIGHT */
						if ($owner_type == 'company' && $theme_slug == 'BigCity') {
							$content .= $this->Rumahku->buildInputToggle('is_recent_property', array(
								'label' => __('Recent Property'),
								'labelClass' => 'taleft col-sm-12',
								'class' => 'col-sm-12',
							));

							$content .= $this->Rumahku->buildInputToggle('is_simple_filter', array(
								'label' => __('Simple Filter'),
								'labelClass' => 'taleft col-sm-12',
								'class' => 'col-sm-12',
							));

							$content .= $this->Rumahku->buildInputToggle('is_property_highlight', array(
								'label' => __('Property Highlight'),
								'labelClass' => 'taleft col-sm-12',
								'class' => 'col-sm-12',
							));

							$field = $this->Rumahku->buildInputForm('id_status_listing', array_merge($optionsForm , array(
								'label'=> __('Pilih Kategori "Property Highlight"'),
								'inputClass' => 'form-control',
								'infoText' => __('Pilih kategori properti yang akan dijadikan sebagai "highlight"'),
								'infoClass' => false,
								'empty' => __('Pilih Kategori'),
								'options' => $category_status,
							)));

							$content .= $this->Html->div('form-group', $field);
						}
						/* E: SETTING PROPERTY HIGHLIGHT */

						if( !empty($is_bg_image) ) {
							$save_path = Configure::read('__Site.general_folder');
							$image = $this->Rumahku->filterEmptyField($data, 'UserCompanySetting', 'bg_image');
							
							$content .= $this->Rumahku->buildInputForm('UserCompanySetting.bg_image', array_merge($optionsForm , array(
								'type' => 'file',
								'label' => __('Background Image Parallax'),
								'preview' => array(
									'photo' => $image,
									'save_path' => $save_path,
									'size' => 'm',
								),
							)));
						}

						if($content){
							$content = $this->Html->tag('li', $content);

							echo $this->Rumahku->_generateMenuSide($label, false, false, $active_menu, 'setting-dashboard', $content, $optionli);

						}

						/*FONT*/
						$label = __('Font').$caret_down;
						$field = $this->Rumahku->buildInputForm('font_size', array_merge($optionsForm , array(
							'label'=> __('Ukuran Font'),
							'id' => 'changeFontSize',
							'inputClass' => 'form-control changeFont',
							'infoText' => __('Ukuran font ini digunakan sebagai standar untuk seluruh halaman website.'),
							'infoClass' => false,
							'empty' => __('Default Ukuran Font'),
							'options' => $font_size,
						)));

						$content = $this->Html->div('form-group', $field);
						$field = $this->Rumahku->buildInputForm('font_type', array_merge($optionsForm , array(
							'label'=> __('Jenis Font'),
							'id' => 'changeFontType',
							'inputClass' => 'form-control changeFont',
							'infoText' => __('Jenis font ini digunakan untuk seluruh tipe font pada website.'),
							'infoClass' => false,
							'empty' => __('Default'),
							'options' => $font_types,
						)));

						$content .= $this->Html->div('form-group', $field);

						$content = $this->Html->tag('li', $content);
						
						echo $this->Rumahku->_generateMenuSide($label, false, false, $active_menu, 'setting-font', $content, $optionli);

						if($owner_type == 'company'){
							/*SLIDESHOW*/
							$label = __('Slideshow').$caret_down;

							if(empty($this->request->data['UserCompanySetting']['slideshow_interval'])){
								$this->request->data['UserCompanySetting']['slideshow_interval'] = 3;
							}

							$content = $this->Rumahku->buildInputToggle('is_autoslideshow', array(
								'label' => __('Auto Slide'),
								'labelClass' => 'taleft col-sm-12',
								'class' => 'col-sm-12',
								'attributes' => array(
									'triggered-selector-class' => 'interval-toggle-input',
								),
							));

							$content .= $this->Rumahku->buildInputForm('slideshow_interval', array_merge($optionsForm , array(
								'id' => 'slideshowInterval',
								'label' => __('Durasi per slide *'),
								'empty' => __('Pilih Durasi Slide'),
								'options' => $slideinterval,
								'inputClass' => 'interval-toggle-input',
							)));

							$content = $this->Html->tag('li', $content);
							
							echo $this->Rumahku->_generateMenuSide($label, false, false, $active_menu, 'setting-slide', $content, $optionli);
						}

						/*Warna*/
						$label		= __('Warna').$caret_down;
						$content	= '';

					//	24 line
						$fields = array(
							'bg_color_top_header' 		=> 'Top Header',
							'bg_header'					=> 'Background Header',
							'bg_color'					=> 'Background Utama',
							'bg_footer'					=> 'Background Footer',
							'font_heading_color'		=> 'Heading Utama',
							'font_heading_footer_color'	=> 'Heading Footer',
							'main_content_color'		=> 'Warna Utama',
							'font_color'				=> 'Font Umum',
							'font_menu_color'			=> 'Font Menu',
							'font_link_color'			=> 'Font Link',
							'button_color'				=> 'Tombol',
						);

						foreach($fields as $field => $text){
							$content.= $this->Rumahku->fieldColorPicker(sprintf('UserCompanySetting.%s', $field), __($text), array(
								'class'			=> 'relative col-md-8 col-xs-12 no-pright',
								'defaultClass'	=> 'col-md-4 col-xs-12',
								'frameClass'	=> 'col-sm-12',
								'dataField'		=> $field,
								'dataDefault'	=> $default_theme_settings,
							));
						}

					//	50 line ?

						if( !empty($is_img_header) ) {
							$save_path = Configure::read('__Site.general_folder');
							$image = $this->Rumahku->filterEmptyField($data, 'UserCompanySetting', 'header_image');
							
							$content .= $this->Rumahku->buildInputForm('UserCompanySetting.header_image', array_merge($optionsForm , array(
								'type' => 'file',
								'label' => __('Bg Image Header ( 1350x150 ) *'),
								'preview' => array(
									'photo' => $image,
									'save_path' => $save_path,
									'size' => 'm',
								),
							)));
						}
						
						$content = $this->Html->tag('li', $content);
						
						echo $this->Rumahku->_generateMenuSide($label, false, false, $active_menu, 'setting-color', $content, $optionli);

						/*SETTING COPYRIGHT*/
						$label = __('Copyright').$caret_down;
						
						$content = $this->Rumahku->buildInputForm('copyright', array_merge($optionsForm , array(
							'label'=> __('Custom Copyright'),
						)));

						$content = $this->Html->tag('li', $content);

						echo $this->Rumahku->_generateMenuSide($label, false, false, $active_menu, 'setting-meta', $content, $optionli);
						/*SETTING COPYRIGHT*/

						if( !empty($is_bg_image_footer) ) {
							$label = __('Konten').$caret_down;
							$save_path = Configure::read('__Site.general_folder');
							$logoSize = $this->Rumahku->_rulesDimensionImage($save_path, 'large', 'size');
							$footer_image = $this->Rumahku->filterEmptyField($data, 'UserCompanySetting', 'footer_image');
							
							$field = $this->Rumahku->buildInputForm('UserCompanySetting.footer_image', array_merge($optionsForm , array(
								'type' => 'file',
								'label' => sprintf(__('Banner Footer ( %s ) *'), $logoSize),
								'preview' => array(
									'photo' => $footer_image,
									'save_path' => $save_path,
									'size' => 'm'
								),
							)));
							$content = $this->Html->div('form-group', $field);
							$content = $this->Html->tag('li', $content);
							echo $this->Rumahku->_generateMenuSide($label, false, false, $active_menu, 'setting-content', $content, $optionli);
						}

						echo $this->Html->tag('li', $this->Html->link('Reset', 'javascript:void(0);', array(
							'class' => 'btn red reset-form'
						)).$this->Form->button(__('Simpan dan Tayangkan'), array(
					   		'type' => 'submit',
					   		'class' => 'btn blue save-custom-template'
					   	)), array(
							'class' => 'li-reset'
						));
				?>
			</ul>
			<?php
				echo $this->Form->end(); 
			?>
		</div>
		<div class="col-sm-9 col-md-9 no-pleft no-pright iframe-box">
			<?php

				if($owner_type == 'company'){
					$domain = FULL_BASE_URL;
				}
				else{
					$domain = Common::hashEmptyField($value, 'UserConfig.personal_web_url');
				}

				if($domain){
					echo($this->Html->tag('iframe', false, array(
						'id'		=> 'iframe_preview', 
						'class'		=> 'frame', 
						'width'		=> '100%', 
						'height'	=> '728', 
						'src'		=> sprintf('%s?flash=false&theme_id=%s', $domain, $theme_id), 
					)));
				}
				else{
					echo($this->Html->tag('p', __('Domain tidak valid')));
				}

			?>
		</div>
		<?php 
				echo $this->Html->tag('span', $domain, array(
					'id' => 'base_url',
					'class' => 'hide'
				));

				echo $this->Html->tag('span', $theme_id, array(
					'id' => 'theme_id',
					'class' => 'hide'
				));
		?>
	</div>
</div>