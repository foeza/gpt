<div class="alert alert-danger hidden-print">
	<?php 
			echo $this->element('blocks/common/alert_cancel');
			echo $this->Html->tag('p', sprintf('%s %s', $this->Html->tag('strong', __('Uppss!')), $message), array(
				'id' => 'msg-text',
			));
			echo $this->Html->tag('div', 'error', array(
				'id' => 'msg-status',
				'class' => 'hide',
			));
	?>
</div>