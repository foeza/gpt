<?php 
		$follow_up = !empty($follow_up)?$follow_up:false;
		$value = !empty($value)?$value:false;

		$activity_id = $this->Rumahku->filterEmptyField($activity, 'CrmProjectActivity', 'id');
		$note = $this->Rumahku->filterEmptyField($activity, 'CrmProjectActivity', 'note');
		$created = $this->Rumahku->filterEmptyField($activity, 'CrmProjectActivity', 'created');

		$dataFollowup = $this->Rumahku->filterEmptyField($activity, 'CrmProjectActivityFollowup');
		$step = $this->Rumahku->filterEmptyField($activity, 'CrmProjectActivity', 'step');

		$bg_color = $this->Rumahku->filterEmptyField($activity, 'AttributeSet', 'bg_color', '#FFF');
		$client = $this->Rumahku->filterEmptyField($activity, 'UserClient', 'full_name');

		$customCreated = $this->Rumahku->formatDate($created, 'd M Y - H:i');
		$lblStyle = sprintf('border-color: %s;', $bg_color);

		$documents = $this->Rumahku->filterEmptyField($activity, 'CrmProjectDocument');
		$closing = $this->Crm->_callProjectClosing($value);
?>
<li>
	<div class="row">
		<div class="col-sm-1 pl0 bullet">
			<div class="activity-status">
				<?php 
						echo $this->Html->tag('div', '', array(
							'class' => 'bullet',
							'style' => $lblStyle,
						));
				?>
			</div>
		</div>
		<div class="col-sm-9 col-xs-11 pl0 wrapper-activity">
			<div class="activity-content">
				<div class="line"></div>
				<div class="tag-header">
					<?php 
							echo $this->Html->tag('span', $customCreated, array(
								'class' => 'date',
							));

							if( !empty($client) ) {
								echo $this->Html->tag('span', sprintf(__('Klien: %s'), $this->Html->tag('span', $client)), array(
									'class' => 'client',
								));
							}
					?>
				</div>
				<?php
						echo $this->Html->tag('div', $this->element('blocks/crm/attributes_info', array(
							'activity' => $activity,
						)), array(
							'class' => 'mb15',
						));

						// echo $this->element('blocks/crm/kpr_info',array(
						// 	'activity' => $activity,
						// 	));

						echo $this->element('blocks/crm/payment_info', array(
							'activity' => $activity,
						));

						if( !empty($note) ) {
							$customNote = str_replace(PHP_EOL, '<br>', $note);
							$contentNote = $this->Html->tag('label', __('Catatan:'));
							$contentNote .= $this->Html->tag('p', $customNote);
							echo $this->Html->tag('div', $contentNote, array(
								'class' => 'note',
							));
						}

						echo $this->element('blocks/crm/documents', array(
							'values' => $documents,
							'value' => $value,
						));

						if( empty($follow_up) && empty($closing) && $step != 'change_status' ) {
							if( empty($dataFollowup) ) {
								echo $this->Html->link(__('Follow Up'), array(
									'controller' => 'crm',
									'action' => 'follow_up',
									$activity_id,
									'admin' => true,
								), array(
									'class' => 'btn default ajaxModal',
									'title' => __('Follow Up Aktivitas'),
								));
							} else {
								echo $this->element('blocks/crm/follow_up', array(
									'dataFollowup' => $dataFollowup,
								));
							}
						}
				?>
			</div>
		</div>
	</div>
</li>