<?php

	$currency	= Configure::read('__Site.config_currency_code');
	$currency	= $currency ? trim($currency) : NULL;
	$record		= isset($record) ? $record : NULL;
	$filename	= 'Invoice-Detail';
// debug($record);die();
	if($record){
	//	invoice detail
		$recordID		  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'id');
		$userID			  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'user_id');
		$invNumber 		  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'invoice_number');
		$R123packagePrice = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'r123_base_price', 0);
		$OLXpackagePrice  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'olx_base_price', 0);

		$packageID		  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedAddonPackageR123', 'id');
		$R123packageName  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedAddonPackageR123', 'name', '-');

		$OLXpackageID 	  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedAddonPackageOLX', 'id');
		$OLXpackageName	  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedAddonPackageOLX', 'name', '-');

		$itemAmount		  = 1;
		$voucherCode	  = $this->Rumahku->filterEmptyField($record, 'VoucherCode', 'code', 'N/A');
		$discountAmount	  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'discount_price', 0);
		$totalAmount	  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'total_price', 0);
		$paymentStatus	  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'payment_status');
		$approvedDate	  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'created');
		$expiredDate	  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'expired_date');
		$paymentDate	  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'payment_datetime');
		$transExpDate	  = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrderAddon', 'transfer_expired_date');

	//	order detail
		$orderNumber	= $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'order_number');
		$name			= $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'name_applicant');
		$companyName	= $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'company_name');
		$phone			= $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'phone');
		$message		= $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'message');
		$domain			= $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'domain');
		$status			= $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'status');
		$orderDate		= $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'created');
		$orderStatus	= $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'status');

		$all_addon = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'is_email_all_addon');
		$mail_all_addon = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'email_all_addon');
		$addon_r123 = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'addon_r123');
		$email_r123 = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'email_r123');
		$addon_olx = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'addon_olx');
		$email_olx = $this->Rumahku->filterEmptyField($record, 'UserIntegratedOrder', 'email_olx');

		$dateFormat		= 'd M Y H:i';
		$approvedDate	= $this->Rumahku->formatDate($approvedDate, $dateFormat);
		$expiredDate	= $this->Rumahku->formatDate($expiredDate, $dateFormat);
		$paymentDate	= $this->Rumahku->formatDate($paymentDate, $dateFormat);
		$transExpDate	= $this->Rumahku->formatDate($transExpDate, $dateFormat);
		$orderDate		= $this->Rumahku->formatDate($orderDate, $dateFormat);

		$rows = array(
			array(
					$this->Html->tag('h2', $invNumber), '', '', $this->Html->tag('strong', __('Tgl. Pengajuan')), $orderDate
			),
			array(
					ucfirst($paymentStatus), '', '', $this->Html->tag('strong', __('Tgl. Disetujui')), $approvedDate
			),
			array(
					'', '', '', $this->Html->tag('strong', __('Tgl. Expired')), $expiredDate
			), 
		);

		$rows = array_merge($rows, array(
			array(
				array($this->Html->tag('hr', ''), array(
					'colspan' => 5, 
				)), 
			),
			array(
				$this->Html->tag('h3', __('Detail Order')), 
				'', 
				'', 
				$this->Html->tag('h3', __('Detail Invoice')), 
				'', 
			),
			array(
				$this->Html->tag('strong', __('Status Order')), 
				ucfirst($orderStatus), 
				'', 
				$this->Html->tag('strong', __('Nomor Invoice')), 
				$this->Html->tag('strong', $invNumber), 
			),
			array(
				$this->Html->tag('strong', __('Nomor Order')), 
				$this->Html->tag('strong', $orderNumber), 
				'', 
				$this->Html->tag('strong', __('Nama Membership Rumah 123')),
				$R123packageName,
			),
			array(
				$this->Html->tag('strong', __('Nama')), 
				$name,
				'',
				$this->Html->tag('strong', __('Harga Membership Rumah 123 (%s)', $currency)), 
				$R123packagePrice, 
			),
			array(
				$this->Html->tag('strong', __('Telepon')), 
				$phone, 
				'', 
				$this->Html->tag('strong', __('Nama Membership OLX')), 
				$OLXpackageName, 
			),
			array(
				$this->Html->tag('strong', __('Perusahaan')), 
				$companyName, 
				'',
				$this->Html->tag('strong', __('Harga Membership OLX (%s)', $currency)), 
				$OLXpackagePrice, 
			),
		));

		if ($all_addon) {
			$rows = array_merge($rows, array(
				array(
					$this->Html->tag('strong', __('Email All Addon')), 
					$mail_all_addon, 
					'',
				)
			));
		} else {
			if ($addon_r123) {
				$rows = array_merge($rows, array(
					array(
						$this->Html->tag('strong', __('Email Addon R123')), 
						$email_r123, 
						'',
					)
				));
			}
			if ($addon_olx) {
				$rows = array_merge($rows, array(
					array(
						$this->Html->tag('strong', __('Email Addon OLX')), 
						$email_olx, 
						'',
					)
				));
			}
		}

		$rows = array_merge($rows, array(
			array(
				'',
				'',
				'',
				$this->Html->tag('strong', __('Potongan (%s)', $currency)), 
				$discountAmount,
			),
			array(
				'', 
				'', 
				'', 
				$this->Html->tag('strong', __('Total (%s)', $currency)), 
				$totalAmount, 
			),

		));

		$content	= $this->Html->tag('tbody', $this->Html->tableCells($rows));
		$content	= $this->Html->tag('table', $content);
		$filename	= $filename.'-'.$invNumber;
	}
	else{
		$content = $this->Html->tag('div', __('Data tidak ditemukan'), array('class' => 'wrapper-border'));
	}
// debug($rows);die();
	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment; filename='.$filename.'.xls');
	header('Content-Transfer-Encoding: binary');

	echo($content);

?>