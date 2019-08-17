<div class="alert alert-info hidden-print bg-red">
	<?php 
			$message = empty($message) ? false : $message;

			echo $this->element('flash_content', array(
				'message' => $message,
			));
	?>
</div>