<?php 
        $_site_name = !empty($_site_name)?$_site_name:false;

        echo $this->element('blocks/users/tabs');
        echo $this->Form->create('User', array(
            'url' => array(
                'controller' => 'users', 
                'action' =>'register',
                'admin' => false,
            ), 
        ));
?>
<div class="login-form">
    <?php 
            echo $this->element('blocks/users/login_facebook', array(
                'data_type' => 'register',
            ));
    ?>
    <div class="form-group">
        <div class="input-group">
            <?php 
                    echo $this->Html->tag('div', $this->Rumahku->icon('user'), array(
                        'class' => 'input-group-addon',
                    ));
                    echo $this->Form->input('full_name', array(
                        'label' => false,
                        'placeholder' => __('Nama Lengkap'),
                        'required' => false,
                        'div' => false,
                        'class' => 'form-control',
                    ));
            ?>
        </div>
        <?php 
                echo $this->Form->error('full_name');
        ?>
    </div>
    <div class="form-group">
        <div class="input-group">
            <?php 
                    echo $this->Html->tag('div', $this->Rumahku->icon('envelope'), array(
                        'class' => 'input-group-addon',
                    ));
                    echo $this->Form->input('email', array(
                        'label' => false,
                        'placeholder' => __('Email'),
                        'required' => false,
                        'div' => false,
                        'class' => 'form-control',
                    ));
            ?>
        </div>
        <?php 
                echo $this->Form->error('email');
        ?>
    </div>
    <div class="form-group">
        <div class="input-group">
            <?php 
                    echo $this->Html->tag('div', $this->Rumahku->icon('lock'), array(
                        'class' => 'input-group-addon',
                    ));
                    echo $this->Form->input('password', array(
                        'label' => false,
                        'placeholder' => __('Password'),
                        'required' => false,
                        'div' => false,
                        'class' => 'form-control',
                    ));
            ?>
        </div>
        <?php 
                echo $this->Form->error('password');
        ?>
    </div>
    <div class="form-group text-center">
        <?php
                echo $this->Html->tag('small', sprintf(__('Dengan menekan tombol "Daftar", Anda setuju dengan %s yang berlaku di %s'), $this->Html->link(__('syarat dan ketentuan'), '#'), $_site_name));
        ?>
    </div>
    <div class="form-group">
        <?php
                echo $this->Form->button(__('Daftar'), array(
                    'type' => 'submit', 
                    'class'=>'btn btn-block text-white',
                ));
        ?>
    </div>
</div>
<?php echo $this->Form->end() ?>