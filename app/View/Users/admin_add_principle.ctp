<?php
        echo $this->Form->create('User', array(
            'type' => 'file',
        ));
        echo $this->element('blocks/users/form_action');
		echo $this->Html->tag('div', $this->element('blocks/users/add_user', array(
			'user_type' => 'Principle',
            'manualUploadPhoto' => true,
            '_email' => true,
            'auth_form' => true,
            'options' => array(
                'frameClass' => 'col-sm-12 col-md-8',
            ),
		)), array(
            'class' => 'sell-form',
        ));
    	echo $this->Form->end(); 
?>