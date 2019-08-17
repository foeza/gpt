<?php 
		$id = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'id');
		$code = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'code');
		$commission = $this->Rumahku->filterEmptyField($params, 'KprCommissionPaymentConfirm', 'commission');
		$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain', FULL_BASE_URL);

		$commission = $this->Rumahku->getCurrencyPrice($commission);
?>
<table align="center" width="600" style="background:#fff" cellpadding="0" cellspacing="0" border="0">
  	<tbody>
	  	<tr>
	  		<td style="padding: 0 20px;">
	      		<?php
	      				echo $this->Html->tag('h1', sprintf(__('Pembayaran Provisi KPR %s'), $code), array(
							'style' => 'border-bottom: 1px solid #ccc; font-size: 18px; font-weight: 700; padding-bottom: 10px; color: #4a4a4a; text-align:center;margin: 20px 0;line-height: 24px;'
						));
	      		?>
	    	</td>
	  	</tr>
	  	<tr>
	    	<td style="padding: 0 20px;">
	      		<?php
	      				echo $this->Html->tag('h4', __('Dibayarkan oleh:'), array(
							'style' => 'margin-top:-10px; font-weight:400; text-align:left; padding-bottom: 10px;font-size: 14px;line-height: 24px;margin: 0;'
						));
						echo $this->element('emails/html/kpr/bank');
	      		?>
	    	</td>
	  	</tr>
	  	<tr>
	    	<td style="padding: 0 20px;">
	    		<?php
		      			echo $this->Html->tag('p', sprintf(__('Provisi telah dibayarkan kepada Rumahku.com sebesar %s'), $this->Html->tag('strong', $commission)), array(
							'style' => 'border-top: 1px solid #ccc; padding-top:20px;font-size: 14px;'
						));
	      		?>
	    	</td>
	  	</tr>
	</tbody>
</table>
