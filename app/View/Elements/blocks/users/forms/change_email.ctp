<?php
		$options = isset($options) ? $options : array();
        $role = !empty($role) ? $role : 'admin';
        $url = array(
            'url' => array(
                'controller' => 'users',
                'action' => 'security', 
                'change_email',
                'admin' => true,
            )
        );
        
        if( $role != 'admin' ) {
            $url['url']['admin'] = false;
            $url['url'][$role] = true;
        }

		echo $this->Html->tag('h2', __('Ganti Email'), array(
        	'class' => 'sub-heading'
        ));
        echo $this->Form->create('User', $url);
?>

<div class="row">
    <div class="col-sm-12">
    	<?php
    			echo $this->Rumahku->buildInputForm('email', array_merge($options, array(
		            'type' => 'text',
		            'label' => __('Email Baru *'),
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