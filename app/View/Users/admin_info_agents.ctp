<?php
		$recordID = !empty($recordID)?$recordID:false;
		echo $this->element('blocks/users/tabs/info');
?>
<div class="tabs-box">
	<?php
			echo $this->element('blocks/users/tables/agents', array(
				'_target' => 'blank',
				'searchUrl' => array(
					'controller' => 'users',
					'action' => 'search',
					'info_agents',
					1,
					$recordID,
					'admin' => true,
				),
			));
	?>
</div>