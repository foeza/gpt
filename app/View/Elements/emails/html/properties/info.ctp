<?php 
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');
		$dataAddress = $this->Rumahku->filterEmptyField($params, 'PropertyAddress');
		$btn_url = !empty($url)?$url:false;

		if( !empty($mls_id) ) {
			// $address = $this->Property->getAddress($dataAddress);
        	$photo = $this->Rumahku->filterEmptyField($params, 'Property', 'photo');
       	 	$title = $this->Rumahku->filterEmptyField($params, 'Property', 'title');
        	$property_path = Configure::read('__Site.property_photo_folder');
	        $label = $this->Property->getNameCustom($params);
	        $slug = $this->Rumahku->toSlug($label);
       	 	$price = $this->Rumahku->filterEmptyField($params, 'KprBankInstallment', 'property_price');

       	 	if( !empty($price) ) {
	        	$price = $this->Rumahku->getCurrencyPrice($price);
	        } else {
	        	$price = $this->Property->getPrice($params);
	        }

	        $customPhoto = $this->Rumahku->photo_thumbnail(array(
	            'save_path' => $property_path, 
	            'src'=> $photo, 
	            'size' => 'l',
	            'fullbase' => true,
	        ), array(
	            'title' => $title,
	            'alt' => $title,
	            'style' => 'width: 150px;margin-right: 15px;',
	        ));
	        $url = $this->Html->url(array(
	            'controller'=> 'properties', 
				'action' => 'shorturl',
	            'mlsid' => $mls_id,
	            'admin'=> false,
	        ), true);
?>
<table class="property" border="0" cellspacing="0" cellpadding="20" width="100%" bgcolor="#f0f0f0" style="border-bottom: 1px solid #e5e5e5; border-radius: 5px 5px 0 0;">
	<tbody>
		<tr>
			<td align="left" valign="top">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td align="left" valign="top">
							<?php 
									echo $this->Html->link($customPhoto, $url, array(
										'escape' => false,
										'target' => '_blank',
										'style' => 'margin: 0;padding: 0;',
									));
							?>
						</td>
						<td align="left" valign="top">
							<div style="color: #000000;">
								<?php 
										echo $this->Html->tag('div', $label, array(
											'style' => 'font-size: 12px; display: block; line-height: 18px;',
										));
										// echo $this->Html->tag('div', $address, array(
										// 	'style' => 'font-size: 12px; display: blpock; line-height: 18px;',
										// ));
										echo $this->Html->link($title, $url, array(
											'escape' => false,
											'target' => '_blank',
											'style' => 'display: block; margin: 0 0 2px; color: #000000; text-decoration: none;',
										));
										echo $this->Html->tag('div', $this->Html->tag('strong', $price), array(
											'style' => 'margin-top: 10px;',
										));

										if( !empty($btn_url) ) {
											echo $btn_url;
										}
								?>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<?php 
	    }
?>