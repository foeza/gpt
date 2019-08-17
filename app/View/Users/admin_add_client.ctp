<?php
        echo $this->Form->create('User', array(
            'type' => 'file',
    	));
?>
<div class="locations-root">
    <?php
            echo $this->element('blocks/users/clients/forms/client_source');
    		echo $this->element('blocks/users/add_user', array(
    			'user_type' => 'client',
                'manualUploadPhoto' => array(
                    'mandatory' => false,
                ),
                'inputAddress' => array(
                    'mandatory' => false,
                ),
                '_email' => true,
                'auth_form' => true,
                'options' => array(
                    'frameClass' => 'col-sm-12 col-md-8',
                ),
                'custom_url_back' => true,
                'value_url_back' => array(
                    'controller' => 'users',
                    'action' => 'clients',
                    'admin' => true,
                ),
    		));
    ?>
</div>
<?php
    	echo $this->Form->end(); 
?>