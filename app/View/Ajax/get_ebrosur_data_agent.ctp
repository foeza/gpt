<?php
		$user_name = $this->Rumahku->filterEmptyField($user, 'User', 'full_name', '');
		$phone = $this->Rumahku->filterEmptyField($user, 'UserProfile', 'no_hp', '');
		$userPhoto = $this->Rumahku->filterEmptyField($user, 'User', 'photo');
		$image = $this->Html->image('/img/view/errors/gent_ps.jpg');

		if( !empty($userPhoto) ) {
			$image = $this->Rumahku->photo_thumbnail(array(
                'save_path' => Configure::read('__Site.profile_photo_folder'), 
                'src'=> $userPhoto, 
                'size' => 'ps',
                'url' => true
            ));
		}

		echo $this->Html->tag('div', $this->Html->image($image), array(
			'id' => 'agent-photo-ebrosur'
		));
		echo $this->Html->tag('div', $userPhoto, array(
			'id' => 'agent-url-photo-ebrosur'
		));
		echo $this->Html->tag('div', $user_name, array(
			'id' => 'agent-name-ebrosur'
		));
		echo $this->Html->tag('div', $phone, array(
			'id' => 'agent-phone-ebrosur'
		));
?>