<?php

		$text = Common::hashEmptyField($params, 'text');
		$is_rollback = Common::hashEmptyField($params, 'is_rollback');
		$rollback_reason = Common::hashEmptyField($params, 'rollback_reason');
		$is_property = Common::hashEmptyField($params, 'UserActivedAgent.is_property');
		$userAgentDetails = Common::hashEmptyField($params, 'UserActivedAgentDetail');
		$company_name = Common::hashEmptyField($params, 'Decline.UserCompany.name');
		$assign_name = Common::hashEmptyField($params, 'Assign.full_name');
		$group_id = Common::hashEmptyField($params, 'Decline.Group.id');
		$groupName = Common::hashEmptyField($params, 'Decline.Group.name');
		$groupName = strtolower($groupName);

		echo $this->Html->tag('h2', __('Selamat bergabung kembali.'), array(
			'style' => 'color: #303030; font-size: 16px; margin: 20px 0; line-height: 20px;'
		));

		// if($rollback_reason){
		// 	echo $this->Html->tag('p', __('Keterangan: %s', $this->Html->tag('strong', $rollback_reason)), array(
		// 		'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		// 	));
		// }

		echo $this->Html->tag('p', __('Terhitung sejak kami mengirimkan email ini, Anda dapat kembali beraktifitas sebagai %s dari %s', $groupName, $company_name), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;',
		));

		if($group_id == 2){
			echo $this->Html->tag('p', sprintf(__('Untuk selanjutnya anda dapat beraktifitas sebagai agen/marketing di  %s & %s'), $this->Html->link(Configure::read('__Site.site_default'), Configure::read('__Site.site_default'), array(
					'style' => 'color: #0462F0; font-size: 14px; margin: 0; padding: 0;',
					'target' => '_blank',
				)), $this->Html->link(FULL_BASE_URL, FULL_BASE_URL, array(
					'style' => 'color: #0462F0; font-size: 14px; margin: 0; padding: 0;',
					'target' => '_blank',
				))), array(
				'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
			));
		}

		if(!empty($is_rollback)){
			echo $this->Html->tag('p', __('Seluruh data properti dan klien yang sebelumnya dialihkan kepada %s sudah kami kembalikan ke akun Anda.', $assign_name), array(
				'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
			));
		}

		// if($is_property){
		// 	echo $this->Html->tag('p', __('Berikut data properti milik anda :'), array(
		// 		'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		// 	));
		// 	if(!empty($userAgentDetails)){
?>
<!-- <div style="margin-top:20px;">
	<table align="center" width="100%" style="background:#fff" cellpadding="0" cellspacing="0" border="0">
	  	<tbody>
	  		<?php
	  				foreach ($userAgentDetails as $key => $detail) {
	  		?>
	  		<tr>
		    	<td style="padding: 0 20px;">
		      		<?php
							echo $this->element('emails/html/properties/info', array(
								'params' => $detail,
							));
					?>
		    	</td>
		  	</tr>
		  	<?php
		  			}
		  	?>
	 	</tbody>
	</table>
</div -->
<?php
		// 	}
		// }
?>