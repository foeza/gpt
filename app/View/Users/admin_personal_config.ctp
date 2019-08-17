<?php

	$user		= empty($user) ? array() : $user;
	$options	= empty($options) ? array() : $options;

	if($user){
		$userID			= Common::hashEmptyField($user, 'User.id');
		$parentID		= Common::hashEmptyField($user, 'User.parent_id');
		$groupID		= Common::hashEmptyField($user, 'User.group_id');
		$isIndependent	= Common::validateRole('independent_agent', $groupID);

		if($isIndependent){
			$urlBack = array(
				'admin'			=> true, 
				'controller'	=> 'users', 
				'action'		=> 'non_companies', 
			);
		}
		else{
			$urlBack = array(
				'admin'			=> true,
				'controller'	=> 'users',
				'action'		=> 'info',
				'parent_id'		=> $parentID,
				$userID,
			);
		}

		echo($this->Form->create('UserConfig', array(
			'id'	=> 'target-form',
			'type'	=> 'file',
		)));

		$elementOpts = array(
			'user'		=> $user, 
			'urlBack'	=> $urlBack, 
			'useForm'	=> false, 
		);

		if(empty($isIndependent)){
			echo($this->element('blocks/users/tabs/agent', $elementOpts));
		}

		echo($this->Html->tag('h2', __('Informasi Dasar'), array(
			'class' => 'sub-heading', 
		)));

		echo($this->element('blocks/users/forms/personal_config', $elementOpts));
		echo($this->element('blocks/users/form_action', array(
			'action_type'	=> 'bottom',
			'urlBack'		=> $urlBack,
		)));

		echo($this->Form->end());
	}

?>