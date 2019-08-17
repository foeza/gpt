<?php 
		$activity = !empty($activity)?$activity:false;
		$value = !empty($value)?$value:false;

		$dataPayment = $this->Rumahku->filterEmptyField($activity, 'CrmProjectPayment');
		$kprApplication = $this->Rumahku->filterEmptyField($activity, 'KprApplication');
		$property_action = $this->Rumahku->filterEmptyField($value, 'PropertyAction', 'inactive_name');

		$crm_project_id = $this->Rumahku->filterEmptyField($activity, 'CrmProject', 'id');

		if( !empty($kprApplication) ) {
			$name = trim($this->Rumahku->filterEmptyField($kprApplication, 'name'));
			$income = $this->Rumahku->filterEmptyField($kprApplication, 'income');
			$no_hp = $this->Rumahku->filterEmptyField($kprApplication, 'no_hp');

			$income = $this->Rumahku->getCurrencyPrice($income);
?>
<div class="payment-info mb15">
	<?php 
			echo $this->Html->tag('p', $this->Html->tag('strong', __('Informasi KPR')), array(
				'class' => 'tag',
			));

			if( !empty($name) ) {
				echo $this->Html->tag('p', sprintf(__('Nama Pengaju: %s'), $this->Html->tag('strong', $name)), array(
					'class' => 'tag',
				));
			}
			if( !empty($income) ) {
				echo $this->Html->tag('p', sprintf(__('Penghasilan /Bulan: %s'), $this->Html->tag('strong', $income)), array(
					'class' => 'tag',
				));
			}
			if( !empty($no_hp) ) {
				echo $this->Html->tag('p', sprintf(__('No. Handphone: %s'), $this->Html->tag('strong', $no_hp)), array(
					'class' => 'tag',
				));
			}

			echo $this->Html->link(__('Lihat Detil'), array(
				'controller' => 'crm',
				'action' => 'project_payment',
				$crm_project_id,
				'admin' => true,
			), array(
				'class' => 'tag',
			));
	?>
</div>
<?php
		} else if( !empty($dataPayment) ) {
			$dp = $this->Rumahku->filterEmptyField($dataPayment, 'down_payment');
			$payment_type = $this->Rumahku->filterEmptyField($dataPayment, 'type');
			$price = $this->Rumahku->filterEmptyField($dataPayment, 'price');
			$credit_total = $this->Rumahku->filterEmptyField($dataPayment, 'credit_total');
			$interest_rate = $this->Rumahku->filterEmptyField($dataPayment, 'interest_rate');

			$customPayment_type = strtoupper($payment_type);
			$price = $this->Rumahku->getCurrencyPrice($price);
			$dp = $this->Rumahku->getCurrencyPrice($dp);

?>
<div class="payment-info mb15">
	<?php 
			echo $this->Html->tag('p', sprintf(__('Tipe Pembayaran: %s'), $this->Html->tag('strong', $customPayment_type)), array(
				'class' => 'tag',
			));

			if( !empty($price) ) {
				echo $this->Html->tag('p', sprintf(__('Harga %s: %s'), $property_action, $this->Html->tag('strong', $price)), array(
					'class' => 'tag',
				));
			}
			if( !empty($dp) ) {
				echo $this->Html->tag('p', sprintf(__('DP: %s'), $this->Html->tag('strong', $dp)), array(
					'class' => 'tag',
				));
			}

			echo $this->Html->link(__('Lihat Detil'), array(
				'controller' => 'crm',
				'action' => 'project_payment',
				$crm_project_id,
				'admin' => true,
			), array(
				'class' => 'tag',
			));
	?>
</div>
<?php
		}
?>