<div class="user-fill">
	<?php

		$user		= empty($user) ? array() : $user;
		$options	= empty($options) ? array() : $options;
		$urlBack	= empty($urlBack) ? false : $urlBack;

		if($user){
			$useForm = Common::hashEmptyField($options, 'use_form');

			if($useForm){
				$formOptions = Common::hashEmptyField($options, 'form', array());
				$formOptions = array_replace_recursive(array(
					'id' => 'target-form',
				), (array) $formOptions);

				echo($this->Form->create('UserConfig', $formOptions));
			}

			$logoPath	= Configure::read('__Site.logo_photo_folder');
			$logoSize	= $this->Rumahku->_rulesDimensionImage($logoPath, 'large', 'size');
			$logo		= Common::hashEmptyField($this->data, 'UserConfig.logo_hide');

			echo($this->Rumahku->buildInputForm('logo', array(
				'label'		=> sprintf(__('Logo ( %s )'), $logoSize),
				'type'		=> 'file',
				'class'		=> 'col-sm-6 col-xl-4',
				'preview'	=> array(
					'photo'		=> $logo,
					'save_path'	=> $logoPath,
					'size'		=> 'xxsm',
				),
			)));

			$themes		= empty($themes) ? array() : $themes;
			$packages	= empty($packages) ? array() : $packages;

			echo($this->Rumahku->buildInputForm('theme_id', array(
				'label'		=> __('Tema'),
				'empty'		=> __('Pilih Tema'),
				'options'	=> $themes,
				'class'		=> 'col-sm-6 col-xl-4',
			)));

			echo($this->Rumahku->buildInputForm('membership_package_id', array(
				'label'		=> __('Paket Membership'),
				'empty'		=> __('Pilih Paket Membership'),
				'options'	=> $packages,
				'class'		=> 'col-sm-6 col-xl-4',
			)));

			echo($this->Rumahku->buildInputForm('date', array(
				'label'				=> __('Masa Aktif'),
				'type'				=> 'text',
				'class'				=> 'col-sm-6 col-xl-4',
				'formGroupClass'	=> 'form-group input-text-center',
				'inputClass'		=> 'date-range', 
			)));

			echo($this->Rumahku->buildInputForm('personal_web_url', array(
				'label'			=> __('Domain'),
				'placeholder'	=> __('Masukkan Domain'),
				'type'			=> 'text',
				'class'			=> 'col-sm-6 col-xl-4',
			)));

			if($useForm){
				echo($this->element('blocks/users/form_action', array(
					'action_type'	=> 'bottom',
					'urlBack'		=> $urlBack,
				)));

				echo($this->Form->end());
			}
		}
		else{
			echo($this->Html->tag('p', __('Data tidak ditemukan')));
		}

	?>
</div>