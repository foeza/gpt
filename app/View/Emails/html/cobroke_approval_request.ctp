<?php 
		$input_data = Common::hashEmptyField($params, 'input_data');
		$approval 	= Common::hashEmptyField($params, 'approval');

		$senderName = Common::hashEmptyField($params, 'User.full_name');

		$mls_id 	= Common::hashEmptyField($params, 'Property.mls_id');
		$title 		= Common::hashEmptyField($params, 'Property.title');

		$code 		= Common::hashEmptyField($params, 'CoBrokeProperty.code');
		$decline 	= Common::hashEmptyField($input_data, 'CoBrokeProperty.decline');
		$decline_reason = Common::hashEmptyField($input_data, 'CoBrokeProperty.decline_reason');

		$greet 		= Common::hashEmptyField($params, 'subject');
		$request 	= Common::hashEmptyField($params, 'request');

		if(!empty($request)){
			$address = Common::hashEmptyField($params, 'PropertyAddress.address');

			$greet = sprintf(__('Agen %s telah mengajukan properti "%s, %s" menjadi properti Co-Broke.<br>Harap lakukan peninjauan pada properti yang diajukan'), $senderName, $title, $address);
		}else if(!empty($approval) && !empty($decline)){
			$greet .= sprintf(__(' dengan alasan : %s'), $decline_reason);
		}

		$even = 'padding: 5px 8px;line-height: 20px;vertical-align: top;text-align: left;background-color: #f4f4f4;';
		$odd = $even.'background-color: transparent;';

		$title_properti = sprintf('%s - #%s', $title, $mls_id);

		$label = $this->Property->getNameCustom($params);
		$slug = $this->Rumahku->toSlug($label);

		$url = $this->Html->url(array(
			'controller' => 'properties', 
			'action'	 => 'detail',
			'mlsid'		 => $mls_id,
			'slug'		 => $slug, 
			'admin'		 => FALSE,
		), true);

		echo $this->Html->tag('p', $greet, array(
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
						?>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<?php
		if(!empty($request)){
			echo $this->Html->tag('div', $this->Html->link(__('Lihat Listing'), $this->Html->url(array(
				'controller' => 'co_brokes',
				'action' => 'approval',
				'admin' => true
			), true), array(
  				'target' => '_blank',
				'style' => 'padding:10px 15px; background:#204798; color: #fff; text-decoration: none; border-radius: 3px; text-align: center;'
			)), array(
				'style' => 'display: block;margin: 20px 0px 0px;text-align: center;'
			));
		}
?>