<?php 
		$senderName 	= Common::hashEmptyField($params, 'CoBrokeUser.name');
		$senderAddress 	= Common::hashEmptyField($params, 'CoBrokeUser.address');
		$senderPhone 	= Common::hashEmptyField($params, 'CoBrokeUser.phone', false, array(
			'urldecode' => false,
		));

		$mls_id = Common::hashEmptyField($params, 'Property.mls_id');
		$title 	= Common::hashEmptyField($params, 'Property.title');

		$code 	= Common::hashEmptyField($params, 'CoBrokeProperty.code');

		$even 	= 'padding: 5px 8px;line-height: 20px;vertical-align: top;text-align: left;background-color: #f4f4f4;';
		$odd 	= $even.'background-color: transparent;';

		$title_properti = sprintf('%s - #%s', $title, $mls_id);

		$label 	= $this->Property->getNameCustom($params);
		$slug 	= $this->Rumahku->toSlug($label);

		$url 	= $this->Html->url(array(
			'controller' => 'properties', 
			'action'	 => 'detail',
			'mlsid'		 => $mls_id,
			'slug'		 => $slug, 
			'admin'		 => FALSE,
		), true);

		echo $this->Html->tag('p', sprintf(__('Penunjukan untuk melakukan follow up permintaan kerjasama co-broking dengan kode Co-Broke %s untuk properti "%s".'), $code, $title_properti), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));

		echo $this->Html->tag('p', sprintf(__('Berikut data yang di ajukan:')), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));
?>
<table style="border: 1px solid #dddddd;border-collapse: separate;width: 100%;margin-bottom: 20px;max-width: 100%;background-color: transparent;border-spacing: 0;font-family: Helvetica Neue, Helvetica, rial, sans-serif;font-size: 14px;line-height: 20px;color: #333333;">
	<tbody>
		<?php 
				$contentTr = $this->Html->tag('th', __('Nama Agen'), array(
					'style' => 'font-weight: bold;color:#303030;'.$even,
				));
				$contentTr .= $this->Html->tag('td', sprintf(': %s', $senderName), array(
					'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
				));
				echo $this->Html->tag('tr', $contentTr);

				$contentTr = $this->Html->tag('th', __('Alamat'), array(
					'style' => 'font-weight: bold;color:#303030;'.$even,
				));
				$contentTr .= $this->Html->tag('td', sprintf(': %s', $senderAddress), array(
					'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
				));
				echo $this->Html->tag('tr', $contentTr);

				$contentTr = $this->Html->tag('th', __('No. Telp'), array(
					'style' => 'font-weight: bold;color:#303030;'.$even,
				));
				$contentTr .= $this->Html->tag('td', sprintf(': %s', $senderPhone), array(
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