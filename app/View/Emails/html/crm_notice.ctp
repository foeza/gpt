<?php 
		$dataLogin = Configure::read('User.data');
        $property_path = Configure::read('__Site.property_photo_folder');
        $photo = $this->Rumahku->filterEmptyField($params, 'Property', 'photo');
        $title = $this->Rumahku->filterEmptyField($params, 'Property', 'title');
        $mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');

		$id = $this->Rumahku->filterEmptyField($params, 'CrmProject', 'id');
		$client = $this->Rumahku->filterEmptyField($params, 'CrmProject', 'client_name');
		$client_hp = $this->Rumahku->filterEmptyField($params, 'CrmProject', 'client_hp');
		$user_login_name = $this->Rumahku->filterEmptyField($dataLogin, 'full_name');

        $readUrl = $this->Html->url(array(
			'controller' => 'crm',
			'action' => 'project_detail',
			$id,
			'admin' => true,
		), true);

		echo $this->Html->tag('p', sprintf(__('Klien %s telah ditambahkan kedalam Project CRM Anda oleh %s. Lakukan aktivitas untuk meningkatkan penjualan Properti Anda.'), $client, $user_login_name), array(
			'style' => 'margin: 15px 0 30px;'
		));
?>
<table class="message-content" cellspacing="0" cellpadding="0" width="100%" style="border: 1px solid #cccccc; border-radius: 3px;">
	<tbody>
		<?php 
				if( !empty($mls_id) ) {
		?>
		<tr>
			<td>
				<?php 
						echo $this->element('emails/html/properties/info');
				?>
			</td>
		</tr>
		<?php 
				}
		?>
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
														echo $this->Html->link(__('Buat Aktivitas'), $readUrl, array(
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