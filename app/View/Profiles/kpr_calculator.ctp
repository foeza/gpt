<div id="content-wrapper" class="detail-banks">
	<?php

		$record			= empty($record) ? array() : $record;
		$exclusiveBank	= empty($exclusiveBank) ? array() : $exclusiveBank;

		echo($this->Form->create('Kpr', array(
			'class' => 'bank-kpr-form',
		)));

		?>
		<div class="subHeader page relative hidden-print">
			<div class="page-description mortgage-cover"></div>
			<div class="container detail-bank">
				<?php

					$propertyID		= Common::hashEmptyField($record, 'Property.id');
					$propertyMlsID	= Common::hashEmptyField($record, 'Property.mls_id');
					$propertyPrice	= Common::hashEmptyField($record, 'Property.price_measure', 0);

					$bankID				= Common::hashEmptyField($exclusiveBank, 'Bank.id');
					$bankSettingID		= Common::hashEmptyField($exclusiveBank, 'BankSetting.id');
					$periodeInstallment	= Common::hashEmptyField($exclusiveBank, 'BankSetting.periode_installment');
					$interestRateFloat	= Common::hashEmptyField($exclusiveBank, 'BankSetting.interest_rate_float');
					$interestRateFix	= Common::hashEmptyField($exclusiveBank, 'BankSetting.interest_rate_fix');
					$interestRateCabs	= Common::hashEmptyField($exclusiveBank, 'BankSetting.interest_rate_cabs');
					$periodeFix			= Common::hashEmptyField($exclusiveBank, 'BankSetting.periode_fix');
					$periodeCab			= Common::hashEmptyField($exclusiveBank, 'BankSetting.periode_cab');
					$dpPercentage		= Common::hashEmptyField($exclusiveBank, 'BankSetting.dp', 0);     
					$dpAmount			= Common::hashEmptyField($exclusiveBank, 'BankSetting.down_payment', 0);

					$dpAmount		= ($propertyPrice / 100) * $dpPercentage;
					$loanPrice		= $this->Kpr->calcLoan($propertyPrice, $dpPercentage, $dpAmount);
					$firstCredit	= $this->Kpr->creditFix($loanPrice, $interestRateFix, $periodeInstallment);

					$appraisal = $this->Kpr->_callGenerateNominal($exclusiveBank, array(
						'price'			=> $propertyPrice,
						'loan_price'	=> $loanPrice,
					), array(
						'fieldName' => 'appraisal',
						'modelName' => array('BankSetting'),
					));

					$administration = $this->Kpr->_callGenerateNominal($exclusiveBank, array(
						'price'			=> $propertyPrice,
						'loan_price'	=> $loanPrice,
					), array(
						'fieldName' => 'administration',
						'modelName' => array('BankSetting'),
					));

					$insurance = $this->Kpr->_callGenerateNominal($exclusiveBank, array(
						'price'			=> $propertyPrice,
						'loan_price'	=> $loanPrice,
					), array(
						'fieldName' => 'insurance',
						'modelName' => array('BankSetting'),
					));

				//	udah pasti 0, is notary ambil dari bank product. sedangkan module ini ga narik data bank produk
					$notaryCosts	= array();
					$notary			= 0;
					$isNotary		= Common::hashEmptyField($exclusiveBank, 'BankProduct.is_notary');

					if($isNotary){
						$notaryCosts = $this->Kpr->getNotary($exclusiveBank, array(
							'price'			=> $propertyPrice,
							'loan_price'	=> $loanPrice,
						), array(
							'model'			=> 'BankSetting', 
							'return_type'	=> 'detail', 
						));

						$notary	= Hash::extract($notaryCosts, '{s}.value');
						$notary	= $notary ? array_sum($notary) : 0;
					}

					$commissionArr	= $this->Kpr->getFilterCommissionAgent($exclusiveBank);
					$provision		= Common::hashEmptyField($commissionArr, 'commission', 0);
					$bankCost		= $appraisal + $administration + $insurance + $provision;
					$grandtotal		= round($dpAmount + $bankCost + $notary + $firstCredit, 0);
					$mortgageItems	= array(
						array(
							'label' => 'Jumlah Pinjaman Maksimum', 
							'value'	=> $this->Rumahku->getCurrencyPrice($loanPrice), 
						), 
						array(
							'label' => 'Uang Muka (DP)', 
							'value'	=> $this->Rumahku->getCurrencyPrice($dpAmount), 
						), 
						array(
							'label' => 'Lama Pinjaman', 
							'value'	=> sprintf('%s tahun', $periodeInstallment), 
						), 
					);

					$bankCosts = array(
						array(
							'label' => 'Appraisal', 
							'value'	=> $this->Rumahku->getCurrencyPrice($appraisal), 
						), 
						array(
							'label' => 'Adminstrasi', 
							'value'	=> $this->Rumahku->getCurrencyPrice($administration), 
						), 
						array(
							'label' => 'Provisi', 
							'value'	=> $this->Rumahku->getCurrencyPrice($provision), 
						), 
						array(
							'label' => 'Asuransi', 
							'value'	=> $this->Rumahku->getCurrencyPrice($insurance), 
						), 
						array(
							'label' => $this->Html->tag('strong', __('Total'), array('class' => 'small')), 
							'value'	=> $this->Html->tag('strong', $this->Rumahku->getCurrencyPrice($bankCost)), 
						), 
					);

					if($interestRateFix){
						$mortgageItems[] = array(
							'label' => 'Suku bunga tetap per tahun', 
							'value'	=> sprintf('%s%%', $interestRateFix), 
						);
					}

					if($interestRateFloat){
						$mortgageItems[] = array(
							'label' => 'Suku bunga floating per tahun', 
							'value'	=> sprintf('%s%%', $interestRateFloat), 
						);
					}

					$named = array(
						'dp'					=> $dpPercentage, 
						'down_payment'			=> $dpAmount, 
						'periode_installment'	=> $periodeInstallment, 
					);

					$ajaxURL = $this->Html->url(array(
						'admin'			=> false,
						'controller'	=> 'profiles',
						'action'		=> 'kpr_calculator',
						'mlsid'			=> $propertyMlsID, 
					));

					echo($this->Html->tag('div', $this->element('blocks/kpr/detailBank/call_filling', array(
						'property'	=> $record, 
						'named'		=> $named, 
						'options'	=> array(
							'editable_price'	=> true, 
							'ajax_attributes'	=> array(
								'data-wrapper-write'	=> '#tab-content.mortgage-tab-content',
								'data-url'				=> $ajaxURL,
							), 
						), 
					))));

				?>
			</div>
		</div>
		<?php /*
		<div class="visible-print">
			<div class="container">
				<div class="row">
					<div class="bank-logo">
						<img src="http://www.informasipropertiagen.com/img/view/logos/xxsm/2015/11/9/563b2b33-9ec4-4569-96a1-65300a0a0b14.jpg" alt="">
					</div>
				</div>
			</div>
		</div>
		*/ ?>
		<div class="mortgage-tab hidden-print properties__bank" id="result-calculator">
			<div class="container">
				<div class="row">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="header__contacts">
							<a href="#result-calculator" class="active scrolling">Rincian</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="container">
			<div id="tab-content" class="mortgage-tab-content tab-content">
				<div id="rincian" class=" tab-pane fade active in" aria-labelledby="rincian-tab">
					<div class="col-md-9">
						<div class="mortgage-section">
							<?php

								foreach($mortgageItems as $key => $templateItem){
									$template = $this->Html->tag('div', $this->Html->tag('label', __($templateItem['label']), array(
										'class' => 'normal', 
									)), array(
										'class' => 'pull-left', 
									));

									$template.= $this->Html->tag('div', $this->Html->tag('span', $templateItem['value'], array(
										'class' => 'normal', 
									)), array(
										'class' => 'pull-right text-right', 
									));

									echo($this->Html->tag('div', $template, array(
										'class' => 'mortgage-item', 
									)));
								}

							?>
						</div>
						<div class="mortgage-section">
							<?php

								$template = $this->Html->tag('div', $this->Html->tag('label', __('Angsuran per bulan'), array(
									'class' => 'normal', 
								)), array(
									'class' => 'pull-left', 
								));

								$firstCredit = $this->Rumahku->getCurrencyPrice($firstCredit); 
								$firstCredit = $this->Html->tag('strong', $firstCredit);

								$template.= $this->Html->tag('div', $this->Html->tag('span', $firstCredit, array(
									'class' => 'normal', 
								)), array(
									'class' => 'pull-right text-right', 
								));

								echo($this->Html->tag('div', $template, array(
									'class' => 'mortgage-item', 
								)));

							?>
						</div>
						<div class="mortgage-section">
							<h6 class="title margin-bottom-2">Biaya Bank</h6>
							<?php

								foreach($bankCosts as $key => $templateItem){
									$template = $this->Html->tag('div', $this->Html->tag('label', __($templateItem['label']), array(
										'class' => 'normal', 
									)), array(
										'class' => 'pull-left', 
									));

									$template.= $this->Html->tag('div', $this->Html->tag('span', $templateItem['value'], array(
										'class' => 'normal', 
									)), array(
										'class' => 'pull-right text-right', 
									));

									echo($this->Html->tag('div', $template, array(
										'class' => 'mortgage-item', 
									)));
								}

							?>
						</div>
						<div class="mortgage-section">
							<h6 class="title margin-bottom-2">Biaya Notaris</h6>
							<?php

								if($notaryCosts){
									foreach($notaryCosts as $key => $item){
										$label = Common::hashEmptyField($item, 'label');
										$value = Common::hashEmptyField($item, 'value');
										$value = $this->Rumahku->getCurrencyPrice($value);

										$template = $this->Html->tag('div', $this->Html->tag('label', __($label), array(
											'class' => 'normal', 
										)), array(
											'class' => 'pull-left', 
										));

										$template.= $this->Html->tag('div', $this->Html->tag('span', $value, array(
											'class' => 'normal', 
										)), array(
											'class' => 'pull-right text-right', 
										));

										echo($this->Html->tag('div', $template, array(
											'class' => 'mortgage-item', 
										)));
									}

									$template = $this->Html->tag('div', $this->Html->tag('label', $this->Html->tag('strong', __('Total')), array(
										'class' => 'normal', 
									)), array(
										'class' => 'pull-left', 
									));

									$notary = $this->Rumahku->getCurrencyPrice($notary);
									$notary = $this->Html->tag('strong', $notary);

									$template.= $this->Html->tag('div', $this->Html->tag('span', $notary, array(
										'class' => 'normal', 
									)), array(
										'class' => 'pull-right text-right', 
									));

									echo($this->Html->tag('div', $template, array(
										'class' => 'mortgage-item', 
									)));
								}
								else{
									$template = $this->Html->tag('div', $this->Html->tag('label', __('Tidak termasuk dalam perhitungan'), array(
										'class' => 'normal', 
									)));

									echo($this->Html->tag('div', $template, array(
										'class' => 'mortgage-item', 
									)));
								}

							?>
						</div>
						<div class="mortgage-section">
							<div class="mortgage-item">
								<div class="pull-left">
									<h6 class="title">Pembayaran Pertama</h6>
									<span>Angsuran 1 + DP + Biaya Bank + Biaya Notaris</span>
								</div>
								<div class="pull-right text-right">
									<span><strong><h6><?php echo($this->Rumahku->getCurrencyPrice($grandtotal)); ?></h6></strong></span>
								</div>
							</div>
						</div>
						<div class="mortgage-section">
							<div class="mortgage-item">
								<div class="info">
									<p>
										<strong>Catatan:</strong>
										Perhitungan ini berdasarkan asumsi kami pada aplikasi KPR secara umum. Data perhitungan di atas dapat berbeda dengan perhitungan bank. Untuk perhitungan yang akurat, silahkan hubungi bank penyedia pinjaman KPR. Khusus nilai tukar USD hanya berdasarkan perkiraan yang mendekati nilai sebenarnya. Dan nilai tukar USD bisa berubah sewaktu - waktu.
										<?php

										//	echo($this->Html->link(__('Selengkapnya'), array(
										//		'admin'			=> false, 
										//		'controller'	=> 'kpr', 
										//		'action'		=> 'termandconditions', 
										//	), array(
										//		'target' => 'blank', 
										//	)));

										?>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php

		echo($this->Form->end());

	?>
</div>