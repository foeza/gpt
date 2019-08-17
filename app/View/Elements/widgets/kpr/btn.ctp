<?php
	
		$save_path = Configure::read('__Site.logo_photo_folder');
		$site_name = Configure::read('__Site.site_name');
		$default_bunga_kpr = Configure::read('__Site.bunga_kpr');
		$default_interest_rate = Configure::read('__Site.interest_rate');
		$default_kpr_credit_fix = Configure::read('__Site.kpr_credit_fix');
		$value = !empty($value)?$value:false;
		$classButtonLeft = !empty($classButtonLeft)?$classButtonLeft:false;
		$classButtonRight = isset($classButtonRight)?$classButtonRight:'no-pleft';

        $_action = $this->Rumahku->filterEmptyField($value, 'Property', 'property_action_id');
        $_type = $this->Rumahku->filterEmptyField($value, 'PropertyType', 'is_building');
       	$sold = $this->Rumahku->filterEmptyField($value, 'Property', 'sold');

        if( $_action == 1 && $_type && empty($sold)) {  
			$mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');

			$propertyPrice = $this->Rumahku->getMeasurePrice($value);
			$detail_calculation = $this->Rumahku->getMeasurePriceText($value);

			if( !empty($bankKpr) ) {

				// foreach($bankKprProducts As $key => $bankKpr){
					$dp = $this->Rumahku->filterEmptyField($bankKpr, 'BankSetting', 'dp', $default_bunga_kpr);
					$interest_rate_fix = $this->Rumahku->filterEmptyField($bankKpr, 'BankSetting', 'interest_rate_fix', $default_interest_rate);
					$periode_installment = $this->Rumahku->filterEmptyField($bankKpr, 'BankSetting', 'periode_installment', $default_kpr_credit_fix);
					$bank_name = $this->Rumahku->filterEmptyField($bankKpr, 'Bank', 'name');
					$bank_code = $this->Rumahku->filterEmptyField($bankKpr, 'Bank', 'code', 'rumahku');
					$bank_code = strtolower($bank_code);
					$color = $this->Rumahku->filterEmptyField($bankKpr, 'Bank', 'bg_color');
					$logo = $this->Rumahku->filterEmptyField($bankKpr, 'Bank', 'logo');

					$bunga_kpr_persen = $this->Rumahku->_getBungaKPRPersen($dp);
					$total_loan = $propertyPrice * $bunga_kpr_persen;
					$total_dp =  $propertyPrice - $total_loan;
					$_allowKpr = $this->Rumahku->_allowKpr($value);
					
					$logo = $this->Rumahku->photo_thumbnail(array(
						'save_path' => $save_path, 
						'src' => $logo, 
						'thumb' => false,
					), array(
						'title'=> $bank_name, 
						'alt'=> $bank_name, 
						'style' => 'background:#'.$color,
					));

					$total_first_credit = $this->Rumahku->creditFix($total_loan, $interest_rate_fix, $periode_installment);

					$urlKpr = array(
						'controller' => 'kpr',
						'action' => 'bank_calculator',
						'slug' => 'kalkulator-kpr',
						'bank_code' => $bank_code,
						'mls_id' => $mls_id,
					);
				// }

			} else {
				$bank_name = $site_name;
				$color = '5eab1f';
				$bank_code = false;
				$logo = $this->Html->image('/img/rumahku_logo_kpr.png', array(
	            	'class' => 'img-thumbnail',
	            	'title'=> $bank_name, 
					'alt'=> $bank_name, 
					'style' => 'background:#5eab1f;',
	        	));
			}


?>

<section id="kpr" class="kpr-wrapper col-sm-12 hidden-print">
	<div class="header-kpr-btn">
		<?php
				echo $this->Html->tag('div', $this->Html->tag('div', __('Simulasi KPR'), array(
					'class' => 'title',
				)), array(
					'class' => 'left-side',
				));
				echo $this->Html->tag('div', $logo, array(
					'class' => 'right-side',
				));
		?>
	</div>
	<div class="content">
		<div class="detail-kpr calculator-kpr-credit" data-price="<?php echo $propertyPrice; ?>" data-target=".pay-btn">
			<ul>
				<li>
					<div class="row">
						<div class="col-sm-6 col-xs-6 no-pright">
							<label>
								<?php
										echo __('Harga Properti ');
										if(!empty($detail_calculation)){
											echo '<br>'.$this->Html->tag('small', $detail_calculation );
										}
								?>
							</label>
						</div>
						<div class="col-sm-6 col-xs-6">
							<?php 
									echo $this->Html->tag('span', $this->Rumahku->getCurrencyPrice($propertyPrice), array(
										'value' => $propertyPrice,
									)); 
							?>
						</div>
					</div>
				</li>
				<li>
					<div class="row">
						<?php
								echo $this->Html->tag('div', $this->Html->tag('label', __('DP ('.$dp.'%) ')), array(
									'class' => 'col-sm-6 col-xs-6 no-pright',
								));
								echo $this->Html->tag('div', $this->Html->tag('span', $this->Rumahku->getCurrencyPrice($total_dp)), array(
									'class' => 'col-sm-6 col-xs-6',
								));
						?>
					</div>
				</li>
				<li>
					<div class="row">
						<?php
								echo $this->Html->tag('div', $this->Html->tag('label', __('Jumlah Pinjaman')), array(
									'class' => 'col-sm-6 col-xs-6 no-pright',
								));
								echo $this->Html->tag('div', $this->Rumahku->getCurrencyPrice($total_loan), array(
									'class' => 'col-sm-6 col-xs-6',
								));
								echo $this->Form->input('credit_fix', array(
										'type' => 'hidden',
										'class' => 'loan-amount',
										'value' => $total_loan
									));
								// echo $this->Html->tag('div', $total_loan, array(
								// 	'class' => 'loan-amount hidden',
								// ));
						?>
					</div>
				</li>
			</ul>
			<ul class="editable">
				<li>
					<div class="row">
						<?php
								echo $this->Html->tag('div', $this->Html->tag('label', __('Jangka Waktu')), array(
									'class' => 'col-sm-6 col-xs-6 no-pright',
								));
						?>
						<div class="col-sm-6 col-xs-6">
							<?php
									$year = array();

									for ($i=1; $i <= 50; $i++) { 
										$year[$i] = $i;
									}

									echo $this->Form->input('credit_fix', array(
										'label' => false, 
										'options' => $year,
										'div' => false,
										'required' => true,
										'class' => 'form-control credit_fix',
										'default' => $periode_installment
									));
									echo $this->Html->tag('span', __('Thn'));
							?>
						</div>
					</div>
				</li>
				<li>
					<div class="row">
						<?php
								echo $this->Html->tag('div', $this->Html->tag('label', __('Estimasi Suku Bunga')), array(
									'class' => 'col-sm-6 col-xs-6 no-pright',
								));
								echo $this->Html->tag('div', $this->Form->input('interest_rate', array(
									'label' => false, 
									'div' => false,
									'required' => false,
									'class' => 'form-control input_number interest_rate',
									'value' => $interest_rate_fix,
								)).$this->Html->tag('span', '%'), array(
									'class' => 'col-sm-6 col-xs-6',
								));
						?>
					</div>
				</li>
				<li>
					<div class="row">
						<?php
								echo $this->Html->tag('div', $this->Html->tag('label', __('Angsuran per bln')), array(
									'class' => 'col-sm-6 col-xs-6 no-pright',
								));
								echo $this->Html->tag('div', $this->Html->tag('span', $this->Rumahku->getCurrencyPrice($total_first_credit), array(
									'class' => 'pay-btn',
								)), array(
									'class' => 'col-sm-6 col-xs-6',
								));
						?>
					</div>
				</li>
				<li class="kpr-action-calc">
					<div class="row">
						<?php 
								echo $this->Html->tag('div', $this->Html->link(__('Lihat Simulasi'), $urlKpr, array(
									'class' => 'link-kpr-detail-simulation hidden-print change-url-kpr default btn--for',
									'escape' => false,
								)), array(
									'class' => 'col-sm-6 col-xs-6 '.$classButtonLeft,
								));

								if( !empty($_allowKpr) ) {
									$urlKpr['apply'] = 'kpr';

									echo $this->Html->tag('div', $this->Html->link(__('Ajukan KPR'), $urlKpr, array(
										'class' => 'link-kpr-detail-simulation btn btn-darkred change-url-kpr',
										'escape' => false,
									)), array(
										'class' => 'col-sm-6 col-xs-6 '.$classButtonRight,
									));
								}
						?>
					</div>
				</li>
			</ul>
		</div>
	</div>
	<?php
			echo $this->Html->tag('div', false, array(
				'class' => 'hidden uri-link-kpr',
				'url' => $this->Html->url($urlKpr)
			));
	?>
</section>
<?php 
		}
?>