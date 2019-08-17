<?php
        echo $this->Form->create('User', array(
            'type' => 'file',
            'id' => 'add-user'
    	));

        echo $this->element('blocks/users/tabs/agent');

		echo $this->element('blocks/users/add_user', array(
			// 'user_type' => 'user',
            'manualUploadPhoto' => true,
            '_email' => true,
            'auth_form' => true,
            'options' => array(
                'frameClass' => 'col-sm-12 col-md-8',
            ),
		));
    	echo $this->Form->end(); 
?>