<?php
		$slugTheme = $this->Rumahku->filterEmptyField($dataCompany, 'Theme', 'slug');
		$classSection = $this->Kpr->widgetClass('section', $dataCompany);

		$property_id = $this->Rumahku->filterEmptyField($value, 'KprCompare', 'property_id');
		$down_payment = $this->Rumahku->filterEmptyField($value, 'KprCompare', 'down_payment');
		$periode_installment = $this->Rumahku->filterEmptyField($value, 'KprCompare', 'periode_installment');

		$kprCompareDetails = $this->Rumahku->filterEmptyField($value, 'KprCompareDetail');
		$actionName = __('Bandingkan Bank Promo');


		$params = array(
			'property_id' => $property_id,
			'down_payment' => $down_payment,
			'periode_installment' => $periode_installment,
		);

		$url_back = $this->Html->url(array_merge(array(
			'controller' => 'kpr',
			'action' => 'select_product',
		), $params), true);

?>
<div class="content <?php echo $classSection; ?>" style="margin-bottom: 30px;">
	<div class="container">

		<div class="app-setup">
			<?php
					echo $this->Html->tag('h2', __('Bandingkan aplikasi KPR dari setiap promo bank pilihan Anda.'), array(
						'class' => 'hidden-print',
					));

					echo $this->Html->link(sprintf('&#8592; %s', __('Kembali ke daftar bank peserta KPR')), $url_back, array(
						'class' => 'back-to',
						'escape' => FALSE,
					));

					echo $this->element('blocks/kpr/forms/product/appSettings', array(
						'noHeader' => TRUE,
						'disabled' => TRUE,
					));

					if($kprCompareDetails){
						$features = array(
							'loan_price' => 'Jumlah Pinjaman',
							'product_name' => __('Nama Promo'),
							'total_first_credit' => __('Angsuran per bulan'),
							'interest_rate_fix'	=> __('Bunga Fix'),
							'interest_rate_cabs' => __('Bunga Cap'),
							'total_cost_bank' => __('Biaya Bank'),
							'total_notaris' => __('Biaya Notaris'),
							'grand_total'	=> __('Pembayaran Pertama'),
							'text_promo'	=> __('Promo'),
							'desc_promo'	=> __('Deskripsi Promo'),
							'term_conditions'	=> __('Syarat dan Ketentuan'),
						);

						$labelIndex = 0;					//	index hidden label
						$rowIndex	= 1;					//	index item
						$cols		= array('&nbsp;');		//	html columns
						$rows		= array();				//	html items
						$countKprs	= count($kprCompareDetails);
						$width 		=  100/($countKprs+1);

						foreach($features as $keyName => $featureName){
							//	hidden label
							$rows[$labelIndex] = array('&nbsp;', array($featureName, array('colspan' => $countKprs)));

							//	first cell of table
							if(empty($rows[$rowIndex][0])){
								if($keyName == 'loan_price'){
									$colspan = $countKprs;
								}else{
									$colspan = 1;
								}
								$rows[$rowIndex][0]	= array($featureName, array('class' => 'right-align'));
							}

							//	B:GENERATE BANK PRODUCT LAYOUT ====================================================================
							
							foreach($kprCompareDetails as $kprKey => $kprCompareDetail){
								$colIndex = count($rows[$rowIndex]);

								if($labelIndex == 0){
									$photoPath	= Configure::read('__Site.logo_photo_folder');
									$title		= $this->Rumahku->filterEmptyField($kprCompareDetail, 'Bank', 'name');
									$photo		= $this->Rumahku->filterEmptyField($kprCompareDetail, 'Bank', 'logo');
									$photo 		= $this->Rumahku->photo_thumbnail(array(
							            'save_path' => Configure::read('__Site.logo_photo_folder'), 
							            'src' => $photo, 
							            'size' => 'xsm',
							        ), array(
							            'alt' => $title,
							            'title' => $title,
							        ));

									$heading = $this->Html->tag('div', $photo, array(
										'class' => 'short-info',
									));
									$cols[] = $heading;

								}

								$value = '';

								$kprCompareFeatures = $this->Kpr->__getSpecification($kprCompareDetail);
								foreach($kprCompareFeatures as $kprCompareFeature){
									$featureKey		= $this->Rumahku->filterEmptyField($kprCompareFeature, 'key');
									$featureValue	= $this->Rumahku->filterEmptyField($kprCompareFeature, 'value', false, false, false);

									if(empty($value) && $featureKey == $keyName && $featureValue){
										$value = $featureValue;
									}
								}
								$value = $value ? $value : $this->Html->tag('span', 'N/A', array('class' => 'text-fade'));	

								if($keyName == 'loan_price'){
									$rows[$rowIndex][1][0] = $value;
									$rows[$rowIndex][1][1] = array('colspan' => $countKprs);
								}else{
									$rows[$rowIndex][$colIndex] = $value;
								}
								
							}
							//	E:GENERATE PROPERTY LAYOUT ====================================================================

							$labelIndex	= $labelIndex + 2;
							$rowIndex	= $rowIndex + 2;
						}

						$cols = $this->Html->tag('thead', $this->Html->tableHeaders($cols, NULL, array('class' => 'col-compare-'.count($cols), 'width' => sprintf('%s%%', $width))));
						$rows = $this->Html->tag('tbody', $this->Html->tableCells($rows, array(
							'class'			=> 'on-mobile', 
							'aria-hidden'	=> 'true'
						)));

						$contents = $this->Html->tag('table', $cols.$rows, array(
							'class' => 'comparison-table',
						));

					}else{
						$contents = $this->Html->tag('p', __('Data tidak ditemukan.'), array(
							'class' => 'alert alert-warning',
						));
					}

					
			
					echo($this->Html->tag('div', $contents, array(
						'class' => 'compare-table bank-table',
					)));

					echo $this->Html->link(sprintf('&#8592; %s', __('Kembali ke daftar bank peserta KPR')), $url_back, array(
						'class' => 'back-to on-mobile',
						'escape' => FALSE,
					));
			?>				
		</div>
	</div>
</div>
