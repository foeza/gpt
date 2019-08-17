<?php
		$dp = $this->Rumahku->filterEmptyField($params, 'KprBankInstallment', 'down_payment');
		$loan_price = $this->Rumahku->filterEmptyField($params, 'KprBankInstallment', 'loan_price');
		$credit_total = $this->Rumahku->filterEmptyField($params, 'KprBankInstallment', 'credit_total');
		$total_first_credit = $this->Rumahku->filterEmptyField($params, 'KprBankInstallment', 'total_first_credit');
		$mls_id = $this->Rumahku->filterEmptyField($params, 'KprBank', 'mls_id');

		$total_first_credit = $this->Rumahku->getCurrencyPrice($total_first_credit);
		$dp = $this->Rumahku->getCurrencyPrice($dp);
		$loan_price = $this->Rumahku->getCurrencyPrice($loan_price);
   	 	$price = $this->Rumahku->filterEmptyField($params, 'KprBankInstallment', 'property_price');
		$product_units = Common::hashEmptyField($params, 'KprProduct');

   	 	if( !empty($price) ) {
        	$price = $this->Rumahku->getCurrencyPrice($price);
        } else {
        	$price = $this->Property->getPrice($params);
        }

		echo $this->Html->tag('h1', __('Rincian Informasi KPR'), array(
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
	          					if( !empty($product_units) ) {
									$domain = Common::hashEmptyField($params, 'Project.domain');
									// $link = $this->Html->url(array(
									// 	'controller' => 'pages', 
									// 	'action' => 'detail_unit',
							  //           $unit_id, 
							  //           'product' => $product_id,
							  //           'admin'=> false,
									// ));
	          						
		          					echo $this->Rumahku->_callLbl('table', __('Unit ID'), $mls_id);
	          					} else {
									$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain');
									$link = $domain.$this->Html->url(array(
										'controller' => 'properties', 
										'action' => 'shorturl',
							            'mlsid' => $mls_id,
										'admin' => false,
									));

		          					echo $this->Rumahku->_callLbl('table', __('Properti ID'), sprintf(__(': %s - %s'), $mls_id, $this->Html->link(__('Lihat Properti'), $link, array(
										'style' => 'text-decoration: none; cursor: pointer;'
									))));
	          					}

	          					echo $this->Rumahku->_callLbl('table', __('Harga Properti'), sprintf(__(': %s'), $price));
	          					echo $this->Rumahku->_callLbl('table', __('Uang Muka'), sprintf(__(': %s'), $dp));
	          					echo $this->Rumahku->_callLbl('table', __('Jumlah Pinjaman'), sprintf(__(': %s'), $loan_price));
	          					echo $this->Rumahku->_callLbl('table', __('Jangka Waktu'), sprintf(__(': %s Tahun'), $credit_total));
	          					echo $this->Rumahku->_callLbl('table', __('Angsuran Per Bulan'), sprintf(__(': %s'), $total_first_credit));
	          			?>
	                </tbody>
              	</table>
            </div>
        </td>
    </tr>
</tbody>
</table>