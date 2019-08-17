<?php 
		echo $this->Form->create('User', array(
			'inputDefaults' => array('div' => false)
		));
?>
<div class="form-group">
    <?php 
    		$label = __('Password Baru');
            echo $this->Form->input('new_password', array(
				'type'=> 'password', 
                'label' => $label,
                'placeholder' => $label,
                'required' => false,
                'div' => false,
                'class' => 'form-control',
            ));
    ?>
</div>
<div class="form-group">
    <?php 
    		$label = __('Konfirmasi Password');
            echo $this->Form->input('new_password_confirmation', array(
				'type'=> 'password', 
                'label' => $label,
                'placeholder' => $label,
                'required' => false,
                'div' => false,
                'class' => 'form-control',
            ));
    ?>
</div>

<div class="form-group">
	<?php 
			echo $this->Form->button(__('Reset Password'), array(
				'type' => 'submit', 
				'class'=> 'btn btn-block text-white'
			)); 
	?>
</div>
<?php echo $this->Form->end();?>