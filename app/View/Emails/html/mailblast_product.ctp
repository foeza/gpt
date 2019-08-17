<?php
		$params   = !empty($params)?$params:false;
		$bankLogo = Common::hashEmptyField($params, 'Bank.logo');
		$items    = Common::hashEmptyField($params, 'BankProductCampaign.content_product');

		if(!empty($bankLogo)){
			$customLogo = $bankLogo;
		}else{
			$customLogo = '/img/primesystem.png';
		}

		// styling inline variable
		$center = 'style="max-width:600px;margin:auto;text-align:center;font-size: 16px;font-family: Helvetica, sans-serif;color: #242124;line-height: 24px;padding: 0;position: relative;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;text-size-adjust: 100%;"';

?>

<html>

	<body>
		<center <?php echo $center; ?>>
			<table border="0" cellpadding="0" cellspacing="0" class="body" width="100%" style="padding-bottom: 20px;">
				<tbody>
					<tr>
						<td align="center" valign="top">
						<table border="0" cellpadding="0" cellspacing="0" class="container" width="600">
							<tbody>
								<tr>
									<!-- pembungkus header | start -->
									<td align="left" valign="top">
										<table border="0" cellpadding="10" cellspacing="0" class="header" width="100%">
											<tbody>
												<tr>
													<td align="center" valign="top"><!-- isi header | start -->
													<table border="0" cellpadding="0" cellspacing="0" width="100%">
														<tbody>
															<tr>
																<td align="left" valign="top">
																<div style="vertical-align: middle; padding-top: 15px;">
																<?php
																		echo $this->Html->image($customLogo, array(
																			'class' => 'logo-bank',
																			'style' => 'max-height: 150px;border: 1px solid #efefef;'
																		));
																?>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													<!-- isi header | end --></td>
												</tr>
											</tbody>
										</table>
									</td>
									<!-- pembungkus header | end -->
								</tr>
								<tr>
									<!-- pembungkus content | start -->
									<td align="left" valign="top">
										<?php
												echo $this->Html->tag('div',
													__('Ada promo menarik dari Kami, cek sekarang juga!'), array(
														'style' => 'padding:10px;',
													)
												);
										?>
									</td>
									<!-- pembungkus content | end -->
								</tr>
								<tr>
									<!-- pembungkus content | start -->
									<td align="left" valign="top">
										<?php
												echo $this->element('blocks/mailblast_primekpr/item_promo', array(
													'items' => $items,
												));
										?>
									</td>
									<!-- pembungkus content | end -->
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
		</center>
	</body>
</html>