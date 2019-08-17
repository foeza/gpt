<div class="warning-full alert">
	<?php 
			echo $this->element('blocks/common/admin/alert_cancel');
			echo $this->Html->tag('p', sprintf('%s %s', $this->Html->tag('strong', __('Hmmm..')), $message), array(
				'id' => 'msg-text',
			));
			echo $this->Html->tag('div', 'error', array(
				'id' => 'msg-status',
				'class' => 'hide',
			));
	?>
</div>