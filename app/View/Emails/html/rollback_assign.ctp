<?php

		$decline_name = Common::hashEmptyField($params, 'Decline.full_name');
		$assign_name = Common::hashEmptyField($params, 'Assign.full_name');

		$is_property = Common::hashEmptyField($params, 'UserActivedAgent.is_property');
		$userAgentDetails = Common::hashEmptyField($params, 'UserActivedAgentDetail');

		$property_count = Common::hashEmptyField($params, 'UserActivedAgent.property_count');
		$client_count = Common::hashEmptyField($params, 'UserActivedAgent.client_count');

		echo $this->Html->tag('h2', __('%s telah diaktifkan kembali', $decline_name), array(
			'style' => 'color: #303030; font-size: 16px; margin: 20px 0; line-height: 20px;'
		));

		echo $this->Html->tag('p', __('Properti dan klien milik %s yang sebelumnya dialihkan kepada anda, saat ini telah dikembalikan', $this->Html->tag('srtong', $decline_name)), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));

		if( $property_count && !empty($userAgentDetails) ){
			echo $this->Html->tag('p', __('Berikut %s properti yang dikembalikan kepada agen %s :', $property_count, $this->Html->tag('srtong', $decline_name)), array(
				'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
			));
?>
<div style="margin-top:20px;">
	<table align="center" width="100%" style="background:#fff" cellpadding="0" cellspacing="0" border="0">
	  	<tbody>
	  		<?php
	  				foreach ($userAgentDetails as $key => $detail) {
	  					if($key >= 2){
	  						break;
	  					}
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
	<?php
			$url = $this->Html->url(array(
				'controller' => 'properties',
				'action' => 'index',
				'admin' => true,
			), true);

			echo $this->Html->tag('p', $this->Html->link(__('Selengkapnya'), $url, array(
				'target' => '_blank',
				'style' => "font-size: 14px; margin: 5% 0 20%; line-height: 20px;",
			)), array(
				'style' => 'text-align: "center";',
			));
	?>
</div>
<?php
		}

		if($client_count && !empty($userAgentDetails)){
			echo $this->Html->tag('p', __('Berikut %s klien yang dikembalikan kepada agen %s :', $client_count, $this->Html->tag('srtong', $decline_name)), array(
				'style' => 'color: #303030; font-size: 14px; margin-top: 20px; line-height: 20px;'
			));
?>
		<table align="center" width="100%" style="background:#fff" cellpadding="0" cellspacing="0" border="1">
	  		<tbody>
	  			<tr>
	  				<th style="padding: 0 20px;">
						NAMA
	  				</th>
	  				<th style="padding: 0 20px;">
						EMAIL
	  				</th>
	  				<th style="padding: 0 20px;">
						NO HP
	  				</th>
	  			</tr>
	  			<?php
	  					$idx = 0;
	  					foreach ($userAgentDetails as $key => $detail) {
	  						$type = Common::hashEmptyField($detail, 'UserActivedAgentDetail.type');

	  						if($type == 'UserClient') {

	  							if($idx >= 2){ break; }
	  							$email = Common::hashEmptyField($detail, 'User.email');
	  							$no_hp = Common::hashEmptyField($detail, 'UserClient.no_hp', false, array(
									'urldecode' => false,
								));
	  							$full_name = Common::hashEmptyField($detail, 'UserClient.full_name');

		  						$td = $this->Html->tag('td', sprintf('&nbsp; %s', $full_name));
		  						$td .= $this->Html->tag('td', sprintf('&nbsp; %s', $email));
		  						$td .= $this->Html->tag('td', sprintf('&nbsp; %s', $no_hp));

		  						echo $this->Html->tag('tr', $td);
		  						$idx++;
	  						}
	  					}
	  			?>
	  		</tbody>
	  	</table>
<?php
				$url = $this->Html->url(array(
					'controller' => 'users',
					'action' => 'client_info',
					'admin' => true,
				), true);

				echo $this->Html->tag('p', $this->Html->link(__('Selengkapnya'), $url, array(
					'target' => '_blank',
					'style' => "font-size: 14px; margin: 5% 0 20%; line-height: 20px;",
				)), array(
					'style' => 'text-align: "center";',
				));
		}

		echo $this->Html->tag('p', __('Terhitung sejak email ini dikirimkan, anda tidak mempunyai hak terhadap data tersebut'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));
?>