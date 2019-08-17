<?php
		$currency = Configure::read('__Site.config_currency_symbol');
		$dataSharingKpr = $this->Rumahku->filterEmptyField($params, 'params_sharing_kpr', 'SharingKpr');
		$datakprBank = $this->Rumahku->filterEmptyField($params, 'params_log_kpr', 'KprBank');
		$datakprBankInstallment = $this->Rumahku->filterEmptyField($params, 'params_log_kpr', 'KprBankInstallment');
		$dataBank = $this->Rumahku->filterEmptyField($params, 'params_log_kpr', 'Bank');

		$logid = $this->Rumahku->filterEmptyField($datakprBank, 'id');
		$bank_code = $this->Rumahku->filterEmptyField($dataBank, 'code');

		$sender_name = $this->Rumahku->filterEmptyField($dataSharingKpr, 'sender_name');
		$mls_id = $this->Rumahku->filterEmptyField($dataSharingKpr, 'mls_id');
		$property_price = $this->Rumahku->filterEmptyField($datakprBankInstallment, 'property_price');
		$dp_price = $this->Rumahku->filterEmptyField($datakprBankInstallment, 'down_payment');
		$loan_price = $this->Rumahku->filterEmptyField($datakprBankInstallment, 'loan_price');
		$dp = $this->Kpr->_setPercentDp($property_price, $dp_price);

		$credit_total = $this->Rumahku->filterEmptyField($datakprBankInstallment, 'credit_total');

		$customPrice = $this->Rumahku->getFormatPrice($property_price);
		$customLoanPrice = $this->Rumahku->getFormatPrice($loan_price);
		$customDpPrice = $this->Rumahku->getFormatPrice($dp_price);
        $customLoanTime = sprintf("%s Bulan (%s Tahun)", $credit_total*12, $credit_total);
?>
<table align="center" width="600" style="background:#fff" cellpadding="0" cellspacing="0" border="0">
  	<tbody>
	  	<tr>
	    	<td style="padding: 0 20px;">
      			<?php
						echo $this->Html->tag('p', sprintf(__('Anda mendapatkan email yang berisi tentang info KPR dari %s, silahkan klik link di bawah ini untuk melihat detail data KPR.'), $sender_name));
	      		?>
      			<table cellpadding="0" cellspacing="0" border="0">
      				<tbody>
	        			<tr>
	          				<td>
					            <div>
					              	<table style="line-height: 1.5em;">
					              		<tbody>
						                	<tr>
						                  		<td width="200">
						                  			<?php  
						                  					echo __('Properti ID');
						                  			?>
						                  		</td>
						                  		<td>
						                  			<?php  
					                  						printf(__(': %s'), $mls_id);

					                  						if( !empty( $mls_id ) ){
							                  					echo ' - ';
							                  					$link = $this->Html->url(array(
																	'controller' => 'properties', 
																	'action' => 'shorturl',
																	$mls_id,
																	'admin' => false,
																), true);
												      			echo $this->Html->link(__('Lihat Properti'), $link, array(
																	'style' => 'text-decoration: none; cursor: pointer;'
																));
							                  				}
						                  			?>
						                  		</td>
						                	</tr>
						                	<tr>
						                  		<td width="200">
						                  			<?php  
						                  					echo __('Harga Properti');
						                  			?>
						                  		</td>
						                  		<td>
						                  			<?php  
					                  						echo sprintf(__(': %s %s'), $currency, $customPrice);
						                  			?>
						                  		</td>
						                	</tr>
						                	<tr>
						                  		<td width="200">
						                  			<?php  
						                  					echo __('Uang Muka');
						                  			?>
						                  		</td>
						                  		<td>
						                  			<?php  
					                  						printf(__(': %s%% ( %s %s )'), $dp, $currency, $customDpPrice);
						                  			?>
						                  		</td>
						                	</tr>
						                	<tr>
						                  		<td width="200">
						                  			<?php  
						                  					echo __('Jumlah Pinjaman');
						                  			?>
						                  		</td>
						                  		<td>
						                  			<?php  
					                  						echo sprintf(__(': %s %s'), $currency, $customLoanPrice);
						                  			?>
						                  		</td>
						                	</tr>
						                	<tr>
						                  		<td width="200">
						                  			<?php  
						                  					echo __('Jangka Waktu');
						                  			?>
						                  		</td>
						                  		<td>
						                  			<?php  
					                  						printf(__(': %s'), $customLoanTime);
						                  			?>
						                  		</td>
						                	</tr>
						                </tbody>
					              	</table>
					            </div>
					        </td>
					    </tr>
					</tbody>
      			</table>
	    	</td>
	  	</tr>
	  	<tr>
	    	<td style="height: 20px; padding-top:20px;">
	      		<?php
	      				$link = $this->Html->url(array(
							'controller' => 'kpr',
							'action' => 'bank_calculator',
							'slug' => 'kalkulator-kpr',
							'bank_code' => $bank_code,
							'mls_id' => $mls_id,
							'logid' => $logid,
						), true);
						
		      			echo $this->Html->link(__('Lihat Rincian'), $link, array(
							'style' => 'width: 200px; padding:10px 15px; background:#204798; color: #fff; text-decoration: none; border-radius: 3px; margin: 20px 0 20px 190px; text-align: center;'
						));
	      		?>
	    	</td>
	  	</tr>
	</tbody>
</table>
