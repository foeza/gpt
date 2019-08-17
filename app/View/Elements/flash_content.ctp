<?php 
		$message = empty($message) ? false : $message;
		echo $this->Html->tag('p', $message);
?>