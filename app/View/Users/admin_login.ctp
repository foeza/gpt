<?php 
		$username = $this->Rumahku->filterEmptyField($this->request->data, 'User', 'username');

		if( !empty($username) ) {
			$addClass = 'focus';
		} else {
			$addClass = '';
		}
?>
<div class="row">
	<?php 
			echo $this->Form->create('User', array(
				'class'	=> 'login-form',
				'url'	=> array(
					'admin'		=> true, 
					'action'	=> 'login', 
				), 
			));
	?>
	<div class="form-group">
		<?php 
				echo $this->Form->input('username', array(
					'label' => false,
					'required' => false,
					'div' => false,
				));
				echo $this->Form->label('username', __('Username'), array(
					'class' => $addClass,
				));
		?>
	</div>
	<div class="form-group">
		<?php 
				echo $this->Form->input('password', array(
					'label' => false,
					'required' => false,
					'div' => false,
				));
				echo $this->Form->label('password', __('Password'));
		?>
	</div>
	<?php 
			echo $this->Html->link(__('Lupa Password?'), array(
				'controller'=>'users', 
				'action'=>'forgotpassword',
				'admin' => true,
			), array(
				'class' => 'forgot',
			));
			echo $this->Form->button(__('Log in'), array(
				'type' => 'submit', 
				'class' => 'btn blue',
			));
	?>
	<?php echo $this->Form->end(); ?>
</div>