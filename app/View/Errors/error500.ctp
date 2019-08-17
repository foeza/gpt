<?php 
		$debug = Configure::read('debug');

		if( empty($debug) ) {
			echo $this->Html->tag('div', $this->Html->image('/img/maintenance-microsite.jpg', array(
				'style' => 'max-width: 100%;',
			)), array(
				'style' => 'margin: 0;overflow-y: hidden;background-color: #FFF;',
			));
		} else {
?>
<h2><?php echo $message; ?></h2>
<p class="error">
	<strong><?php echo __d('cake', 'Error'); ?>: </strong>
	<?php echo __d('cake', 'An Internal Error Has Occurred.'); ?>
</p>
<?php
			if (Configure::read('debug') > 0):
				echo $this->element('exception_stack_trace');
			endif;
		}
?>
