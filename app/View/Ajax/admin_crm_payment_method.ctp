<?php 
		$payment_type = !empty($payment_type)?$payment_type:false;
?>
<div id="wrapper-kpr-write" class="crm-kpr-payment crm">
	<?php 
			switch ($payment_type) {
				case 'kpr':
					echo $this->element('blocks/kpr/forms/info');
					break;
				
				default:
					# code...
					break;
			}
   	?>
</div>