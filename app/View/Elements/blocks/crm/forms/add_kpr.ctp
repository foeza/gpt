<?php 
		$data 					= $this->request->data;
		$currency 				= Configure::read('__Site.config_currency_symbol');
		$actionId 				= $this->Rumahku->filterEmptyField($value, 'PropertyAction', 'id');
		$actionName 			= $this->Rumahku->filterEmptyField($value, 'PropertyAction', 'inactive_name');
		$payment_type 			= $this->Rumahku->filterEmptyField($value, 'CrmProjectPayment', 'type');
		$step 					= $this->Rumahku->filterEmptyField($value, 'CrmProjectActivity', 'step');
		$property_type_name 	= $this->Rumahku->filterEmptyField($value,'PropertyType','name');
		$loan_price 			= $this->Rumahku->filterEmptyField($value,'KprApplication','loan_price');
		$dataPayment 			= $this->Rumahku->filterEmptyField($value, 'CrmProjectPayment');
		$down_payment 			= $this->Rumahku->filterEmptyField($dataPayment,'down_payment');
		$price 					= $this->Rumahku->filterEmptyField($dataPayment, 'price');
		$persen_loan 			= $this->Rumahku->filterEmptyField($dataPayment, 'persen_loan');
		$mls_id 				= $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');
		$credit_total 			= $this->Rumahku->filterEmptyField($dataPayment, 'credit_total');
		$interest_rate 			= $this->Rumahku->filterEmptyField($dataPayment, 'interest_rate');
		$default_dp = Configure::read('__Site.bunga_kpr');
		//COUNT
		$cnt_ready 				= $this->Rumahku->filterEmptyField($count_bank, 'cnt_ready',false,0);
		$cnt_total 				= $this->Rumahku->filterEmptyField($count_bank, 'cnt_total',false,0); 


		
		// Set Build Input Form
		$options = array(
			'formGroupClass' => false,
			'wrapperClass' => 'wrapper-input',
		    'frameClass' => false,
		    'labelClass' => false,
		    'class' => false,
		);
		if($payment_type == 'kpr' && !empty($banks)){
?>
<div class="col-sm-12">
	<div class="mortgage-bank-list" id="mortgage-bank-list">
		<div id="crumbtitle" class="clear">
			<row>
				<div class="col-sm-6">
				<?php
					$dataColumns = array(
				            'checkall' => array(
				                'name' => $this->Rumahku->buildCheckOption('CrmProjectActivity'),
				                'class' => 'tacenter',
				            ),
				        );

					$fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table' );

					if( !empty($fieldColumn) ) {
		                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
		            }
					$labelSummary =sprintf('%s dari %s Bank KPR sesuai dengan kebutuhan Anda', $cnt_ready, $cnt_total);

				?>
				</div>
				<div class="col-sm-6">
					<div class="taright color-grey">
						<span class=""><?php echo $labelSummary; ?></span>
					</div>
				</div>
			</row>
		</div>
		<ul>
			<?php
					foreach($banks AS $key => $val){

						$kpr_application_request = !empty($data['KprApplicationRequest'][$key])?$data['KprApplicationRequest'][$key]:false;
						$bank_commission_setting_loan = $this->Rumahku->filterEmptyField($val, 'BankCommissionSettingLoan');

						if(!empty($bank_commission_setting_loan)){
							$komisi_agen = $this->Rumahku->filterEmptyField($bank_commission_setting_loan, 'rate');
							$komisi_rku = $this->Rumahku->filterEmptyField($bank_commission_setting_loan, 'rate_company');

							$com_agen['rate_komisi'] = $komisi_agen;
							$com_rku['rate_komisi'] = $komisi_rku;

						}

						$com_agen = !empty($com_agen)?$com_agen:false;
						$com_rku = !empty($com_rku)?$com_rku:false;
						

						if($kpr_application_request){
        					$id = $this->Rumahku->filterEmptyField($kpr_application_request, 'id');
	                        $periode_installment = $this->Rumahku->filterEmptyField($kpr_application_request, 'credit_total');
	                        $rate = $this->Rumahku->filterEmptyField($kpr_application_request, 'interest_rate_fix');            
	                        $dp_bank = $this->Rumahku->filterEmptyField($kpr_application_request, 'down_payment', $default_dp);
	                        $interest_rate_float = $this->Rumahku->filterEmptyField($val, 'BankSetting', 'interest_rate_float');
	                        $credit_fix = $this->Rumahku->filterEmptyField($val, 'BankSetting', 'periode_fix');
	                        $work_day = $this->Rumahku->filterEmptyField($val, 'BankSetting', 'work_day');

						}else{
                            $id = $this->Rumahku->filterEmptyField($val, 'Bank', 'id');
                            $periode_installment = $this->Rumahku->filterEmptyField($val, 'BankSetting', 'periode_installment');
                            $rate = $this->Rumahku->filterEmptyField($val, 'BankSetting', 'interest_rate_fix');            
                            $dp_bank = $this->Rumahku->filterEmptyField($val, 'BankSetting', 'dp', $default_dp);            
                            $interest_rate_float = $this->Rumahku->filterEmptyField($val, 'BankSetting', 'interest_rate_float');
                            $credit_fix = $this->Rumahku->filterEmptyField($val, 'BankSetting', 'periode_fix');
                            $work_day = $this->Rumahku->filterEmptyField($val, 'BankSetting', 'work_day');
                            $rate_komisi = $this->Rumahku->filterEmptyField($val, 'BankCommissionSettingLoan', 'rate', null);
                            $setting_loan_id = $this->Rumahku->filterEmptyField($val, 'BankCommissionSettingLoan', 'id', null);
                            $flag_ready = !empty($val['flag_ready'])?$val['flag_ready']:false;
                           
						}

                        echo $this->element('blocks/kpr/forms/banks', array(
                            'key' => $key,
                            'val' => $val,
                            'id' => $id,
                            'periode_installment' => $periode_installment,
                            'setting_loan_id' => $setting_loan_id,
                            'rate' => $rate,
                            'dp_bank' => $dp_bank,
                            'interest_rate_float' => $interest_rate_float,
                            'credit_fix' => $credit_fix,
                            'work_day' => $work_day,
                            'rate_komisi' => $rate_komisi,
                            'com_agen' => $com_agen,
                            'com_rku' => $com_rku,
                            'flag_ready' => $flag_ready,
                            'calculator' => true,
                        ));
					}
			?>
		</ul>
	</div>
</div>
<?php
		}

		echo $this->Form->input('KprApplicationRequest.price',array(
			'type' => 'hidden',
			'default' => $price,
			'disabled',
			'class' => 'KPR-price',
		));
?>
