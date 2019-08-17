<?php
		$paymentStatus  = Common::hashEmptyField($record, 'UserIntegratedOrderAddon.payment_status');

		$expiredDate 	= Common::hashEmptyField($record, 'UserIntegratedOrderAddon.expired_date');
		$paymentDate 	= Common::hashEmptyField($record, 'UserIntegratedOrderAddon.payment_datetime');
		$paymentCode 	= Common::hashEmptyField($record, 'UserIntegratedOrderAddon.payment_code');
		$transExpDate 	= Common::hashEmptyField($record, 'UserIntegratedOrderAddon.transfer_expired_date');

		$orderDate 		= Common::hashEmptyField($record, 'UserIntegratedOrder.created');

		$dateFormat		= 'd M Y H:i';
		$expiredDate	= $this->Rumahku->formatDate($expiredDate, $dateFormat);
		$paymentDate	= $this->Rumahku->formatDate($paymentDate, $dateFormat);
		$transExpDate	= $this->Rumahku->formatDate($transExpDate, $dateFormat);
		$orderDate		= $this->Rumahku->formatDate($orderDate, $dateFormat);
?>

<div class="col-xs-12 col-md-7">
	<?php

		$statuses = array(
			'pending'	=> $this->Html->tag('span', __('Pending'), array('class' => 'badge')), 
			'process'	=> $this->Html->tag('span', __('Process'), array('class' => 'badge badge-warning')),
			'expired'	=> $this->Html->tag('span', __('Expired'), array('class' => 'badge badge-inverse')), 
			'paid'		=> $this->Html->tag('span', __('Paid'), array('class' => 'badge badge-success')), 
			'failed'	=> $this->Html->tag('span', __('Failed'), array('class' => 'badge badge-danger')), 
			'waiting'	=> $this->Html->tag('span', __('Waiting'), array('class' => 'badge badge-warning')), 
		);

		$badge = !empty($statuses[$paymentStatus]) ? $statuses[$paymentStatus] : '-';

		$template = $this->Html->tag('div', $this->Html->tag('b', $this->Html->tag('h2', $invoiceNumber)), array('class' => 'col-xs-12 col-sm-6 col-md-12'));
		$template.= $this->Html->tag('div', $badge, array('class' => 'col-xs-12 col-sm-6 col-md-12'));
		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);

	?>
</div>
<div class="col-xs-12 col-md-5">
	<?php

		$template = $this->Html->tag('div', $this->Html->tag('b', __('Tgl. Daftar')), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
		$template.= $this->Html->tag('div', $orderDate, array('class' => 'col-xs-12 col-sm-6 col-md-6'));
		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);

		$template = $this->Html->tag('div', $this->Html->tag('b', __('Tgl. Expired Dokumen')), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
		$template.= $this->Html->tag('div', $expiredDate, array('class' => 'col-xs-12 col-sm-6 col-md-6'));
		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);

		if(in_array($paymentStatus, array('waiting', 'paid'))){
			if($paymentStatus == 'waiting'){
				$template = $this->Html->tag('div', $this->Html->tag('b', __('Kode Pembayaran')), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
				$template.= $this->Html->tag('div', $this->Html->tag('b', $paymentCode), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
				$template = $this->Html->tag('div', $template, array('class' => 'row'));

				echo($template);
			}

			$caption = $paymentStatus == 'waiting' ? 'Batas ' : 'Tgl. ';
			$caption.= 'Pembayaran';

			$displayDate = $paymentStatus == 'waiting' ? $transExpDate : $paymentDate;

			$template = $this->Html->tag('div', $this->Html->tag('b', __($caption)), array('class' => 'col-xs-12 col-sm-6 col-md-6'));
			$template.= $this->Html->tag('div', $displayDate, array('class' => 'col-xs-12 col-sm-6 col-md-6'));
			$template = $this->Html->tag('div', $template, array('class' => 'row'));

			echo($template);
		}

	?>
</div>