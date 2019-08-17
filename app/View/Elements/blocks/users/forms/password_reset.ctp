<?php
        $modelName = !empty($modelName) ? $modelName : 'User';
?>
<div class="row">
    <?php 
            echo $this->Form->create($modelName, array(
                'class' => 'login-form',
            ));
    ?>
    <div class="form-group">
        <?php 
                echo $this->Form->input('new_password', array(
                	'type' => 'password',
                    'label' => false,
                    'required' => false,
                    'div' => false,
                ));
                echo $this->Form->label('new_password', __('Password Baru'));
        ?>
    </div>
    <div class="form-group">
        <?php 
                echo $this->Form->input('new_password_confirmation', array(
                	'type' => 'password',
                    'label' => false,
                    'required' => false,
                    'div' => false,
                ));
                echo $this->Form->label('new_password_confirmation', __('Konfirmasi Password'));
        ?>
    </div>
    <?php 
            echo $this->Form->button(__('Reset Password'), array(
                'type' => 'submit', 
                'class' => 'btn blue',
            ));
    ?>
    <?php echo $this->Form->end(); ?>
</div>