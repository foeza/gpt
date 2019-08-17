<?php 
		$message = !empty($message)?$message:false;
?>
<div id="wrapper-modal-write">
	<div id="message-box">
		<div id="contact-stop">
			<div class="centered">
				<?php 
						echo $this->Html->tag('div', $this->Html->image('/img/direct.png'), array(
							'class' => 'icon-header padding-top-30',
						));
				?>
			</div>
			<div class="message">
				<?php 
						echo $this->Html->tag('p', $message, array(
							'class' => 'text-center',
						));
				?>
			</div>
		</div>
	</div>
</div>