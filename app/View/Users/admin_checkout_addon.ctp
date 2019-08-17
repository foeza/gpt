<?php

	$paymentChannels	= Configure::read('__Site.payment_channels');
	$paymentTenors		= Configure::read('__Site.payment_tenors');
	$record				= isset($record) ? $record : NULL;

	if($record){
		$defaultOptions = array(
			'frameClass'	=> 'col-xs-12', 
			'labelClass'	=> 'col-xs-12 col-md-4 control-label taright',
			'class'			=> 'relative col-xs-12 col-md-8',
		);

		$integratedOrderID = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'user_integrated_order_id');

		$is_email_all_addon = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'is_email_all_addon');
		$addon_r123 = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'addon_r123');
		$addon_olx = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'addon_olx');

		$createDate = $this->Rumahku->filterEmptyField($this->request->data, 'UserIntegratedOrder', 'created');
		$createDate = date('d/m/Y H:i', strtotime($createDate));

		$this->request->data['UserIntegratedOrder']['created'] = $createDate;

		echo($this->Form->create('UserIntegratedOrderAddon', array(
			'id' => 'PaymentAdminCheckoutForm',
		)));
		echo($this->Html->tag('h2', __('Detail Pemesanan'), array('class' => 'sub-heading')));

		?>
		<div class="row">
			<div class="col-xs-12 col-md-7">
				<?php

					echo($this->Rumahku->buildInputForm('UserIntegratedOrder.name_applicant', array_merge($defaultOptions, array(
						'label'		=> __('Nama'), 
						'readonly'	=> TRUE, 
					))));

					echo($this->Rumahku->buildInputForm('UserIntegratedOrder.phone', array_merge($defaultOptions, array(
						'label'		=> __('No. Telepon'), 
						'readonly'	=> TRUE
					))));

					if ($is_email_all_addon) {
						echo($this->Rumahku->buildInputForm('UserIntegratedOrder.email_all_addon', array_merge($defaultOptions, array(
							'label'		=> __('Email All Addon'), 
							'readonly'	=> TRUE
						))));
					} else {
						if ($addon_r123) {
							echo $this->Rumahku->buildInputForm('UserIntegratedOrder.email_r123', array_merge($defaultOptions, array(
								'label'		=> __('Email Rumah 123'), 
								'readonly'	=> TRUE
							)));
						}
						if ($addon_olx) {
							echo $this->Rumahku->buildInputForm('UserIntegratedOrder.email_olx', array_merge($defaultOptions, array(
								'label'		=> __('Email OLX'), 
								'readonly'	=> TRUE
							)));
						}
					}

					echo($this->Rumahku->buildInputForm('UserIntegratedOrder.company_name', array_merge($defaultOptions, array(
						'label'		=> __('Nama Perusahaan'), 
						'readonly'	=> TRUE
					))));

					echo($this->Rumahku->buildInputForm('UserIntegratedOrder.created', array_merge($defaultOptions, array(
						'type'		=> 'text', 
						'label'		=> __('Tgl. Pemesanan'), 
						'readonly'	=> TRUE
					))));

					echo($this->Rumahku->buildInputForm('UserIntegratedOrderAddon.payment_channel', array_merge($defaultOptions, array(
						'label'		=> __('Metode Pembayaran'), 
						'empty'		=> __('Pilih Metode Pembayaran'), 
						'options'	=> $paymentChannels, 
					))));

					echo($this->Rumahku->buildInputForm('UserIntegratedOrderAddon.tenor', array_merge($defaultOptions, array(
						'label'				=> __('Tenor'), 
						'empty'				=> __('Full Payment'), 
						'options'			=> $paymentTenors, 
						'formGroupClass'	=> 'form-group hide'
					))));

					echo($this->Rumahku->buildInputForm('UserIntegratedOrderAddon.installment_acquirer', array(
						'readonly'			=> TRUE, 
						'formGroupClass'	=>'hide'
					)));

					echo($this->Rumahku->buildInputForm('UserIntegratedOrderAddon.promo_id', array(
						'type'				=> 'text', 
						'readonly'			=> TRUE, 
						'formGroupClass'	=>'hide'
					)));

					$termsURL = $this->Html->link(__('Syarat dan Ketentuan'), 
						array(
							'controller'	=> 'memberships', 
							'action'		=> 'terms_and_conditions',
							'admin'			=> FALSE, 
						), 
						array(
						//	'class'	=> 'ajaxModal',
							'target'	=> '_blank', 
							'title'		=> __('Syarat dan Ketentuan'),
						)
					);

					$content = $this->Html->tag('div', 
						$this->Form->checkbox('Payment.agreement', 
							array(
								'class'	=> 'check-option',
								'value'	=> 1,
								'div'	=> FALSE,
							)
						), 
						array(
							'class' => 'col-xs-1'
						)
					);
					$content.= $this->Html->div('col-xs-11', __('Saya telah membaca dan menyetujui %s yang berlaku di %s.', $termsURL, Configure::read('__Site.site_name')));
					$content = $this->Html->div('row', $content);
					$content = $this->Html->tag('div', $this->Html->tag('p', $content, array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-7 col-md-offset-4'));
					echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));

				?>
			</div>

			<div class="col-xs-12 col-md-5">
				<div class="box box-success">
					<div class="box-header">
						<?php echo($this->Html->tag('h3', __('Informasi Pembelian'))); ?>
					</div>
					<div class="box-body">
						<?php

							$currency = Configure::read('__Site.config_currency_code');
							$invoiceNumber = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'invoice_number');
							$R123packageID = $this->Rumahku->filterEmptyField($record, 'UserIntegratedAddonPackageR123', 'id');
							$R123packageName = $this->Rumahku->filterEmptyField($record, 'UserIntegratedAddonPackageR123', 'name');
							$R123monthDuration = $this->Rumahku->filterEmptyField($record, 'UserIntegratedAddonPackageR123', 'month_duration');

							$OLXpackageID = $this->Rumahku->filterEmptyField($record, 'UserIntegratedAddonPackageOLX', 'id');
							$OLXpackageName= $this->Rumahku->filterEmptyField($record, 'UserIntegratedAddonPackageOLX', 'name');
							$OLXmonthDuration = $this->Rumahku->filterEmptyField($record, 'UserIntegratedAddonPackageOLX', 'month_duration');

							$R123_basePrice = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'r123_base_price', 0);
							$R123_basePrice = number_format($R123_basePrice, 2, '.', ',');

							$discountAmount	= $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'discount_amount', 0);
							$discountAmount = number_format($discountAmount, 2, '.', ',');
							$totalPrice = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'total_price', 0);
							$totalPrice = number_format($totalPrice, 2, '.', ',');
							$itemAmount = 1;
							$voucherCode = $this->Rumahku->filterEmptyField($record, 'VoucherCode', 'code');

							echo($this->Form->hidden('UserIntegratedOrderAddon.invoice_number'));

							$content = $this->Html->tag('div', $this->Form->label('UserIntegratedOrderAddon.invoice_number', __('Nomor Invoice')), array('class' => 'col-xs-12 col-md-6'));
							$content.= $this->Html->tag('div', $this->Html->tag('p', $invoiceNumber, array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-6'));

							echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));

							if (!empty($R123packageID)) {
								// r123 package name
								$content = $this->Html->tag('div', $this->Form->label('UserIntegratedAddonPackageR123.name', __('Nama Paket Rumah 123')), array('class' => 'col-xs-12 col-md-6'));
								$content.= $this->Html->tag('div', $this->Html->tag('p', $R123packageName, array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-6'));

								echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));

								$R123_basePrice = sprintf('%s %s', $currency, $this->Html->tag('span', $R123_basePrice, array('id' => 'PaymentBaseAmount')));
								$content = $this->Html->tag('div', $this->Form->label('UserIntegratedOrderAddon.r123_base_price', __('Harga')), array('class' => 'col-xs-12 col-md-6'));
								$content.= $this->Html->tag('div', $this->Html->tag('p', $R123_basePrice, array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-6'));

								echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));
							}

							if (!empty($OLXpackageID)) {
								$content = $this->Html->tag('div', $this->Form->label('UserIntegratedAddonPackageR123.name', __('Nama Paket')), array('class' => 'col-xs-12 col-md-6'));
								$content.= $this->Html->tag('div', $this->Html->tag('p', $OLXpackageName, array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-6'));

								echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));
							}


							if($voucherCode){
								$voucherCode = $this->Html->tag('span', $voucherCode, array('id' => 'VoucherCodeCode'));
								$content = $this->Html->tag('div', $this->Form->label('VoucherCode.code', __('Kode Voucher')), array('class' => 'col-xs-12 col-md-6'));
								$content.= $this->Html->tag('div', $this->Html->tag('p', $voucherCode, array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-6'));

								echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));
							}

							$totalPrice = sprintf('%s %s', $currency, $this->Html->tag('span', $totalPrice, array('id' => 'PaymentTotalAmount')));
							$content = $this->Html->tag('div', $this->Form->label('UserIntegratedOrderAddon.total_amount', __('Total Pembayaran')), array('class' => 'col-xs-12 col-md-6'));
							$content.= $this->Html->tag('div', $this->Html->tag('p', $totalPrice, array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-6'));

							echo($this->Html->tag('div', $content, array('class' => 'row form-group-static total-price')));

						?>						
					</div>
					<!-- s: section voucher goes here -->
					<!-- e: section voucher goes here -->
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="action-group bottom">
					<div class="btn-group floright">
						<?php

							echo($this->Html->link(__('Kembali'), array('controller' => 'users','action' => 'register_integration','admin' => true), array('class' => 'btn default')));
							echo($this->Form->button(__('Lanjutkan Pembayaran'), array('id' => 'btnContinuePayment', 'type' => 'button', 'class'=> 'btn blue')));

						?>
					</div>
				</div>
			</div>
		</div>
		<?php

		echo($this->Form->end());
	}

?>