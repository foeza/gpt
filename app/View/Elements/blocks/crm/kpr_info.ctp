<?php
	$activity = !empty($activity)?$activity:false;
	$value = !empty($value)?$value:false;
	
	$kpr_application_request = $this->Rumahku->filterEmptyField($activity, 'KprApplicationRequest');
	$kprApplication 		 = $this->Rumahku->filterEmptyField($activity, 'KprApplication');
	$property_action 		 = $this->Rumahku->filterEmptyField($value, 'PropertyAction', 'inactive_name');

	$crm_project_id = $this->Rumahku->filterEmptyField($activity, 'CrmProject', 'id');

	if(!empty($kpr_application_request)){
?>

		<div class="payment-info mb15">
<?php
		echo $this->Html->tag('p', $this->Html->tag('strong', __('Pengajuan KPR')), array(
			'class' => 'tag',
		));
?>
		</div>
<?php

			foreach($kpr_application_request AS $key => $value){
				echo '<div class="payment-info mb15">';

				$bank_name 			= $this->Rumahku->filterEmptyField($value,'Bank','name');
				$down_payment 		= $this->Rumahku->filterEmptyField($value,'KprApplicationRequest','down_payment');
				$credit_total 		= $this->Rumahku->filterEmptyField($value,'KprApplicationRequest','credit_total');
				$total_first_credit = $this->Rumahku->filterEmptyField($value,'KprApplicationRequest','total_first_credit');

				if($bank_name){
					echo $this->Html->tag('p', sprintf(__('Bank Pengajuan: %s'), $this->Html->tag('strong', $bank_name)), array(
						'class' => 'tag',
					));
				}

				if($down_payment){
					echo $this->Html->tag('p', sprintf(__('Down Payment: %s'), $this->Html->tag('strong', $this->Rumahku->getCurrencyPrice($down_payment))), array(
						'class' => 'tag',
					));
				}

				if($credit_total){
					echo $this->Html->tag('p', sprintf(__('Total Kredit: %s'), $this->Html->tag('strong', sprintf('%s %s',$credit_total,__('Tahun')))), array(
						'class' => 'tag',
					));
				}

				if($total_first_credit){
					echo $this->Html->tag('p', sprintf(__('Cicilan Pertama: %s'), $this->Html->tag('strong', $this->Rumahku->getCurrencyPrice($total_first_credit))), array(
						'class' => 'tag',
					));
				}

				echo '</div>';

			}
?>
		<div class="payment-info mb15">
<?php
			echo $this->Html->link(__('Lihat Detil'), array(
				'controller' => 'crm',
				'action' => 'project_submission',
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