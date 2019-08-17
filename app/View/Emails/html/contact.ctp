<?php 
		$senderName = $this->Rumahku->filterEmptyField($params, 'Contact', 'name');
		$senderEmail = $this->Rumahku->filterEmptyField($params, 'Contact', 'email');
		$senderPhone = $this->Rumahku->filterEmptyField($params, 'Contact', 'phone');
		$senderMessage = $this->Rumahku->filterEmptyField($params, 'Contact', 'message');
		$messageSubject = $this->Rumahku->filterEmptyField($params, 'Contact', 'subject');

		$even = 'padding: 5px 8px;line-height: 20px;vertical-align: top;text-align: left;background-color: #f4f4f4;';
		$odd = $even.'background-color: transparent;';

		echo $this->Html->tag('p', sprintf(__('Anda mendapatkan pesan baru dari %s di %s.'), $senderName, FULL_BASE_URL), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));
?>
<table style="border: 1px solid #dddddd;border-collapse: separate;width: 100%;margin-bottom: 20px;max-width: 100%;background-color: transparent;border-spacing: 0;font-family: Helvetica Neue, Helvetica, rial, sans-serif;font-size: 14px;line-height: 20px;color: #333333;">
	<tbody>
		<?php 
				if(!empty($params['subject'])){
					$contentTr = $this->Html->tag('th', __('Subject'), array(
						'style' => 'font-weight: bold;color:#303030;'.$even,
					));
					$contentTr .= $this->Html->tag('td', sprintf(': %s', $messageSubject), array(
						'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
					));
					echo $this->Html->tag('tr', $contentTr);
				}

				$contentTr = $this->Html->tag('th', __('Pengirim'), array(
					'style' => 'font-weight: bold;color:#303030;'.$even,
				));
				$contentTr .= $this->Html->tag('td', sprintf(': %s', $senderName), array(
					'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
				));
				echo $this->Html->tag('tr', $contentTr);

				$contentTr = $this->Html->tag('th', __('Email'), array(
					'style' => 'font-weight: bold;color:#303030;'.$even,
				));
				$contentTr .= $this->Html->tag('td', sprintf(': %s', $senderEmail), array(
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

				$contentTr = $this->Html->tag('th', __('Pesan'), array(
					'style' => 'font-weight: bold;color:#303030;'.$even,
				));
				$contentTr .= $this->Html->tag('td', sprintf(': %s', $senderMessage), array(
					'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
				));
				echo $this->Html->tag('tr', $contentTr);
		?>
	</tbody>
</table>