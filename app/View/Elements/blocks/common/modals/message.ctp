<?php 
		$message = !empty($message)?$message:false;
?>
<div id="wrapper-modal-write">
	<div class="content">
		<?php 
				echo $this->Html->tag('p', $message, array(
					'class' => 'tacenter',
				));
		?>
	</div>
</div>