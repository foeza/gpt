<?php
		$admin = $this->Rumahku->_isAdmin();
		$dataCompany = !empty($dataCompany)?$dataCompany:false;
		$recordID = !empty($recordID)?$recordID:false;
		
		$company_group_id = $this->Rumahku->filterEmptyField($dataCompany, 'User', 'group_id');

		echo $this->element('blocks/users/tabs/info');
?>
<div class="tabs-box">
	<?php
			echo $this->element('blocks/properties/tables/admin_index', array(
				'_target' => 'blank',
				'_action' => ($company_group_id == 4 && empty($admin))?false:true,
				'searchUrl' => array(
					'controller' => 'properties',
					'action' => 'search',
					'info',
					1,
					$recordID,
					'admin' => true,
				),
			));
	?>
</div>