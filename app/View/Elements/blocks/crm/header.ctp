<?php 
		$completed 				= Configure::read('__Site.Global.Variable.CRM.Completed');
		$cancel 				= Configure::read('__Site.Global.Variable.CRM.Cancel');
		$id 					= $this->Rumahku->filterEmptyField($value, 'CrmProject', 'id');
		$attribute_set_id 		= $this->Rumahku->filterEmptyField($value, 'CrmProject', 'attribute_set_id');
		$name 					= $this->Rumahku->filterEmptyField($value, 'CrmProject', 'name');
		$progress 				= $this->Rumahku->filterEmptyField($value, 'CrmProject', 'progress');
		$project_date 			= $this->Rumahku->filterEmptyField($value, 'CrmProject', 'project_date');
		$completed_date 		= $this->Rumahku->filterEmptyField($value, 'CrmProject', 'completed_date');
		$is_cancel 				= $this->Rumahku->filterEmptyField($value, 'CrmProject', 'is_cancel');
		$note 					= $this->Rumahku->filterEmptyField($value, 'CrmProject', 'note');

		$payment_type 			= $this->Rumahku->filterEmptyField($value, 'CrmProjectPayment', 'type');
		$payment_id 			= $this->Rumahku->filterEmptyField($value, 'CrmProjectPayment', 'id');

		$createdBy 				= $this->Rumahku->filterEmptyField($value, 'CreatedBy', 'full_name');
        $dataKpr 				= $this->Rumahku->filterEmptyField($value, 'KprApplication');
        $count_kpr_request 		= $this->Rumahku->filterEmptyField($value,'KprApplication','count_kpr_request');
        $kpr_application_id 	= $this->Rumahku->filterEmptyField($dataKpr,'id');

		$customProjectDate 		= $this->Rumahku->formatDate($project_date, 'd M Y');
		$customCompletedDate 	= $this->Rumahku->formatDate($completed_date, 'd M Y', false);
		$lblStatus 				= $this->Crm->getStatus($value);
		$closing 				= $this->Crm->_callProjectClosing($value);

		$documentCategories 	= !empty($documentCategories)?$documentCategories:false;
		$uncompleted 			= $this->Rumahku->filterEmptyField($documentCategories, 'uncompleted');
		$lblDate = __('Tanggal project selesai');

		if( !in_array($attribute_set_id, $completed) && empty($is_cancel) ) {
			$customCompletedDate = __('(masih berjalan)');
			$completed_date = date('Y-m-d');
		} else if( !empty($is_cancel) ) {
			$lblDate = __('Tanggal project dibatalkan');
		}

		$customProjectDayCount = $this->Rumahku->dateDiff($project_date, $completed_date, 'day');
		$crmType = array(
			'detail' => __('Aktivitas'),
			'clients' => __('Daftar Klien'),
			'contract' => __('Info Project'),
			'document' => __('Dokumen'),
		);

		if( !empty($payment_type) ) {
			$crmType['payment'] = __('Pembayaran');
		}

		if(!empty($count_kpr_request)){
			$crmType['submission'] = __('Bank Submission');
		}


		echo $this->element('blocks/crm/back', array(
			'url' => array(
				'controller' => 'crm',
				'action' => 'projects',
				'admin' => true,
			),
		));
?>
<div class="detail-project-info">
	<?php 
			if( $attribute_set_id != $cancel && empty($closing) ) {
				$btnEdit = $this->AclLink->link($this->Rumahku->icon('rv4-pencil').__('Edit'), array(
					'controller' => 'crm',
					'action' => 'project_edit',
					$id,
					'admin' => true,
				), array(
					'escape' => false,
					'class' => 'edit',
					'title' => __('Edit Project'),
				));
			} else {
				$btnEdit = false;
			}

			echo $lblStatus.$btnEdit;
			echo $this->Html->tag('h1', sprintf('%s #%s', $name, $id), array(
				'class' => 'project-title',
			));
	?>
	<ul class="project-info-date">
		<?php 
				$lbl = $this->Html->tag('label', __('Dibuat oleh:'));
				$val = $this->Html->tag('span', $this->Html->tag('strong', $createdBy));
				echo $this->Html->tag('li', $lbl.$val);

				$lbl = $this->Rumahku->icon('rv4-calendar2');
				$val = $this->Html->tag('span', sprintf(__('%s %s'), $customProjectDate, $this->Html->tag('strong', sprintf(__('(%s Hari)'), $customProjectDayCount))));
				echo $this->Html->tag('li', $lbl.$val, array(
					'data-toggle' => 'tooltip',
					'title' => __('Tanggal project dimulai'),
				));

				$lbl = $this->Rumahku->icon('rv4-done');
				$val = $this->Html->tag('span', $this->Html->tag('strong', $customCompletedDate));
				echo $this->Html->tag('li', $lbl.$val, array(
					'data-toggle' => 'tooltip',
					'title' => $lblDate,
				));
		?>
	</ul>
	<?php 
			if( !empty($is_cancel) && !empty($note) ) {
				$lbl = $this->Html->tag('label', __('Keterangan Pembatalan:'));
				$val = $this->Html->tag('span', $this->Html->tag('strong', $note));
				echo $this->Html->tag('div', $lbl.$val, array(
					'class' => 'cancel-note',
				));
			}
	?>
</div>
<?php 	
		if( !empty($crm_step) && $crm_step == 'activity' && empty($closing) ) {
			echo $this->element('blocks/crm/notice');
		}
?>
<div class="crm-tab-menu detail-project-menu">
	<?php 
			if( !empty($crmType) ) {
	?>
	<ul class="desktop-only">
		<?php 
				$activeLbl = __('Aktivitas');
				$contentType = '';
				foreach ($crmType as $type => $typeName) {
					$active = '';
					$url = array(
						'controller' => 'crm',
						'action' => 'project_'.$type,
						$id,
						'admin' => true,
					);

					if($type == $active_tab) {
						$active = 'active';
						$activeLbl = $typeName;
					}

					$contentType .= $this->Html->tag('li', $this->Html->link($typeName, $url, array(
						'class' => $active,
					)));
				}

				echo $contentType;
		?>
	</ul>
	<div class="mobile-only">
		<div class="dropdown">
			<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		    	<?php 
		    			echo $activeLbl;
		    	?>
		    	<span class="caret"></span>
		  	</button>
		  	<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
		  		<?php 
						echo $contentType;
		  		?>
		  	</ul>
		  </div>
	</div>
	<?php 
			}
	?>
</div>