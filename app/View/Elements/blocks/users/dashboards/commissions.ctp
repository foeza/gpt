<?php 
		$target_commission = $this->Rumahku->filterEmptyField($user, 'UserConfig', 'commission');

		echo $this->element('blocks/users/dashboards/chart', array(
			'url' => array(
	            	'controller' => 'reports',
	                'action' => 'commission_add',
	                'admin' => true,
    		),
            'urlTitle' => __('Lihat semua'),
            'ajaxUrl' => array(
            	'controller' => 'ajax',
                'action' => 'get_dashboard_report',
                'commissions',
            ),
            'targetCommission' => $target_commission,
            'hiddenForm' => true,
		));
?>