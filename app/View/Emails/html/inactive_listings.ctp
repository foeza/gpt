<?php 
        $property_path = Configure::read('__Site.property_photo_folder');
        $property = $this->Rumahku->filterEmptyField($params, 'Property');
        $expired_day = $this->Rumahku->filterEmptyField($params, 'expired_day');

        $otherUrl = $this->Html->url(array(
            'controller'=> 'properties', 
            'action' => 'index',
            'status' => 'inactive',
            'admin'=> true,
        ), true);

		echo $this->Html->tag('p', sprintf(__('Properti Anda telah Kami non-aktifkan dikarenakan selama %s hari tidak ada update/refresh terhadap properti tersebut.'), $expired_day), array(
			'style' => 'margin: 15px 0 5px;'
		));
		echo $this->Html->tag('p', __('Mohon aktifkan kembali apabila iklan tersebut masih available: '), array(
			'style' => 'margin: 0 0 30px;'
		));
?>
<table class="message-content" cellspacing="0" cellpadding="0" width="100%" style="border: 1px solid #cccccc; border-radius: 3px;">
	<tbody>
		<?php 
				if( !empty($property) ) {
					$idx = 0;

					foreach ($property as $key => $value) {
						if( $idx < 3 ) {
					        $mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');
					        $url = $this->Html->url(array(
					            'controller'=> 'properties', 
					            'action' => 'index',
					            'mlsid' => $mls_id,
					            'admin'=> true,
					        ), true);
		?>
		<tr>
			<td>
				<?php 
						echo $this->element('emails/html/properties/info', array(
							'params' => $value,
							'url' => $this->Html->link(__('Aktifkan'), $url, array(
								'escape' => false,
								'style' => 'display: inline-block;margin: 10px 0 0;padding: 0 10px;color: #FFFFFF;font-size: 14px;font-weight: 400;text-decoration: none;background-color: #069D54;border-radius: 3px;border-bottom-width: 3px;',
								'full_base' => true,
							)),
						));
				?>
			</td>
		</tr>
		<?php 
						}

						$idx++;
					}
				}

				if( count($property) > 3 ) {
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
														echo $this->Html->link(__('Lihat Lainnya'), $otherUrl, array(
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
		<?php 
				}
		?>
	</tbody>
</table>