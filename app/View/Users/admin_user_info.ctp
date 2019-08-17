<?php
		$admin = $this->Rumahku->_isAdmin();
		$dataCompany = !empty($dataCompany)?$dataCompany:false;
		$recordID = !empty($recordID)?$recordID:false;
		$self = !empty($self)?$self:false;
		$tab = !empty($tab) ? $tab : false;

		$company_group_id = $this->Rumahku->filterEmptyField($dataCompany, 'User', 'group_id');

		$searchUrl = array(
			'controller' => 'users',
			'action' => 'search',
			'user_info',
			1,
			'admin' => true,
		);	

		if(empty($self)){
			$searchUrl[] = $recordID;
		}

		if(!empty($tab)){
			echo $this->element('blocks/users/tabs/info');
		}
?>
<div class="tabs-box">
	<?php
			echo $this->element('blocks/users/tables/agents', array(
				'searchUrl' => $searchUrl,
			));
	?>
</div>