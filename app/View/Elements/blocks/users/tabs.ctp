<?php
		$group = !empty($group) ? $group : 'admin';
		$url = array(
			'controller' => 'users', 
			'action' => 'login',
			$group => true,
		);
?>
<div class="tab-auth">
	<div class="btn-group btn-group-justified">
		<?php
				echo $this->Html->link(__('Login'), $url);
		?>
	</div>
</div>