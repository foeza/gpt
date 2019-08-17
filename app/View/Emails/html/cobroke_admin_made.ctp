<?php
		$type_commision = Configure::read('Config.Type.CoBroke.Commission');

		$mls_id 						= Common::hashEmptyField($params, 'Property.mls_id');
		$title 							= Common::hashEmptyField($params, 'Property.title');
		$commission 					= Common::hashEmptyField($params, 'Property.commission');
		$type_co_broke_commission 		= Common::hashEmptyField($params, 'Property.type_co_broke_commission');
		$co_broke_commision 			= Common::hashEmptyField($params, 'Property.co_broke_commision');
		$type_price_co_broke_commision 	= Common::hashEmptyField($params, 'Property.type_price_co_broke_commision');

		$property_cobroke_commission = $this->CoBroke->commissionName($co_broke_commision, $type_co_broke_commission, $type_price_co_broke_commision);

		$senderName = Common::hashEmptyField($params, 'User.full_name');

		$title_properti = sprintf('%s - #%s', $title, $mls_id);

		$even = 'padding: 5px 8px;line-height: 20px;vertical-align: top;text-align: left;background-color: #f4f4f4;';
		$odd = $even.'background-color: transparent;';

		$label = $this->Property->getNameCustom($params);
		$slug = $this->Rumahku->toSlug($label);

		$url = $this->Html->url(array(
			'controller' => 'properties', 
			'action'	 => 'detail',
			'mlsid'		 => $mls_id,
			'slug'		 => $slug, 
			'admin'		 => FALSE,
		), true);

		echo $this->Html->tag('p', sprintf(__('Properti Anda dengan id "%s" telah dijadikan Co-Broke oleh Admin/Principal Anda'), $title_properti), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));
?>
<table class="message-content" cellspacing="0" cellpadding="0" width="100%" style="border: 1px solid #cccccc; border-radius: 3px;">
	<tbody>
		<tr>
			<td>
				<?php 
						echo $this->element('emails/html/properties/info');
				?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
				<table style="border: 1px solid #dddddd;border-collapse: separate;width: 100%;max-width: 100%;background-color: transparent;border-spacing: 0;font-family: Helvetica Neue, Helvetica, rial, sans-serif;font-size: 14px;line-height: 20px;color: #333333;">
					<tbody>
						<?php 
								$contentTr = $this->Html->tag('th', __('Nama Agen'), array(
									'style' => 'font-weight: bold;color:#303030;'.$even,
								));
								$contentTr .= $this->Html->tag('td', sprintf(': %s', $senderName), array(
									'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
								));
								echo $this->Html->tag('tr', $contentTr);

								$contentTr = $this->Html->tag('th', __('Properti'), array(
									'style' => 'font-weight: bold;color:#303030;'.$even,
								));
								$contentTr .= $this->Html->tag('td', sprintf(': %s', $this->Html->link($title_properti, $url)), array(
									'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
								));
								echo $this->Html->tag('tr', $contentTr);

								$contentTr = $this->Html->tag('th', __('Komisi Agen'), array(
									'style' => 'font-weight: bold;color:#303030;'.$even,
								));
								$contentTr .= $this->Html->tag('td', sprintf(': %s%% dari Penjualan Properti', $commission), array(
									'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
								));
								echo $this->Html->tag('tr', $contentTr);

								$contentTr = $this->Html->tag('th', __('Komisi Broker'), array(
									'style' => 'font-weight: bold;color:#303030;'.$even,
								));
								$contentTr .= $this->Html->tag('td', sprintf(': %s', $property_cobroke_commission), array(
									'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
								));
								echo $this->Html->tag('tr', $contentTr);
						?>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>