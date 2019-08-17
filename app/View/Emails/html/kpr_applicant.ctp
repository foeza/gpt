<?php 
		$prime_url = FULL_BASE_URL;
		$currency = Configure::read('__Site.config_currency_symbol');
		$property = $this->Rumahku->filterEmptyField($params, 'property');
		$kprBank['KprBank'] = $this->Rumahku->filterEmptyField($params, 'KprBank');
		$bank['Bank'] = $this->Rumahku->filterEmptyField($params, 'Bank');

		$id = $this->Rumahku->filterEmptyField($params, 'kpr_bank_id');
		$code = $this->Rumahku->filterEmptyField($params, 'Kpr', 'code');
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Kpr', 'mls_id');
		$property_price = $this->Rumahku->filterEmptyField($params, 'Kpr', 'property_price');
		$loan_price = $this->Rumahku->filterEmptyField($params, 'Kpr', 'loan_price');
		$dp = $this->Rumahku->filterEmptyField($params, 'Kpr', 'down_payment');
		$interest_rate = $this->Rumahku->filterEmptyField($params, 'Kpr', 'interest_rate');
		$credit_total = $this->Rumahku->filterEmptyField($params, 'Kpr', 'credit_total');
		
		$ktp = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'ktp');
		$full_name = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'full_name');
		$name = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'name', $full_name);
		$email = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'email');
		$phone = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'phone');
		$createdCustom = $this->Rumahku->formatDate(date('Y-m-d'), 'd/m/Y');

		$first_credit = $this->Kpr->creditFix($loan_price, $interest_rate, $credit_total);
		$customDp = $this->Rumahku->getCurrencyPrice($dp, '-', $currency);
		$customPrice = $this->Rumahku->getCurrencyPrice($property_price, '-', $currency); 
		$customLoanPrice = $this->Rumahku->getCurrencyPrice($loan_price, '-', $currency);
		$customFirstCredit = $this->Rumahku->getCurrencyPrice($first_credit, '-', $currency);

		$customMlsId = $mls_id;
		if( $mls_id != '-' ) {
			$label = $this->Property->getNameCustom($property);
			$slug = $this->Rumahku->toSlug($label);

			$url = array(
                'controller'=> 'properties',
                'action' => 'detail',
                'mlsid' => $mls_id,
                'slug'=> $slug, 
                'admin'=> false,
            );
            $customMlsId = $this->Html->link($mls_id, $url, array(
            	'target' => '_blank',
            ));
		}
?>
<table align="center" width="600" style="background:#fff" cellpadding="0" cellspacing="0" border="0">
  	<tbody>
  		<tr>
	  		<td style="padding: 0 20px;">
	      		<?php
	      				echo $this->Html->tag('h1', sprintf(__('%s telah mengajukan KPR'), $name), array(
							'style' => 'font-size: 18px; font-weight: 700; color: #4a4a4a; text-align:center;'
						));
	      		?>
	    	</td>
	  	</tr>
	  	<tr>
	  		<td>
	      		<?php
	      			echo $this->Html->tag('h1', __('Pemohon'), array(
						'style' => 'border-bottom: 1px solid #ccc; font-size: 18px; font-weight: 700; padding-bottom: 10px; color: #000;'
					));
	      		?>

	      		<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
	      			<tbody>
		        		<tr>
		          			<td>
					            <div>
					              	<table style="line-height: 1.5em;">
					                	<tr>
					                  		<td width="200">
					                  			<?php  
					                  				echo __('Tanggal Pengajuan');
					                  			?>
					                  		</td>
					                  		<td>
					                  			<?php  
					                  				echo sprintf(__(': %s'), $createdCustom);
					                  			?>
					                  		</td>
					                	</tr>
					                	<tr>
					                  		<td width="200">
					                  			<?php  
					                  				echo __('KTP');
					                  			?>
					                  		</td>
					                  		<td>
					                  			<?php  
					                  				echo sprintf(__(': %s'), $ktp);
					                  			?>
					                  		</td>
					                	</tr>
					                	<tr>
					                  		<td width="200">
					                  			<?php  
					                  				echo __('Nama');
					                  			?>
					                  		</td>
					                  		<td>
					                  			<?php  
					                  				echo sprintf(__(': %s'), $name);
					                  			?>
					                  		</td>
					                	</tr>
					                	<tr>
					                  		<td width="200">
					                  			<?php  
					                  				echo __('Email');
					                  			?>
					                  		</td>
					                  		<td>
					                  			<?php  
					                  				echo sprintf(__(': %s'), $email);
					                  			?>
					                  		</td>
					                	</tr>
					                	<tr>
					                  		<td width="200">
					                  			<?php  
					                  				echo __('No. Telp');
					                  			?>
					                  		</td>
					                  		<td>
					                  			<?php  
					                  				echo sprintf(__(': %s'), $phone);
					                  			?>
					                  		</td>
					                	</tr>
					                	<tr>
						                  		<td width="200">
						                  			<?php  
						                  				echo __('Properti ID');
						                  			?>
						                  		</td>
						                  		<td>
						                  			<?php  
						                  				echo sprintf(__(': %s'), $customMlsId);
						                  			?>
						                  		</td>
						                	</tr>
					              	</table>
					            </div>
		          			</td>
		        		</tr>
		        	</tbody>
	      		</table>
	    	</td>
	  	</tr>
		  	<?php
					echo $this->element('emails/html/kpr/list_banks', array(
						'val' => $kprBank,
						'bank' => $bank
					));
		  	?>
	  	<tr>
	    	<td>
	    		<?php
		      			echo $this->Html->tag('h4', __('Untuk melihat detil permohonan KPR dan menindak lanjuti permohonan, klik di sini:'), array(
							'style' => 'border-top: 1px solid #ccc; font-weight:400; padding-top:20px;margin-bottom:10px;'
						));
	      		?>
	    	</td>
	  	</tr>
	  	<tr>
	    	<td style="height: 70px;">
	      		<?php
		      			$link = $prime_url.$this->Html->url(array(
							'controller' => 'kpr', 
							'action' => 'application_detail',
							$id,
							'admin' => true,
						));
		      			echo $this->Html->link(__('Lihat Detil Permohonan'), $link, array(
							'style' => 'width: 200px; padding:10px 15px; background:#204798; color: #fff; text-decoration: none; border-radius: 3px; margin: 10px 0 30px 190px; text-align: center;'
						), array(
							'target' => '_blank',
						));
	      		?>
	    	</td>
	  	</tr>
	</tbody>
</table>
