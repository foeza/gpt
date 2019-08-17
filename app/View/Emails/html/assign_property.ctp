<?php
		$id = Common::hashEmptyField($params, 'id');
		$decline_name = Common::hashEmptyField($params, 'Decline.User.full_name');
		$assign_name = Common::hashEmptyField($params, 'Assign.User.full_name');

		$property_count = Common::hashEmptyField($params, 'UserActivedAgent.property_count');
		$client_count = Common::hashEmptyField($params, 'UserActivedAgent.client_count');

	  	$userAgentDetails = Common::hashEmptyField($params, 'UserActivedAgentDetail');

		echo $this->Html->tag('h2', __('Anda telah mendapatkan data properti dan klien dari agen %s', $decline_name), array(
			'style' => 'color: #303030; font-size: 16px; margin: 20px 0; line-height: 20px;'
		));

		echo $this->Html->tag('p', __('Agen %s telah dinon-aktifkan. Data properti yang berkaitan dengan agen tersebut saat ini telah dialihkan kepada Anda. Namun apabila agen %s diaktifkan kembali, maka data akan dikembalikan kepada agen %s seperti sedia kala.', $decline_name, $decline_name, $decline_name), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));

		if( $property_count && !empty($userAgentDetails) ){
			echo $this->Html->tag('p', __('Berikut %s properti yang alihkan kepada Anda:', $property_count), array(
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
				'status' => 'assign',
                'document_id' => $id,
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
			echo $this->Html->tag('p', __('Berikut %s klien yang alihkan kepada Anda:', $client_count), array(
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
?>