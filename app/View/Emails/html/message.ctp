<?php 
		$data = $this->Rumahku->filterEmptyField($params, 'Message');
		$name = $this->Rumahku->filterEmptyField($params, 'Message', 'name', false, true, 'ucwords');
		$email = $this->Rumahku->filterEmptyField($params, 'Message', 'email', false, true, 'mailto');
		$phone = $this->Rumahku->filterEmptyField($params, 'Message', 'phone', false, true, 'phone');
		$message = $this->Rumahku->filterEmptyField($params, 'Message', 'message');
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');

		$group_id = $this->Rumahku->filterEmptyField($params, 'ToUser', 'group_id');
		$parent_id = $this->Rumahku->filterEmptyField($params, 'ToUser', 'parent_id');

		$full_base_url = $this->Rumahku->filterEmptyField($params, 'Message', 'full_base_url', FULL_BASE_URL, true, 'link-target-blank');
		$userCompany = $this->Rumahku->filterEmptyField($params, 'ToUser', 'UserCompany');
		$domain = $this->Rumahku->filterEmptyField($userCompany, 'domain', false, false, false, 'trailing_slash');
		$category = $this->Rumahku->filterEmptyField($params, 'MessageCategory', 'name');

		if(!empty($params['Property'])){
			$title = __('Anda mendapat pesan dari iklan properti yang ditayangkan di %s', $full_base_url);
		}else{
			$title = __('Anda mendapat pesan dari %s di %s', $name, $full_base_url);
		}

		echo $this->Html->tag('p', $title, array(
			'style' => 'margin: 15px 0 30px;'
		));

		if( !empty($params['ToUser']) && ( $group_id == 1 || empty($parent_id) ) ) {
			$from_id = $this->Rumahku->filterEmptyField($params, 'Message', 'from_id');
			$from_slug = $this->Rumahku->filterEmptyField($params, 'FromUser', 'username');

			if($domain){
				$readUrl = $domain.$this->Html->url(array(
					'controller' => 'users',
					'action' => 'profile',
					$from_id,
					$from_slug,
					'admin' => false,
				));
			}else{
		        $readUrl = $this->Html->url(array(
					'controller' => 'users',
					'action' => 'profile',
					$from_id,
					$from_slug,
					'admin' => false,
				), true);
			}
		} else {
			if($domain){
				$readUrl = $domain.$this->Html->url(array(
					'controller' => 'messages',
					'action' => 'index',
					'admin' => true,
				));
			}else{
		        $readUrl = $this->Html->url(array(
					'controller' => 'messages',
					'action' => 'index',
					'admin' => true,
				), true);
			}
		}
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
														<?php 
																if( !empty($category) ) {
																	echo $this->Html->tag('tr',
																		$this->Html->tag('td', __('Subject:'), array(
																			'width' => 132,
																		)).
																		$this->Html->tag('td', $this->Html->tag('strong', $category))
																	);
																}
														?>
														<tr>
															<?php 
																	echo $this->Html->tag('td', __('Email:'), array(
																		'width' => 132,
																	));
																	echo $this->Html->tag('td', $this->Html->tag('strong', $email));
															?>
														</tr>
														<tr>
															<?php 
																	echo $this->Html->tag('td', __('No. Handphone:'), array(
																		'width' => 132,
																	));
																	echo $this->Html->tag('td', $this->Html->tag('strong', $phone));
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
			<td style="padding: 0 20px;">
				<?php
						if( !empty($mls_id) ) {
			      			echo $this->Html->tag('h4', __('Informasi properti sebagai berikut:'), array(
								'style' => 'font-weight:bold;text-align: left;padding-bottom:5px;font-size: 14px;margin: 0;'
							));
							echo $this->element('emails/html/properties/info');
						}
				?>
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