<?php
        echo $this->Form->create('User', array(
            'type' => 'file',
        ));
        echo $this->element('blocks/users/director_action');
?>

<div class="sell-form">
    <?php 
            switch ($step) {
                case 'Company':
                    echo $this->element('blocks/users/forms/company');
                    break;
                
                default:
					echo $this->element('blocks/users/add_user', array(
                        'user_type' => 'Direktur',
                        'manualUploadPhoto' => true,
                        '_email' => true,
                        'options' => array(
                            'frameClass' => 'col-sm-12 col-md-8',
                        ),
					));
                    break;
            }
    ?>
</div>
<?php
    	echo $this->Form->end(); 
?>