<?php 
        $group = !empty($group) ? $group : 'admin';
        $email = $this->Rumahku->filterEmptyField($this->request->data, 'User', 'forgot_email');

        if( !empty($email) ) {
            $addClass = 'focus';
        } else {
            $addClass = '';
        }
        
        echo $this->element('blocks/users/tabs', array(
            'group' => $group
        ));
?>
<div class="row">
    <?php 
            echo $this->Form->create('User', array(
                'class' => 'login-form',
            ));
    ?>
    <div class="form-group">
        <?php 
                echo $this->Form->input('forgot_email', array(
                    'label' => false,
                    'required' => false,
                    'div' => false,
                    'error' => false,
                ));
                echo $this->Form->label('forgot_email', __('Email'), array(
                    'class' => $addClass,
                ));
        ?>
    </div>
    <?php 
            echo $this->Form->button(__('Kirim'), array(
                'type' => 'submit', 
                'class' => 'btn blue',
            ));
    ?>
    <?php 
            echo $this->Form->end(); 
    ?>
</div>