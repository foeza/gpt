<?php
		$follow_up = !empty($follow_up)?$follow_up:false;
		$value = !empty($value)?$value:false;
?>
<div class="activity-list-wrapper">
	<?php 
			echo $this->Html->tag('h2', __('Catatan Aktivitas'));

			if( !empty($value) ) {
	?>
	<ul class="activity-list">
		<?php 
				echo $this->element('blocks/crm/activity_item', array(
					'activity' => $value,
					'follow_up' => $follow_up,
				));
		?>
	</ul>
	<?php 
			} else {
				echo $this->Html->tag('div', __('Tidak ada aktivitas project'), array(
					'class' => 'error-full alert',
				));
			}
	?>
</div>