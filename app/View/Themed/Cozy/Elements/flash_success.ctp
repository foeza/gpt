<div class="alert alert-success hidden-print">
	<?php 
			echo $this->element('blocks/common/alert_cancel');
			echo $this->Html->tag('p', sprintf('%s %s', $this->Html->tag('strong', __('Selamat!')), $message), array(
				'id' => 'msg-text',
			));
			echo $this->Html->tag('div', 'success', array(
				'id' => 'msg-status',
				'class' => 'hide',
			));
	?>
</div>