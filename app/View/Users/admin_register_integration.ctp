<?php
        echo $this->Form->create('UserIntegratedOrder', array(
            'type' => 'file',
    	));
		echo $this->element('blocks/users/add_user', array(
            'custom_form' => true,
            'path_form' => 'blocks/users/forms/register_integration',
            'custom_url_back' => true,
            'value_url_back' => $value_url_back,
            'inputAddress' => array(
                'mandatory' => false,
            ),
            'options' => array(
                'frameClass' => 'col-sm-12 col-md-12',
                'labelClass' => 'col-sm-2 col-xl-2 control-label taright',
                'class' => 'col-sm-5 col-xl-4 relative'
            ),
		));
    	echo $this->Form->end(); 
?>