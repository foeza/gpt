<?php
		echo $this->Form->create('UserCompany', array(
            'type' => 'file',
        ));
        echo $this->element('blocks/users/form_action', array(
        	'type' => 'principle'
        ));
        echo $this->element('blocks/users/forms/company');
        echo $this->element('blocks/users/form_action', array(
            'action_type' => 'bottom',
        ));
        echo $this->Form->end(); 
?>