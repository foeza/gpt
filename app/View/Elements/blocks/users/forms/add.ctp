<?php 
		$user = !empty($user)?$user:false;
		$username_disabled = $this->Rumahku->filterEmptyField($user, 'UserConfig', 'username_disabled');

		if( $user_type != 'client' ) {
			echo $this->Rumahku->buildInputForm('username', array_merge($options, array(
	            'label' => __('Username *'),
	            'infoText' => __('Anda hanya dapat melakukan perubahan username sebanyak 1 kali'),
	            'disabled' => $username_disabled,
	        )));
        }
        echo $this->Rumahku->buildInputForm('email', array_merge($options, array(
            'label' => __('Email *'),
            'autocomplete' => 'off',
        )));
?>