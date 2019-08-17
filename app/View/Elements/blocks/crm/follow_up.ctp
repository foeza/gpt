<?php 
		$followup_id = $this->Rumahku->filterEmptyField($dataFollowup, 'id');
		$note = $this->Rumahku->filterEmptyField($dataFollowup, 'note');
		$activity_date = $this->Rumahku->filterEmptyField($dataFollowup, 'activity_date');
		$activity_time = $this->Rumahku->filterEmptyField($dataFollowup, 'activity_time');

		$customActivityDate = $this->Rumahku->formatDate($activity_date, 'd M Y');
		$customActivityTime = $this->Rumahku->formatDate($activity_time, 'H:i');
		$customNote = str_replace(PHP_EOL, '<br>', $note);
?>
<div class="follow-up">
	<?php 
			echo $this->Html->tag('span', sprintf('%s - %s', $customActivityDate, $customActivityTime), array(
				'class' => 'date',
			));

			$contentNote = $this->Html->tag('label', __('Catatan:'));
			$contentNote .= $this->Html->tag('p', $customNote.$this->Html->link(__(' Edit'), array(
				'controller' => 'crm',
				'action' => 'edit_followup',
				$followup_id,
				'admin' => true,
			), array(
				'class' => 'ajaxModal',
				'title' => __('Edit Follow Up Aktivitas'),
			)));
			echo $this->Html->tag('div', $contentNote, array(
				'class' => 'note',
			));
	?>
</div>