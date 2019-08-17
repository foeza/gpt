<?php
		$options = isset($options) ? $options : array();
        $role = !empty($role) ? $role : 'admin';
        $modelName = !empty($modelName) ? $modelName : 'User';
        $url = array(
            'url' => array(
                'controller' => 'users',
                'action' => 'security', 
                'change_password',
                'admin' => true,
            )
        );

        if( $role != 'admin' ) {
            $url['url']['admin'] = false;
            $url['url'][$role] = true;
        }

		echo $this->Html->tag('h2', __('Ganti Password'), array(
            'class' => 'sub-heading'
        ));
        echo $this->Form->create($modelName, $url);
?>
<div class="row">
    <div class="col-sm-12">
        <?php
                echo $this->Rumahku->buildInputForm('current_password', array_merge($options, array(
                    'type' => 'password',
                    'label' => __('Password Lama *'),
                    'autocomplete' => 'off',
                )));
                echo $this->Rumahku->buildInputForm('new_password', array_merge($options, array(
                    'type' => 'password',
                    'label' => __('Password Baru *'),
                    'autocomplete' => 'off',
                )));
                echo $this->Rumahku->buildInputForm('new_password_confirmation', array_merge($options, array(
                    'type' => 'password',
                    'label' => __('Konfirmasi Password Baru *'),
                    'autocomplete' => 'off',
                )));
        ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group">
                <?php
	                    echo $this->Form->button(__('Simpan Perubahan'), array(
	                        'type' => 'submit', 
	                        'class'=> 'btn blue',
	                    ));
                ?>
            </div>
        </div>
    </div>
</div>
<?php
		echo $this->Form->end();
?>