<?php 
		$id = $this->Rumahku->filterEmptyField($params, 'Message', 'id');
		$name = $this->Rumahku->filterEmptyField($params, 'Message', 'name');
		$email = $this->Rumahku->filterEmptyField($params, 'Message', 'email');
		$phone = $this->Rumahku->filterEmptyField($params, 'Message', 'phone');
		$message = $this->Rumahku->filterEmptyField($params, 'Message', 'message');
		$from_id = $this->Rumahku->filterEmptyField($params, 'Message', 'from_id');
		$to_id = $this->Rumahku->filterEmptyField($params, 'Message', 'to_id');
		
        $readUrl = $this->Html->url(array(
			'controller' => 'messages',
			'action' => 'read',
			$from_id,
			$to_id,
			'admin' => true,
		), true);

		echo $this->Html->tag('p', sprintf(__('Anda mendapat pesan dari pengunjung yang melihat profil Anda di %s'), $this->Html->link(FULL_BASE_URL, FULL_BASE_URL, array(
			'target' => '_blank',
		))), array(
			'style' => 'margin: 15px 0 30px;'
		));
?>
<table class="message-content" cellspacing="0" cellpadding="0" width="100%" style="border: 1px solid #cccccc; border-radius: 3px;">
	<tbody>
		<tr>
			<td align="left" valign="top">
				<table class="message" border="0" cellspacing="0" cellpadding="0" width="100%">
					<tbody>
						<tr>
							<td align="left" valign="top">
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tbody>
										<tr>
											<td align="left" valign="center">
												<div style="padding: 20px 20px 0;">
													<?php 
															echo str_replace(PHP_EOL, '<br>', $message)
													?>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
				<table class="sender" border="0" cellspacing="0" cellpadding="20" width="100%">
					<tbody>
						<tr>
							<td align="left" valign="top">
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tbody>
										<tr>
											<td align="left" valign="top">
												<div style="padding: 10px 15px; background-color: #f5f5f5; font-size: 13px; border: 1px solid #e5e5e5; border-radius: 3px">
													<table>
														<tr>
															<?php 
																	echo $this->Html->tag('td', __('Pengirim:'), array(
																		'width' => 132,
																	));
																	echo $this->Html->tag('td', $this->Html->tag('strong', $name));
															?>
														</tr>
														<tr>
															<?php 
																	echo $this->Html->tag('td', __('Email:'), array(
																		'width' => 132,
																	));
																	echo $this->Html->tag('td', $this->Html->tag('strong', $this->Html->link($email, __('mailto:%s', $email))));
															?>
														</tr>
														<tr>
															<?php 
																	echo $this->Html->tag('td', __('No. Handphone:'), array(
																		'width' => 132,
																	));
																	echo $this->Html->tag('td', $this->Html->tag('strong', $this->Html->link($phone, __('tel:%s', $phone))));
															?>
														</tr>
													</table>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center">
				<table class="cta-button" border="0" cellspacing="0" cellpadding="0" width="100%">
					<tbody>
						<tr>
							<td align="center" valign="top">
								<table border="0" cellspacing="0" cellpadding="0" width="180">
									<tbody>
										<tr>
											<td align="center" valign="top">
												<?php 
														echo $this->Html->link(__('Balas Pesan Ini'), $readUrl, array(
															'style' => 'display: block; margin: 30px 0 50px; padding: 10px; color: #FFFFFF; font-size: 18px; font-weight: 400; text-shadow: 0 1px 0 #0447f0; text-decoration: none; background-color: #0462f0; border: 1px solid #0447f0; border-radius: 3px; border-bottom-width: 3px;',
														));
												?>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>