
<div class="activity-list-wrapper mt15">
	<?php 
			echo $this->Html->tag('h2', __('Catatan Aktivitas'));

			if( !empty($activities) ) {
	?>
	<ul class="activity-list">
		<?php 
				foreach ($activities as $key => $activity) {
					echo $this->element('blocks/crm/activity_item', array(
						'activity' => $activity,
					));
				}
		?>
	</ul>
	<?php 
			} else {
				echo $this->Html->tag('div', $this->Html->tag('div', __('Tidak ada aktivitas project'), array(
					'class' => 'warning-full alert',
				)), array(
					'class' => 'mt15',
				));
			}
			
			echo $this->element('blocks/common/pagination');
	?>
</div>