<?php 
		$data = $this->request->data;
		$payment_type = !empty($payment_type)?$payment_type:false;
		$payment_type = $this->Rumahku->filterEmptyField($data, 'CrmProjectPayment', 'type', $payment_type);
?>
<div id="wrapper-kpr-write">
	<?php 
			switch ($payment_type) {
				case 'kpr':
					echo $this->element('blocks/kpr/forms/info');
					break;
			}
   	?>
</div>