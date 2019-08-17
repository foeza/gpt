<?php
		
		$bank_name = $this->Rumahku->filterEmptyField($bank, 'Bank', 'name');
		$kpr_bank_installments = $this->Rumahku->filterEmptyField($val, 'KprBank', 'KprBankInstallment');
		$kpr_bank_installment = !empty($kpr_bank_installments[0]) ? $kpr_bank_installments[0] : false;
		
		if(empty($kpr_bank_installment)){
			$kpr_bank_installment['KprBankInstallment'] = $kpr_bank_installments;
		}

		$header_title = $this->Html->tag('h1', __('Rincian Properti KPR %s', $bank_name), array(
			'style' => 'border-bottom: 1px solid #ccc; font-size: 18px; font-weight: 700; padding-bottom: 10px; color: #000;'
		));

		$property_price = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'property_price');
		$down_payment = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'down_payment');
		$loan_price = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'loan_price');
		$total_first_credit = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'total_first_credit');
		$credit_total = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'credit_total');
		$interest_rate_fix = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'interest_rate_fix');
		$interest_rate_cabs = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'interest_rate_cabs');
		$periode_fix = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'periode_fix');
		$periode_cab = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'periode_cab');

		$customDp = $this->Rumahku->getCurrencyPrice($down_payment, '-');
		$customPrice = $this->Rumahku->getCurrencyPrice($property_price, '-'); 
		$customLoanPrice = $this->Rumahku->getCurrencyPrice($loan_price, '-');
		$customFirstCredit = $this->Rumahku->getCurrencyPrice($total_first_credit, '-');
?>

<tr>
	<td>
		<?php
  			echo $this->Html->tag('h1', __('Rincian KPR %s', $bank_name), array(
				'style' => 'border-bottom: 1px solid #ccc; font-size: 18px; font-weight: 700; padding-bottom: 10px; color: #000;'
			));
  		?>
			<table cellpadding="0" cellspacing="0" border="0">
				<tbody>
	    			<tr>
	      				<td>
				            <div>
				              	<table style="line-height: 1.5em;">
				              		<tbody>
				              			<?php
				              					$options = array(
				              						'labelStyle' => array(
				              							'width' => '200',
				              						),
				              					);

				              					echo $this->Kpr->setrowEmail(__('Harga Properti'), $customPrice, $options);
				              					echo $this->Kpr->setrowEmail(__('Jumlah Pinjaman'), $customLoanPrice, $options);
				              					echo $this->Kpr->setrowEmail(__('Cicilan'), $customFirstCredit, $options);
				              					echo $this->Kpr->setrowEmail(__('Jangka Waktu'), sprintf('%s Tahun', $credit_total), $options);
				              					
				              			?>
					                </tbody>
				              	</table>
				            </div>
				        </td>
				    </tr>
				</tbody>
			</table>
	</td>
</tr>