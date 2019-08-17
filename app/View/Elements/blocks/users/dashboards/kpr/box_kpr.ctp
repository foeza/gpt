<?php
	
	$frameClass = !empty($frameClass)?$frameClass:false;
	$urlTitle = !empty($urlTitle)?$urlTitle:false;
	$controller = !empty($controller)?$controller:false;
	$action = !empty($action)?$action:false;

	$total_kpr = !empty($total_kpr)?$total_kpr:false;
	$cnt_pending = $this->Rumahku->filterEmptyField($total_kpr, 'cnt_pending');
	$cnt_rejected = $this->Rumahku->filterEmptyField($total_kpr, 'cnt_rejected');
	$cnt_process = $this->Rumahku->filterEmptyField($total_kpr, 'cnt_process');
	$cnt_approved_proposal = $this->Rumahku->filterEmptyField($total_kpr, 'cnt_approved_proposal');
	$cnt_approved = $this->Rumahku->filterEmptyField($total_kpr, 'cnt_approved');

	echo $this->element('blocks/users/dashboards/box', array(
		'frameClass' => $frameClass,
		'class' => 'greyclr',
		'icon' => 'rv4-kpr',
		'value' => $cnt_pending,
		'title' => __('KPR Pending'),
		'url' => array(
			'controller' => $controller,
			'action' => $action,
			'status' => 'pending',
			'admin' => true,
		),
		'urlTitle' => $urlTitle,
	));
	echo $this->element('blocks/users/dashboards/box', array(
		'frameClass' => $frameClass,
		'class' => 'redclr',
		'icon' => 'rv4-trash',
		'value' => $cnt_rejected,
		'title' => __('KPR Ditolak'),
		'url' => array(
			'controller' => $controller,
			'action' => $action,
			'status' => 'rejected',
			'admin' => true,
		),
		'urlTitle' => $urlTitle,
	));
	echo $this->element('blocks/users/dashboards/box', array(
		'frameClass' => $frameClass,
		'class' => 'yellowclr',
		'icon' => 'rv4-doc-list',
		'value' => $cnt_process,
		'title' => __('KPR Proses'),
		'url' => array(
			'controller' => $controller,
			'action' => $action,
			'status' => 'half_rejected',
			'admin' => true,
		),
		'urlTitle' => $urlTitle,
	));
	echo $this->element('blocks/users/dashboards/box', array(
		'frameClass' => $frameClass,
		'class' => 'blueclr',
		'icon' => 'rv4-done',
		'value' => $cnt_approved_proposal,
		'title' => __('Referral Disetujui'),
		'url' => array(
			'controller' => $controller,
			'action' => $action,
			'status' => 'approved_proposal',
			'admin' => true,
		),
		'urlTitle' => $urlTitle,
	));
	echo $this->element('blocks/users/dashboards/box', array(
		'frameClass' => $frameClass,
		'class' => 'greenclr',
		'icon' => 'rv4-klien',
		'value' => $cnt_approved,
		'title' => __('KPR Setujui'),
		'url' => array(
			'controller' => $controller,
			'action' => $action,
			'status' => 'approved',
			'admin' => true,
		),
		'urlTitle' => $urlTitle,
	));

?>