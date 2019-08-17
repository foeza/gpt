<?php
class RmKprComponent extends Component {

	var $components = array(
		'RmCommon', 'RmUser', 'RmImage',
		'RmCrm', 'Auth', 'RmProperty'
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
		$this->User = ClassRegistry::init('User');
	}

	function getCalculatecostKPR($value,$bank){
		$costKPR = array();
		$get_kpr_app 	= $this->RmCommon->filterEmptyField($value,'KprApplication');
		$bank_setting 	= $this->RmCommon->filterEmptyField($bank,'BankSetting');
		if($get_kpr_app && $bank_setting){

			$id 				 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'id');
            $code 				 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'code', '-');
            $status 			 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'status_desc', '-');
            $full_name 			 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'full_name', '-');
            $gender_id 			 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'gender_id');
            $birthplace 		 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'birthplace');
            $birthday 			 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'birthday');
            $email 				 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'email', '-');
            $address 			 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'address', '-');
            $address_domisili 	 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'address_2', '-');
            $rt 				 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'rt');
            $rw 				 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'rw');
            $phone 				 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'phone');
            $no_hp 				 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'no_hp', '-');
            $no_hp_2 			 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'no_hp_2');
            $created 			 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'created');
            $approved_admin_date = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'approved_admin_date');
            $note 				 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'note');
            $job_type 			 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'job_type', '-');

            $mls_id 			 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'mls_id', '-');
            $other_installment 	 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'other_installment', '-');
            $property_price 	 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'property_price', 0);
            $loan_price 		 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'loan_price', 0);
            $interest_rate 		 = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'interest_rate_fix', 0);
            $floating_rate 		 = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'interest_rate_float',0);
            $persen_loan 		 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'persen_loan', 0);

            $credit_fix 		 = $this->RmCommon->filterEmptyField($value, 'BankSetting', 'periode_fix', 0);
            $creedit_total 		 = $this->RmCommon->filterEmptyField($value,'CrmProjectPayment','credit_total'); 
            $credit_float  		 = $creedit_total - $credit_fix;

            // BANK CHARGE
            $provision 			 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'provision', 0);
            $insurance 			 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'insurance', 0);
            $appraisal 			 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'appraisal', 0);
            $administration 	 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'administration', 0);

            $credit_agreement 	 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'credit_agreement', 0);
            $SKMHT_persen 		 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'letter_mortgage', 0);
            $APHT_persen 		 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'imposition_act_mortgage', 0);
            $HT_persen 		  	 = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'mortgage', 0);
			
            $sale_purchase_certificate_persen 	= $this->RmCommon->filterEmptyField($value, 'KprApplication', 'sale_purchase_certificate', 0);
            $other_certificate_persen 			= $this->RmCommon->filterEmptyField($value, 'KprApplication', 'other_certificate', 0);
            $transfer_title_charge_persen 		= $this->RmCommon->filterEmptyField($value, 'KprApplication', 'transfer_title_charge', 0);

			$costKPR = $this->_callCalcCostKpr(array(
	            'KprApplication' => array(
	                'interest_rate' => $interest_rate,
	                'credit_fix' => $credit_fix,
	                'credit_float' => $credit_float,
	                'property_price' => $property_price,
	                'persen_loan' => $persen_loan,
	                'loan_price' => $loan_price,
	                'appraisal' => $appraisal,
	                'administration' => $administration,
	                'provision' => $provision,
	                'insurance' => $insurance,
	                'sale_purchase_certificate' => $sale_purchase_certificate_persen,
	                'transfer_title_charge' => $transfer_title_charge_persen,
	                'letter_mortgage' => $SKMHT_persen,
	                'imposition_act_mortgage' => $APHT_persen,
	                'mortgage' => $HT_persen,
	                'other_certificate' => $other_certificate_persen,
	                'credit_agreement' => $credit_agreement,
	            ),
	        ));
		}
		return $costKPR;
	}

    function _callCalcCostKpr ( $data, $modelName = 'KprApplication' ) {
        $interest_rate = $this->RmCommon->filterEmptyField($data, $modelName, 'interest_rate');
        $credit_fix = $this->RmCommon->filterEmptyField($data, $modelName, 'credit_fix');
        $credit_float = $this->RmCommon->filterEmptyField($data, $modelName, 'credit_float');
        $property_price = $this->RmCommon->filterEmptyField($data, $modelName, 'property_price');
        $persen_loan = $this->RmCommon->filterEmptyField($data, $modelName, 'persen_loan');
        $loan_price = $this->RmCommon->filterEmptyField($data, $modelName, 'loan_price');
        $appraisal = $this->RmCommon->filterEmptyField($data, $modelName, 'appraisal');
        $administration = $this->RmCommon->filterEmptyField($data, $modelName, 'administration');
        $provision = $this->RmCommon->filterEmptyField($data, $modelName, 'provision');
        $insurance = $this->RmCommon->filterEmptyField($data, $modelName, 'insurance');
        $sale_purchase_certificate = $this->RmCommon->filterEmptyField($data, $modelName, 'sale_purchase_certificate');
        $transfer_title_charge = $this->RmCommon->filterEmptyField($data, $modelName, 'transfer_title_charge');
        $letter_mortgage = $this->RmCommon->filterEmptyField($data, $modelName, 'letter_mortgage');
        $imposition_act_mortgage = $this->RmCommon->filterEmptyField($data, $modelName, 'imposition_act_mortgage');
        $mortgage = $this->RmCommon->filterEmptyField($data, $modelName, 'mortgage');
        $other_certificate = $this->RmCommon->filterEmptyField($data, $modelName, 'other_certificate');
        $credit_agreement = $this->RmCommon->filterEmptyField($data, $modelName, 'credit_agreement');

        $credit_total = $this->_callCalcCreditTotal($credit_fix, $credit_float);
        $down_payment = $this->_callCalcDp($property_price, $persen_loan);
        $total_bank_charge = $this->_callCalcBankCost($loan_price, array(
            'appraisal' => $appraisal,
            'administration' => $administration,
            'provision' => $provision,
            'insurance' => $insurance,
        ));
        $total_notary_charge = $this->_callCalcNotaryCost($property_price, array(
            'sale_purchase_certificate_persen' => $sale_purchase_certificate,
            'transfer_title_charge_persen' => $transfer_title_charge,
            'SKMHT_persen' => $letter_mortgage,
            'APHT_persen' => $imposition_act_mortgage,
            'HT_persen' => $mortgage,
            'other_certificate_persen' => $other_certificate,
            'credit_agreement' => $credit_agreement,
        ));
        $total_first_credit = $this->creditFix($loan_price, $interest_rate, $credit_total);
        $total = $down_payment + $total_bank_charge + $total_notary_charge + $total_first_credit;

        return array(
            'credit_total' => $credit_total,
            'down_payment' => $down_payment,
            'total_bank_charge' => $total_bank_charge,
            'total_notary_charge' => $total_notary_charge,
            'total_first_credit' => $total_first_credit,
            'total' => $total,
        );
    }	

    function _callCalcCreditTotal ( $flat, $floating ) {
        return $flat + $floating;
    }

    function _callCalcDp ( $price, $rate ) {
        return @($price * ($rate / 100 ));
    }

    function _callDpPercent( $price, $down_payment){
    	return @round(($down_payment/$price)*100,0);
    }

    function _callCalcBankCost ( $loan_price, $data ) {
        $appraisal = $this->RmCommon->filterEmptyField($data, 'appraisal', false, 0);
        $administration = $this->RmCommon->filterEmptyField($data, 'administration', false, 0);
        $provision = $this->RmCommon->filterEmptyField($data, 'provision', false, 0);
        $insurance = $this->RmCommon->filterEmptyField($data, 'insurance', false, 0);

        $provision_price = (floatval($provision / 100) * $loan_price);
        $insurance_price = (floatval($insurance / 100) * $loan_price);

        return $appraisal + $administration + $provision_price + $insurance_price;
    }

    function _callCalcNotaryCost ( $price, $data ) {
        $sale_purchase_certificate_persen = $this->RmCommon->filterEmptyField($data, 'sale_purchase_certificate_persen', false, 0);
        $transfer_title_charge_persen = $this->RmCommon->filterEmptyField($data, 'transfer_title_charge_persen', false, 0);
        $SKMHT_persen = $this->RmCommon->filterEmptyField($data, 'SKMHT_persen', false, 0);
        $APHT_persen = $this->RmCommon->filterEmptyField($data, 'APHT_persen', false, 0);
        $HT_persen = $this->RmCommon->filterEmptyField($data, 'HT_persen', false, 0);
        $other_certificate_persen = $this->RmCommon->filterEmptyField($data, 'other_certificate_persen', false, 0);
        $credit_agreement = $this->RmCommon->filterEmptyField($data, 'credit_agreement', false, 0);

        $sale_purchase_certificate = floatval( $sale_purchase_certificate_persen / 100 ) * $price;
        $transfer_title_charge = floatval( $transfer_title_charge_persen / 100 ) * $price;
        $SKMHT = floatval( $SKMHT_persen / 100 ) * $price;
        $APHT = floatval( $APHT_persen / 100 ) * $price;
        $HT = floatval( $HT_persen / 100 ) * $price;
        $other_certificate = floatval( $other_certificate_persen / 100 ) * $price;
        
        return round($sale_purchase_certificate + $transfer_title_charge + $credit_agreement + $SKMHT + $APHT + $HT + $other_certificate, 0);
    }

	function creditFix($amount, $rate, $year=20){
		
		if( empty($rate) ){
			return 0;
		} else {

			if( $rate != 0 ) {
				$rate = ($rate/100)/12;
			}
			$rateYear = pow((1+$rate), ($year*12));
			$rateMin = (pow((1+$rate), ($year*12))-1);

			if( $rateMin != 0 ) {
				$rateYear = $rateYear / $rateMin;
			}

			$mortgage = $rateYear * $amount * $rate; // rumus angsuran fix baru 

    		$api = Configure::read('__Site.is_rest');

    		if( !empty($api) ) {
    			$mortgage = round($mortgage, 0);
    		}

			return $mortgage;
		}
	}

	function calcLoan ( $price, $bunga_kpr = false ) {
		$bunga_kpr_persen = $this->_getBungaKPRPersen( $bunga_kpr );
		return $price * $bunga_kpr_persen;
	}

	function calcLoanFromDp ( $price, $down_payment = false ) {
		return $price - $down_payment;
	}

	function calValueFromPercent($price, $percent){
		return ($percent/100)*$price;
	}

	function _getBungaKPRPersen ( $bunga_kpr = false ) {
        $bunga_kpr = !empty($bunga_kpr)?$bunga_kpr:Configure::read('__Site.bunga_kpr');
        return ( 100 - $bunga_kpr ) / 100;
    }

	function getCalculateSummary($data,$value){
		if(!empty($data)){
			foreach($data AS $key => $bank){
				$id = $this->RmCommon->filterEmptyField($bank,'Bank','id');
				$bank = $this->calculate_kpr_installment_summary( $bank, $value );
				$data[$key] = $bank;

			}
		}
		return $data;
	}


	function calculate_kpr_installment_summary($bankKpr, $data){
		$kpr_data['summary'] = array();
		$code_kpr = !empty($data['BankApplyCategory']['code'])?$data['BankApplyCategory']['code']:false;
		if(!empty($bankKpr['BankSetting']) && !empty($data['KprApplication']) && $code_kpr == 'KPR'){	
			$total_loan = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'loan_price', 0);

			$interest_rate = $this->RmCommon->filterEmptyField($bankKpr, 'BankSetting', 'interest_rate_fix', 0);
			$credit_total = $this->RmCommon->filterEmptyField($data, 'CrmProjectPayment', 'credit_total', 0);
			$total_first_credit = $this->creditFix($total_loan, $interest_rate, $credit_total);

			$kpr_data['summary'] = array(
				'angsuran' 	=> $total_first_credit,
				'rate' 		=>  $interest_rate
				);
		}
		$bankKpr = array_merge($bankKpr,$kpr_data);
		return $bankKpr;
	}

	function beforeViewInstallmentDetail($value, $bank_setting, $params){
		$property_price = Common::hashEmptyField($value, 'Property.price_measure');
		$loan_price = Common::hashEmptyField($params, 'named.loan_price');
		$credit_total = Common::hashEmptyField($params, 'named.tenor');
		$interest_rate = Common::hashEmptyField($bank_setting, 'BankSetting.interest_rate_fix');
		$credit_fix = Common::hashEmptyField($bank_setting, 'BankSetting.periode_fix');
		$cap_rate = Common::hashEmptyField($bank_setting, 'BankSetting.interest_rate_cabs');
		$periode_cab = Common::hashEmptyField($bank_setting, 'BankSetting.periode_cab');
		$floating_rate = Common::hashEmptyField($bank_setting, 'BankSetting.interest_rate_float');

		return $this->calculate_kpr_installment_detail(false, array(
			'Kpr' => array(
				'loan_price' => $loan_price,
				'interest_rate' => $interest_rate,
				'credit_fix' => $credit_fix,
				'credit_cap' => $periode_cab,
				'credit_total' => $credit_total,
				'cap_rate' => $cap_rate,
				'floating_rate' => $floating_rate,
			),
		));
	}

	function calculate_kpr_installment_detail( $bankKpr, $data ) {
		$kpr_data = array();

		if( isset($data['Kpr']) && isset($data['Kpr']['loan_price']) ){
			$total_loan = $this->RmCommon->filterEmptyField($data, 'Kpr', 'loan_price', 0);
			$interest_rate = $this->RmCommon->filterEmptyField($data, 'Kpr', 'interest_rate', 0);
			$credit_fix = $this->RmCommon->filterEmptyField($data, 'Kpr', 'credit_fix', 0);
			$credit_cap = $this->RmCommon->filterEmptyField($data, 'Kpr', 'credit_cap', 0);
			$credit_total = $this->RmCommon->filterEmptyField($data, 'Kpr', 'credit_total', 0);
			$cap_rate = $this->RmCommon->filterEmptyField($data, 'Kpr', 'cap_rate', 0);
			$floating_rate = $this->RmCommon->filterEmptyField($data, 'Kpr', 'floating_rate', $interest_rate);
			$credit_float = $credit_total - $credit_fix;
			$total_first_credit = !empty($total_loan) ? $this->creditFix($total_loan, $interest_rate, $credit_total) : 0;

			// GENERATE TABLE
			$count_mount_fix = $credit_total * 12;
			$half_mount_fix = intval($count_mount_fix / 2);
			$rate_portion = $total_loan;
			$main_portion = 0;
			$rest_loan = $total_loan;
			$flag_float = 0;
			$flag_cap = 0;
			$total_period_cap = 0;

			if(!empty($credit_cap) && !empty($cap_rate)){
				$total_period_cap = ($credit_fix) * 12;
				$total_period = (!empty($credit_float)) ? (($credit_fix+$credit_cap) * 12) : 0;
			}else{
				$total_period = (!empty($credit_float)) ? (($credit_fix) * 12) : 0;
			}

			for ($i = 1; $i <= $count_mount_fix; $i++) {

				if(!empty($credit_cap) && !empty($cap_rate)){
					if(($i > $total_period_cap && $i <= $total_period) && $total_period_cap != 0){
						if($flag_cap == 0){
							$credit = $this->creditFloat($rest_loan, $cap_rate, ($credit_fix), $credit_total);
							$flag_cap = 1;
						}
					}else if($i > $total_period && $total_period != 0){
						if($flag_float == 0){
							$credit = $this->creditFloat($rest_loan, $floating_rate, ($credit_fix+$credit_cap), $credit_total);
							$flag_float = 1;
						}							
					}else{
						$credit = $total_first_credit;
					}
				}else{
					if($i > $total_period && $total_period != 0){
						if($flag_float == 0){
							$credit = $this->creditFloat($rest_loan, $floating_rate, $credit_fix, $credit_total);
							$flag_float = 1;
						}							
					}else{
						$credit = $total_first_credit;
					}
				}
				
				if($i == 1) {
					$rate_portion = $this->rate_portion( $rate_portion, $interest_rate);
				}else {
					if(!empty($credit_cap) && !empty($cap_rate)){
						if(($i > $total_period_cap && $i <= $total_period) && $total_period_cap != 0){
							$rate_portion = $this->rate_portion( $rest_loan, $cap_rate);
						}else if($i > $total_period && $total_period != 0) {
							$rate_portion = $this->rate_portion( $rest_loan, $floating_rate);
						}else{
							$rate_portion = $this->rate_portion( $rest_loan, $interest_rate);
						}
					}else{
						if($i > $total_period && $total_period != 0) {
							$rate_portion = $this->rate_portion( $rest_loan, $floating_rate);
						}else{
							$rate_portion = $this->rate_portion( $rest_loan, $interest_rate);
						}
					}
				}
				
				$main_portion = $credit - $rate_portion;
				$rest_loan -=  $main_portion;

				if(!empty($credit_cap) && !empty($cap_rate)){
					if($total_period_cap != 0 && !empty($cap_rate) && ($i > $total_period_cap && $i <= $total_period)){
						$rate = $cap_rate;
					}else if($total_period != 0 && !empty($floating_rate) && $i > $total_period){
						$rate = $floating_rate;
					}else{
						$rate = $interest_rate; 
					}
				}else{
					if($total_period != 0 && !empty($floating_rate) && $i > $total_period){
						$rate = $floating_rate;
					}else{
						$rate = $interest_rate; 
					}
				}


				$kpr_data[] = array(
					'SisaPokokKredit' => (number_format($rest_loan) == '-0') ? abs(number_format(sprintf('%1.0f', $rest_loan))) : number_format(sprintf('%1.0f', $rest_loan)),
					'PorsiPokok' => number_format(sprintf('%1.0f', $main_portion)),
					'PorsiBunga' => number_format(sprintf('%1.0f', $rate_portion)),
					'Angsuran' => number_format($credit),
					'Bunga' => $rate.'%'
				);
			}
		}

		return $kpr_data;
	}

	function rate_portion($amount, $rate){
		$rate_persen = $rate/100;
		$rate_per_year = $rate_persen/12;
		return $amount*$rate_per_year;
	}

	function creditFloat($amount, $floating_rate, $first_installment, $next_installment){
		$floating_rate = $floating_rate/100;
		$over_year = $next_installment - $first_installment;
		$mortgage = pow((1+($floating_rate/12)), ($over_year*12))/(pow((1+$floating_rate/12), ($over_year*12))-1)*$amount*($floating_rate/12);
		return $mortgage;	
	}

	function _callGetBankApplyCategory ( $property_type_id ) {
		if($property_type_id == 3){
			return 2;
		}else {
			return 1;
		}
	}

	function getCountSummary($banks){

		$cnt_ready = 0;
		$cnt_total = 0;
		foreach($banks AS $key=> $bank){
			$flag_ready = $this->RmCommon->filterEmptyField($bank, 'flag_ready');

			if(!empty($flag_ready)){
				$cnt_ready +=  1;
			}

			$cnt_total += 1;

		}
		$result['cnt_ready'] = $cnt_ready;
		$result['cnt_total'] = $cnt_total;

		return $result;
		
	}

	function _callBeforeAdd ( $data, $dataClient = false ) {
		$company_id = Common::hashEmptyField($data, 'Kpr.company_id', null);
		$agent_id = Common::hashEmptyField($data, 'Kpr.agent_id', null);
		$first_name = null;
		$last_name = null;
		$client = array();

		if( !empty($dataClient) ) {
			$client = Common::hashEmptyField($dataClient, 'UserClient', array());

			$first_name = Common::hashEmptyField($dataClient, 'UserClient.first_name');
			$last_name = Common::hashEmptyField($dataClient, 'UserClient.last_name');
			$job_type = Common::hashEmptyField($dataClient, 'UserClient.job_type', null);
			$gender_id = Common::hashEmptyField($dataClient, 'UserClient.gender_id', null);
			$client_email = Common::hashEmptyField($dataClient, 'User.email');
			
			$data['Kpr']['client_job_type_id'] = $job_type;
			$data['Kpr']['gender_id'] = $gender_id;
			$data['Kpr']['email'] = $client_email;
			$data['Kpr']['client_email'] = $client_email;

			$client = Common::_callUnset($client, array(
				'id',
				'company_id',
				'user_id',
				'agent_id',
				'status',
				'created',
				'modified',
			));
		} else {
			$data_client = Common::hashEmptyField($data, 'Client', array());
			$kpr_application = $this->RmCommon->filterEmptyField($data, 'Kpr');

			$client_type = $this->RmCommon->filterEmptyField($kpr_application, 'client_type');
			$job_type = Common::hashEmptyField($kpr_application, 'client_job_type_id', null);
			$gender_id = Common::hashEmptyField($kpr_application, 'gender_id', null);
			$birthday = $this->RmCommon->filterEmptyField($kpr_application, 'birthday');
			
			$client_name = $this->RmCommon->filterEmptyField($kpr_application, 'client_name');
			$clientArr = !empty($client_name)?explode(' ', $client_name):false;

			if( !empty($clientArr) ) {
				if( !empty($clientArr[0]) ) {
					$first_name = $clientArr[0];
					unset($clientArr[0]);
				}
				if( !empty($clientArr[1]) ) {
					$last_name = implode(' ', $clientArr);
				}
			}
		
			$client_email = $this->RmCommon->filterEmptyField($kpr_application, 'client_email');
	        $client_email = $this->RmCommon->getEmailConverter($client_email);
	        $birthday = $this->RmCommon->getDate($birthday);

			if( !empty($data_client) ) {
				$data['User'] = $data_client;
			}
			
			$client = array(
				'full_name' => $client_name,
				'no_hp' => $this->RmCommon->filterEmptyField($kpr_application, 'client_hp'),
				'birthplace' => $this->RmCommon->filterEmptyField($kpr_application, 'birthplace'),
				'birthday' => !empty($birthday)?$birthday:NULL,
				'ktp' => $this->RmCommon->filterEmptyField($kpr_application, 'ktp'),
				'address' => $this->RmCommon->filterEmptyField($kpr_application, 'address'),
				'status_marital' => $this->RmCommon->filterEmptyField($kpr_application, 'status_marital'),
			);
		}

		$data['Kpr']['birthday'] = Common::hashEmptyField($client, 'birthday');
		$data['KprApplication'][0] = array_merge($client, array(
			'first_name' => $first_name,
			'last_name' => $last_name,
			'email' => $client_email,
			'gender_id' => $gender_id,
		));

		if(!empty($job_type)){
			$data['KprApplication'][0]['job_type_id'] = $job_type;
		}

		if( !empty($client_type) && $client_type == 'new' ) {
			$existUser = $this->controller->User->getData('first', array(
        		'conditions' => array(
        			'User.email' => $client_email,
    			),
    		), array(
    			'status' => 'all',
    		));

			$dataMerge = array(
				'group_id' => 10,
				'agent_id' => $agent_id,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $client_email,
				'gender_id' => $gender_id,
			);

			$userProfile = array_merge($client, array(
				'company_id' => $company_id,
				'job_type' => $job_type,
			));

			if( !empty($existUser) ) {
				$userData = array(
					'id' => Common::hashEmptyField($existUser, 'User.id'),
					'deleted' => 0,
				);
			} else {
			 	$userData = $dataMerge;
				$data['User']['UserProfile'] = $userProfile;
			}

			$data['User'] = $userData;
			$data['User']['UserClient'][] = array_merge($dataMerge, $userProfile);
			$data['User']['UserClientRelation'][] = array(
				'company_id' => $company_id,
				'agent_id' => $agent_id,
				'primary' => true,
			);
		}

		return $data;
	}

	function _callDataDocuments ( $data ) {
		$crmDocuments = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocument', false, array());

		if( !empty($crmDocuments) ) {
			$dataDocuments = array();

			foreach ($crmDocuments as $key => $doc) {
				$file = $this->RmCommon->filterEmptyField($doc, 'file', 'name');
				$doc_category_id = $this->RmCommon->filterEmptyField($doc, 'document_category_id');

				if( !empty($file) ) {
					$dataDocuments[$doc_category_id] = array(
						'file_name' => $file,
					);
				}
			}

			$data['DataCrmProjectDocument'] = $dataDocuments;
		}

		return $data;
	}

	function _callBeforeSave ( $data, $id = false ) {
        if( !empty($data) ) {
        	$dataSave = $data;
        	$dataSave = $this->RmCommon->dataConverter($dataSave, array(
				'date' => array(
					'Kpr' => array(
						'sold_date',
						'kpr_date',
					),
				),
				'price' => array(
					'Kpr' => array(
						'property_price',
					),
				),
			));
			
			$property_id = $this->RmCommon->filterEmptyField($dataSave, 'Property', 'id');

			$property = $this->controller->User->Property->_callPropertyMerge($dataSave, $property_id, 'Property.id');
			$company_id = $this->RmCommon->filterEmptyField($property, 'User', 'parent_id', null);

			$property_id = $this->RmCommon->filterEmptyField($property, 'Property', 'id', null);
			$agent_id = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id', null);
			$mls_id = $this->RmCommon->filterEmptyField($property, 'Property', 'mls_id');
			$keyword = $this->RmCommon->filterEmptyField($property, 'Property', 'keyword');
			$property_type_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_type_id', null);
			$bank_apply_category_id = ($property_type_id == 3)?2:1;

			$dataSave = $this->RmUser->_callGetClientData($dataSave, 'Kpr');

			$crm_project_id = $this->RmCommon->filterEmptyField($dataSave, 'Kpr', 'crm_project_id', null);
			$client_name = $this->RmCommon->filterEmptyField($dataSave, 'Kpr', 'client_name');
			$client_type = Common::hashEmptyField($dataSave, 'Kpr.client_type');

			$client_email = $this->RmCommon->filterEmptyField($dataSave, 'Kpr', 'client_email');
            $client_email = $this->RmCommon->getEmailConverter($client_email);

            if( !empty($client_email) && $client_type != 'new' ) {
            	$client = $this->controller->User->UserClient->getData('first', array(
            		'conditions' => array(
            			'User.email' => $client_email,
        			),
        			'contain' => array(
        				'User',
    				),
        		), array(
        			'mine' => true,
        		));
            } else {
            	$client = array();
            }

			$crm_project_payment_id = $this->RmCommon->filterEmptyField($dataSave, 'CrmProjectPayment', 'id');
			$client_id = Common::hashEmptyField($client, 'User.id');

			if( !empty($crm_project_id) ) {
				$conditions = array(
					'Kpr.id <>' => $id,
					'OR' => array(
						array(
							'Kpr.crm_project_id' => $crm_project_id,
						),
						array(
							'Kpr.property_id' => $property_id,
							'Kpr.client_email' => $client_email,
						),
					),
				);
			} else {
				$conditions = array(
					'Kpr.id <>' => $id,
					'Kpr.property_id' => $property_id,
					'Kpr.client_email' => $client_email,
				);
			}

			$cnt_kpr = $this->controller->User->Kpr->getData('count', array(
				'conditions' => $conditions,
			), array(
				'admin_mine' => true,
			));

			$dataSave['Kpr']['property_id'] = $property_id;
			$dataSave['Kpr']['user_id'] = $client_id;
			$dataSave['Kpr']['agent_id'] = $agent_id;
			$dataSave['Kpr']['mls_id'] = $mls_id;
			$dataSave['Kpr']['company_id'] = $company_id;
			$dataSave['Kpr']['bank_apply_category_id'] = $bank_apply_category_id;
			$dataSave['Kpr']['keyword'] = sprintf('%s, %s, %s', $keyword, $client_name, $client_email);
			$dataSave['Kpr']['register'] = !empty($cnt_kpr)?true:false;

			$dataSave = $this->_callBeforeAdd($dataSave, $client);
			$this->controller->User->Kpr->validator()->add('property_id', 'required', array(
			    'rule' => 'notempty',
				'message' => 'Property harap dipilih',
			));

			$dataSave = $this->_callDataDocuments($dataSave);
			$dataSave['DataSave'] = array(
				'CrmProjectPayment' => array(
					'id' => $crm_project_payment_id,
					'sold_date' => $this->RmCommon->filterEmptyField($dataSave, 'Kpr', 'sold_date'),
					'price' => $this->RmCommon->filterEmptyField($dataSave, 'Kpr', 'property_price'),
				),
			);

			// Pilih Bank
			$dataSave = $this->_saveKprBank($dataSave);
        }

        return $dataSave;
	}

	function _saveKprBank ( $data, $value = null, $product = false , $params = array() ) {
		$params = !empty($params)?$params:$this->controller->params->params;
		$from_kpr = Common::hashEmptyField($params, 'from_kpr', 'backend');

		$kpr_id = Common::hashEmptyField($value, 'Kpr.id', null);
		$banks = Common::hashEmptyField($data, 'Bank.id');
		$agent_id = Common::hashEmptyField($data, 'Kpr.agent_id', null);

		$property_type_id = Common::hashEmptyField($data, 'Property.property_type_id', null);
		$region_id = Common::hashEmptyField($data, 'PropertyAddress.region_id', null);
		$city_id = Common::hashEmptyField($data, 'PropertyAddress.city_id', null);
		$property_price = Common::hashEmptyField($data, 'Kpr.property_price');
		$project_id = Common::hashEmptyField($data, 'Kpr.project_id', null);

		$flag_duplicated = $this->bankDuplicated($banks);
		$data['Kpr']['bank_duplicated'] = $flag_duplicated;
		
		if(!empty($product) && !empty($data['BankSetting']['id'])){
			$data = $this->setListFillingProduct($data, $value, $params);
		} else if(!empty($banks)){
			$i = 0;
			$dataBank = Common::hashEmptyField($data, 'Bank');

			foreach($banks AS $key => $id){
				$bank_setting = $this->controller->Kpr->KprBank->Bank->BankSetting->getData('first', array(
					'conditions' => array(
						'BankSetting.id' => $id,
					),
				), array(
					'type' => 'all',
				));
				$bank_id = Common::hashEmptyField($bank_setting, 'BankSetting.bank_id');

				$additionalNoted = Common::hashEmptyField($dataBank, 'noted.'.$id);
				$dp = Common::hashEmptyField($dataBank, 'dp.'.$id);
				$down_payment = Common::hashEmptyField($dataBank, 'down_payment.'.$id);
				$credit_total = Common::hashEmptyField($dataBank, 'credit_total.'.$id);
				$sales_id = Common::hashEmptyField($dataBank, 'sales_id.'.$id);
				$down_payment = Common::_callPriceConverter($down_payment);
	
				$down_payment_temp = $this->_callCalcDp( $property_price, $dp );
				$down_payment = !empty($down_payment)?$down_payment:$down_payment_temp;
				$loan_price = $this->calcLoanFromDp($property_price, $down_payment);

				if( !empty($sales_id) && !is_numeric($sales_id) ) {
					$sales = $this->controller->Kpr->KprBank->Bank->BankUser->getData('first', array(
						'conditions' => array(
							'BankUser.code' => $sales_id,
							'BankUser.bank_id' => $bank_id,
						),
						'order' => false,
					), array(
						'is_sales' => false,
					));
					$sales_id = Common::hashEmptyField($sales, 'BankUser.id');
				}

				if(!empty($id)){
					$val['KprBank'][$i] = array(
						'bank_id' => $bank_id,
						'setting_id' => $id,
						'property_price' => $property_price,
						'property_type_id' => $property_type_id,
						'region_id' => $region_id,
						'city_id' => $city_id,
						'noted' => $additionalNoted,
						'dp' => $dp,
						'down_payment' => $down_payment,
						'credit_total' => $credit_total,
						'sales_id' => $sales_id,
						'loan_price' => $loan_price,
					);

					if( !empty($kpr_id) ) {
						$val['KprBank'][$i]['kpr_id'] = $kpr_id;
					}
					if( !empty($project_id) ) {
						$val['KprBank'][$i]['project_id'] = $project_id;
					}

					$i++;
				}
			}
			$data['KprBank'] = Common::hashEmptyField($val, 'KprBank');
		}

		$kprBanks = Common::hashEmptyField($data, 'KprBank');

		if( !empty($kprBanks) ){
			foreach($kprBanks AS $key => $kprBank){
				$typeCommissions = Configure::read('__Site.typeCommission');
				$down_payment = Common::hashEmptyField($kprBank, 'down_payment');
				$loan_price = Common::hashEmptyField($kprBank, 'loan_price');
				$credit_total = Common::hashEmptyField($kprBank, 'credit_total');
				$sales_id = Common::hashEmptyField($kprBank, 'sales_id', null);
				$document_status = Common::hashEmptyField($kprBank, 'document_status', 'process');

				$kprBank = $this->getCommissionKPR($kprBank, $down_payment);

				$setting_loan_id = Common::hashEmptyField($kprBank, 'setting_loan_id');
				$bank_id = Common::hashEmptyField($kprBank,'bank_id');
				$setting_id = Common::hashEmptyField($kprBank,'setting_id');
				$percent_agent = Common::hashEmptyField($kprBank,'percent_agent');
				$percent_company = Common::hashEmptyField($kprBank,'percent_company');
				$noteKpr = Common::hashEmptyField($kprBank,'note');
				$region_name = Common::hashEmptyField($kprBank,'region_name');
				$city_name = Common::hashEmptyField($kprBank,'city_name');
				$region_id = Common::hashEmptyField($kprBank,'region_id', null);
				$city_id = Common::hashEmptyField($kprBank,'city_id', null);

				## GET RATE KOMISI
				$kprBank = $this->User->Kpr->Bank->getMerge($kprBank, $bank_id);

				if(!empty($setting_id)){
					$kprBank = $this->User->Kpr->Bank->BankSetting->getMerge($kprBank, $setting_id, false, 'BankSetting.id', array(
						'type' => 'all',
					));	
				}else{
					$kprBank = $this->User->Kpr->Bank->BankSetting->getMerge($kprBank, $bank_id);					
				}	

				if(!empty($kprBank['BankSetting'])){
					$product_id = Common::hashEmptyField($kprBank, 'BankSetting.product_id');
					$kprBank = $this->User->Kpr->Bank->BankSetting->BankProduct->getMerge($kprBank, $product_id);
				}

				$bank_setting_id = $this->RmCommon->filterEmptyField($kprBank, 'BankSetting', 'id');
				$work_day = $this->RmCommon->filterEmptyField($kprBank,'BankSetting','work_day');
				$note = $this->RmCommon->filterEmptyField($kprBank, 'BankSetting', 'note');

				$commission_agent 	= $this->_GenerateCommission($percent_agent,$loan_price);
				$commission_company = $this->_GenerateCommission($percent_company,$loan_price);

				$typeCommissions = $this->setCommission($typeCommissions, array(
					'percent' => array(
						$percent_agent,
						$percent_company,
					),
					'commission' => array(
						$commission_agent, 
						$commission_company,
					),
				));
				## SET KPRBANK
				$dataBank = $this->setKprBank( $data, $kprBank, array(
					'document_status' => $document_status,
					'from_kpr' => $from_kpr,
				));
				$data['KprBank'][$key] = array_merge($data['KprBank'][$key], $dataBank);

				## SET KPR_BANK_INSTALLMENT
				$data['KprBank'][$key]['KprBankInstallment'][0] = $this->setKprBankInstallment($kprBank, array(
					'down_payment' => $down_payment,
					'property_price' => $property_price,
					'credit_total' => $credit_total,
				));
				$data['KprBank'][$key]['KprBankInstallment'][0]['sales_id'] = $sales_id;
				$data['KprBank'][$key]['KprBankDate'][] = $this->getKprBankDate($document_status, array(
					// 'note' => $noted,
				));

				if(!empty($typeCommissions)){
					foreach($typeCommissions AS $i => $typeCommission){
						$type = $this->RmCommon->filterEmptyField($typeCommission, 'type');
						$percent = $this->RmCommon->filterEmptyField($typeCommission, 'percent');
						$commission = $this->RmCommon->filterEmptyField($typeCommission, 'commission');

						if(!empty($percent) && !empty($commission)){
							$data['KprBank'][$key][sprintf('unpaid_%s', $type)] = "pending";
							$data['KprBank'][$key]['KprBankInstallment'][0]['KprBankCommission'][] = $this->setKprBankCommission( $type, array(
								'percent' => $percent,
								'commission' => $commission,
								'region_id' => $region_id,
								'region_name' => $region_name,
								'city_id' => $city_id,
								'city_name' => $city_name,
								'note' => $noteKpr,
							));

							if( $type == 'agent' ) {
								$data['KprBank'][$key]['KprBankInstallment'][0]['provision'] = $percent;
								$data['KprBank'][$key]['KprBankInstallment'][0]['commission'] = $commission;
							}
						}else{
							$data['KprBank'][$key][sprintf('unpaid_%s', $type)] = "no_provision";
						}
					}
				}				
			}
		}

		$data = Common::_callUnset($data, array(
			'Bank',
			'Property',
			'PropertyAddress',
		));

		return $data;
	}

	function _callBeforeFastSave ( $data, $id = false ) {
        if( !empty($data) ) {
        	$data = $this->RmCommon->dataConverter($data, array(
				'date' => array(
					'Kpr' => array(
						'sold_date',
						'kpr_date',
					),
				),
				'price' => array(
					'Kpr' => array(
						'property_price',
					),
				),
			));
			
			$property_id = Common::hashEmptyField($data, 'Property.id');
			$property = $this->controller->User->Property->getData('first', array(
				'conditions' => array(
					'Property.id' => $property_id,
				),
			), array(
				'admin_mine' => true,
			));
			$property_id 	= Common::hashEmptyField($property, 'Property.id', null);
			$agent_id 		= Common::hashEmptyField($property, 'Property.user_id', null);
			$mls_id 		= Common::hashEmptyField($property, 'Property.mls_id');
			$keyword 		= Common::hashEmptyField($property, 'Property.keyword');

			$property_type_id 		= Common::hashEmptyField($property, 'Property.property_type_id', null);
			$bank_apply_category_id = ($property_type_id == 3) ? 2 : 1;

			$data = $this->RmUser->_callGetClientData($data, 'Kpr');
			$client_id = Common::hashEmptyField($data, 'Kpr.client_id', null);
			$data = $this->controller->User->Property->_callPropertyMerge($data, $property_id, 'Property.id', $client_id);
			$company_id = Common::hashEmptyField($data, 'User.parent_id', null);

			$client_name = Common::hashEmptyField($data, 'Kpr.client_name');
			
			$client_email = Common::hashEmptyField($data, 'Kpr.client_email');
            $client_email = $this->RmCommon->getEmailConverter($client_email);

			$crm_project_id = Common::hashEmptyField($data, 'Kpr.crm_project_id', null);

			$job_type_id = Common::hashEmptyField($data, 'Client.job_type', null);
			$client_job_type_id = Common::hashEmptyField($data, 'Kpr.client_job_type_id', $job_type_id);

			$gender_id = Common::hashEmptyField($data, 'Client.gender_id', null);
			$gender_id = Common::hashEmptyField($data, 'Kpr.gender_id', $gender_id);

			$data = $this->controller->User->CrmProject->CrmProjectPayment->getMerge($data, $crm_project_id, 'CrmProjectPayment.crm_project_id');
			$crm_project_payment_id = Common::hashEmptyField($data, 'CrmProjectPayment.id');
			$keyword = sprintf('%s, %s, %s', $keyword, $client_name, $client_email);

			$data = hash::insert($data, 'Kpr.gender_id', $gender_id);
			$data = hash::insert($data, 'Kpr.client_job_type_id', $client_job_type_id);
			$data = hash::insert($data, 'Kpr.email', $client_email);
			$data = hash::insert($data, 'Kpr.client_email', $client_email);
			$data = hash::insert($data, 'Kpr.property_id', $property_id);
			$data = hash::insert($data, 'Kpr.user_id', $client_id);
			$data = hash::insert($data, 'Kpr.agent_id', $agent_id);
			$data = hash::insert($data, 'Kpr.mls_id', $mls_id);
			$data = hash::insert($data, 'Kpr.company_id', $company_id);
			$data = hash::insert($data, 'Kpr.bank_apply_category_id', $bank_apply_category_id);
			$data = hash::insert($data, 'Kpr.keyword', $keyword);

			$data['KprApplication'] = $data['Kpr'];
			
			if( !empty($crm_project_id) ) {
				$conditions = array(
					'Kpr.id <>' => $id,
					'OR' => array(
						array(
							'Kpr.crm_project_id' => $crm_project_id,
						),
						array(
							'Kpr.property_id' => $property_id,
							'Kpr.client_email' => $client_email,
						),
					),
				);
			} else {
				$conditions = array(
					'Kpr.id <>' => $id,
					'Kpr.property_id' => $property_id,
					'Kpr.client_email' => $client_email,
				);
			}

			$cnt_kpr = $this->controller->User->Kpr->getData('count', array(
				'conditions' => $conditions,
			), array(
				'admin_mine' => true,
			));

			$data['Kpr']['register'] = !empty($cnt_kpr)?true:false;

			$this->controller->User->Kpr->validator()->add('property_id', 'required', array(
			    'rule' => 'notempty',
				'message' => 'Property harap dipilih',
			));

			$data = $this->_callDataDocuments($data);
			
			$data['DataSave'] = array(
				'CrmProjectPayment' => array(
					'id' => $crm_project_payment_id,
					'sold_date' => Common::hashEmptyField($data, 'Kpr', 'sold_date'),
					'price' => Common::hashEmptyField($data, 'Kpr', 'property_price'),
				),
			);
        }
        return $data;
	}

	function _callBeforeView ( $value = array(), $dataSave = array() ) {
		$data = $this->controller->request->data;
		$documentOptions = false;
		$params = $this->controller->params->params;

		if( empty($data) ) {
			if( !empty($value) ) {
				$data = $value;

				$id = Common::hashEmptyField($value, 'Kpr.id');
				$crm_project_id = Common::hashEmptyField($value, 'CrmProject.id');
				$property_id = Common::hashEmptyField($value, 'Kpr.property_id');
				$client_id = Common::hashEmptyField($value, 'Kpr.user_id', 0);
				$booking_fee = Common::hashEmptyField($value, 'Kpr.booking_fee');	

				$data = $this->controller->User->Property->_callPropertyMerge($data, $property_id, 'Property.id', $client_id);
				$owner_id = Common::hashEmptyField($data, 'Property.client_id');
				$mls_id = Common::hashEmptyField($data, 'Property.mls_id');
				$property_title = Common::hashEmptyField($data, 'Property.title');
				$data = $this->controller->User->getMerge($data, $owner_id, false, 'Owner');

				$kpr_price = Common::hashEmptyField($value, 'Kpr.property_price');
				$price = Common::hashEmptyField($data, 'Property.price_measure');
				$price = Common::hashEmptyField($data, 'CrmProjectPayment.price', $price);

				$price = !empty($kpr_price)?$kpr_price:$price;
				$client_email = Common::hashEmptyField($data, 'Kpr.client_email');
				$client_hp = Common::hashEmptyField($data, 'Kpr.client_hp');
				$client_job_type_id = Common::hashEmptyField($data, 'Kpr.client_job_type_id', null);
				$address = Common::hashEmptyField($data, 'Kpr.address');
				$birthplace = Common::hashEmptyField($data, 'Kpr.birthplace');
				$ktp = Common::hashEmptyField($data, 'Kpr.ktp');
				$status_marital = Common::hashEmptyField($data, 'Kpr.status_marital');
				
				$dataClient = Common::hashEmptyField($data, 'Client');
				$dataClient = Common::hashEmptyField($data, 'UserClient', $dataClient);

				$birthday = Common::hashEmptyField($data, 'Kpr.birthday');
				$birthday = Common::hashEmptyField($dataClient, 'birthday', $birthday, array(
					'date' => 'Y-m-d',
				));

				if( empty($birthday) ) {
					$client_user_id = Common::hashEmptyField($dataClient, 'user_id');
					$client_user_profile = $this->controller->User->UserProfile->getData('first', array(
						'conditions' => array(
							'UserProfile.user_id' => $client_user_id,
						),
					));
					$birthday = Common::hashEmptyField($client_user_profile, 'UserProfile.birthday');
				}

				$client_first_name = Common::hashEmptyField($dataClient, 'first_name');
				$client_last_name = Common::hashEmptyField($dataClient, 'last_name');

				if($client_first_name || $client_last_name){
					$client_full_name = sprintf('%s %s', $client_first_name, $client_last_name);
				}else{
					$client_full_name = $this->RmCommon->filterEmptyField($data, 'Kpr', 'client_name');
				}
				
				$birthday = $this->RmCommon->getDate($birthday, true);
				$dataTemp['Kpr'] = array(
					'client_name' 	=> $client_full_name,
					'client_email' 	=> Common::hashEmptyField($data, 'Client.email', $client_hp),
					'client_hp' 	=> Common::hashEmptyField($data, 'Client.no_hp', $client_hp),
					'client_job_type_id' => Common::hashEmptyField($data, 'Client.client_type_id', $client_job_type_id),
					'address'		=> Common::hashEmptyField($data, 'Client.address', $address),
					'birthplace'	=> Common::hashEmptyField($data, 'Client.birthplace', $birthplace),
					'ktp'			=> Common::hashEmptyField($data, 'Client.ktp' , $ktp),
					'status_marital' => Common::hashEmptyField($data, 'Client.status_marital', $status_marital),
					'region_id'		=> Common::hashEmptyField($data, 'Client.region_id', null),
					'city_id'		=> Common::hashEmptyField($data, 'Client.city_id', null),
					'subarea_id'	=> Common::hashEmptyField($data, 'Client.subarea_id', null),
					'gender_id'		=> Common::hashEmptyField($data, 'Client.gender_id', null),
					'birthday'		=> !empty($birthday)?$birthday:NULL,
					'property_price'=> $price,
					'sold_date'		=> Common::hashEmptyField($data, 'Kpr.sold_date', date('Y-m-d')),
					'kpr_date'		=> Common::hashEmptyField($data, 'Kpr.kpr_date', date('Y-m-d')),
					'booking_fee_hide' => $booking_fee,
					'imb_hide'		=> $booking_fee,
					'property_title'=> __('%s, %s',$mls_id, $property_title),
				);

				$data = array_merge($data, $dataTemp);

				$data['Property']['imb_hide'] = $booking_fee;

				if( !empty($id) ) {
					$documentOptions = array(
						'kpr_id' => $id,
						'document_type' => 'kprs',
					);
				} else {
					$documentOptions = array(
						'crm_project_id' => $crm_project_id,
						'property_id' => $property_id,
					);
				}
			} else {
				$data['Kpr']['sold_date'] = date('Y-m-d');
				$data['Kpr']['kpr_date'] = date('Y-m-d');
			}

	    	$data = $this->RmCommon->dataConverter($data, array(
				'date' => array(
					'Kpr' => array(
						'sold_date',
						'kpr_date',
					),
				),
			), true);

			$banks = $this->User->Kpr->callGetBank($data);
			$this->controller->set(array(
				'value' => $data,
				'banks' => $banks,
				'show_banks' => true,
			));
		}else{
			$documents = $this->RmCommon->filterEmptyField($dataSave, 'CrmProjectDocument');
			$property_id = Common::hashEmptyField($data, 'Property.id');
			$data = $this->controller->User->Property->_callPropertyMerge($data, $property_id, 'Property.id');

			if(!empty($documents)){
				foreach ($documents as $key => $document) {
					$document_category_id = $this->RmCommon->filterEmptyField($document, 'document_category_id');
					$value = $this->controller->User->Kpr->CrmProject->CrmProjectDocument->DocumentCategory->getData('first', array(
						'conditions' => array(
							'DocumentCategory.id' => $document_category_id
						)
					));
					$value['CrmProjectDocument'] = $document;
					$data['CrmProjectDocument'][$key] = $value;
				}
			}

			$bank_setting_checked = Common::hashEmptyField($data, 'Bank.id');
			$banks = $this->User->Kpr->callGetBank($data, $bank_setting_checked);

	    	$data = $this->RmCommon->dataConverter($data, array(
				'price' => array(
					'Kpr' => array(
						'property_price',
					),
				),
			), true);
			$this->controller->set(array(
				'value' => $data,
				'banks' => $banks,
				'show_banks' => true,
			));
		}

    	if( $this->controller->action == 'admin_edit' ) {
			$documentCategories = $this->_callDocumentCategories(array(
				'DocumentCategory.is_required' => 1,
				'DocumentCategory.id' => array( 1,5 ),
			), $documentOptions);
			$this->controller->set(array(
				'documentCategories' => $documentCategories,
			));
		}
		
		$clientJobTypes = $this->controller->User->Kpr->KprApplication->JobType->getList();

		$kpr_application_id = Common::hashEmptyField($params, 'named.kpr_application_id');
		$this->_callCookieNotice($kpr_application_id, 'kpr_add');
		
		$this->RmCommon->_callRequestSubarea('Kpr');
		$this->controller->set(array(
			'clientJobTypes' => $clientJobTypes,
			'active_menu' => 'kpr_add',
		));

		$this->controller->request->data = $data;
	}

	function _callBeforeSaveKprApplication ( $data, $value) {
        if( !empty($data) ) {
        	$save_path = Configure::read('__Site.document_folder');
        	$crm_path = Configure::read('__Site.document_folder');

        	if(!empty($data['KprApplication'][0])){
				foreach($data['KprApplication'] AS $key => $val){
					$val = $this->RmCommon->dataConverter($val, array(
						'date' => array(
							'birthday',
						),
						'price' => array(
							'income',
							'household_fee',
							'other_installment',
						),
					));
					$data['KprApplication'][$key] = $val;
				}
			}else{
				$data = $this->RmCommon->dataConverter($data, array(
					'date' => array(
						'KprApplication' => array(
							'birthday',
						),
					),
					'price' => array(
						'KprApplication' => array(
							'income',
							'household_fee',
							'other_installment',
						),
					),
				));
			}

			$kpr_applications = $this->RmCommon->filterEmptyField( $data, 'KprApplication');
			$data['UserClient'] = $kpr_application = !empty($kpr_applications[0])?$kpr_applications[0]:false;
			$kpr_application_particular = !empty($kpr_applications[1])?$kpr_applications[1]:false;

			$id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id');
			$client_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'user_id', null);
			$property_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'property_id', null);
			$crm_project_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'crm_project_id', null);

			$data['UserClient']['user_id'] = $client_id;
			$data = $this->RmCommon->dataConverter($data, array(
				'unset' => array(
					'UserClient' => array(
						'id',
					),
				),
			));

			## KPR APPLICATION PREMIER
			$same_as_address_ktp = $this->RmCommon->filterEmptyField($kpr_application, 'same_as_address_ktp');
        	$address = $this->RmCommon->filterEmptyField($kpr_application, 'address');
        	$current_client_name = $this->RmCommon->filterEmptyField($kpr_application, 'client_name');
        	$birthplace = $this->RmCommon->filterEmptyField($kpr_application, 'birthplace');
        	$birthday = $this->RmCommon->filterEmptyField($kpr_application, 'birthday');
        	$gender_id = $this->RmCommon->filterEmptyField($kpr_application, 'gender_id', null);
        	$no_hp = $this->RmCommon->filterEmptyField($kpr_application, 'no_hp');
        	$job_type_id = $this->RmCommon->filterEmptyField($kpr_application, 'job_type_id', null);

        	$address_2 = $this->RmCommon->filterEmptyField($kpr_application, 'address_2');
			$address_2 = !empty($same_as_address_ktp)?$address:$address_2;

			$client_name = $this->RmCommon->filterEmptyField($value , 'Kpr', 'client_name');
			
			$client_email = $this->RmCommon->filterEmptyField($value , 'Kpr', 'client_email');
            $client_email = $this->RmCommon->getEmailConverter($client_email);

            if( !empty($current_client_name) ) {
				$full_name_arr =explode(' ', $current_client_name);
				$first_name = !empty($full_name_arr[0])?$full_name_arr[0]:false;

				if(!empty($first_name)){
					$data['KprApplication'][0]['first_name'] = $first_name;
					unset($full_name_arr[0]);
					$last_name = implode(" ", $full_name_arr);

					if(!empty($last_name)){
						$data['KprApplication'][0]['last_name'] = $last_name;
					}
				}

				$data['KprApplication'][0]['full_name'] = $current_client_name;
				$data['Kpr']['client_name'] = $current_client_name;
			}

			$data['KprApplication'][0]['email'] = $client_email;
			$data['KprApplication'][0]['address_2'] = $address_2;
			$data['KprApplication'][0]['kpr_id'] = $id;

			$data['Kpr']['snyc'] = false;
			$data['Kpr']['client_hp'] = $no_hp;
			$data['Kpr']['client_job_type_id'] = $job_type_id;
			$data['Kpr']['gender_id'] = $gender_id;
			$data['Kpr']['birthplace'] = $birthplace;
			$data['Kpr']['birthday'] = $birthday;
			$data['Kpr']['address'] = $address;

			$same_address_premier = $this->RmCommon->filterEmptyField($kpr_application, 'same_address_premier');
			## KPR SPOUSE PARTICULAR
			if(!empty($kpr_application_particular)){
				$full_name = $this->RmCommon->filterEmptyField($kpr_application_particular, 'full_name');
				$full_name_arr =explode(' ', $full_name);
				$first_name = !empty($full_name_arr[0])?$full_name_arr[0]:false;
				if(!empty($first_name)){
					$data['KprApplication'][1]['first_name'] = $first_name;
					unset($full_name_arr[0]);
					$last_name = implode(" ", $full_name_arr);

					if(!empty($last_name)){
						$data['KprApplication'][1]['last_name'] = $last_name;
					}
				}
				$data['KprApplication'][1]['status_marital'] = "marital";
				if(!empty($same_address_premier)){
					$data['KprApplication'][1]['kpr_id'] = $id;
					$data['KprApplication'][1]['rt'] = $this->RmCommon->filterEmptyField($kpr_application, 'rt');
					$data['KprApplication'][1]['rw'] = $this->RmCommon->filterEmptyField($kpr_application, 'rw');
					$data['KprApplication'][1]['region_id'] = Common::hashEmptyField($kpr_application, 'region_id', null);
					$data['KprApplication'][1]['city_id'] = Common::hashEmptyField($kpr_application, 'city_id', null);
					$data['KprApplication'][1]['subarea_id'] = Common::hashEmptyField($kpr_application, 'subarea_id', null);
					$data['KprApplication'][1]['zip'] = $this->RmCommon->filterEmptyField($kpr_application, 'zip');
					$data['KprApplication'][1]['address'] = $address;
					$data['KprApplication'][1]['address_2'] = $address_2;
					$data['KprApplication'][1]['same_as_address_ktp'] = $same_as_address_ktp;

				}else{
					$same_as_address_ktp = $this->RmCommon->filterEmptyField($kpr_application_particular, 'same_as_address_ktp');
					$address = $this->RmCommon->filterEmptyField($kpr_application_particular, 'address');
		        	$address_2 = $this->RmCommon->filterEmptyField($kpr_application_particular, 'address_2');
					$address_2 = !empty($same_as_address_ktp)?$address:$address_2;
					$data['KprApplication'][1]['address_2'] = $address_2;
				}
			}else{
				$data['Kpr']['remove_spose'] = TRUE;
			}

			$data = $this->buildDocument( $data, array(
				'save_path' => $save_path,
				'owner_id' => !empty($id)?$id:0,
				'client_id' => $client_id,
				'property_id' => $property_id,
				'document_type' => 'kpr_application',
				'document_type_particular' => 'kpr_spouse_particular',
				'options' => array(
					'client_id' => $client_id,
					'crm_project_id' => $crm_project_id,
					'kpr_application_id' => $id,
				),
			), array(2));
			$data = $this->mergeDocumentApplication($data);

        }
        return $data;
	}

	function mergeDocumentApplication( $data ){
		if(!empty($data)){
			$crmDocument = $this->RmCommon->filterEmptyField( $data, 'CrmProjectDocument', false, array());
			$crmDocumentMstr = $this->RmCommon->filterEmptyField( $data, 'CrmProjectDocumentMstr', false, array());
			$particularDocument = $this->RmCommon->filterEmptyField( $data, 'ParticularDocument', false, array());
			$docs = array_merge($crmDocument, $crmDocumentMstr);
			$docs = array_merge($docs, $particularDocument);

			foreach($docs AS $key => $doc){
				$document_category_id = $this->RmCommon->filterEmptyField($doc, 'document_category_id');
				$documents[$document_category_id] = $doc;
			}

			if(!empty($documents)){
				$document['DataCrmProjectDocument'] = $documents;
				$data = array_merge( $data, $document);
			}

		}
		return $data;
	}

	function getSummaryKpr($value, $banks){	

		if(!empty($banks)){
			foreach($banks AS $field => $values){
				
				if( !empty($values) ) {
					
					if($field <> 'filter'){
						foreach ($values as $key => $bank) {
							$cost['summaryKPR'] = $this->getCalculatecostKPR($value,$bank);

							$bank = array_merge($bank,$cost);
							$values[$key] = $bank;
						}
					}
					

					$banks[$field] = $values;
				}
			}
		}
		return $banks;	
		
	}

	function _callBeforeViewKprApplication ( $data, $value) {
		if( empty($data) ) {
			$data = $value;
		}

		if(!empty($data['KprApplication'])){
			foreach($data['KprApplication'] AS $key => $val){
				$same_as_address_ktp = Common::hashEmptyField($val, 'same_as_address_ktp');
				$val = $this->RmCommon->dataConverter($val, array(
					'date' => array(
						'birthday',
					)
				), true);

				if($same_as_address_ktp){
					$val['address_2'] = null;
				}

				$data['KprApplication'][$key] = $val;
			}
		}
		return $data;
	}

	function _callBeforeViewKprFiling ( $data, $value ) {
		if( empty($data) && !empty($value) ) {
			$data = $value;
		}

		$options = $this->_callDataKpr($data);
		$banks = $this->_callGetBank($value, $options);
		$this->controller->set(compact(
			'banks'
		));
		return $data;
	}

	function setListFillingProduct($data, $value, $params = null){
		if(!empty($data['BankSetting']['id'])){
			$property_price = Common::hashEmptyField($data, 'Kpr.property_price');
			$property_type_id = Common::hashEmptyField($value, 'Property.property_type_id');
			$region_id = Common::hashEmptyField($value, 'PropertyAddress.region_id');
			$city_id = Common::hashEmptyField($value, 'PropertyAddress.city_id');
			$bank_setting_ids  = Common::hashEmptyField($data, 'BankSetting.id');
			
			$dp = Common::hashEmptyField($params, 'dp');
			$credit_total = Common::hashEmptyField($params, 'credit_total');
			$down_payment = Common::hashEmptyField($params, 'down_payment');
			$document_status = Common::hashEmptyField($params, 'document_status', 'process');

			$data['Kpr']['document_status'] = $document_status;

			if(!empty($bank_setting_ids)){
				foreach($bank_setting_ids AS $setting_id){

					$bank_setting = $this->controller->User->Kpr->KprBank->Bank->BankSetting->getData('first', array(
						'conditions' => array(
							'BankSetting.id' => $setting_id,
						),
					), array(
						'type' => 'all',
					));
					$bank_id = Common::hashEmptyField($bank_setting, 'BankSetting.bank_id');

					$data['KprBank'][] = array(
						'bank_id' => $bank_id,
						'setting_id' => $setting_id,
						'property_price' => $property_price,
						'property_type_id' => $property_type_id,
						'region_id' => $region_id,
						'city_id' => $city_id,
						'type_kpr' => 'frontend',
						'dp' => $dp,
						'credit_total' => $credit_total,
						'down_payment' => $down_payment,
						'document_status' => $document_status,
					);
				}
			}
		}
		return $data;
	}

	function beforeSaveProduct($filling, $params = array()){

		$value = $this->RmCommon->filterEmptyField($params, 'value');
		$document_status = $this->RmCommon->filterEmptyField($params, 'document_status');
		$session_id = $this->RmCommon->filterEmptyField($params, 'session_id');
		$from_kpr = $this->RmCommon->filterEmptyField($params, 'from_kpr');
		$params['noted'] = __('Klien telah memilih pengajuan bank KPR via frontend PRIME');

		$agent_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id', null);
		$keyword = $this->RmCommon->filterEmptyField($value, 'Property', 'keyword');
		$price_measure = $this->RmCommon->filterEmptyField($value, 'Property', 'price_measure');
		$mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id');
		$property_id = $this->RmCommon->filterEmptyField($value, 'Property', 'id', null);
		$property_type_id = $this->RmCommon->filterEmptyField($value, 'Property', 'property_type_id', null);
		$bank_apply_category_id = ($property_type_id == 3)?2:1;

		$dataSave = $this->_saveKprBank($filling, $value, true, $params);

		$kpr = $this->controller->User->Kpr->getData('first', array(
			'conditions' => array(
				'Kpr.session_id' => $session_id,
			),
			'order' => false,
		));
		$agent = $this->controller->User->getData('first', array(
			'conditions' => array(
				'User.id' => $agent_id,
			),
		));

		$parent_id = $this->RmCommon->filterEmptyField($agent, 'User', 'parent_id', null);

		if(!empty($kpr)){
			$dataSave['Kpr']['id'] = $this->RmCommon->filterEmptyField($kpr, 'Kpr', 'id');
		}

		$dataSave['Kpr']['keyword'] = $keyword;
		$dataSave['Kpr']['agent_id'] = $agent_id;
		$dataSave['Kpr']['company_id'] = $parent_id;
		$dataSave['Kpr']['mls_id'] = $mls_id;
		$dataSave['Kpr']['property_price'] = $price_measure;
		$dataSave['Kpr']['property_id'] = $property_id;
		$dataSave['Kpr']['bank_apply_category_id'] = $bank_apply_category_id;
		$dataSave['Kpr']['document_status'] = $document_status;
		$dataSave['Kpr']['session_id'] = $session_id;
		$dataSave['Kpr']['from_kpr'] = $from_kpr;
		return $dataSave;
	}

	function getKprBankDate($slug, $options = array()){
		$kpr_bank_id = $this->RmCommon->filterEmptyField($options, 'kpr_bank_id');
		$note = $this->RmCommon->filterEmptyField($options, 'note');
		$date = date('Y-m-d H:i:s');

		if(!empty($slug)){
			if(!empty($kpr_bank_id)){
				$value['kpr_bank_id'] = $kpr_bank_id;
			}
			$value['slug'] = $slug;
			$value['action_date'] = $date;

			if( !empty($note) ) {
				$value['note'] = $note;
			}

			return $value;
		}
		return FALSE;
	}

	function setCommission($types, $values = array()){
		$result = array();
		if(!empty($types)){
			$percents = $this->RmCommon->filterEmptyField($values, 'percent');
			$commissions = $this->RmCommon->filterEmptyField($values, 'commission');
			foreach ($types as $key => $type) {
				$percent = !empty($percents[$key])?$percents[$key]:false;
				$commission = !empty($commissions[$key])?$commissions[$key]:false;
				$result[] = array(
					'type' => $type,
					'percent' => $percent,
					'commission' => $commission,
				);
			}
		}
		return $result;
	}

	function setKprBankCommission($type, $options = array()){
		$percent = $this->RmCommon->filterEmptyField($options, 'percent');
		$commission = $this->RmCommon->filterEmptyField($options, 'commission');
		$note = $this->RmCommon->filterEmptyField($options, 'note');
		$region_id = Common::hashEmptyField($options, 'region_id', null);
		$city_id = Common::hashEmptyField($options, 'city_id', null);
		$region_name = $this->RmCommon->filterEmptyField($options, 'region_name');
		$city_name = $this->RmCommon->filterEmptyField($options, 'city_name');

		if(!empty($percent) && !empty($commission)){
			return array(
				'type' => $type,
				'percent' => $percent,
				'value' => $commission,
				'note' => $note,
				'region_id' => $region_id,
				'city_id' => $city_id,
				'region_name' => $region_name,
				'city_name' => $city_name,
			);
		}
	}

	function setKprBank($value, $val, $options = array()){
		$document_status = Common::hashEmptyField($options, 'document_status', 'process');
		$application_status = $this->RmCommon->filterEmptyField($options, 'application_status', false, 'pending');
		$from_kpr = $this->RmCommon->filterEmptyField($options, 'from_kpr', false, 'backend');

		$kpr_id = Common::hashEmptyField($value, 'Kpr.id', null);
		$kpr_application_id = Common::hashEmptyField($value, 'KprApplication.id', null);
		$bank_id = Common::hashEmptyField($val, 'bank_id', null);
		$setting_id = Common::hashEmptyField($val, 'setting_id', null);
		$work_day = $this->RmCommon->filterEmptyField($val, 'BankSetting', 'work_day');
		$noted = $this->RmCommon->filterEmptyField($val, 'noted');

		if($kpr_id){
			$data['kpr_id'] = $kpr_id;
		}

		if($kpr_application_id){
			$data['kpr_application_id'] = $kpr_application_id;
		}

		$data['bank_id'] = $bank_id;
		$data['setting_id'] = $setting_id;
		$data['work_day'] = $work_day;
		$data['document_status'] = $document_status;
		$data['application_status'] = $application_status;
		$data['from_kpr'] = $from_kpr;
		$data['noted'] = $noted;

		return $data;
	}

	function setKprBankInstallment($val, $options = array()){
		$down_payment = $this->RmCommon->filterEmptyField($options, 'down_payment');
		$property_price = $this->RmCommon->filterEmptyField($options, 'property_price', false, 0);
		$credit_total = $this->RmCommon->filterEmptyField($options, 'credit_total');
        $loan_price = $this->calcLoanFromDp( $property_price, $down_payment );

        $optionsPrice = array(
			'price' => $property_price,
			'loan_price' => $loan_price,
		);

        $is_notary = $this->RmCommon->filterEmptyField($val, 'BankProduct', 'is_notary');

        $interest_rate_fix = $this->RmCommon->filterEmptyField($val, 'BankSetting', 'interest_rate_fix',0);
		$interest_rate_float = $this->RmCommon->filterEmptyField($val,'BankSetting','interest_rate_float',0);
		$interest_rate_cabs = $this->RmCommon->filterEmptyField($val,'BankSetting','interest_rate_cabs',0);
		$periode_installment = $this->RmCommon->filterEmptyField($val,'BankSetting','periode_installment',0);
		$commission = $this->RmCommon->filterEmptyField($val,'BankSetting','commission',0);
		$provision = $this->RmCommon->filterEmptyField($val,'BankSetting','provision',0);
		$provision_company = $this->RmCommon->filterEmptyField($val,'BankSetting','provision_company',0);
		$periode_fix = $this->RmCommon->filterEmptyField($val,'BankSetting','periode_fix',0);
		$periode_cab = $this->RmCommon->filterEmptyField($val,'BankSetting','periode_cab',0);
        $total_first_credit = $this->creditFix($loan_price, $interest_rate_fix, $credit_total);
		$provision = $this->RmCommon->filterIssetField($val, 'percent_agent', false, $provision);

		$commission = !empty($commission) ? $commission : $this->calValueFromPercent($loan_price, $provision);
		
		$provision_company = $this->RmCommon->filterIssetField($val, 'percent_company', false, $provision_company);
		$commission_company = $this->calValueFromPercent($loan_price, $provision_company);

		$category_appraisal = $this->RmCommon->filterEmptyField($val,'BankSetting','category_appraisal');
		$category_administration = $this->RmCommon->filterEmptyField($val,'BankSetting','category_administration');
		$category_insurance = $this->RmCommon->filterEmptyField($val,'BankSetting','category_insurance');
		$category_sale_purchase_certificate = $this->RmCommon->filterEmptyField($val,'BankSetting','category_sale_purchase_certificate');
		$category_transfer_title_charge = $this->RmCommon->filterEmptyField($val,'BankSetting','category_transfer_title_charge');
		$category_credit_agreement = $this->RmCommon->filterEmptyField($val,'BankSetting','category_credit_agreement');
		$category_letter_mortgage = $this->RmCommon->filterEmptyField($val,'BankSetting','category_letter_mortgage');
		$category_imposition_act_mortgage = $this->RmCommon->filterEmptyField($val,'BankSetting','category_imposition_act_mortgage');
		$category_mortgage = $this->RmCommon->filterEmptyField($val,'BankSetting','category_mortgage');
		$category_other_certificate = $this->RmCommon->filterEmptyField($val,'BankSetting','category_other_certificate');

		$param_appraisal = $this->RmCommon->filterEmptyField($val,'BankSetting','param_appraisal');
		$param_administration = $this->RmCommon->filterEmptyField($val,'BankSetting','param_administration');
		$param_insurance = $this->RmCommon->filterEmptyField($val,'BankSetting','param_insurance');
		$param_sale_purchase_certificate = $this->RmCommon->filterEmptyField($val,'BankSetting','param_sale_purchase_certificate');
		$param_transfer_title_charge = $this->RmCommon->filterEmptyField($val,'BankSetting','param_transfer_title_charge');
		$param_credit_agreement = $this->RmCommon->filterEmptyField($val,'BankSetting','param_credit_agreement');
		$param_letter_mortgage = $this->RmCommon->filterEmptyField($val,'BankSetting','param_letter_mortgage');
		$param_imposition_act_mortgage = $this->RmCommon->filterEmptyField($val,'BankSetting','param_imposition_act_mortgage');
		$param_mortgage = $this->RmCommon->filterEmptyField($val,'BankSetting','param_mortgage');
		$param_other_certificate = $this->RmCommon->filterEmptyField($val,'BankSetting','param_other_certificate');

		$appraisal = $this->RmCommon->filterEmptyField($val,'BankSetting','appraisal',0);
		$administration = $this->RmCommon->filterEmptyField($val,'BankSetting','administration',0);
		$insurance = $this->RmCommon->filterEmptyField($val,'BankSetting','insurance',0);
		$sale_purchase_certificate = $this->RmCommon->filterEmptyField($val,'BankSetting','sale_purchase_certificate',0);
		$transfer_title_charge = $this->RmCommon->filterEmptyField($val,'BankSetting','transfer_title_charge',0);
		$credit_agreement = $this->RmCommon->filterEmptyField($val,'BankSetting','credit_agreement',0);
		$letter_mortgage = $this->RmCommon->filterEmptyField($val,'BankSetting','letter_mortgage',0);
		$imposition_act_mortgage = $this->RmCommon->filterEmptyField($val,'BankSetting','imposition_act_mortgage',0);
		$mortgage = $this->RmCommon->filterEmptyField($val,'BankSetting','mortgage',0);
		$other_certificate = $this->RmCommon->filterEmptyField($val,'BankSetting','other_certificate',0);
        
		$appraisal_arr = $this->getPercent($appraisal, $optionsPrice, array(
			'category' => $category_appraisal,
			'params' => $param_appraisal,
		));

		$appraisal = $this->RmCommon->filterEmptyField($appraisal_arr, 'nominal', false, 0);
		$appraisal_percent = $this->RmCommon->filterEmptyField($appraisal_arr, 'percent', false, 0);
		$administration_arr = $this->getPercent($administration, $optionsPrice, array(
			'category' => $category_administration,
			'params' => $param_administration,
		));
		$administration = $this->RmCommon->filterEmptyField($administration_arr, 'nominal', false, 0);
		$administration_percent = $this->RmCommon->filterEmptyField($administration_arr, 'percent', false, 0);

		$insurance_arr = $this->getPercent($insurance, $optionsPrice, array(
			'category' => $category_insurance,
			'params' => $param_insurance,
		));
		$insurance = $this->RmCommon->filterEmptyField($insurance_arr, 'nominal', false, 0);
		$insurance_percent = $this->RmCommon->filterEmptyField($insurance_arr, 'percent', false, 0);

		$sale_purchase_certificate_arr = $this->getPercent($sale_purchase_certificate, $optionsPrice, array(
			'category' => $category_sale_purchase_certificate,
			'params' => $param_sale_purchase_certificate,
		));
		$sale_purchase_certificate = $this->RmCommon->filterEmptyField($sale_purchase_certificate_arr, 'nominal', false, 0);
		$sale_purchase_certificate_percent = $this->RmCommon->filterEmptyField($sale_purchase_certificate_arr, 'percent', false, 0);

		$transfer_title_charge_arr = $this->getPercent($transfer_title_charge, $optionsPrice, array(
			'category' => $category_transfer_title_charge,
			'params' => $param_transfer_title_charge,
		));
		$transfer_title_charge = $this->RmCommon->filterEmptyField($transfer_title_charge_arr, 'nominal', false, 0);
		$transfer_title_charge_percent = $this->RmCommon->filterEmptyField($transfer_title_charge_arr, 'percent', false, 0);

		$credit_agreement_arr = $this->getPercent($credit_agreement, $optionsPrice, array(
			'category' => $category_credit_agreement,
			'params' => $param_credit_agreement,
		));
		$credit_agreement = $this->RmCommon->filterEmptyField($credit_agreement_arr, 'nominal', false, 0);
		$credit_agreement_percent = $this->RmCommon->filterEmptyField($credit_agreement_arr, 'percent', false, 0);

		$letter_mortgage_arr = $this->getPercent($letter_mortgage, $optionsPrice, array(
			'category' => $category_letter_mortgage,
			'params' => $param_letter_mortgage,
		));
		$letter_mortgage = $this->RmCommon->filterEmptyField($letter_mortgage_arr, 'nominal', false, 0);
		$letter_mortgage_percent = $this->RmCommon->filterEmptyField($letter_mortgage_arr, 'percent', false, 0);

		$imposition_act_mortgage_arr = $this->getPercent($imposition_act_mortgage, $optionsPrice, array(
			'category' => $category_imposition_act_mortgage,
			'params' => $param_imposition_act_mortgage,
		));
		$imposition_act_mortgage = $this->RmCommon->filterEmptyField($imposition_act_mortgage_arr, 'nominal', false, 0);
		$imposition_act_mortgage_percent = $this->RmCommon->filterEmptyField($imposition_act_mortgage_arr, 'percent', false, 0);

		$mortgage_arr = $this->getPercent($mortgage, $optionsPrice, array(
			'category' => $category_mortgage,
			'params' => $param_mortgage,
		));
		$mortgage = $this->RmCommon->filterEmptyField($mortgage_arr, 'nominal', false, 0);
		$mortgage_percent = $this->RmCommon->filterEmptyField($mortgage_arr, 'percent', false, 0);

		$other_certificate_arr = $this->getPercent($other_certificate, $optionsPrice, array(
			'category' => $category_other_certificate,
			'params' => $param_other_certificate,
		));
		$other_certificate = $this->RmCommon->filterEmptyField($other_certificate_arr, 'nominal', false, 0);
		$other_certificate_percent = $this->RmCommon->filterEmptyField($other_certificate_arr, 'percent', false, 0);

        $total_notary_charge = KprCommon::_callCalcNotary($val, $optionsPrice);
		$total_cost_bank = $appraisal + $administration + $insurance + $commission;
		$grand_total = $down_payment + $total_first_credit + $total_cost_bank + $total_notary_charge;

		return array(
			'property_price' => $property_price,
			'down_payment' => $down_payment,
			'loan_price' => $loan_price,
			'credit_total' => $credit_total,
			'interest_rate_fix' => $interest_rate_fix,
			'total_first_credit' => $total_first_credit,
			'interest_rate_float' => $interest_rate_float,
			'interest_rate_cabs' => $interest_rate_cabs,
			'periode_fix' => $periode_fix,
			'periode_cab' => $periode_cab,
			'periode_installment' => $credit_total,
			'provision' => $provision,
			'commission' => $commission,
			'provision_rumahku' => $provision_company,
			'commission_rumahku' => $commission_company,
			'administration' => $administration,
			'administration_percent' => $administration_percent,
			'administration_params' => $param_administration,
			'appraisal' => $appraisal,
			'appraisal_percent' => $appraisal_percent,
			'appraisal_params' => $param_appraisal,
			'insurance' => $insurance,
			'insurance_percent' => $insurance_percent,
			'insurance_params' => $param_insurance,
			'sale_purchase_certificate' => $sale_purchase_certificate,
			'sale_purchase_certificate_percent' => $sale_purchase_certificate_percent,
			'sale_purchase_certificate_params' => $param_sale_purchase_certificate,
			'transfer_title_charge' => $transfer_title_charge,
			'transfer_title_charge_percent' => $transfer_title_charge_percent,
			'transfer_title_charge_params' => $param_transfer_title_charge,
			'credit_agreement' => $credit_agreement,
			'credit_agreement_percent' => $credit_agreement_percent,
			'credit_agreement_params' => $param_credit_agreement,
			'letter_mortgage' => $letter_mortgage,
			'letter_mortgage_percent' => $letter_mortgage_percent,
			'letter_mortgage_params' => $param_letter_mortgage,
			'imposition_act_mortgage' => $imposition_act_mortgage,
			'imposition_act_mortgage_percent' => $imposition_act_mortgage_percent,
			'imposition_act_mortgage_params' => $param_imposition_act_mortgage,
			'mortgage' => $mortgage,
			'mortgage_percent' => $mortgage_percent,
			'mortgage_params' => $param_mortgage,
			'other_certificate' => $other_certificate,
			'other_certificate_percent' => $other_certificate_percent,
			'other_certificate_params' => $param_other_certificate,
			'notary' => $total_notary_charge,
			'is_notary' => $is_notary,
			'grandtotal' => $grand_total,
		);
	}

	function getPercent($value = 0, $optionPrices = array(), $params = array()){
		$price = $this->RmCommon->filterEmptyField($optionPrices, 'price',false, 0);
		$loan_price = $this->RmCommon->filterEmptyField($optionPrices, 'loan_price', false, 0);
		$category = $this->RmCommon->filterEmptyField($params, 'category');
		$param = $this->RmCommon->filterEmptyField($params, 'params');
		$parameter = ($param == 'price')?$price:$loan_price;

		if($category == 'percent'){
			$result = @(round(($value/100)*$parameter));
			return array(
				'nominal' => $result,
				'percent' => $value,
			);
		}else{
			$result = @(round(($value/$parameter)*100, 0));
			return array(
				'nominal' => $value,
				'percent' => $result,
			);
		}
	}

	function getCommissionKPR($data, $down_payment, $bank_setting = false){
		$this->BankCommissionSetting = ClassRegistry::init('BankCommissionSetting');
		$setting_loan_id = null;
		$type_kpr = Common::hashEmptyField($data, 'type_kpr', 'backend');
		$property_price = Common::hashEmptyField($data, 'property_price');
		$bank_id = Common::hashEmptyField($data, 'bank_id');
		$setting_id = Common::hashEmptyField($data, 'setting_id');
		$property_type_id = Common::hashEmptyField($data, 'property_type_id');
		$region_id = Common::hashEmptyField($data, 'region_id');
		$city_id = Common::hashEmptyField($data, 'city_id');
		$project_id = Common::hashEmptyField($data, 'project_id');
		$loan_price = $property_price - $down_payment;

		if( !empty($setting_id) && !empty($bank_id) && !empty($property_price) ){
			if(empty($bank_setting)){
				$bank_setting = $this->BankCommissionSetting->Bank->BankSetting->getData('first', array(
					'conditions' => array(
						'BankSetting.id' => $setting_id,
						'BankSetting.bank_id' => $bank_id,
					)
				), array(
					'type' => 'all',
				));
			}

			$bank_setting = $this->BankCommissionSetting->Bank->getMerge($bank_setting, $bank_id);
			$region_id_bank = Common::hashEmptyField($bank_setting, 'Bank.region_id');
			$city_id_bank = Common::hashEmptyField($bank_setting, 'Bank.city_id');
			$percent_agent = Common::hashEmptyField($bank_setting, 'BankSetting.provision');
			$percent_company = Common::hashEmptyField($bank_setting, 'BankSetting.provision_company');

			$value = $this->BankCommissionSetting->getData('first', array(
				'conditions' => array(
					'BankCommissionSetting.bank_id' => $bank_id,
					'BankCommissionSetting.property_type_id' => $property_type_id,
					'OR' => array(
						array(
							'BankCommissionSetting.region_id' => $region_id,
							'BankCommissionSetting.city_id' => $city_id,
						),
						array(
							'BankCommissionSetting.region_id' => $region_id,
							'BankCommissionSetting.city_id' => null,
						),
						array(
							'BankCommissionSetting.region_id' => null,
							'BankCommissionSetting.city_id' => null,
						),
					),
				),
				'order' => false,
			), array(
				'company_id' => Configure::read('Principle.id'),
				'project_id' => $project_id,
			));	

			if(!empty($value)){
				$bank_setting_id = Common::hashEmptyField($value, 'BankCommissionSetting.id');
				$commission = $this->BankCommissionSetting->BankCommissionSettingLoan->getData('first',array(
					'conditions' => array(
						'BankCommissionSettingLoan.bank_setting_id' => $bank_setting_id,
						'BankCommissionSettingLoan.min_loan <=' => $loan_price,
					),
					'order' => array(
						'BankCommissionSettingLoan.min_loan' => 'DESC',
					),
				));
				$setting_loan_id = Common::hashEmptyField($commission, 'BankCommissionSettingLoan.id');
				$percent_agent = Common::hashEmptyField($commission, 'BankCommissionSettingLoan.rate', $percent_agent);
				$percent_company = Common::hashEmptyField($commission, 'BankCommissionSettingLoan.rate_company', $percent_company);

				$region_id = Common::hashEmptyField($value, 'BankCommissionSetting.region_id', $region_id_bank);
				$city_id = Common::hashEmptyField($value, 'BankCommissionSetting.city_id', $city_id_bank);
				$note = Common::hashEmptyField($value, 'BankCommissionSetting.description');
			} else {
				$note = false;
				$setting_loan_id = null;
				$percent_agent = Common::hashEmptyField($bank_setting, 'BankSetting.provision');
				$percent_company = Common::hashEmptyField($bank_setting, 'BankSetting.provision_company');
			}

			$value = $this->BankCommissionSetting->Bank->Region->getMerge($value, $region_id, 'Region', array(
				'cache' => array(
					'name' => __('Region.%s', $region_id),
				),
			));
			$value = $this->BankCommissionSetting->Bank->City->getMerge($value, $city_id, 'City', 'City.id', array(
				'cache' => __('City.%s', $city_id),
			));

			$region_name = Common::hashEmptyField($value, 'Region.name');
			$city_name = Common::hashEmptyField($value, 'City.name');

			if(in_array(TRUE, array($percent_agent, $percent_company))){
				$arr = array(
					'setting_loan_id' => $setting_loan_id,
					'percent_agent' => $percent_agent,
					'percent_company' => $percent_company,
					'note' => $note,
					'region_name' => $region_name,
					'region_id' => $region_id,
					'city_name' => $city_name,
					'city_id' => $city_id,

				);
				$data = array_merge($data, $arr);
			}
		}

		return $data;
	}

	function _GenerateCommission($rate = 0,$loan_price = 0){

		return ($rate/100)*$loan_price;

	}

	function setPropertySold($data, $value){
		$document_status = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'document_status');
		if(in_array($document_status, array('akad_credit'))){

			$property_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'property_id');
			$sold_date = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'sold_date');
			$property_action_id = $this->RmCommon->filterEmptyField($value, 'Property', 'property_action_id');

			$agent_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'agent_id');
			$client_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'user_id');
			$currency_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'currency_id');

			$agent = $this->controller->User->getData('first', array(
				'conditions' => array(
					'User.id' => $agent_id
				),
			));
			$client = $this->controller->User->getData('first', array(
				'conditions' => array(
					'User.id' => $client_id
				),
			));

			$agent_email = $this->RmCommon->filterEmptyField( $agent, 'User', 'email');
			$client_name = $this->RmCommon->filterEmptyField( $client, 'User', 'client_email');
			$currency_id = $this->RmCommon->filterEmptyField( $value, 'KprApplication', 'currency_id');
			$property_price = $this->RmCommon->filterEmptyField( $value, 'KprApplication', 'property_price');

			$data['PropertySold'] = array(
				'sold_by_name' => $agent_email,
				'client_name' => $client_name,
				'currency_id' => 1,
				'price_sold' => $property_price,
				'property_id' => $property_id,
				'property_action_id' => $property_action_id,
				'client_id' => $client_id,
				'sold_date' => $sold_date,

			);
			$data['Property'] = array(
				'sold' => true,
			);
		}

		return $data;
	}

	function parsingDocument( $documentCategories, $kpr_application, $model = 'KprApplication'){
		$fields = array(
			1 => 'booking_fee_file',
			2 => 'ktp_file',
			4 => 'income_file',
			5 => 'imb_file',
			6 => 'npwp_file',
		);
		foreach($documentCategories AS $key => $documentCategori){
			$crm_project_document = $this->RmCommon->filterEmptyField( $documentCategori, 'CrmProjectDocument');
			if(!empty($crm_project_document)){
				$document_category_id = $this->RmCommon->filterEmptyField($documentCategori, 'CrmProjectDocument', 'document_category_id');
				$file = $this->RmCommon->filterEmptyField($documentCategori, 'CrmProjectDocument', 'file');
				$save_path = $this->RmCommon->filterEmptyField($documentCategori, 'CrmProjectDocument', 'save_path');
				$field = $fields[$document_category_id];
				$kpr_application[$model][$field] = $file;
				$kpr_application[$model][sprintf('%s_path', $field)] = $save_path;
			}
			
		}

		return $kpr_application;
	}

	function _callGetDocument ( $category, $value ) {
		$document_type = $this->RmCommon->filterEmptyField($value, 'document_type');
		$id = $this->RmCommon->filterEmptyField($category, 'DocumentCategory', 'id');
		$type = $this->RmCommon->filterEmptyField($category, 'DocumentCategory', 'type');
		switch ($document_type) {
			case 'kpr_application':
				$owner_id = $this->RmCommon->filterEmptyField($value, 'kpr_application_id');
				$type = $document_type;
				break;
			case 'kprs':
				$owner_id = $this->RmCommon->filterEmptyField($value, 'kpr_id');
				$type = $document_type;
				break;
			default:
				$owner_id = $this->RmCrm->_callDocumentOwner($type, $value);
				break;
		}

		if( !empty($owner_id) ) {
			$document = $this->controller->User->CrmProject->CrmProjectDocument->getData('first', array(
				'conditions' => array(
					'document_type' => $type,
					'owner_id' => $owner_id,
					'document_category_id' => $id,
				),
			));
		} else {
			$document = false;
		}

		return $document;
	}

	function _callDocumentCategories($conditions = array(), $value = false, $sort = 'DESC'){
		$categories = $this->controller->User->CrmProject->CrmProjectDocument->DocumentCategory->getData( 'all', array(
			'conditions' => $conditions,
		));


    	if(!empty($categories)){
			foreach($categories AS $key => $category){
				$document = $this->_callGetDocument($category, $value);
				$categories[$key]['CrmProjectDocument'] = $this->RmCommon->filterEmptyField($document, 'CrmProjectDocument');
			}
		}

		return $categories;
    }

    function getListDocument($value, $options = array()){
    	$document_arr = array();
    	$id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id', null);
    	$client_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'user_id', null);
    	$property_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'property_id', null);
    	$document_type = $this->RmCommon->filterEmptyField($value, 'Kpr', 'document_type');

    	$data_set = Common::hashEmptyField($options, 'data_set', true, array(
    		'isset' => false,
		));
		$document_merge = Common::hashEmptyField($options, 'document_merge', false, array(
    		'isset' => false,
		));
		$optionsDocuments = array(
			'DocumentCategory.is_required' => 1,
			'DocumentCategory.id <>' => array( 3, 7, 19, 20),
		);

		if($document_type == 'developer') {
			$optionsDocuments['DocumentCategory.is_external'] = true;
		}

    	$documentCategories = $this->getDocumentSort($optionsDocuments, array(
			'id' => $id,
			'owner_id' => $client_id,
			'property_id' => $property_id,
			'document_type' => 'kpr_application',
		), $value);

		if(!empty($documentCategories)){
			foreach($documentCategories AS $key => $documentCategori){
				$name = $this->RmCommon->filterEmptyField($documentCategori, 'DocumentCategory', 'alias');
				$crmProjectDocument = $this->RmCommon->filterEmptyField($documentCategori, 'CrmProjectDocument');

				if($crmProjectDocument){
					$document_arr[] = $name;
				}
			}
		}

		$documentCategoriesSpouse = $this->getDocumentSort(
			array(
				'DocumentCategory.is_required' => 1,
				'DocumentCategory.id <>' => array( 1, 2, 3, 4, 5, 6, 7, 15, 16, 17),
			), array(
				'id' => $id,
				'owner_id' => $client_id,
				'document_type' => 'kpr_spouse_particular',
		), $value);

		if(!empty($documentCategoriesSpouse)){
			foreach($documentCategoriesSpouse AS $key => $documentCategori){
				$name = $this->RmCommon->filterEmptyField($documentCategori, 'DocumentCategory', 'alias');
				$crmProjectDocument = $this->RmCommon->filterEmptyField($documentCategori, 'CrmProjectDocument');

				if($crmProjectDocument){
					$document_arr[] = $name;
				}
			}
		}

		if($document_arr){
			$value['Kpr']['list_document'] = implode(', ', $document_arr);
		}else{
			$value['Kpr']['list_document'] = 'N/A';
		}

		if($data_set){
			$this->controller->set(array(
				'documentCategories' => $documentCategories,
				'documentCategoriesSpouse' => $documentCategoriesSpouse,
			));
		}
		if( !empty($document_merge) ) {
			$value['DocumentCategory'] = $documentCategories;
			$value['DocumentCategorySpouse'] = $documentCategoriesSpouse;
		}

		return $value;
    }

	function getDocumentSort($options_conditions = array(), $options = array(), $value = false, $sort = 'DESC'){
		$document_type = Common::hashEmptyField($value, 'Kpr.document_type');

		switch ($document_type) {
			case 'developer':
				$options_conditions['DocumentCategory.is_external'] = true;
				break;
		}

		$data = $this->controller->User->CrmProject->CrmProjectDocument->DocumentCategory->getData( 'all', array(
			'conditions' => $options_conditions,
			'order' => array(
				'DocumentCategory.order' => 'ASC',
				'DocumentCategory.id' => 'ASC',
			),
		));
    	$id = !empty($options['id'])?$options['id']:0;
    	$document_type = !empty($options['document_type'])?$options['document_type']:false;
    	$owner_id = !empty($options['owner_id'])?$options['owner_id']:null;
    	$property_id = !empty($options['property_id'])?$options['property_id']:0;

    	if(!empty($data)){
			foreach($data AS $key => $document_category){
				$document_category_id = $this->RmCommon->filterEmptyField($document_category, 'DocumentCategory', 'id');

				if($document_category_id == 5){
					$v_id = $property_id;
					$v_document_type = 'property';
				
				}else{
					$v_id = $id;
					$user_id = $owner_id;
					$v_document_type = $document_type;
				}	

				$document_category = $this->controller->User->CrmProject->CrmProjectDocument->getDataSort($document_category, array(
					'id' => $id,
					'document_type' => $v_document_type,
					'owner_id' => !empty($v_id)?$v_id:0,
					'sort' => $sort,
				));

				$data[$key] = $document_category;
			}
		}
		return $data;
    }

    function getEmptyFile(){
    	return array(
			'name' => '',
			'type' => '',
			'tmp_name' => '',
			'error' => (int) 4,
			'size' => (int) 0
		);
    }

    function documentMandatory($document_category_id = false, $mandatory = array(), $error = 0, $flag = false){
		
		if(in_array($document_category_id, $mandatory) && $error == 1){
			$flag = true;
		}

    	return $flag;
    }

    function buildDocument( $data, $options = array(), $mandatory = array()){
    	$documentCategories = array();
    	$documentCategoriesSpouse = array();
    	$old_path = $this->RmCommon->filterEmptyField($options, 'old_path');
    	$owner_id = $this->RmCommon->filterEmptyField($options, 'owner_id', false, null);
    	$particular_id = $this->RmCommon->filterEmptyField($options, 'particular_id', false, null);
    	$document_type = $this->RmCommon->filterEmptyField($options, 'document_type');
    	$document_type_particular = $this->RmCommon->filterEmptyField($options, 'document_type_particular');
    	$property_id = $this->RmCommon->filterEmptyField($options, 'property_id', false, null);
    	$data_options = $this->RmCommon->filterEmptyField($options, 'options');

    	$projectDocument = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocument');
		$particularDocument = $this->RmCommon->filterEmptyField($data, 'ParticularDocument');

		if(!empty($projectDocument)){
			unset($data['CrmProjectDocument']);

			foreach($projectDocument AS $key => $val){
				$document_category_id = $this->RmCommon->filterEmptyField($val, 'document_category_id', false, null);
				$doc_file = $this->RmCommon->filterEmptyField($val, 'file');

				if( !empty($val['file']) ) {
					$doc_file = $val['file'];
				} else {
					$doc_file = $val;
				}

				$category = $this->controller->User->CrmProject->CrmProjectDocument->DocumentCategory->getMerge(array(), $document_category_id);

    			$documentCategories[$key] = $category;
				$category_type = $this->RmCommon->filterEmptyField($category, 'DocumentCategory', 'type');
				$category_title = $this->RmCommon->filterEmptyField($category, 'DocumentCategory', 'name');

				if( !empty($doc_file['name']) || !empty($val['file_hide']) ){
    				$save_path = $this->RmCommon->filterEmptyField($options, 'save_path');
					$owner_id = $this->RmCrm->_callDocumentOwner($document_type, $data_options);
					$owner_id_mstr = $this->RmCrm->_callDocumentOwner($category_type, $data_options);
					$master_document_type = $category_type;

					if( !empty($doc_file['name']) ) {
						$document = $this->RmImage->upload( $doc_file, $save_path, String::uuid(), array(
							'fullsize' => true,
							'allowed_ext' => array('jpg', 'jpeg', 'png', 'gif', 'pdf'),
							'allowed_mime' => array(
								'image/gif', 'image/jpeg', 
								'image/png', 'image/pjpeg', 'image/x-png',
								'application/pdf',
							),
						));

						$photo_name = $this->RmCommon->filterEmptyField($document, 'imageName');
						$baseName = $this->RmCommon->filterEmptyField($document, 'baseName');
						$error = $this->RmCommon->filterEmptyField($document, 'error');
					} else {
						$photo_name = $this->RmCommon->filterEmptyField($val, 'file_hide');
						$baseName = $this->RmCommon->filterEmptyField($val, 'file_hide');
						$error = false;
					}
				} else {
					$doc = $this->_callGetDocument($category, $data_options);
					$master_document_type = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'document_type');
					$owner_id_mstr = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'owner_id', null);
					$save_path = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'save_path');
					
					$photo_name = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'file', null);
					$baseName = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'name');

					$error = false;
				}

				$document['CrmProjectDocument'] = $val;

				if(!empty($mandatory)){
					$cnt_document = $this->controller->User->CrmProject->CrmProjectDocument->getData('all', array(
						'conditions' => array(
							'OR' => array(
								array(
									'CrmProjectDocument.document_type' => $document_type,
									'CrmProjectDocument.owner_id' => $owner_id,
									'CrmProjectDocument.status' => TRUE,
								),
								array(
									'CrmProjectDocument.document_type' => $master_document_type,
									'CrmProjectDocument.owner_id' => $owner_id_mstr,
									'CrmProjectDocument.status' => TRUE,
								),
							)
						),
					));

					$flag_document = $this->documentMandatory($document_category_id, $mandatory, $error);

					if($cnt_document > 0){
						$flag_document = FALSE;
					}

					$document['CrmProjectDocument']['mandatory'] = $flag_document;

					if($flag_document){
						$data['CrmProjectDocument']['mandatory'] = TRUE;
					}
				}

				$document['CrmProjectDocument']['title'] = $category_title;
				$document['CrmProjectDocument']['file'] = $photo_name;
				$document['CrmProjectDocument']['file_name'] = $photo_name;
				$document['CrmProjectDocument']['file_hide'] = $photo_name;
				$document['CrmProjectDocument']['file_save_path'] = $save_path;				
				$document['CrmProjectDocument']['name'] = $baseName;

				$document['CrmProjectDocument']['document_type'] = $document_type;
				$document['CrmProjectDocument']['save_path'] = $save_path;
				$document['CrmProjectDocument']['owner_id'] = !empty($owner_id)?$owner_id:0;

				if( !empty($photo_name) ) {
					if( !empty($doc_file['name']) && !empty($document['CrmProjectDocument']) ) {
						$documentCategories[$key]['CrmProjectDocument'] = $document['CrmProjectDocument'];
					} else if( !empty($doc['CrmProjectDocument']) ) {
						$documentCategories[$key]['CrmProjectDocument'] = $doc['CrmProjectDocument'];
					}
				}

				$data['CrmProjectDocument'][$key] = $document['CrmProjectDocument'];

				if( !empty($doc_file['name']) ){
					$document['CrmProjectDocument']['document_type'] = $master_document_type;
					$document['CrmProjectDocument']['owner_id'] = !empty($owner_id_mstr)?$owner_id_mstr:0;
					$data['CrmProjectDocumentMstr'][$key] = $document['CrmProjectDocument'];
				}
			}
		}

		if(!empty($particularDocument)){
			$save_path = $this->RmCommon->filterEmptyField($options, 'save_path');
			unset($data['ParticularDocument']);

			foreach($particularDocument AS $key => $val){
				$doc_file = $this->RmCommon->filterEmptyField($val, 'file');
				$file_name = $this->RmCommon->filterEmptyField($val, 'file_name');
				$document_category_id = $this->RmCommon->filterEmptyField($val, 'document_category_id', false, null);

				if( !empty($val['file']) ) {
					$doc_file = $val['file'];
				} else {
					$doc_file = $val;
				}

				$category = $this->controller->User->CrmProject->CrmProjectDocument->DocumentCategory->getMerge(array(), $document_category_id);
				$documentCategoriesSpouse[$key] = $category;
				$category_type = $this->RmCommon->filterEmptyField($category, 'DocumentCategory', 'type');
				$category_title = $this->RmCommon->filterEmptyField($category, 'DocumentCategory', 'name');

				if( !empty($doc_file['name']) || !empty($val['file_hide']) ){
    				$save_path = $this->RmCommon->filterEmptyField($options, 'save_path');
					$owner_id = $this->RmCrm->_callDocumentOwner($document_type, $data_options);
					$owner_id_mstr = $this->RmCrm->_callDocumentOwner($category_type, $data_options);
					$master_document_type = $category_type;

					if( !empty($doc_file['name']) ) {
						$document = $this->RmImage->upload( $doc_file, $save_path, String::uuid(), array(
							'fullsize' => true,
							'allowed_ext' => array('jpg', 'jpeg', 'png', 'gif', 'pdf'),
							'allowed_mime' => array(
								'image/gif', 'image/jpeg', 
								'image/png', 'image/pjpeg', 'image/x-png',
								'application/pdf',
							),
						));
						$photo_name = $this->RmCommon->filterEmptyField($document, 'imageName');
						$baseName = $this->RmCommon->filterEmptyField($document, 'baseName');
						$error = $this->RmCommon->filterEmptyField($document, 'error');
					} else {
						$photo_name = $this->RmCommon->filterEmptyField($val, 'file_hide');
						$baseName = $this->RmCommon->filterEmptyField($val, 'file_hide');
						$error = false;
					}
				} else {
					$doc = $this->_callGetDocument($category, $data_options);
					
					$master_document_type = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'document_type');
					$owner_id_mstr = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'owner_id', null);
					$save_path = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'save_path');
					
					$photo_name = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'file', null);
					$baseName = $this->RmCommon->filterEmptyField($doc, 'CrmProjectDocument', 'name');

					$error = false;
				}

				if( !empty($photo_name) ) {
					if( !empty($doc_file['name']) && !empty($document['CrmProjectDocument']) ) {
						$documentCategoriesSpouse[$key]['ParticularDocument'] = $document['CrmProjectDocument'];
					} else if( !empty($doc['CrmProjectDocument']) ) {
						$documentCategoriesSpouse[$key]['ParticularDocument'] = $doc['CrmProjectDocument'];
					}
				}
				
				if( !empty($document) ) {
					$error = $this->RmCommon->filterEmptyField($document, 'error');
					$document['ParticularDocument'] = $val;

					$document['ParticularDocument']['file'] = $photo_name;
					$document['ParticularDocument']['file_hide'] = $photo_name;
					$document['ParticularDocument']['file_name'] = $photo_name;
					$document['ParticularDocument']['file_save_path'] = $save_path;				
					$document['ParticularDocument']['name'] = $baseName;
					$document['ParticularDocument']['document_type'] = $document_type_particular;
					$document['ParticularDocument']['save_path'] = $save_path;
					$document['ParticularDocument']['owner_id'] = !empty($owner_id)?$owner_id:0;

					$data['ParticularDocument'][$key] = $document['ParticularDocument'];
				}
			}
		}

		$this->controller->set('documentCategories', $documentCategories);
		$this->controller->set('documentCategoriesSpouse', $documentCategoriesSpouse);
		return $data;
    }

    function beforeSaveCreditAgreement($data, $value){
    	$application_document = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'Document', array());
    	$particular_document = $this->RmCommon->filterEmptyField($data, 'KprApplicationParticular', 'Document', array());
    	$documents = array_merge($application_document, $particular_document);

    	$data = $this->RmCommon->_callUnset(array(
    		'KprApplication' => array(
    			'id',
    			'kpr_bank_id',
    			'parent_id',
    			'JobType',
    			'Region',
    			'City',
    			'Subarea',
    			'Document',
    			'created',
    			'modified',
    		),
    		'KprApplicationParticular' => array(
    			'kpr_bank_id',
    			'parent_id',
    			'JobType',
    			'Region',
    			'City',
    			'Subarea',
    			'Document',
    			'created',
    			'modified',
    		),
    		'KprBankCreditAgreement' => array(
    			'id',
    			'kpr_bank_id',
    			'created',
    			'modified',
    		),
    	), $data);
    	$prime_kpr_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'kpr_id', null);
    	$kpr_application = $this->RmCommon->filterEmptyField($data, 'KprApplication');
    	$kpr_application_particular = $this->RmCommon->filterEmptyField($data, 'KprApplicationParticular');

    	if(!empty($kpr_application)){
    		$application = $this->controller->User->Kpr->KprApplication->getData('first', array(
    			'conditions' => array(
    				'KprApplication.kpr_id' => $prime_kpr_id,
    				'KprApplication.parent_id' => NULL
    			),
    		));
    		
    		$parent_application_id = $this->RmCommon->filterEmptyField($application, 'KprApplication', 'id');
    		$dataKprApplication = Common::hashEmptyField($application, 'KprApplication', array());

    		$arr['KprApplication'][]['KprApplication'] = array_merge($dataKprApplication, $kpr_application);

    	}

    	if(!empty($kpr_application_particular) && !empty($parent_application_id)){
    		$application = $this->controller->User->Kpr->KprApplication->getData('first', array(
    			'conditions' => array(
    				'KprApplication.kpr_id' => $prime_kpr_id,
    				'KprApplication.parent_id' => $parent_application_id,
    			),
    		));

    		if( empty($application) ) {
    			$application['KprApplication'] = array();
    			$kpr_application_particular['parent_id'] = $parent_application_id;
    			$kpr_application_particular['kpr_id'] = $prime_kpr_id;
    		}

    		$dataKprApplication = Common::hashEmptyField($application, 'KprApplication', array());
			$arr['KprApplication'][]['KprApplication'] = array_merge($dataKprApplication, $kpr_application_particular);
    	}


    	if(!empty($arr)){
    		$documents = $this->beforeSaveDocument($documents, $prime_kpr_id);
    		$data = $this->RmCommon->_callUnset( array(
    			'KprApplicationParticular'
    		),$data);

    		$data = array_merge($data, $arr);
    		$data['Document'] = $documents;
    	}

    	return $data;
    }

    function beforeSaveDocument($documents, $kpr_application_id){
    	$docment_arr = array();

    	if(!empty($documents) && !empty($kpr_application_id)){
    		foreach($documents AS $key => $document){
    			$doc = $this->RmCommon->filterEmptyField($document, 'Document');

    			if(!empty($doc)){
    				$document_category_id = $this->RmCommon->filterEmptyField($doc, 'document_category_id');
    				$document_type = $this->RmCommon->filterEmptyField($doc, 'document_type');

    				$document['Document']['document_type'] = ($document_type == 'property') ? 'kprs' : $document_type;
    				$document['Document']['owner_id'] = !empty($kpr_application_id)?$kpr_application_id:0;

    				$document = $this->RmCommon->_callUnset(array(
    					'Document' => array(
    						'id',
    					),
    				), $document);

    				$docment_arr[]['CrmProjectDocument'] = $document['Document'];
    			}	
    		}
    	}else{
    		$docment_arr = $documents;
    	}
    	return $docment_arr;
    }

    function SortFromKPR($documents, $value){
    	$document = array();
    	$id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'id', 0);
    	$company_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'company_id');
    	$property_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'property_id', 0);

    	if(!empty($documents)){
    		foreach($documents AS $key => $val){
    			$doc = $this->RmCommon->filterEmptyField($val, 'Document');

    			if(!empty($doc)){
    				$document_type = $this->RmCommon->filterEmptyField($doc, 'document_type');
    				$doc_replace = null;
    				switch($document_type){
    					case 'kpr_application':
    						$doc_replace = array(
    							'owner_id' => $id,
    							'company_id' => $company_id,
    						);
    					break;
    					case 'property':
    						$doc_replace = array(
    							'owner_id' => $property_id,
    							'company_id' => $company_id,
    						);
    					break;
    				}
					$doc = array_merge( $doc, $doc_replace);
    				$document['CrmProjectDocument'][] = $doc;

    			}
    		}
    	}	
    	return $document;
    }

    function _callDataByProperty ( $data ) {
		if( empty($data['Kpr']) ) {
			$property_id = $this->RmCommon->filterEmptyField($data, 'Property', 'id');
			$client_email = $this->RmCommon->filterEmptyField($data, 'UserClient', 'email');

			$options = array(
                'conditions' => array(
                	'Kpr.property_id' => $property_id,
            	),
            );

			if( !empty($client_email) ) {
				$options['conditions']['Kpr.client_email'] = $client_email;
			}

			$value = $this->controller->User->Kpr->getData('first', $options);

			if( !empty($value) ) {
				$kpr_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id');
				$value = $this->controller->User->Kpr->KprBank->getMerge($value, $kpr_id, 'Kpr', 'all');
			}

			if( !empty($data['Kpr']) ) {
				$sold_price = $this->RmCommon->filterEmptyField($data, 'Kpr', 'property_price');
				$sold_date = $this->RmCommon->filterEmptyField($data, 'Kpr', 'sold_date');
			} else {
				$data = $this->controller->User->Property->PropertySold->getMerge($data, $property_id);

				$sold_price = $this->RmCommon->filterEmptyField($data, 'PropertySold', 'price_sold');
				$sold_date = $this->RmCommon->filterEmptyField($data, 'PropertySold', 'sold_date');
			}

			$sold_date = $this->RmCommon->formatDate($sold_date, 'd/m/Y', false);

			$data['Property']['sold_price'] = $sold_price;
			$data['Property']['sold_date'] = $sold_date;
		}

		return $data;
    }

    function _callDataByCRM ( $data ) {
		$property_id = $this->RmCommon->filterEmptyField($data, 'Property', 'id');

		if( empty($data['Kpr']) ) {
			$client_email = $this->RmCommon->filterEmptyField($data, 'UserClient', 'email');

			$options = array(
                'conditions' => array(
                	'Kpr.property_id' => $property_id,
            	),
            );

			if( !empty($client_email) ) {
				$options['conditions']['Kpr.client_email'] = $client_email;
			}

			$value = $this->controller->User->Kpr->getData('first', $options);

			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}

		return $data;
    }

    function _callCookieNotice ( $id, $type ) {
    	if( !empty($id) ) {
	    	$cookie_name = sprintf('Kpr.Notice.%s.%s', $type, $id);
			$read = $this->controller->Cookie->read($cookie_name);

			if( empty($read) ) {
				$this->controller->Cookie->write($cookie_name, true, false, '1 Year');
				return __('Cookie KPR notifikasi');
			} else {
				return __('Cookie KPR telah terdaftar');
			}
		} else {
			return __('KPR tidak ditemukan');
		}
    }

    function filterKprApplicationParticular($data, $value, $id){
    	## POST
    	$data_application = $this->RmCommon->filterEmptyField($data, 'KprApplication');
    	$status_marital = !empty($data_application[0]['status_marital'])?$data_application[0]['status_marital']:false;
    	## QUERY
    	$kpr_application = $this->RmCommon->filterEmptyField($value, 'KprApplication');
    	$kpr_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id', null);
    	$parent_id = !empty($kpr_application[0]['id'])?$kpr_application[0]['id']:null;
    	$spouse_id  = !empty($kpr_application[1]['id'])?$kpr_application[1]['id']:null;

    	if(!empty($parent_id)){
    		$data['KprApplication'][0]['id'] = $parent_id;
    	}

    	if($status_marital <> 'marital'){
    		if(!empty($data['KprApplication'][1])){
	    		unset($data['KprApplication'][1]);			
    		}

    		$data = $this->RmCommon->_callUnset( array(
    			'ParticularDocument'
    		), $data);
    	}else{
    		if(isset($spouse_id)){
    			$data['KprApplication'][1]['id'] = $spouse_id;
    		}
    		$data['KprApplication'][1]['kpr_id'] = $kpr_id;
    		$data['KprApplication'][1]['parent_id'] = $parent_id;
    	}
    	return $data;
    }

    function valueFromPlafond($banks, $options = array()){
		$property_price = $this->RmCommon->filterEmptyField($options, 'property_price');
		$down_payment = $this->RmCommon->filterEmptyField($options, 'down_payment');
		$plafond = $property_price - $down_payment;

		if(!empty($banks)){
			if(!empty($banks[0])){
				foreach($banks AS $key => $bank){
					$bank = $this->generateNominalPercent($bank, array(
						'plafond' => $plafond,
						'property_price' => $property_price,
					));
					$banks[$key] = $bank;
				}
			}else{
				$banks = $this->generateNominalPercent($banks, array(
					'plafond' => $plafond,
					'property_price' => $property_price,
				));
			}
		}

		return $banks;
	}

	function generateNominalPercent($bank, $options = array()){
		$property_price = $this->RmCommon->filterEmptyField($options, 'property_price');
		$plafond = $this->RmCommon->filterEmptyField($options, 'plafond');

		$insurance = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'insurance');
		$appraisal = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'appraisal');
		$administration = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'administration');
		$sale_purchase_certificate = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'sale_purchase_certificate');
		$transfer_title_charge = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'transfer_title_charge');
		$letter_mortgage = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'letter_mortgage');
		$imposition_act_mortgage = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'imposition_act_mortgage');
		$mortgage = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'mortgage');
		$other_certificate = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'other_certificate');
		$credit_agreement = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'credit_agreement');

		$category_insurance = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'category_insurance');
		$category_appraisal = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'category_appraisal');
		$category_administration = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'category_administration');
		$category_sale_purchase_certificate = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'category_sale_purchase_certificate');
		$category_transfer_title_charge = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'category_transfer_title_charge');
		$category_letter_mortgage = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'category_letter_mortgage');
		$category_imposition_act_mortgage = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'category_imposition_act_mortgage');
		$category_mortgage = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'category_mortgage');
		$category_other_certificate = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'category_other_certificate');
		$category_credit_agreement = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'category_credit_agreement');


		if(!empty($insurance) && $category_insurance == 'nominal'){
			$insurance = ($insurance/$property_price)*100;
			$bank['BankSetting']['insurance'] = $insurance;
		}

		if(!empty($appraisal) && $category_appraisal == 'percent'){
			$appraisal = round(($appraisal/100)*$plafond,0);
			$bank['BankSetting']['appraisal'] = $appraisal;
		}

		if(!empty($administration) && $category_administration == 'percent'){
			$administration = round(($administration/100)*$plafond, 0);
			$bank['BankSetting']['administration'] = $administration;
		}

		if(!empty($credit_agreement) && $category_credit_agreement == 'percent'){
			$credit_agreement = round(($credit_agreement/100)*$plafond, 0);
			$bank['BankSetting']['credit_agreement'] = $credit_agreement;
		}
		
		return $bank;
	}

	function doBeforeSaveUserProfile($data){
		return array(
			'UserProfile' => $data['KprBankTransfer'],
		);
	}

	function doBeforeSaveActionKPR($data, $value){
		$kpr_bank_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'prime_kpr_bank_id');
		$document_status = $this->RmCommon->filterEmptyField($data, 'KprBank', 'document_status');

		$data = $this->RmCommon->_callUnset( array(
			'KprBankCreditAgreement' => array(
				'id',
				'kpr_bank_id',
			),
		), $data);

		$data = $this->controller->Kpr->KprBank->KprBankDate->getSlug($data, $value, array(
			'slug' => $document_status,
		), '/KprBank/KprBankDate/KprBankDate');

		return $data;
	}

	function sendEmailActionKPR($data, $value, $params = array()){
		$_site_name = Configure::read('__Site.site_name');
		$link = $this->RmCommon->filterEmptyField($params, 'link');
		$value['BankDomain'] = $this->RmCommon->filterEmptyField($value, 'Bank');

		if(!empty($data) && !empty($value)){
			## status yang dilakukan / dirubah oleh bank
			$document_status = Common::hashEmptyField($data, 'KprBank.document_status');
			$code = Common::hashEmptyField($data, 'KprBank.code');
			$bank_id = Common::hashEmptyField($data, 'KprBank.bank_id', null);
			$bank_name = Common::hashEmptyField($data, 'Bank.name');
			$agent_id = Common::hashEmptyField($value, 'Agent.id');
			$agent_name = Common::hashEmptyField($value, 'Agent.full_name');
			$agent_email = Common::hashEmptyField($value, 'Agent.email');
			$company_id = Common::hashEmptyField($value, 'Kpr.company_id', null);
			$value['status_KPR'] = $document_status;

			switch($document_status){
				case 'approved_bi_checking':
					$subject = sprintf(__('Selamat aplikasi KPR anda dengan kode %s telah lulus BI checking'), $code);
					$template = 'kpr_proposal_approved';
					$notif = sprintf(__('Aplikasi KPR anda dengan kode %s telah lulus BI checking'), $code);
					break;
				
				case 'rejected_bi_checking':
					$subject = sprintf(__('Mohon maaf aplikasi KPR anda dengan kode %s tidak lulus BI checking'), $code);
					$template = 'kpr_proposal_approved';
					$notif = sprintf(__('Aplikasi KPR anda dengan kode %s tidak lulus BI checking'), $code);
					break;

				case 'approved_verification':
					$subject = sprintf(__('Selamat aplikasi KPR dengan kode %s telah lulus verifikasi dokumen oleh %s'), $code, $bank_name);
					$template = 'kpr_proposal_approved';
					$notif = sprintf(__('Aplikasi KPR dengan kode %s telah lulus verifikasi dokumen oleh %s'), $code, $bank_name);
					break;

				case 'rejected_verification':
					$subject = sprintf(__('Mohon maaf aplikasi KPR dengan kode %s tidak lulus verifikasi dokumen oleh %s'), $code, $bank_name);
					$template = 'kpr_proposal_approved';
					$notif = sprintf(__('Aplikasi KPR dengan kode %s tidak lulus verifikasi dokumen oleh %s'), $code, $bank_name);
					break;

				case 'approved_admin' :
					$value['headerDomain'] = 'PRIMESYSTEM';
					$subject = sprintf(__('Admin %s telah melanjutkan atau meneruskan pengajuan KPR %s ke %s'), $_site_name, $code, $bank_name);
					$template = 'kpr_forward_admin';
					$notif = $subject;
					break;

				case 'approved_proposal' :
					$subject = sprintf(__('Pengajuan KPR Anda %s telah disetujui oleh %s'), $code, $bank_name);
					$template = 'kpr_proposal_approved';
					$notif = sprintf(__('Pengajuan KPR %s disetujui oleh %s'), $code, $bank_name);
					break;

				case 'proposal_without_comiission' :
					$subject = sprintf(__('Pengajuan KPR %s telah disetujui namun %s tidak menyediakan Provisi'), $code, $bank_name);
					$template = 'kpr_proposal_approved';
					$notif = $subject;
					break;

				case 'rejected_proposal' :
					$subject = sprintf(__('%s menolak Pengajuan KPR %s'), $bank_name, $code);
					$template = 'kpr_proposal_rejected';
					$notif = $subject;
					break;

				case 'approved_bank' :
					$subject = sprintf(__('Aplikasi KPR Anda %s telah disetujui oleh %s'), $code, $bank_name);
					$template = 'kpr_application_approved';
					$notif = sprintf(__('%s telah menyetujui aplikasi KPR %s'), $bank_name, $code);
					break;

				case 'rejected_bank' :
					$subject = sprintf(__('%s menolak Aplikasi KPR %s'), $bank_name, $code);
					$template = 'kpr_application_rejected'; // belum ada templatenya
					$notif = $subject;
					break;

				case 'approved_credit' :
					$subject = sprintf(__('%s telah menetapkan jadwal Akad Kredit %s'), $bank_name, $code);
					$template = 'kpr_akad_credit';
					$notif = $subject;
					break;

				case 'rejected_credit' :
					$subject = sprintf(__('%s menolak Akad Kredit %s'), $bank_name, $code);
					$template = 'rejected_akad_credit'; // belum ada templatenya
					$notif = $subject;
					break;
			}

			if( !empty($template) ) {
				if(!empty($agent_email)){
					$this->RmCommon->validateEmail(array(
						'SendEmail' => array(
							'bank_id' => $bank_id,
	                    	'subject' => $subject,
	                    	'template' => $template,
	                    	'data' => $value,
	                    	'include_role' => array(
	                    		'from_parent_id' => $company_id,
	                    		'role' => array('admin', 'principle'),
	                    	),
	                    ),
					));

					$this->RmCommon->validateEmail(array(
						'SendEmail' => array(
							'bank_id' => $bank_id,
	                    	'to_name' => $agent_name,
	                    	'to_email' => $agent_email,
	                    	'subject' => $subject,
	                    	'template' => $template,
	                    	'data' => $value,
	                    	// 'debug' => 'text',
	                    ),
					));
				}

				$this->RmCommon->_saveNotification(array(
					'Notification' => array(
                        'name' => $notif,
                        'link' => $link,
                        'include_role' => array(
                    		'from_parent_id' => $company_id,
                    		'role' => array('admin', 'principle'),
                    	),
                    ),
				));

				$this->RmCommon->_saveNotification(array(
					'Notification' => array(
                        'user_id' => $agent_id,
                        'name' => $notif,
                        'link' => $link,
                    ),
				));
			}

			if($document_status == 'approved_credit'){
				if( !empty($data['KprBankCreditAgreement']['sent_email']) ){
					$value['KprBank']['bank_kpr_id'] = $this->RmCommon->filterEmptyField($data, 'KprBank', 'id', null);
					$bank_id = $this->RmCommon->filterEmptyField($value, 'Bank', 'id', null);
					$to_name = $this->RmCommon->filterEmptyField( $data, 'KprBankCreditAgreement', 'staff_name');
					$staff_email = $this->RmCommon->filterEmptyField($data, 'KprBankCreditAgreement', 'staff_email');
					$bank_name = $this->RmCommon->filterEmptyField($value, 'Bank', 'name');
					$value['BankDomain'] =  $this->RmCommon->filterEmptyField($value, 'Bank');

					$value = $this->controller->User->Kpr->Bank->BankContact->getMerge($value, $bank_id);
					$this->RmCommon->validateEmail(array(
						'SendEmail' => array(
	                    	'to_name' => $to_name,
	                    	'to_email' => $staff_email,
	                    	'subject' => sprintf(__('%s telah menentukan jadwal Proses Akad Kredit'), $bank_name),
	                    	'template' => 'infomation_staff_bank',
	                    	'data' => $value,
	                    	// 'debug' => 'text',
                    	),
					));
				}
			}

		}
	}

	function sendEmailCommission($data, $value){
		$type = $this->RmCommon->filterEmptyField($data, 'KprBankCommission', 'type');
		$bank_name = $this->RmCommon->filterEmptyField($value, 'Bank', 'name');
		$kpr_bank_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'id');
		$bank_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'bank_id');
		$code = $this->RmCommon->filterEmptyField($data, 'KprBank', 'code');
		$kpr_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id');
		$agent_id = $this->RmCommon->filterEmptyField($value, 'Agent', 'id');

		$subject = sprintf(__('Pembayaran Provisi oleh %s atas KPR %s'), $bank_name, $code);
		$notif = $subject;
		$template = 'kpr_commission_agent_paid';
		$link = array(
            'controller' => 'kpr',
            'action' => 'application_detail',
            $kpr_bank_id,
            'admin' => true,
        );

		if($type == 'agent'){
			$send_name = $this->RmCommon->filterEmptyField($value, 'Agent', 'full_name');
			$send_email = $this->RmCommon->filterEmptyField($value, 'Agent', 'email');
			$this->RmCommon->_saveNotification(array(
				'Notification' => array(
                    'user_id' => $agent_id,
                    'name' => $notif,
                    'link' => $link,
                ),
			));
			$admin = true;
		}else{
			$send_name = Configure::read('__Site.site_name');
			$send_email = Configure::read('__Site.send_email_from');
		}

		if(!empty($admin)){
			$company_id = Common::hashEmptyField($value, 'Kpr.company_id');

			$this->RmCommon->_saveNotification(array(
				'Notification' => array(
                    'name' => $notif,
                    'link' => $link,
                    'include_role' => array(
                		'role' => array(
                			'admin',
                			'principle',
                		),
                		'from_parent_id' => $company_id,
                	),
                ),
			));

			$this->RmCommon->validateEmail(array(
				'SendEmail' => array(
                	'subject' => $subject,
                	'template' => $template,
                	'data' => array_merge($data, $value),
                	'include_role' => array(
                		'role' => array(
                			'admin',
                			'principle',
                		),
                		'from_parent_id' => $company_id,
                	),
                ),
			));
		}

		if( !empty($template) ) {
			$this->RmCommon->validateEmail(array(
				'SendEmail' => array(
					'bank_id' => $bank_id,
                	'to_name' => $send_name,
                	'to_email' => $send_email,
                	'subject' => $subject,
                	'template' => $template,
                	'data' => array_merge($data, $value),
                ),
			));
		}
	}

	function getProvision($bank, $property, $loan_price){
		$this->Bank = ClassRegistry::init('Bank');
		$bank_id = $this->RmCommon->filterEmptyField($bank, 'Bank', 'id');
		$property_type_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_type_id');
		$region_id = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'region_id');
		$city_id = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'city_id');

		$value = $this->Bank->BankCommissionSetting->getData('first', array(
			'conditions' => array(
				'BankCommissionSetting.bank_id' => $bank_id,
				'BankCommissionSetting.property_type_id' => $property_type_id,
				'OR' => array(
					array(
						'BankCommissionSetting.region_id' => $region_id,
						'BankCommissionSetting.city_id' => $city_id,
					),
					array(
						'BankCommissionSetting.region_id' => $region_id,
						'BankCommissionSetting.city_id' => null,
					),
					array(
						'BankCommissionSetting.region_id' => null,
						'BankCommissionSetting.city_id' => null,
					),
				),
			),
		), array(
			'company_id' => Configure::read('Principle.id'),
		));	
		$bank_setting_id = $this->RmCommon->filterEmptyField($value, 'BankCommissionSetting', 'id');
		$commission = $this->Bank->BankCommissionSetting->BankCommissionSettingLoan->getData('first',array(
			'conditions' => array(
				'BankCommissionSettingLoan.bank_setting_id' => $bank_setting_id,
				'BankCommissionSettingLoan.min_loan <=' => $loan_price,
			),
			'order' => array(
				'BankCommissionSettingLoan.min_loan' => 'DESC',
			),
		));

		if(!empty($commission)){
			$provision = $this->RmCommon->filterEmptyField($commission, 'BankCommissionSettingLoan', 'rate');
			$provision_rku = $this->RmCommon->filterEmptyField($commission, 'BankCommissionSettingLoan', 'rate_company');

			return array(
				'provision' => $provision,
				'provision_rku' => $provision_rku,
			);
		}else{
			$provision = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'provision');
			$provision_rku = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'provision_company');

			return array(
				'provision' => $provision,
				'provision_rku' => $provision_rku,
			);
		}
	}

	function viewBankCalculator($data = false, $bank, $property, $options = array()){
		$interest_rate_fix = $this->RmCommon->filterEmptyField($options, 'interest_rate_fix');
		$periode_installment = $this->RmCommon->filterEmptyField($options, 'periode_installment');

		$interest_rate_fix = !empty($interest_rate_fix)?$interest_rate_fix:Configure::read('__Site.interest_rate');
		$interest_rate_fix = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'interest_rate_fix', $interest_rate_fix);
		$interest_rate_fix = $this->RmCommon->filterEmptyField($data, 'Kpr', 'interest_rate', $interest_rate_fix);
		$interest_rate_float = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'interest_rate_float');
		$credit_fix = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'periode_fix');
		$credit_fix = $this->RmCommon->filterEmptyField($data, 'Kpr', 'credit_fix', $credit_fix);
		$periode_installment = !empty($periode_installment)?$periode_installment:Configure::read('__Site.kpr_credit_fix');
		$periode_installment = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'periode_installment', $periode_installment);



		$property_price = $this->RmCommon->filterEmptyField($property, 'Property', 'price');
		$property_price = $this->RmCommon->filterEmptyField($property, 'Property', 'price_measure', $property_price);
		$property_price = $this->RmCommon->filterEmptyField($data, 'Kpr', 'property_price', $property_price);
		$property_type_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_type_id');

		$dp = !empty($dp)?$dp:Configure::read('__Site.bunga_kpr');
		$dp = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'dp', $dp);
		$dp = $this->RmCommon->filterEmptyField($data, 'Kpr', 'persen_loan', $dp);

		$down_payment = $this->_callCalcDp($property_price, $dp);
		$down_payment = $this->RmCommon->filterEmptyField($data, 'Kpr', 'down_payment', $down_payment);
		$loan_price = @($property_price - $down_payment);

		$provision_arr = $this->getProvision($bank, $property, $loan_price);

		$appraisal = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'appraisal', Configure::read('__Site.KPR.appraisal'));
		$administration = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'administration', Configure::read('__Site.KPR.administration'));
		$insurance = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'insurance', Configure::read('__Site.KPR.insurance'));
		$provision = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'provision', Configure::read('__Site.KPR.provision'));
		$provision = $this->RmCommon->filterEmptyField($provision_arr, 'provision', false, $provision);

		$kpr_application = $this->RmCommon->filterEmptyField($data, 'Kpr', 'KprApplication');
		$kpr_application['bank_apply_category_id'] = $this->RmCommon->filterEmptyField($data, 'Kpr', 'bank_apply_category_id', $this->_callGetBankApplyCategory($property_type_id));

		$birthday = $this->RmCommon->getDate($this->RmCommon->filterEmptyField($kpr_application, 'birthday'), true);
		$kpr_application['birthday'] = !empty($birthday)?$birthday:NULL;
		$kpr_application['loan_price'] = $loan_price;

		return array(
			'interest_rate' => $interest_rate_fix,
			'floating_rate' => $interest_rate_float,
			'credit_fix' => $credit_fix,
			'credit_total' => $periode_installment,
			'appraisal' => $appraisal,
			'administration' => $administration,
			'provision' => $provision,
			'insurance' => $insurance,
			'persen_loan' => $dp,
			'property_price' => $property_price,
			'down_payment' => $down_payment,
			'loan_price' => $loan_price,
			'KprApplication' => $kpr_application,
		);
	}

	function beforeSaveFrontEnd($data, $mls_id = false, $options = array()){
		$save_path = Configure::read('__Site.document_folder');
		$company_id = Configure::read('Principle.id');
		$typeCommissions = Configure::read('__Site.typeCommission');

		$kpr = $this->RmCommon->filterEmptyField( $data, 'Kpr');
		$data['Kpr'] = $this->RmImage->_uploadPhoto( $kpr, 'KprApplication', 'ktp_file', $save_path );
		$data['Kpr'] = $this->RmImage->_uploadPhoto( $data['Kpr'], 'KprApplication', 'income_file', $save_path );

		$bank = $this->RmCommon->filterEmptyField($options, 'bank');
		$bank_id = $this->RmCommon->filterEmptyField($bank, 'Bank', 'id');
		$work_day = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'work_day');
		$property = $this->RmCommon->filterEmptyField($options, 'property');
		$property_type_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_type_id', null);
		$region_id = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'region_id', null);
		$city_id = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'city_id', null);
		$property_id = $this->RmCommon->filterEmptyField($property, 'Property', 'id', null);
		$keyword = $this->RmCommon->filterEmptyField($property, 'Property', 'keyword');
		$agent_id = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id', null);
		$kpr_application = $this->RmCommon->filterEmptyField($data, 'Kpr', 'KprApplication');

		$address = $this->RmCommon->filterEmptyField($kpr_application, 'address');
		$address_2 = $this->RmCommon->filterEmptyField($kpr_application, 'address_2');
		$same_as_address_ktp = $this->RmCommon->filterEmptyField($kpr_application, 'same_as_address_ktp');

		$data['Kpr']['KprApplication']['address_2'] = !empty($same_as_address_ktp)?$address:$address_2;
		$data['Kpr'] = $this->RmCommon->dataConverter($data['Kpr'], array(
			'date' => array(
				'KprApplication' => array(
					'birthday',
				),	
			),
			'price' => array(
				'KprApplication' => array(
					'down_payment',
					'income',
					'household_fee',
					'other_installment',
				),
			),
		));

		$data = $this->RmCommon->dataConverter($data, array(
			'price' => array(
				'Kpr'=> array(
					'loan_price',
					'property_price',
					'down_payment',
				),
			),
		));
		$email = $this->RmCommon->filterEmptyField($kpr_application, 'email');
		$no_hp = $this->RmCommon->filterEmptyField($kpr_application, 'no_hp');
		$name = $this->RmUser->_getUserFullName($data['Kpr'], 'reverse', 'KprApplication', 'name');

		$data['Kpr']['KprApplication']['full_name'] = !empty($name)?implode(' ', $name):'-';
		$data['Kpr']['KprApplication']['first_name'] = $this->RmCommon->filterEmptyField($name, 'first_name', false, '');
		$data['Kpr']['KprApplication']['last_name'] = $this->RmCommon->filterEmptyField($name, 'last_name', false, '');

		$data = $this->RmUser->userRegister($data['Kpr'], $email, array(
			'first_name' => $this->RmCommon->filterEmptyField($name, 'first_name', false, ''),
			'last_name' => $this->RmCommon->filterEmptyField($name, 'last_name', false, ''),
			'no_hp' => $no_hp,
		));
		$property_price = $this->RmCommon->filterEmptyField($data, 'Kpr', 'property_price');
		$down_payment = $this->RmCommon->filterEmptyField($data, 'Kpr', 'down_payment');
		$persen_loan = $this->RmCommon->filterEmptyField($data, 'Kpr', 'persen_loan');
		$credit_total = $this->RmCommon->filterEmptyField($data, 'Kpr', 'credit_total');
		$job_type_id = $this->RmCommon->filterEmptyField($data['Kpr'], 'KprApplication', 'job_type_id', null);
		$gender_id = $this->RmCommon->filterEmptyField($data['Kpr'], 'KprApplication', 'gender_id', null);
		$birthplace = $this->RmCommon->filterEmptyField($data['Kpr'], 'KprApplication', 'birthplace');
		$birthday = $this->RmCommon->filterEmptyField($data['Kpr'], 'KprApplication', 'birthday');
		$ktp = $this->RmCommon->filterEmptyField($data['Kpr'], 'KprApplication', 'ktp');

		$loan_price = $this->calcLoanFromDp($property_price, $down_payment);

		$data['Kpr']['bank_apply_category_id'] = $this->RmCommon->filterEmptyField($kpr_application, 'bank_apply_category_id', false, null);
		$data['Kpr']['client_email'] = $email;
		$data['Kpr']['company_id'] = $company_id;
		$data['Kpr']['client_name'] = implode(' ',$name);
		$data['Kpr']['client_hp'] = $no_hp;
		$data['Kpr']['client_job_type_id'] = $job_type_id;
		$data['Kpr']['gender_id'] = $gender_id;
		$data['Kpr']['address'] = $address;
		$data['Kpr']['birthplace'] = $birthplace;
		$data['Kpr']['birthday'] = $birthday;
		$data['Kpr']['ktp'] = $ktp;
		$data['Kpr']['property_price'] = $property_price;
		$data['Kpr']['credit_total'] = $credit_total;
		$data['Kpr']['down_payment'] = $down_payment;
		$data['Kpr']['loan_price'] = $this->RmCommon->filterEmptyField($data, 'Kpr', 'loan_price', $loan_price);
		$data['Kpr']['dp'] = $persen_loan;

		if( !empty($mls_id) ) {
			$data['Kpr']['property_id'] = $property_id;
			$data['Kpr']['mls_id'] = $mls_id;
			$data['Kpr']['agent_id'] = $agent_id;
			$data['Kpr']['keyword'] = sprintf('%s, %s, %s', $keyword, implode(' ',$name), $email);
		}

		$data['KprBank']['bank_id'] = !empty($bank_id)?$bank_id:null;
		$data['KprBank']['application_status'] = 'completed';
		$data['KprBank']['work_day'] = $work_day;
		$data['KprBank']['from_kpr'] = "frontend";

		$file_ktp = $this->RmCommon->filterEmptyField($data['Kpr'], 'KprApplication', 'ktp_file');
		if(!empty($file_ktp)){
			$file_name = $this->RmCommon->filterEmptyField($data['Kpr'], 'KprApplication', 'ktp_file_name');
			$data['CrmProjectDocument'][]['CrmProjectDocument'] = array(
				'file' => $file_ktp,
				'document_category_id' => 2,
				'document_type' => 'kpr_application',
				'save_path' => $save_path,
				'name' => $file_name,
			);
		}

		$income_file = $this->RmCommon->filterEmptyField($data['Kpr'], 'KprApplication', 'income_file');
		if(!empty($income_file)){
			$file_name = $this->RmCommon->filterEmptyField($data['Kpr'], 'KprApplication', 'income_file_name');
			$data['CrmProjectDocument'][]['CrmProjectDocument'] = array(
				'file' => $income_file,
				'document_category_id' => 4,
				'document_type' => 'kpr_application',
				'save_path' => $save_path,
				'name' => $file_name,
			);
		}

		$data['Kpr'] = $this->RmCommon->_callUnset(array(
			'KprApplication' => array(
				'bank_apply_category_id',
				'down_payment',
				'credit_total',
			),
		), $data['Kpr']);

		$commission = $this->getCommissionKPR( array(	
			'type_kpr' => 'frontend',
			'property_price' => $property_price,
			'bank_id' => $bank_id,
			'property_type_id' => $property_type_id,
			'region_id' => $region_id,
			'city_id' => $city_id,
		), $down_payment);

		$percent_agent = $this->RmCommon->filterEmptyField($result,'percent_agent');
		$percent_company = $this->RmCommon->filterEmptyField($result,'percent_company');
		$commission_agent 	= $this->_GenerateCommission($percent_agent, $loan_price);
		$commission_company = $this->_GenerateCommission($percent_company, $loan_price);
		$region_id = $this->RmCommon->filterEmptyField($result, 'region_id', false, null);
		$region_name = $this->RmCommon->filterEmptyField($result, 'region_name');
		$city_id = $this->RmCommon->filterEmptyField($result, 'city_id', false, null);
		$city_name = $this->RmCommon->filterEmptyField($result, 'city_name');
		$note = $this->RmCommon->filterEmptyField($result, 'note');

		$typeCommissions = $this->setCommission($typeCommissions, array(
			'percent' => array(
				$percent_agent,
				$percent_company,
			),
			'commission' => array(
				$commission_agent, 
				$commission_company,
			),
		));

		$data['KprBank']['KprBankInstallment'][] = $this->setKprBankInstallment($bank, array(
			'down_payment' => $down_payment,
			'property_price' => $property_price,
			'credit_total' => $credit_total,
		));

		$data['KprBank']['KprBankDate'][] = $this->getKprBankDate('process', array(
			'note' => __('Pengajuan KPR melalui user/ frontend prime'),
		));

		if(!empty($typeCommissions)){
			foreach($typeCommissions AS $i => $typeCommission){
				$type = $this->RmCommon->filterEmptyField($typeCommission, 'type');
				$percent = $this->RmCommon->filterEmptyField($typeCommission, 'percent');
				$commission = $this->RmCommon->filterEmptyField($typeCommission, 'commission');

				if(!empty($percent) && !empty($commission)){
					$data['KprBank']['KprBankInstallment'][0]['KprBankCommission'][] = $this->setKprBankCommission( $type, array(
						'percent' => $percent,
						'commission' => $commission,
						'region_id' => $region_id,
						'region_name' => $region_name,
						'city_id' => $city_id,
						'city_name' => $city_name,
						'note' => $note,
					));
				}
				
			}
		}
		return $this->filterDataKprFrontEnd($data);
	}

	function filterDataKprFrontEnd($data){
		$result = array();
		$userProfile = $this->RmCommon->filterEmptyField($data, 'UserProfile');

		if(!empty($data)){
			$result['User'] = $this->RmCommon->filterEmptyField($data, 'User');

			if(!empty($userProfile)){
				$result['UserProfile'] = $this->RmCommon->filterEmptyField($data, 'UserProfile');
			}

			$result['Kpr'] = $this->RmCommon->filterEmptyField($data, 'Kpr');
			$result['Kpr']['KprBank'][] = $this->RmCommon->filterEmptyField($data, 'KprBank');
			$result['CrmProjectDocument'] = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocument');
		}	
		return $result;
	}

	function fill_form(){
		$user_data = Configure::read('User.data');

		$parent_id = $this->RmCommon->filterEmptyField($user_data, 'parent_id', false, null);
		$user_data = $this->controller->User->UserCompany->getMerge($user_data, $parent_id);

		$address = $this->RmCommon->filterEmptyField($user_data, 'UserProfile', 'address');
		$address2 = $this->RmCommon->filterEmptyField($user_data, 'UserProfile', 'address2');
		$address = !empty($address2)?PHP_EOL.$address2:$address;

		$data['Kpr']['KprApplication'] = array(
			'name' => $this->RmCommon->filterEmptyField($user_data, 'full_name'),
			'gender_id' => $this->RmCommon->filterEmptyField($user_data, 'gender_id', false, null),
			'birthday' => $this->RmCommon->filterEmptyField($user_data, 'UserProfile', 'birthday'),
			'email' => $this->RmCommon->filterEmptyField($user_data, 'email'),
			'address_2' => $address,
			'region_id' => $this->RmCommon->filterEmptyField($user_data, 'UserProfile', 'region_id', null),
			'city_id' => $this->RmCommon->filterEmptyField($user_data, 'UserProfile', 'city_id', null),
			'subarea_id' => $this->RmCommon->filterEmptyField($user_data, 'UserProfile', 'subarea_id', null),
			'zip' => $this->RmCommon->filterEmptyField($user_data, 'UserProfile', 'zip'),
			'phone' => $this->RmCommon->filterEmptyField($user_data, 'UserProfile', 'phone'),
			'no_hp' => $this->RmCommon->filterEmptyField($user_data, 'UserProfile', 'no_hp'),
			'no_hp_2' => $this->RmCommon->filterEmptyField($user_data, 'UserProfile', 'no_hp_2'),
			'company' => $this->RmCommon->filterEmptyField($user_data, 'UserCompany', 'name')
		);
		return $data;
	}

	function beforeSaveKprLog($value){
		if(!empty($value)){
			$user_id = Configure::read('User.id');
			$company_id = Configure::read('Principle.id');
			$down_payment = $this->RmCommon->filterEmptyField($value, 'down_payment');
			$property_price = $this->RmCommon->filterEmptyField($value, 'property_price');
			$credit_total = $this->RmCommon->filterEmptyField($value, 'credit_total');

			$mls_id = $this->RmCommon->filterEmptyField($value, 'mls_id');
			$property_id = $this->RmCommon->filterEmptyField($value, 'property_id', false, null);
			$property = $this->User->Property->getMerge(array(), $property_id);
			$agent_id = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id', null);
			$keyword = $this->RmCommon->filterEmptyField($property, 'Property', 'keyword');
			$property_type_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_type_id', null);
			$bank_apply_category_id = ($property_type_id == 3)?2:1;

			## GET DATA USER LOGIN
			$user = $this->User->getMerge(array(), $user_id, true);
			$client_email = $this->RmCommon->filterEmptyField($user, 'User', 'email');
			$client_name = $this->RmCommon->filterEmptyField($user, 'User', 'full_name');
			$client_hp = $this->RmCommon->filterEmptyField($user, 'UserProfile', 'no_hp');
			$job_type_id = $this->RmCommon->filterEmptyField($user, 'UserProfile', 'job_type');
			$keyword = sprintf('%s, %s, %s', $keyword, $client_name, $client_email);
			$property_price = $this->RmCommon->filterEmptyField($value, 'property_price');
			$credit_total = $this->RmCommon->filterEmptyField($value, 'credit_total');
			$down_payment = $this->RmCommon->filterEmptyField($value, 'down_payment');
			$dp = $this->RmCommon->filterEmptyField($value, 'persen_loan');
			$form_kpr = 'frontend';
			$type = 'logkpr';
			$type_log = 'app-calculate';
			$data['Kpr'] = array(
				'type' => $type,
				'type_log' => $type_log,
				'bank_apply_category_id' => $bank_apply_category_id,
				'mls_id' => $mls_id,
				'property_id' => $property_id,
				'agent_id' => $agent_id,
				'user_id' => $user_id,
				'company_id' => $company_id,
				'client_email' => $client_email,
				'client_name' => $client_name,
				'client_hp' => $client_hp,
				'client_job_type_id' => $job_type_id,
				'keyword' => $keyword,
				'property_price' => $property_price,
				'credit_total' => $credit_total,
				'dp' => $dp,
				'down_payment' => $down_payment,
				'from_kpr' => $form_kpr,
			);
			## GET DATA BANK
			$bank_id = $this->RmCommon->filterEmptyField($value, 'Bank', 'id', null);
			$bank_temp = $this->RmCommon->filterEmptyField($value, 'Bank');
			$bank_setting = $this->RmCommon->filterEmptyField($value, 'BankSetting');

			if(!empty($bank_temp) && !empty($bank_setting)){
				$bank = array(
					'Bank' => $bank_temp,
					'BankSetting' => $bank_setting,
				);
			}

			$data['KprBank'] = array(
				'type' => 'logkpr',
				'type_log' => 'app-calculate',
				'bank_id' => $bank_id,
				'from_kpr' => 'frontend',
			);

			$data['KprBankInstallment'] = $this->setKprBankInstallment($bank, array(
				'down_payment' => $down_payment,
				'property_price' => $property_price,
				'credit_total' => $credit_total,
			));
			$data['KprBankInstallment']['type'] = 'logkpr';
			$data['KprBankInstallment']['type_log'] = 'app-calculate';
		}
		return $data;
	}

	function setKprSold($value, $data){
		$client_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'user_id', null);
		$client_name = $this->RmCommon->filterEmptyField($value, 'Kpr', 'client_name');
		$agent_email = $this->RmCommon->filterEmptyField($value, 'Agent', 'email');
		$property_id = $this->RmCommon->filterEmptyField($value, 'Property', 'id', null);
		$property_action_id = $this->RmCommon->filterEmptyField($value, 'Property', 'property_action_id', null);
		$price_sold = $this->RmCommon->filterEmptyField($value, 'KprBankInstallment', 'property_price');
		$sold_date = $this->RmCommon->filterEmptyField($value, 'Kpr', 'sold_date');
		$end_date = $this->RmCommon->filterEmptyField($data, 'KprBankCreditAgreement', 'action_date');

		$value['PropertySold'] = array(
			'client_id' => $client_id,
			'client_name' => $client_name,
			'sold_by_name' => $agent_email,
			'property_id' => $property_id,
			'property_action_id' => $property_action_id,
			'currency_id' => 1,
			'price_sold' => $price_sold,
			'sold_date' => $sold_date,
			'end_date' => $end_date,
		);

		return $value;
	}

	function transferAndCreditAggrement($data, $kpr){
    	$kpt_bank_date = $this->RmCommon->filterEmptyField($data, 'KprBankDate');
    	$slug_arr = Set::classicExtract($kpt_bank_date, '{n}.KprBankDate.slug');
    	$slug_arr = is_array($slug_arr)?$slug_arr:array();
    	$agent_id = $this->RmCommon->filterEmptyField($kpr, 'agent_id', false, null);
		$Agent = $this->controller->User->getMerge(array(), $agent_id, true);

    	$rekening_nama_akun = $this->RmCommon->filterEmptyField($Agent, 'UserProfile', 'rekening_nama_akun');
    	$no_rekening = $this->RmCommon->filterEmptyField($Agent, 'UserProfile', 'no_rekening');
    	$rekening_bank = $this->RmCommon->filterEmptyField($Agent, 'UserProfile', 'rekening_bank');
    	$no_npwp = $this->RmCommon->filterEmptyField($Agent, 'UserProfile', 'no_npwp');

    	$contact_name = $this->RmCommon->filterEmptyField($data, 'KprBank', 'contact_name');
    	$contact_bank = $this->RmCommon->filterEmptyField($data, 'KprBank', 'contact_bank');
    	$contact_email = $this->RmCommon->filterEmptyField($data, 'KprBank', 'contact_email');
    	$description_akad = $this->RmCommon->filterEmptyField($data, 'KprBank', 'description_akad');
    	$process_akad_date = $this->RmCommon->filterEmptyField($data, 'KprBank', 'process_akad_date');
    	$send_email_staff = $this->RmCommon->filterEmptyField($data, 'KprBank', 'send_email_staff');


    	if(in_array('credit_process', $slug_arr)){
    		$data['KprBankTransfer'] = array(
    			'agent_id' => $agent_id,
    			'name_account' => $rekening_nama_akun,
    			'no_account' => $no_rekening,
    			'bank_name' => $rekening_bank,
    			'no_npwp' => $no_npwp,
    		);
    	}

    	if(in_array('approved_credit', $slug_arr)){
    		$data['KprBankCreditAgreement'] = array(
    			'staff_name' => $contact_name,
    			'staff_phone' => $contact_bank,
    			'staff_email' => $contact_email,
    			'note' => $description_akad,
    			'action_date' => $process_akad_date,
    			'sent_email' => $send_email_staff,
    		);
    	}
    	return $data;
    }

	function buildBankDate( $slug, $kpr_bank_id, $action_date = false, $note = false){
    	return array(
    		'KprBankDate' => array(
    			'kpr_bank_id' => $kpr_bank_id,
    			'slug' => $slug,
    			'action_date' => $action_date,
    			'note' => $note,
    		),
    	);
    }

	function mergeKprBankDate($data, $kpr_bank_id, $modelName, $slugFields = array()){
    	$result = array();
    	if(!empty($data) && !empty($slugFields)){
    		foreach($slugFields AS $slug => $field){
    			$enum = $this->RmCommon->filterEmptyField($field, 'enum');
    			$date = $this->RmCommon->filterEmptyField($field, 'date');
    			$note = $this->RmCommon->filterEmptyField($field, 'note');
    			$field = $this->RmCommon->filterEmptyField($field, 'field');

    			if(!empty($enum)){
    				$field = $this->RmCommon->filterEmptyField($enum, 'field');
    				$option = $this->RmCommon->filterEmptyField($enum, 'option');
    			}

    			$value = $this->RmCommon->filterEmptyField($data, $modelName, $field);
    			$action_date = $this->RmCommon->filterEmptyField($data, $modelName, $date);

    			if(!empty($option) && ($value == $option)){
    				$value = TRUE;
    			}

    			if(!empty($value)){
    				$result[] = $this->buildBankDate($slug, $kpr_bank_id, $action_date, $note);
    				$result['document_status'] = $slug;
    			}else{
    				if($field == 'pending'){
    					$result[] = $this->buildBankDate($slug, $kpr_bank_id, $action_date, $note);
    				}
    			}
    		}
    		if(empty($result['document_status'])){
				$result['document_status'] = 'pending';
    		}
    	}
    	return $result;
    }

	function G_kpr_bank_date($value){
    	$code = $this->RmCommon->filterEmptyField($value, 'KprBank', 'code');
    	$kpr_bank_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'id');
    	return $this->mergeKprBankDate($value, $kpr_bank_id, 'KprBank', array(
    		'pending' => array(
				'field' => 'pending',
				'date' => 'created',
				'note' => __('Berhasil mengajukan KPR'),
    		),
    		'approved_admin' => array(
				'field' => 'approved_admin',
				'date' => 'approved_admin_date',
				'note' => __('Disetujui oleh admin rumahku untuk kode KPR %s', $code),
    		),
    		'rejected_admin' => array(
    			'field' => 'rejected_admin',
    			'date' => 'rejected_admin_date',
				'note' => __('Ditolak oleh admin rumahku untuk kode KPR %s', $code),
    		),
    		'rejected_proposal' => array(
    			'field' => 'reject_proposal',
    			'date' => 'proposal_date',
				'note' => __('Ditolak referral oleh bank untuk kode KPR %s', $code),
    		),
    		'approved_proposal' => array(
    			'field' => 'aprove_proposal',
    			'date' => 'proposal_date',
				'note' => __('Disetujui referral oleh bank untuk kode KPR %s', $code),
    		),
    		'proposal_without_comiission' => array(
    			'field' => 'aprove_proposal',
    			'field2' => 'rejected_commission',
    			'date' => 'proposal_date',
				'note' => __('Disetujui referral tanpa Provisi bank untuk kode KPR %s', $code),
    		),
    		'approved_bank' => array(
    			'field' => 'approved',
    			'date' => 'approved_date',
				'note' => __('Disetujui aplikasi oleh bank untuk kode KPR %s', $code),
    		),
    		'rejected_bank' => array(
    			'field' => 'rejected',
    			'date' => 'rejected_date',
				'note' => __('Ditolak aplikasi oleh bank untuk kode KPR %s', $code),
    		),
    		'credit_process' => array(
    			'field' => 'assign_project',
    			'date' => 'assign_project_date',
				'note' => __('Disetujui aplikasi oleh agen untuk kode KPR %s', $code),
    		),
    		'cancel' => array(
    			'field' => 'cancel_project',
    			'date' => 'cancel_project_date',
    			'note' => __('Dibatalkan aplikasi oleh agen untuk kode KPR %s', $code),
    		),
    		'rejected_credit' => array(
    			'enum' => array(
    				'field' => 'document_status_application',
    				'option' => 'cancel_credit'
    			),
    			'date' => 'process_akad_date',
    			'note' => __('Menolak akad kredit oleh bank untuk kode KPR %s', $code),
    		),
    		'approved_credit' => array(
    			'enum' => array(
    				'field' => 'document_status_application',
    				'option' => 'akad_credit'
    			),
    			'date' => 'process_akad_date',
    			'note' => __('Menyetujui akad kredit oleh bank untuk kode KPR %s', $code),
    		),
    	));
    }

	function setExtractKPR($value_arr){
    	if(in_array('approved', $value_arr)){
    		$value = 'approved';
    	}elseif (in_array('pending', $value_arr)){
    		$value = 'pending';
    	}else{
    		$value = 'no_provision';
    	}
    	return $value;
	}

	function checkPaidCommission($values, $modelName, $options = array()){
	    $com = $this->RmCommon->filterEmptyField($options, 'com');
	    $buildSave = $this->RmCommon->filterEmptyField($options, 'buildSave');
	    $status = $this->RmCommon->filterEmptyField($options, 'status');
	    $alias = $this->RmCommon->filterEmptyField($options, 'alias', false, 'KprCommission');

    	$unpaid = array(
    		'unpaid_agent' => 'no_provision',
    		'unpaid_rumahku' => 'no_provision',
    	);

    	$commission = array(); 

    	if(!empty($values)){
    		foreach($values AS $key => $value){
    			$type_komisi = $this->RmCommon->filterEmptyField($value, $modelName, 'type_komisi');
    			$rate_komisi = $this->RmCommon->filterEmptyField($value, $modelName, 'rate_komisi');
    			$commission = $this->RmCommon->filterEmptyField($value, $modelName, 'commission');

    			$type = ($type_komisi == 'agen') ? 'agent' : 'rumahku';
    			$type_commission = ($type_komisi == 'agen') ? '' : '_rumahku';
    			$paid = $this->RmCommon->filterEmptyField($value, $modelName, 'paid_fee_approved');
    			$status_paid = !empty($paid) ? 'approved' : 'pending';
    			$unpaid[sprintf('unpaid_%s', $type)] = $status_paid;

    			if(!empty($com)){
    				$unpaid[sprintf('provision%s', $type_commission)] = $rate_komisi;
    				$unpaid[sprintf('commission%s', $type_commission)] = $commission;
    			}

    			if(!empty($buildSave)){
    				$keterangan = $this->RmCommon->filterEmptyField($value, $modelName, 'keterangan');
    				$region_name = $this->RmCommon->filterEmptyField($value, $modelName, 'region_name');
    				$city_name = $this->RmCommon->filterEmptyField($value, $modelName, 'city_name');
    				$paid_fee_approved = $this->RmCommon->filterEmptyField($value, $modelName, 'paid_fee_approved');
    				$approve_date = $this->RmCommon->filterEmptyField($value, $modelName, 'approve_date');
    				$paid_fee_rejected = $this->RmCommon->filterEmptyField($value, $modelName, 'paid_fee_rejected');
    				$cancel_date = $this->RmCommon->filterEmptyField($value, $modelName, 'cancel_date');
    				$note_reason = $this->RmCommon->filterEmptyField($value, $modelName, 'note_reason');

    				if(!empty($paid_fee_approved)){
    					$paid_status = 'approved';
    					$action_date = $approve_date;

    				}else if($paid_fee_rejected){
    					$paid_status = 'rejected';
    					$action_date = $cancel_date;

    				}else{
    					$paid_status = 'pending';
    					$action_date = null;
    				}

    				$unpaid[$alias][$key] = array(
    					$alias => array(
	    					'type' => $type,
	    					'percent' => $rate_komisi,
	    					'value' => $commission,
	    					'note' => $keterangan,
	    					'region_name' => $region_name,
	    					'city_name' => $city_name,
	    					'paid_status' => $paid_status,
	    					'action_date' => $action_date,
	    					'status_confirm' => $status,
    					)
    				);
    			}
    		}
    	}

    	return $unpaid;
    }

	function installmentKPR($value, $modelName, $modelCommission, $status = false){
    	$credit_fix = $this->RmCommon->filterEmptyField($value, $modelName, 'periode_fix');
    	$credit_total = $this->RmCommon->filterEmptyField($value, $modelName, 'credit_total');
    	$loan_price = $this->RmCommon->filterEmptyField($value, $modelName, 'loan_price');
    	$interest_rate = $this->RmCommon->filterEmptyField($value, $modelName, 'interest_rate_fix');

    	$total_first_credit = $this->creditFix($loan_price, $interest_rate, $credit_total);
    	$commission = $this->RmCommon->filterEmptyField($value, $modelCommission);
    	$provision_arr = $this->checkPaidCommission($commission, $modelCommission, array(
    		'com' => true,
    		'buildSave' => true,
    		'status' => $status,
    		'alias' => 'KprBankCommission',
    	));
    	$unpaid_agent = $this->RmCommon->filterEmptyField($provision_arr, 'unpaid_agent');
    	$unpaid_rumahku = $this->RmCommon->filterEmptyField($provision_arr, 'unpaid_rumahku');
    	$provision = $this->RmCommon->filterEmptyField($provision_arr, 'provision');
    	$commission = $this->RmCommon->filterEmptyField($provision_arr, 'commission');
    	$provision_rumahku = $this->RmCommon->filterEmptyField($provision_arr, 'provision_rumahku');
    	$commission_rumahku = $this->RmCommon->filterEmptyField($provision_arr, 'commission_rumahku');
    	$kpr_bank_commission = $this->RmCommon->filterEmptyField($provision_arr, 'KprBankCommission');

    	$loan_plafond = $this->RmCommon->filterEmptyField($value, $modelName, 'loan_plafond', 0);
    	$down_payment = $this->RmCommon->filterEmptyField($value, $modelName, 'down_payment', 0);

    	if(!empty($loan_plafond)){
    		$fieldLoanPrice = 'loan_plafond';
    	}else{
    		$fieldLoanPrice = 'loan_price';
    		$loan_plafond = $this->RmCommon->filterEmptyField($value, $modelName, 'loan_price', 0);
    	}
    	$property_price = $loan_plafond + $down_payment;
    	$params = array(
    		'property_price' => $property_price,
    		'loan_price' => $loan_plafond,
    	);

    	$data['KprBankInstallment'] = array(
    		'property_price' => $property_price,
    		'down_payment' => $down_payment,
    		$fieldLoanPrice => $loan_plafond,
    		'credit_total' => $credit_total,
    		'interest_rate_fix' => $interest_rate,
    		'total_first_credit' => $total_first_credit,
    		'interest_rate_float' => $this->RmCommon->filterEmptyField($value, $modelName, 'interest_rate_float'),
    		'periode_fix' => $credit_fix,
    		'periode_installment' => $credit_total,
    		'provision' => $provision,
    		'commission' => $commission,
    		'provision_rumahku' => $provision_rumahku,
    		'commission_rumahku' => $commission_rumahku,
    		'administration' => $this->RmCommon->filterEmptyField($value, $modelName, 'administration', 0),
    		'administration_percent' => $this->migrateGetPercent($value, $modelName, 'administration', $params),
    		'administration_params' => $this->RmCommon->filterEmptyField($value, 'BankSetting', 'param_administration'),
    		'appraisal' => $this->RmCommon->filterEmptyField($value, $modelName, 'appraisal', 0),
    		'appraisal_percent' => $this->migrateGetPercent($value, $modelName, 'appraisal', $params),
    		'param_appraisal' => $this->RmCommon->filterEmptyField($value, 'BankSetting', 'param_appraisal'),
    		'insurance' => $this->RmCommon->filterEmptyField($value, $modelName, 'insurance', 0),
    		'insurance_percent' => $this->migrateGetPercent($value, $modelName, 'insurance', $params),
    		'insurance_params' => $this->RmCommon->filterEmptyField($value, 'BankSetting', 'param_insurance'),
    		'sale_purchase_certificate' => $this->RmCommon->filterEmptyField($value, $modelName, 'sale_purchase_certificate', 0),
    		'sale_purchase_certificate_percent' => $this->migrateGetPercent($value, $modelName, 'sale_purchase_certificate', $params),
    		'sale_purchase_certificate_params' => $this->RmCommon->filterEmptyField($value, 'BankSetting', 'param_sale_purchase_certificate'),
    		'transfer_title_charge' => $this->RmCommon->filterEmptyField($value, $modelName, 'transfer_title_charge', 0),
    		'transfer_title_charge_percent' => $this->migrateGetPercent($value, $modelName, 'transfer_title_charge', $params),
    		'transfer_title_charge_params' => $this->RmCommon->filterEmptyField($value, 'BankSetting', 'param_transfer_title_charge'),
    		'credit_agreement' => $this->RmCommon->filterEmptyField($value, $modelName, 'credit_agreement', 0),
    		'credit_agreement_percent' => $this->migrateGetPercent($value, $modelName, 'credit_agreement', $params),
    		'credit_agreement_params' => $this->RmCommon->filterEmptyField($value, 'BankSetting', 'param_credit_agreement'),
    		'letter_mortgage' => $this->RmCommon->filterEmptyField($value, $modelName, 'letter_mortgage', 0),
    		'letter_mortgage_percent' => $this->migrateGetPercent($value, $modelName, 'letter_mortgage', $params),
    		'letter_mortgage_params' => $this->RmCommon->filterEmptyField($value, 'BankSetting', 'param_letter_mortgage'),
    		'imposition_act_mortgage' => $this->RmCommon->filterEmptyField($value, $modelName, 'imposition_act_mortgage', 0),
    		'imposition_act_mortgage_percent' => $this->migrateGetPercent($value, $modelName, 'imposition_act_mortgage', $params),
    		'imposition_act_mortgage_params' => $this->RmCommon->filterEmptyField($value, 'BankSetting', 'param_imposition_act_mortgage'),
    		'mortgage' => $this->RmCommon->filterEmptyField($value, $modelName, 'mortgage', 0),
    		'mortgage_percent' => $this->migrateGetPercent($value, $modelName, 'mortgage', $params),
    		'mortgage_params' => $this->RmCommon->filterEmptyField($value, 'BankSetting', 'param_mortgage'),
    		'other_certificate' => $this->RmCommon->filterEmptyField($value, $modelName, 'other_certificate', 0),
    		'other_certificate_percent' => $this->migrateGetPercent($value, $modelName, 'other_certificate', $params),
    		'other_certificate_params' => $this->RmCommon->filterEmptyField($value, 'BankSetting', 'param_other_certificate'),
    		'status_confirm' => $status,
    		'unpaid_agent' => $unpaid_agent,
    		'unpaid_rumahku' => $unpaid_rumahku,

    	);

		if(!empty($kpr_bank_commission)){
			$data = array_merge($data, array(
				'KprBankCommission' => $kpr_bank_commission,
			));
		}
		return $data;
    }

    function migrateGetPercent($value, $modelName, $field, $params = array(), $empty = 0){
    	$property_price = $this->RmCommon->filterEmptyField($params, 'property_price');
    	$loan_price = $this->RmCommon->filterEmptyField($params, 'loan_price');

    	$val = $this->RmCommon->filterEmptyField($value, $modelName, $field);
    	$parameter = $this->RmCommon->filterEmptyField($value, 'BankSetting', sprintf('param_%s', $field));

    	if($parameter == 'price' && isset($val)){
    		return ($val/$property_price)*100;
    	}else{
    		return ($val/$loan_price)*100;
    	}
    }

	function G_kpr_bank($kpr_bank, $kpr){
		if(!empty($kpr_bank) && !empty($kpr)){
			$application_form = $this->RmCommon->filterEmptyField($kpr, 'application_form');
			$application_snyc = $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'application_snyc');
			$resend = $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'resend');
			$application_status = $this->getApplicationStatus($application_form, $application_snyc, $resend);
			return array(
				'id' => $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'id', null),
				'type' => $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'type'),
				'type_log' => $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'type_log'),
				'kpr_id' => $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'kpr_application_id', null),
				'bank_id' => $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'bank_id', null),
				'code' => $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'code'),
				'application_status' => $application_status,
				'work_day' => $this->RmCommon->filterEmptyField($kpr_bank, 'BankSetting', 'work_day'),
				'snyc' => $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'snyc'),
				'application_snyc' => $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'application_snyc'),
				'snyc' => $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'snyc'),
				'status' => $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'status'),
				'from_kpr' => $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'from_kpr'),
				'approved_agent' => $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'approved_agent'),
			);
		}
	}

	function getApplicationStatus($application_form, $application_snyc, $resend){
		$status = null;
		if(!empty($application_form)){
			switch ($application_form) {
				case 'none':
					if(empty($application_snyc) && empty($resend)){
						$status = 'pending';
					}elseif (empty($application_snyc) && !empty($resend)){
						$status = 'resend';
					}
					break;
				case 'fill':
					if(empty($application_snyc) && empty($resend)){
						$status = 'completed';
					}elseif (empty($application_snyc) && !empty($resend)){
						$status = 'resend';
					}elseif(!empty($application_snyc)){
						$status = 'sent';
					}
					break;
			}
			return $status;
		}
	}

	############################################################################################

	function list_banks( $value, $kpr, $ids = array(), $bank_id){
		$price = Common::hashEmptyField($value, 'Property.price_measure');
		$property_type_id = Common::hashEmptyField($value, 'Property.property_type_id', null);

		$dp = Common::hashEmptyField($value, 'BankSetting.dp', 0);
		$periode_installment = Common::hashEmptyField($value, 'BankSetting.periode_installment', 30);
		$down_payment = Common::hashEmptyField($value, 'BankSetting.down_payment', 0);
		$kpr_banks = Common::hashEmptyField($kpr, 'KprBank');
		$setting_ids = Set::extract($kpr_banks, '/KprBank/setting_id');

		$bankConditions = array();

		if(!empty($bank_id)){
			$bankConditions['conditions']['Bank.id'] = $bank_id;
		}

		$bank_ids = $this->controller->User->Kpr->Bank->getData('list', array_merge( $bankConditions, array(
			'fields' => array('id', 'id'),
		)));

		$conditionsSetting = $this->controller->User->Kpr->Bank->BankSetting->getData('conditions', false, array(
			'type' => 'product',
		));
		$banks = $this->controller->User->Kpr->Bank->BankProduct->getData('all', array(
			'conditions' => array_merge($conditionsSetting, array(
				'BankSetting.bank_id' =>  $bank_ids,
				'BankSetting.dp <=' =>  $dp,
				'BankSetting.periode_installment >=' => $periode_installment,
			)),
			'contain' => array(
				'BankSetting',
			),
			'order' => array('BankProduct.id' => 'DESC'),
		), array(
			'status' => 'publish',
			'price' => $price,
			'property_type_id' => $property_type_id,
			'company_id' => Configure::read('Principle.id'),
			'bank_id' => $bank_id,
		));
		$banks = $this->controller->User->Kpr->Bank->BankProduct->getMergeList($banks, array(
			'contain' => array(
				'Bank',
			),
		));

		if(!empty($banks)){
			$checkAll = true;
			$target_id = !empty($setting_ids) ? $setting_ids : $ids;

			if( is_array($target_id) ) {
				$target_id = array_filter($target_id);
			}

			if( !empty($target_id) ) {
				$trigger_bank_id = $this->controller->User->Kpr->Bank->BankSetting->getData('list', array(
					'conditions' => array(
						'BankSetting.id' => $target_id,
					),
					'fields' => array('bank_id', 'bank_id'),
				), array(
					'type' => 'all',
				));
			} else {
				$trigger_bank_id = array();
			}
			
			foreach($banks AS $key => $bank){
				$bank_id = Common::hashEmptyField($bank, 'Bank.id');
				$bank_setting_id = Common::hashEmptyField($bank, 'BankSetting.id');
				
				if(in_array($bank_setting_id, $setting_ids)){
					$bank['BankSetting']['checked'] = true;

					if(!in_array($bank_setting_id, $setting_ids) && in_array($bank_id, $trigger_bank_id)){
						$bank['BankSetting']['disabled'] = 'disabled';
					}
				}else{
					if(in_array($bank_setting_id, $ids)){
						$bank['BankSetting']['checked'] = true;
					}else{
						$bank['BankSetting']['checked'] = false;
						$checkAll = false;

						if(!in_array($bank_setting_id, $ids) && in_array($bank_id, $trigger_bank_id)){
							$bank['BankSetting']['disabled'] = 'disabled';
						}
					}
				}

				$bank['BankSetting']['down_payment'] = $down_payment;
				$bank['BankSetting']['periode_installment'] = $periode_installment;
				$bank['BankProduct']['first_credit'] = $this->controller->User->Kpr->Bank->BankSetting->getFirstCredit($bank, array(
					'price'=> $price,
					'dp_custom' => $dp,
					'periode_installment_custom' => $periode_installment,
				));

				$bank = $this->getSummaryKprProduct($value, $bank);
				$banks[$key] = $bank;
			}
			
			$banks['checkAll'] = $checkAll;
		}

		return $banks;
	}

	function getDataField($data, $options = array()){
		$model = $this->RmCommon->filterEmptyField($options, 'model');
		$field = $this->RmCommon->filterEmptyField($options, 'field');
		$optionPrice = $this->RmCommon->filterEmptyField($options, 'optionPrice');
		$view = $this->RmCommon->filterEmptyField($options, 'view', false, 'all');


		$category = $this->RmCommon->filterEmptyField($data, $model, sprintf('category_%s', $field));
		$param = $this->RmCommon->filterEmptyField($data, $model, sprintf('param_%s', $field));
		$value = $this->RmCommon->filterEmptyField($data, $model, $field);

		$value_arr = $this->getPercent($value, $optionPrice, array(
			'category' => $category,
			'params' => $param,
		));

		if($view == 'nominal'){
			return !empty($value_arr['nominal'])?$value_arr['nominal']:false;
		}else if($view == 'percent'){
			return !empty($value_arr['percent'])?$value_arr['percent']:false;
		}else{
			return $value_arr;
		}
	}

	function dataFieldList($data, $model, $options = array()){
		$optionsPrice = $this->RmCommon->filterEmptyField($options, 'optionsPrice');
		$fields = $this->RmCommon->filterEmptyField($options, 'fields');
		$view = $this->RmCommon->filterEmptyField($options, 'view');
		$data_arr = array();

		if(!empty($fields)){
			foreach($fields AS $key => $field){
				$data_arr[$field] = $this->getDataField($data, array(
					'model' => $model,
					'field' => $field,
					'optionPrice' => $optionsPrice,
					'view' => $view,
				));
			}
		}

		return $data_arr;
	}

	function getSummaryKprProduct($value, $bank, $modelSetting = 'BankSetting'){
		$credit_cabs = false;
		$property_id = Common::hashEmptyField($value, 'Property.id', null);
		$mls_id = Common::hashEmptyField($value, 'Property.mls_id');
		$price = Common::hashEmptyField($value, 'Property.price_measure');
		$property_type_id = Common::hashEmptyField($value, 'Property.property_type_id', null);
		$region_id = Common::hashEmptyField($value, 'PropertyAddress.region_id', null);
		$city_id = Common::hashEmptyField($value, 'PropertyAddress.city_id', null);

		## DATA BANK
		$bank_id = Common::hashEmptyField($bank, 'Bank.id', null);
		$product_name = Common::hashEmptyField($bank, 'BankProduct.name');
		$text_promo = Common::hashEmptyField($bank, 'BankProduct.text_promo');
		$desc_promo = Common::hashEmptyField($bank, 'BankProduct.desc_promo');
		$is_notary = Common::hashEmptyField($bank, 'BankProduct.is_notary');

		$setting_id = $this->RmCommon->filterEmptyField($bank, $modelSetting, 'id', null);
		$dp = $this->RmCommon->filterEmptyField($bank, $modelSetting, 'dp');
		$down_payment = $this->RmCommon->filterEmptyField($bank, $modelSetting, 'down_payment');
		$periode_installment = $this->RmCommon->filterEmptyField($bank, $modelSetting, 'periode_installment');
		$interest_rate_fix = $this->RmCommon->filterEmptyField($bank, $modelSetting, 'interest_rate_fix');
		$interest_rate_cabs = $this->RmCommon->filterEmptyField($bank, $modelSetting, 'interest_rate_cabs');
		$interest_rate_float = $this->RmCommon->filterEmptyField($bank, $modelSetting, 'interest_rate_float');
		$provision = $this->RmCommon->filterEmptyField($bank, $modelSetting, 'provision');
		$periode_fix = $this->RmCommon->filterEmptyField($bank, $modelSetting, 'periode_fix');
		$periode_cab = $this->RmCommon->filterEmptyField($bank, $modelSetting, 'periode_cab');

		// Parameter ketika filter dp dan tahun/tenor
		$down_payment = !empty($down_payment) ? $down_payment : $this->_callCalcDp($price, $dp);
		$loan_price = $this->calcLoanFromDp($price, $down_payment);
		$dp = !empty($down_payment) ? $this->_callDpPercent($price, $down_payment) : $dp;

		$optionPrice = array(
			'price' => $price,
			'loan_price' => $loan_price,
		);

		$data_arr = $this->dataFieldList($bank, $modelSetting, array(
			'optionsPrice' => $optionPrice,
			'fields' => array(
				'appraisal',
				'administration',
				'insurance',
				'sale_purchase_certificate',
				'transfer_title_charge',
				'letter_mortgage',
				'credit_agreement',
				'imposition_act_mortgage',
				'mortgage',
				'other_certificate',
			),
			'view' => 'nominal',
		));

		$appraisal = $this->RmCommon->filterEmptyField($data_arr, 'appraisal');
		$administration = $this->RmCommon->filterEmptyField($data_arr, 'administration');
		$insurance = $this->RmCommon->filterEmptyField($data_arr, 'insurance');
		$sale_purchase_certificate = $this->RmCommon->filterEmptyField($data_arr, 'sale_purchase_certificate');
		$transfer_title_charge = $this->RmCommon->filterEmptyField($data_arr, 'transfer_title_charge');
		$letter_mortgage = $this->RmCommon->filterEmptyField($data_arr, 'letter_mortgage');
		$credit_agreement = $this->RmCommon->filterEmptyField($data_arr, 'credit_agreement');
		$imposition_act_mortgage = $this->RmCommon->filterEmptyField($data_arr, 'imposition_act_mortgage');
		$mortgage = $this->RmCommon->filterEmptyField($data_arr, 'mortgage');
		$other_certificate = $this->RmCommon->filterEmptyField($data_arr, 'other_certificate');
		$total_first_credit = @round($this->creditFix($loan_price, $interest_rate_fix, $periode_installment), 0);
		## perhitungannya belum fix
		// $credit_cabs = !empty($interest_rate_cabs)? $this->creditFloat($loan_price, $interest_rate_cabs, $periode_fix, $periode_installment) : false;

		if($modelSetting == 'BankSetting'){
			$commission = $this->getCommissionKPR( array(
				'type_kpr' => 'frontend',
				'property_price' => $price,
				'bank_id' => $bank_id,
				'property_type_id' => $property_type_id,
				'region_id' => $region_id,
				'city_id' => $city_id,
				'setting_id' => $setting_id,
			), $down_payment, $bank);
		}else{
			$commission['percent_agent'] = $this->RmCommon->filterEmptyField($bank, $modelSetting, 'provision');
		}

		$provision = $this->RmCommon->filterEmptyField($commission, 'percent_agent');
		$provision_custom = $this->calValueFromPercent($loan_price, $provision);
		$total_cost_bank = $appraisal + $administration + $insurance + $provision_custom;

		## COST NOTARIS
		$total_notaris = 0;
		if($is_notary){
			$total_notaris = $sale_purchase_certificate + $transfer_title_charge + $letter_mortgage + $imposition_act_mortgage + $mortgage + $other_certificate + $credit_agreement;		
		}
		##TOTAL PEMBAYARAN PERTAMA
		$grand_total = $down_payment + $total_first_credit + $total_cost_bank + $total_notaris;

		$intallment_arr = $this->calculate_kpr_installment_detail(false, array(
			'Kpr' => array(
				'loan_price' => $loan_price,
				'interest_rate' => $interest_rate_fix,
				'credit_fix' => $periode_fix,
				'credit_cap' => $periode_cab,
				'credit_total' => $periode_installment,
				'cap_rate' => $interest_rate_cabs,
				'floating_rate' => $interest_rate_float,
			),
		));

		if(!empty($intallment_arr) && !empty($interest_rate_cabs)){
			$idx = ($periode_fix*12)+1;
			$credit_cap = !empty($intallment_arr[$idx]) ? $intallment_arr[$idx] : false;
			$credit_cabs = $this->RmCommon->filterEmptyField($credit_cap, 'Angsuran');
			$credit_cabs = $this->RmCommon->_callPriceConverter($credit_cabs);
		}


		return array_merge($bank, array(
			'display' => array(
				'mls_id' => $mls_id,
				'property_price' => $price,
				'property_id' => $property_id,
				'down_payment' => $down_payment,
				'loan_price' => $loan_price,
				'dp' => $dp,
				'interest_rate' => $interest_rate_fix,
				'interest_rate_cabs' => $interest_rate_cabs,
				'interest_rate_float' => $interest_rate_float,
				'periode_installment' => $periode_installment,
				'first_credit' => $total_first_credit,
				'cap_credit' => $credit_cabs,
				'appraisal' => $appraisal,
				'administration' => $administration,
				'insurance' => $insurance,
				'provision' => $provision_custom,
				'credit_agreement' => $credit_agreement,
				'total_cost_bank' => $total_cost_bank,
				'sale_purchase_certificate' => $sale_purchase_certificate,
				'transfer_title_charge' => $transfer_title_charge,
				'letter_mortgage' => $letter_mortgage,
				'imposition_act_mortgage' => $imposition_act_mortgage,
				'mortgage' => $mortgage,
				'other_certificate' => $other_certificate,
				'total_notaris' => $total_notaris,
				'grand_total' => $grand_total,
				'periode_fix' => $periode_fix,
				'periode_cab' => $periode_cab,
				'product_name' => $product_name,
				'text_promo' => $text_promo,
				'desc_promo' => $desc_promo,
			),
		));
	}

	function checkExistKpr($data){
		$email = $this->RmCommon->filterEmptyField($data, 'Kpr', 'client_email');
		$mls_id = $this->RmCommon->filterEmptyField($data, 'Kpr', 'mls_id');

		$cnt_data = $this->controller->User->Kpr->getData('count', array(
			'conditions' => array(
				'Kpr.client_email' => trim($email),
				'Kpr.mls_id' => $mls_id,
			),
		), array(
			'company' => false,
		));
		
		if(empty($email)){
			return false;
		}else{
			if($cnt_data > 0){
				return true;
			}else{
				return false;
			}
		}
	}


	function beforeSaveShareKprs($datas, $setting){
    	$from_web = $this->RmCommon->filterEmptyField($setting, 'Setting', 'name');

		if(!empty($datas)){
			foreach($datas AS $key => $data){
				$modified = $this->RmCommon->filterEmptyField($data, 'KprBank', 'modified');
				$data = $this->RmUser->userExist($data, array(
					'modelTarget' => 'KprBank',
					'fieldTarget' => 'agent_id',
				));

				$rumahku_kpr_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'id', null);
				$mls_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'mls_id');

				if(!empty($mls_id)){
					$data['KprBank']['kpr_code'] = $this->RmCommon->filterEmptyField($data, 'KprBank', 'code');
					$data = $this->RmCommon->dataConverter($data, array(
						'unset' => array(
							'KprApplication' => array(
								'id',
								'kpr_bank_id',
								'modified',
								'created',
								'KprApplicationJob' => array(
									'id',
									'kpr_application_id',
									'region_id',
									'city_id',
									'subarea_id',
									'zip',
									'created',
									'modified',
								),
							),
							'KprBank' => array(
								'id',
								'code',
								'created',
								'modified',
							),
							'KprBankDate' => array(
								'id',
								'kpr_bank_id',
								'created',
								'modified'
							),
							'KprBankInstallment' => array(
								'id',
								'kpr_bank_id',
								'modified',
								'created',
								'KprBankCommission' => array(
									0 => array(
										'KprBankCommission' => array(
											'id',
											'kpr_bank_installment_id',
											'modified',
											'created',
										),
									),
								),
							),
						),
					));

					if(!empty($data['KprApplication']['KprApplicationJob'])){
						$data = $this->mergeKprApplicationJob($data);
					}

					$data = $this->setDataKpr($data, $rumahku_kpr_id, $from_web);
					$data['KprBank']['from_web'] = $from_web;
					
					$dataSave['Kpr'] =  $this->RmCommon->filterEmptyField($data, 'Kpr');
		    		$dataSave['KprApplication'] =  $this->RmCommon->filterEmptyField($data, 'KprApplication');
		    		$dataSave['Document'] = $this->RmCommon->filterEmptyField($data, 'Document');
		    		$dataSave['KprBank'] =  $this->RmCommon->filterEmptyField($data, 'KprBank');
		    		$dataSave['KprBank']['KprBankDate'] = $this->RmCommon->filterEmptyField($data, 'KprBankDate');
		    		$dataSave['KprBank']['KprBankInstallment'] = $this->RmCommon->filterEmptyField($data, 'KprBankInstallment');

					$datas[$key] = $dataSave;
				}else{
					unset($datas[$key]);
				}
			}
			$datas['modified'] = $modified;
		}
		return $datas;
    }

    function mergeKprApplicationJob($data){
    	if(!empty($data['KprApplication']['KprApplicationJob'])){
    		$data['KprApplication'] = array_merge($data['KprApplication'], $data['KprApplication']['KprApplicationJob']);
    		$data = $this->RmCommon->_callUnset(array(
    			'KprApplication' => array(
    				'KprApplicationJob'
    			),
    		), $data);
    	}
    	return $data;
    }

    function setDataKpr($data, $rumahku_kpr_id, $from_web){
    	if(!empty($data)){
    		$date_now = date('Y-m-d H:i:s');
    		$agent_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'agent_id', null);
    		$agent = $this->controller->User->getData('first', array(
    			'conditions' => array(
    				'User.id' => $agent_id,
    			),
    		));
    		$company_id = $this->RmCommon->filterEmptyField($agent, 'User', 'parent_id', 0);

    		$property_price = $this->RmCommon->filterEmptyField($data, 'KprBankInstallment', 'property_price');
    		$down_payment = $this->RmCommon->filterEmptyField($data, 'KprBankInstallment', 'down_payment');
    		$dp = $this->_callDpPercent($property_price, $down_payment);

    		$type_arr = Set::classicExtract($data, 'KprBankInstallment.KprBankCommission.{n}.KprBankCommission.type');
    		
    		$unpaid_agent = (in_array('agent', $type_arr)) ? 'pending' : 'no_provision';
    		$unpaid_rumahku = (in_array('rumahku', $type_arr)) ? 'pending' : 'no_provision';

    		$data['Kpr'] = array(
    			'type' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'type'),
    			'type_log' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'type_log'),
    			'company_id' => $company_id,
    			'from_web' => $from_web,
    			'bank_apply_category_id' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'bank_apply_category_id', null),
    			'mls_id' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'mls_id'),
    			'property_id' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'property_id', null),
    			'agent_id' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'agent_id', null),
    			'code' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'kpr_code'),
    			'user_id' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'user_id', null),
    			'currency_id' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'currency_id', null),
    			'client_email' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'client_email'),
    			'gender_id' => $this->RmCommon->filterEmptyField($data, 'KprApplication', 'gender_id', null),
    			'address' => $this->RmCommon->filterEmptyField($data, 'KprApplication', 'address'),
    			'birthplace' => $this->RmCommon->filterEmptyField($data, 'KprApplication', 'birthplace'),
    			'birthday' => $this->RmCommon->filterEmptyField($data, 'KprApplication', 'birthday'),
    			'ktp' => $this->RmCommon->filterEmptyField($data, 'KprApplication', 'ktp'),
    			'client_name' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'client_name'),
    			'client_hp' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'client_hp'),
    			'client_job_type_id' => $this->RmCommon->filterEmptyField($data, 'KprBank', 'client_job_type_id', null),
    			'property_price' => $property_price,
    			'credit_total' => $this->RmCommon->filterEmptyField($data, 'KprBankInstallment', 'credit_total'),
    			'dp' => $dp,
    			'down_payment' => $down_payment,
    			'loan_price' => $this->RmCommon->filterEmptyField($data, 'KprBankInstallment', 'loan_price'),
    			'interest_rate' => $this->RmCommon->filterEmptyField($data, 'KprBankInstallment', 'interest_rate_fix'),
    			'sold_date' => $date_now,
    			'kpr_date' => $date_now,
    			'document_status' => 'process',
    			'sync' => false,
    			'rumahku_kpr_id' => $rumahku_kpr_id,
    			'unpaid_agent' => $unpaid_agent,
    			'unpaid_rumahku' => $unpaid_rumahku,
    			'keyword' => $this->RmCommon->filterEmptyField($data, 'Property', 'keyword'),
    		);

    		$kpr_bank_date = $this->RmCommon->filterEmptyField($data, 'KprBankDate');

    		if($kpr_bank_date){
    			unset($data['KprBankDate']);
    		}

    		$data['KprBankDate'][]['KprBankDate'] = $kpr_bank_date;

    		$data['KprBank']['document_status'] = 'process';
    		$data['KprBank']['application_status'] = 'completed';
    		$data['KprBank']['from_kpr'] = 'frontend';
    		$data['KprBank']['rumahku_kpr_id'] = $rumahku_kpr_id;

    		return $data;
    	}else{
    		return false;
    	}
    }

   	function api_action_kpr($data){
   		$result = array();
   		$prime_kpr_bank_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'prime_kpr_bank_id', null);
		$document_status_KPR = $this->RmCommon->filterEmptyField($data, 'KprBank', 'document_status');

		if(!empty($prime_kpr_bank_id)){
			$value = $this->User->Kpr->KprBank->MergeEmailKPR($prime_kpr_bank_id);
			$kpr_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id');
			$document_status = $this->RmCommon->filterEmptyField($value, 'KprBank', 'document_status');

			$data = $this->doBeforeSaveActionKPR($data, $value);
			$result = $this->User->Kpr->KprBank->doUpdateActionKPR($data, $value);
			$status = $this->RmCommon->filterEmptyField($result, 'status');

			if($status == 'success'){
				## UPDATE SUMARRY KPR
				$result_summary = $this->User->Kpr->_summaryDocumentStatus($kpr_id);
				$this->RmCommon->setProcessParams($result_summary, false, array(
					'noRedirect' => true
				));
				##
				$value = $this->User->Kpr->KprBank->KprBankDate->getFromSlug($value, $prime_kpr_bank_id);
				$kpr_bank_date = $this->RmCommon->filterEmptyField($value, 'KprBankDate');
				$result_credit_agreement = $this->User->Kpr->KprBank->KprBankCreditAgreement->doSaveActionKPR($data, $value);

				$value = $this->User->Kpr->KprBank->KprBankCreditAgreement->getMerge($value, $prime_kpr_bank_id, array(
					'fieldName' => 'kpr_bank_id'
				));

				$log_document_status = Set::classicExtract($kpr_bank_date, '{n}.KprBankDate.slug');

				if(in_array('approved_bank', $log_document_status)){
					$status_confirm = 'confirm'; 
				}else{
					$status_confirm = 'no_confirm';
				}

				# get VALUE INSTALLMENT N Provisi
				$value = $this->User->Kpr->KprBank->KprBankInstallment->getMerge($value, $prime_kpr_bank_id, array(
					'elements' => array(
						'status' => $status_confirm,
					),
					'fieldName' => 'kpr_bank_id',
				));

				$kpr_bank_installment_id = $this->RmCommon->filterEmptyField($value, 'KprBankInstallment', 'id');

				$value = $this->User->Kpr->KprBank->KprBankInstallment->KprBankCommission->getMerge($value, $kpr_bank_installment_id, array(
					'elements' => array(
						'status' => $status_confirm,
					),
					'find' => 'all',
					'fieldName' => 'kpr_bank_installment_id',
				));
				## LINK untuk notif dan email
				$link = array(
                    'controller' => 'kpr',
                    'action' => 'application_detail',
                    $prime_kpr_bank_id,
                    'admin' => true,
                );
				## sendEmail 
				$this->sendEmailActionKPR($data, $value, array(
					'link' => $link,
				));
				##	
				$this->RmCommon->setProcessParams($result_credit_agreement, false, array(
					'noRedirect' => true
				));
			}
		}
		return $result;
   	}

   	function api_action_credit_agreement($data){
   		if(!empty($data)){
   			$prime_kpr_bank_id = Common::hashEmptyField($data, 'KprBank.prime_kpr_bank_id', null);
   			$document_status_KPR = Common::hashEmptyField($data, 'KprBank.document_status');

			$value = $this->controller->Kpr->KprBank->getData('first', array(
				'conditions' => array(
					'KprBank.id' => $prime_kpr_bank_id
				),
			));

			$value = $this->controller->Kpr->KprBank->KprBankInstallment->getMerge($value, $prime_kpr_bank_id, array(
				'fieldName' => 'kpr_bank_id',
				'elements' => array(
					'status' => 'confirm',
				),
			));

			$kpr_id = Common::hashEmptyField($value, 'KprBank.kpr_id');
			$value = $this->User->Kpr->getMerge($value, $kpr_id);
			
			$kpr_bank_id = Common::hashEmptyField($value, 'KprBank.id', null);
			$bank_id = Common::hashEmptyField($value, 'KprBank.bank_id', null);
			$property_id = Common::hashEmptyField($value, 'Kpr.property_id', null);
			$agent_id = Common::hashEmptyField($value, 'Kpr.agent_id', null);
			
			$value = $this->controller->User->Property->getMerge($value, $property_id);
			$value = $this->controller->User->Property->PropertySold->getMerge($value, $property_id);
			$value = $this->controller->User->Kpr->Bank->getMerge($value, $bank_id);
			$value = $this->controller->User->Kpr->Bank->BankContact->getMerge($value, $bank_id);
			$value = $this->controller->User->getMerge($value, $agent_id, TRUE, 'Agent');
			$value = $this->setKprSold($value, $data);
			$value = $this->RmProperty->_callBeforeSold($value, $property_id);

			$data = $this->beforeSaveCreditAgreement($data, $value);
			
			$result = $this->controller->User->Kpr->KprBank->doUpdateCreditAgreement($data, $value);
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$this->RmCommon->setProcessParams($result, false, array(
				'noRedirect' => true
			));

			if($status == 'success'){
				## sendEmail 
				$link = array(
	                'controller' => 'kpr',
	                'action' => 'application_detail',
	                $kpr_bank_id,
	                'admin' => true,
	            );
	            $result_summary = $this->User->Kpr->_summaryDocumentStatus($kpr_id);
				$this->RmCommon->setProcessParams($result_summary, false, array(
					'noRedirect' => true
				));

	            $value = $this->User->Kpr->KprBank->KprBankCreditAgreement->getMerge($value, $kpr_bank_id, array(
	            	'fieldName' => 'kpr_bank_id',
	            ));

				$this->sendEmailActionKPR($data, $value, array(
					'link' => $link,
				));
				##	
			}
   			
   		}
		return $result;
	}

    function getDocuments($value){
    	$kpr_bank_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'id');
		$result = array();

    	if( !empty($value['KprApplication'][0]) ) {
			$documents = $this->documentList($value['KprApplication'][0], array(
				'KprApplication' => array(
					'document_type' => 'kpr_application',
					'exlude' => array(3, 7, 19, 20),
				),
			));

			if( !empty($documents) ) {
				foreach ($documents as $key => $val) {
	    			$category = $this->RmCommon->filterEmptyField($val, 'DocumentCategory', 'slug');
	    			$documentCategories = $this->RmCommon->filterEmptyField($val, 'CrmProjectDocument');

	    			if( !empty($documentCategories) ) {
						$result[$category] = $documentCategories;
	    			}
				}
			}
		}

		return $result;
    }

    function getDocumentsByCategory($documents){
    	$result = array();
    	
		if( !empty($documents) ) {
			foreach ($documents as $key => $val) {
    			$category = $this->RmCommon->filterEmptyField($val, 'DocumentCategory', 'slug');
    			$documentCategories = $this->RmCommon->filterEmptyField($val, 'CrmProjectDocument');

    			if( !empty($documentCategories) ) {
					$result[$category] = $documentCategories;
    			}
			}
		}

		return $result;
    }

    function documentList($value, $options = array(), $params = array()){
    	$result = array();

    	if(!empty($value)){
    		$kpr_bank_id = $this->RmCommon->filterEmptyField($value, 'id', false, null);
	    	$kpr_application_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'id', null);

	    	if(!empty($options) && is_array($options)){
	    		foreach($options AS $modelName => $option){
	    			$document_type = $this->RmCommon->filterEmptyField($option, 'document_type');
	    			$exlude = $this->RmCommon->filterEmptyField($option, 'exlude');

    				$val = $this->RmCommon->filterEmptyField($value, $modelName, false, array());
    				$result = array_merge($result, $this->getDocumentSort( array(
						'DocumentCategory.is_required' => 1,
						'DocumentCategory.id <>' => $exlude,
					), $val, array(
						'id' => $kpr_application_id, 
						'kpr_bank_id' => $kpr_bank_id,
						'document_type' => $document_type,
					)));
	    		}
	    	}
    	}
    	return $result;
    }

    function _callStatus ( $status, $is_hold = false ) {
    	if( !empty($is_hold) ) {
            $status = __('HOLD');
        } else {
	        switch ($status) {
	            case 'rejected' :
	                $status = __('Ditolak');
	                break;
	            case 'cancel' :
	                $status = __('Cancel');
	                break;
	            case 'approved_admin' :
	                $status = __('Terkirim');
	                break;
	            case 'rejected_admin' :
	                $status = __('Ditolak');
	                break;
	            case 'process' :
	                $status = __('Proses');
	                break;
	            case 'approved_proposal' :
	                $status = __('KPR Diterima');
	                break;
	            case 'rejected_proposal' :
	                $status = __('KPR Ditolak');
	                break;
	            case 'approved_bank' :
	                $status = __('Appraisal');
	                break;
	            case 'rejected_bank' :
	                $status = __('Ditolak Bank');
	                break;
	            case 'credit_process' :
	                $status = __('Proses Akad');
	                break;
	            case 'rejected_credit' :
	                $status = __('Akad Ditolak');
	                break;
	            case 'approved_credit' :
	                $status = __('Akad Disetujui');
	                break;
	            case 'completed' :
	                $status = __('Completed');
	                break;
	            case 'approved_verification' :
	                $status = __('Lulus Verifikasi Dokumen');
	                break;
                case 'rejected_verification':
            		$status = __('Tidak Lulus Verifikasi');
                    break;
	            case 'hold' :
	                $status = __('HOLD');
	                break;
	            case 'rejected_bi_checking' :
	                $status = __('Tidak Lulus BI Checking');
	                break;
	            case 'approved_bi_checking' :
	                $status = __('BI Checking Disetujui');
	                break;
	            case 'approved' :
	                $status = __('Appraisal');
	                break;
	            case 'process_appraisal' :
	                $status = __('Proses');
	                break;
	            default:
	                $status = __('Pending');
	                break;
	        }
	    }

        return $status;
    }

    function _callColor ( $status, $is_hold = false ) {
		$status_color = Configure::read('__Site.Global.Variable.KPR.status_color');

		if( !empty($is_hold) ) {
			$status = 'hold';
		}

    	return Common::hashEmptyField($status_color, $status);
    }

    function _callRoleCondition ( $value ) {
        $id = $this->RmCommon->filterEmptyField($value, 'User', 'id', null);
        $group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id', null);
        $options = array();

        switch ($group_id) {
            case '4':
                $principle_id = $this->controller->User->getAgents($id, true, 'list', false, array(
                    'role' => 'principle',
                ));

                if(!empty($principle_id)){
	                $options = array(
	                    'conditions' => array(
	                    	'Kpr.company_id' => $principle_id,
	                    ),
	                );
                }
                
                $this->controller->set('active_menu', 'director');
                break;
            case '3':
                $options = array(
                    'conditions' => array(
                    	'Kpr.company_id' => $id,
                    ),
                );
                
                $this->controller->set('active_menu', 'principal');
                break;
            case '2':
                $options = array(
                    'conditions' => array(
                    	'Kpr.agent_id' => $id,
                    ),
                );
                $this->controller->set('active_menu', 'agent');
                break;
        }

        return $options;
    }

    function getSelectProduct($named, $return = false, $kpr, $property){
		$named = $this->RmCommon->dataConverter($named, array(
			'price' => array(
				'down_payment',	
			),
			'round' => array(
				'down_payment',
			),
		), 0);

		$ids = Common::hashEmptyField($named, 'id', array());
		$setting_id = Common::hashEmptyField($named, 'setting_id');
		$bank_id = Common::hashEmptyField($named, 'bank_id');
		$property_id = Common::hashEmptyField($named, 'property_id');
		$down_payment = Common::hashEmptyField($named, 'down_payment');
		$periode_installment = Common::hashEmptyField($named, 'periode_installment');
		$filter = Common::hashEmptyField($named, 'filter');

		if(empty($ids)){
			$ids[] = $setting_id;
		}

		if( !empty($setting_id) ) {
			$value = $this->controller->Kpr->Bank->BankSetting->getData('first', array(
				'conditions' => array(
					'BankSetting.id' => $setting_id,
				),
			), array(
				'type' => 'all',
			));
			$value = $this->controller->Kpr->Bank->BankSetting->getMergeList($value, array(
				'contain' => array(
					'BankProduct',
					'Bank',
				),
			));
		} else {
			$value = array();
		}

		// get data property
		if( !empty($property) ) {
			$value = array_merge($value, $property);
		} else {
			$value = $this->controller->User->Property->getMerge($value, $property_id);
		}

		$value = $this->User->Property->getMergeList($value, array(
			'contain' => array(
				'PropertyAddress' => array(
					'Region',
					'City',
					'Subarea',
				),
			),
		));

		$price = Common::hashEmptyField($value, 'Property.price');
		$price = Common::hashEmptyField($value, 'Property.price_measure', $price);
		$dp = $this->_callDpPercent($price, $down_payment);

		$b_dp = Common::hashEmptyField($value, 'BankSettig.dp');
		$b_periode_installment = Common::hashEmptyField($value, 'BankSettig.periode_installment');

		$value['BankSetting']['dp'] = !empty($dp) ? $dp : $b_dp;
		$value['BankSetting']['down_payment'] = !empty($down_payment) ? $down_payment : false;
		$value['BankSetting']['periode_installment'] = !empty($periode_installment) ? $periode_installment : $b_periode_installment;

		$list_banks = $this->list_banks($value, $kpr, $ids, $bank_id);

		if($filter){
			switch ($filter) {
				case 'rate_lowest':
					$list_banks = Set::Sort($list_banks, '{n}.BankSetting.interest_rate_fix', 'ASC');
					break;
				case 'rate_higher':
					$list_banks = Set::Sort($list_banks, '{n}.BankSetting.interest_rate_fix', 'DESC');		
					break;
				case 'installment_lowest':
					$list_banks = Set::Sort($list_banks, '{n}.BankProduct.first_credit', 'ASC');		
					break;
				case 'installment_higher':
					$list_banks = Set::Sort($list_banks, '{n}.BankProduct.first_credit', 'DESC');		
					break;
			}
		}

		if(!empty($list_banks)){
			$this->controller->set(array(
				'list_banks' => $list_banks,
			));
		} else {
			$this->RmCommon->redirectReferer(__('data tidak ditemukan'));
		}

		if($return){
			return $value;
		}
	}

	function beforeSaveProductKpr($data, $value){

		if(!empty($data) && !empty($value)){
			$save_path = Configure::read('__Site.document_folder');
			$data = $this->RmImage->_uploadPhoto( $data, 'KprApplication', 'ktp_file', $save_path );
			$data = $this->RmImage->_uploadPhoto( $data, 'KprApplication', 'income_file', $save_path );			

			$data = $this->RmCommon->dataConverter($data, array(
				'date' => array(
					'KprApplication' => array(
						'birthday',
					),
				),
				'price' => array(
					'KprApplication' => array(
						'income',
						'household_fee',
						'other_installment',
						'loan_price',
						'down_payment',
					),
				),
			));

			$kpr_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id', null);
			$agent_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'agent_id', null);
			$code = $this->RmCommon->filterEmptyField($value, 'Kpr', 'code');
			$mls_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'mls_id');

			$bank_apply_category_id = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'bank_apply_category_id', null);
			$email = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'email');
			$name = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'name');
			$no_hp = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'no_hp');
			$job_type_id = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'job_type_id', null);
			$gender_id = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'gender_id', null);
			$address = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'address');
			$birthplace = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'birthplace');
			$birthday = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'birthday');
			$ktp = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'ktp');

			$data['KprApplication']['kpr_id'] = $kpr_id;
			$data['Kpr']['id'] = $kpr_id;
			$data['Kpr']['mls_id'] = $mls_id;
			$data['Kpr']['code'] = $code;
			$data['Kpr']['sold_date'] = date('Y-m-d H:i:s');
			$data['Kpr']['kpr_date'] = date('Y-m-d H:i:s');
			$data['Kpr']['client_email'] = $email;
			$data['Kpr']['client_name'] = $name;
			$data['Kpr']['client_hp'] = $no_hp;
			$data['Kpr']['client_job_type_id'] = $job_type_id;
			$data['Kpr']['gender_id'] = $gender_id;
			$data['Kpr']['address'] = $address;
			$data['Kpr']['birthplace'] = $birthplace;
			$data['Kpr']['birthday'] = $birthday;
			$data['Kpr']['ktp'] = $ktp;
			$data['Kpr']['document_status'] = 'process';

			$email = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'email');
			$no_hp = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'no_hp');
			$name = $this->RmUser->_getUserFullName($data, 'reverse', 'KprApplication', 'name');

			$data['KprApplication']['full_name'] = !empty($name)?implode(' ', $name):'-';
			$data['KprApplication']['first_name'] = $this->RmCommon->filterEmptyField($name, 'first_name', false, '');
			$data['KprApplication']['last_name'] = $this->RmCommon->filterEmptyField($name, 'last_name', false, '');
			
			$data = $this->RmUser->userRegister($data, $email, array(
				'first_name' => $this->RmCommon->filterEmptyField($name, 'first_name', false, ''),
				'last_name' => $this->RmCommon->filterEmptyField($name, 'last_name', false, ''),
				'no_hp' => $no_hp,
			));

			if($data['KprApplication']['same_as_address_ktp']){
				$data['KprApplication']['address_2'] = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'address');
			}

			$data = $this->controller->User->getMerge($data, $agent_id, false, 'Agent'); // get data agent untuk data email

			$file_ktp = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'ktp_file');
			if(!empty($file_ktp)){
				$file_name = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'ktp_file_name');
				$data['CrmProjectDocument'][]['CrmProjectDocument'] = array(
					'file' => $file_ktp,
					'document_category_id' => 2,
					'document_type' => 'kpr_application',
					'save_path' => $save_path,
					'name' => $file_name,
				);
			}

			$income_file = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'income_file');
			if(!empty($income_file)){
				$file_name = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'income_file_name');
				$data['CrmProjectDocument'][]['CrmProjectDocument'] = array(
					'file' => $income_file,
					'document_category_id' => 4,
					'document_type' => 'kpr_application',
					'save_path' => $save_path,
					'name' => $file_name,
				);
			}
		}
		
		return $data;
	}

	function beforeSaveKprCompare($data, $uuid){
		$dataSave = false;
		$this->BankSetting = ClassRegistry::init('BankSetting');
		$user_id = $this->controller->user_id;

		$data = $this->RmCommon->dataConverter($data, array(
			'price' => array(
				'Kpr' => array(
					'down_payment',
				),
			),
		));

		$down_payment = $this->RmCommon->filterEmptyField($data, 'Kpr', 'down_payment');
		$periode_installment = $this->RmCommon->filterEmptyField($data, 'Kpr', 'periode_installment');
		$property_id = $this->RmCommon->filterEmptyField($data, 'Kpr', 'property_id', null);
		$ids = $this->RmCommon->filterEmptyField($data, 'Kpr', 'id');

		if($ids){

			$property = $this->controller->User->Property->getData('first', array(
				'conditions' => array(
					'Property.id' => $property_id,
				),
			));

			$property = $this->controller->User->Property->getMergeList($property, array(
				'contain' => array(
					'PropertyAddress',
				),
			));
			$region_id = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'region_id', null);
			$city_id = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'city_id', null);
			$subarea_id = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'subarea_id', null);
			$property_price = $this->RmCommon->filterEmptyField($property, 'Property', 'price_measure');
			$property_type_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_type_id', null);

			$loan_price = $this->calcLoanFromDp($property_price, $down_payment);

			$dataSave['KprCompare'] = array(
				'property_id' => $property_id,
				'property_price' => $property_price,
				'loan_price' => $loan_price,
				'down_payment' => $down_payment,
				'periode_installment' => $periode_installment,
				'region_id' => $region_id,
				'city_id' => $city_id,
				'subarea_id' => $subarea_id,
				'user_id' => $user_id,
				'uuid' => $uuid,
			);

			foreach($ids AS $key => $id){
				$bank = $this->BankSetting->getData('first', array(
					'conditions' => array(
						'BankSetting.id' => $id,
					),
				), array(
					'type' => 'all',
				));

				$bank = $this->BankSetting->getMergeList($bank, array(
					'contain' => array(
						'Bank',
						'BankProduct',
					),
				));

				$bank_id = $this->RmCommon->filterEmptyField($bank, 'Bank', 'id');
				$bank_setting_id = $this->RmCommon->filterEmptyField($bank, 'BankSetting', 'id', null);
				$bank_product_id = $this->RmCommon->filterEmptyField($bank, 'BankProduct', 'id', null);

				$dataSave['KprCompareDetail'][$key]['KprCompareDetail'] = array(
					'property_id' => $property_id,
					'bank_id' => $bank_id,
					'bank_product_id' => $bank_product_id,
					'bank_setting_id' => $bank_setting_id,
					'user_id' => $user_id,
				);

				$commission = $this->getCommissionKPR( array(
					'type_kpr' => 'frontend',
					'property_price' => $property_price,
					'bank_id' => $bank_id,
					'property_type_id' => $property_type_id,
					'region_id' => $region_id,
					'city_id' => $city_id,
				), $down_payment, $bank);

				$percent_agent = $this->RmCommon->filterEmptyField($commission,'percent_agent');
				$commission_agent 	= $this->_GenerateCommission($percent_agent, $loan_price);

				if(!empty($percent_agent) && is_numeric($percent_agent)){
					$bank['BankSetting']['provision'] = $percent_agent;
				}

				$dataSave['KprCompareDetail'][$key]['KprBankInstallment'] = $this->setKprBankInstallment($bank, array(
					'down_payment' => $down_payment,
					'property_price' => $property_price,
					'credit_total' => $periode_installment,
				));

				$dataSave['KprCompareDetail'][$key]['KprBankInstallment']['type'] = 'logkpr';
				$dataSave['KprCompareDetail'][$key]['KprBankInstallment']['type_log'] = 'app-compare';
			}
		}
		return $dataSave;
	}

	function doSaveProduct($data, $value, $options = array()){
		$name_cookie = Common::hashEmptyField($options, 'name_cookie');
		$session_id = Common::hashEmptyField($options, 'session_id');

		$data = $this->RmCommon->dataConverter($data, array(
			'price' => array(
				'Kpr' => array(
					'down_payment'
				),
			),
		));

		$setting_ids = Common::hashEmptyField($data, 'Kpr.id');

		if(!empty($setting_ids)){
			$property_price = Common::hashEmptyField($value, 'Property.price');
			$down_payment = Common::hashEmptyField($data, 'Kpr.down_payment');
			$credit_total = Common::hashEmptyField($data, 'Kpr.periode_installment');
			$dp = $this->_callDpPercent($property_price, $down_payment);

			$filling = array(
				'Kpr' => array(
					'property_price' => $property_price,
				),
				'BankSetting' => array(
					'id' => $setting_ids
				),
			);

			$dataSave = $this->beforeSaveProduct($filling, array(
				'value' => $value,
				'document_status' => 'cart_kpr',
				'session_id' => $session_id,
				'from_kpr' => 'frontend',
				'down_payment' => $down_payment,
				'dp' => $dp,
				'credit_total' => $credit_total,
			));

			$result = $this->controller->User->Kpr->doSaveProductKpr($dataSave, $session_id);
			$status = $this->RmCommon->filterEmptyField($result, 'status');

			if($status == 'success'){
				$this->controller->Cookie->write($name_cookie, $session_id, false, '24 hour');
			}
		}else{
			$result = array(
				'msg' => __('Pilih promo KPR terlebih dahulu'),
				'status' => 'error',
			);
		}
		return $result;
	}

	function bankDuplicated($bank_setting_ids = false){
		if($bank_setting_ids){
			$list_banks = $this->controller->User->Kpr->KprBank->Bank->BankSetting->getData('list', array(
				'conditions' => array(
					'BankSetting.id' => $bank_setting_ids,
				),
				'fields' => array(
					'bank_id',
					'bank_id',
				)
			), array(
				'type' => 'all',
			));

			if(count($list_banks) == count($bank_setting_ids)){
				return false;
			}else{
				return true;
			}
		}else{
			return null;
		}
	}

	function doBeforeSaveView($dataView, $value){
		if(!empty($dataView)){
			$dataView['BankSlideView']['url_banner'] = $this->RmCommon->filterEmptyField($value, 'BankBanner', 'url');
		}
		return $dataView;
	}

	function checkExistingKPR ( $value ) {
		$id = Common::hashEmptyField($value, 'Kpr.id');
		$property_id = Common::hashEmptyField($value, 'Kpr.property_id');
		$document_type = Common::hashEmptyField($value, 'Kpr.document_type');

		$existKPR = $this->controller->Kpr->getData('count', array(
			'conditions' => array(
				'Kpr.property_id' => $property_id,
				'Kpr.document_type' => $document_type,
				'Kpr.id <>' => $id,
			),
		), array(
			'status' => 'active',
		));
		$value['Kpr']['kpr_existing_count'] = $existKPR;

		return $value;
	}

	function _callBeforeDeveloperView ( $value = array(), $dataSave = array() ) {
		$data = $this->controller->request->data;
		$params = $this->controller->params->params;
		$documentOptions = false;

		if( empty($data) ) {
			$sold_date = date('Y-m-d');

			if( !empty($value) ) {
				$data = $value;

				$price = Common::hashEmptyField($data, 'items.unit_price');
				$currency_id = Common::hashEmptyField($data, 'items.currency_id');
				$sold_date = Common::hashEmptyField($value, 'order.created_date', $sold_date, array(
					'date' => 'Y-m-d',
				));

				$data['Kpr']['property_price'] = $value['Kpr']['property_price'] = $price;
				$data['Kpr']['currency_id'] = $currency_id;
			}

			$data['Kpr']['sold_date'] = $sold_date;
			$data['Kpr']['kpr_date'] = $sold_date;

	    	$data = $this->RmCommon->dataConverter($data, array(
				'date' => array(
					'Kpr' => array(
						'sold_date',
						'kpr_date',
					),
				),
			), true);
		}else{
			$documents = $this->RmCommon->filterEmptyField($dataSave, 'CrmProjectDocument');

			if(!empty($documents)){
				foreach ($documents as $key => &$document) {
					$document_category_id = $this->RmCommon->filterEmptyField($document, 'document_category_id');
					
					$crmDocument = $this->controller->User->Kpr->CrmProject->CrmProjectDocument->DocumentCategory->getData('first', array(
						'conditions' => array(
							'DocumentCategory.id' => $document_category_id
						)
					));
					$crmDocument['CrmProjectDocument'] = $document;
					$data['CrmProjectDocument'][$key] = $crmDocument;
				}
			}
		}

    	$data = $this->RmCommon->dataConverter($data, array(
			'price' => array(
				'Kpr' => array(
					'property_price',
				),
			),
		), true);
		
		$clientJobTypes = $this->controller->User->Kpr->KprApplication->JobType->getList();
		$documentCategories = $this->getDocumentSort(array(
			'DocumentCategory.is_required' => 1,
			'DocumentCategory.id' => Configure::read('__Site.Global.Variable.KPR.document_client'),
		), array(
			'document_type' => 'kpr_application',
		), $value);
		
        $project_id = Common::hashEmptyField($value, 'project.id');
        $company_id = Common::hashEmptyField($value, 'project.company_id');

        $price = Common::hashEmptyField($value, 'Kpr.property_price');
		$price = Common::hashEmptyField($data, 'Kpr.property_price', $price);
		
		if( !empty($price) ) {
			$records = $this->RmCommon->getAPI(array(
				'controller' => 'kpr',
				'action' => 'bank_promo',
				'admin' => false,
				'ext' => 'json',
			), array(
	            'header' => array(
	                'slug' => 'api-bank-list-promo',
	                'data' => array(
		            	'project' => $project_id,
	            	),
	            ),
				'get' => array(
		            'price' => $price,
				),
	        ));
			$banks['qualify'] = Common::hashEmptyField($records, 'data');
		}
		
		$this->RmCommon->_callRequestSubarea('Kpr');
		$this->controller->set(array(
			'banks' => !empty($banks)?$banks:false,
			'value' => $value,
			'clientJobTypes' => $clientJobTypes,
			'documentCategories' => $documentCategories,
			'active_menu' => 'kpr_add',
		));

		$this->controller->request->data = $data;
	}

	function _callBeforeDeveloperSave ( $data, $value, $id = null ) {
		$dataSave = array();

        if( !empty($data) ) {
			$company_id = Configure::read('Principle.id');
			$property_type_id = Common::hashEmptyField($value, 'project.PropertyType.id', null);
			$property_type_id = Common::hashEmptyField($value, 'project.property_type_id', $property_type_id);
        	$data = $this->RmCommon->dataConverter($data, array(
				'date' => array(
					'Kpr' => array(
						'sold_date',
					),
				),
			));
			$sold_date = Common::hashEmptyField($data, 'Kpr.sold_date');
			$invoice_number = Common::hashEmptyField($value, 'order.invoice_number');
			$booking_code = Common::hashEmptyField($value, 'order.booking_code');
			$booking_code = Common::hashEmptyField($data, 'Kpr.booking_code', $booking_code);

			$value = $this->controller->Kpr->Property->PropertyType->getMerge($value, $property_type_id);
			$bank_apply_category_id = Common::hashEmptyField($value, 'PropertyType.bank_apply_category_id', null);
			$stock_id = Common::hashEmptyField($value, 'items.stock_id', null);
			$property_price = Common::hashEmptyField($value, 'items.unit_price');

			$client_email = Common::hashEmptyField($value, 'buyer_data.email');
			$client_name = Common::hashEmptyField($value, 'buyer_data.name');
			$client_no_hp = Common::hashEmptyField($value, 'buyer_data.no_hp');
			$client_identity_number = Common::hashEmptyField($value, 'buyer_data.identity_number', null);
			$client_address = Common::hashEmptyField($value, 'buyer_data.address', null);
        	$client = $this->controller->User->UserClient->getData('first', array(
        		'conditions' => array(
        			'User.email' => $client_email,
    			),
    			'contain' => array(
    				'User',
				),
    		), array(
    			'mine' => true,
    		));

        	// Proses Perhitungan KPR
        	$project_address = Common::hashEmptyField($value, 'project.address');
			$data = array_merge($data, array(
				'Kpr' => array(
					'agent_id' => Configure::read('User.id'),
					'property_price' => $property_price,
				),
				'Property' => array(
					'property_type_id' => $property_type_id,
				),
				'PropertyAddress' => array(
					'address' => $project_address,
					'region_id' => Common::hashEmptyField($value, 'project.region_id', null),
					'city_id' => Common::hashEmptyField($data, 'project.city_id', null),
				),
			));

			$document_type = 'developer';
			$property_photo = Common::hashEmptyField($value, 'project.logo');
			$property_photo = Common::hashEmptyField($value, 'project.cover_img_sync', $property_photo);

			$data['Kpr']['project_id'] = Common::hashEmptyField($value, 'project.id', null);
			$data['Kpr']['document_type'] = $document_type;
			$kprBank = $this->_saveKprBank($data);

			$dataSave = array(
				'Kpr' => array(
					'reference_code' => $invoice_number,
					'booking_code' => $booking_code,
					'company_id' => $company_id,
					'bank_apply_category_id' => $bank_apply_category_id,
					'agent_id' => Configure::read('User.id'),
					'kpr_date' => $sold_date,
					'sold_date' => $sold_date,
					'user_id' => Common::hashEmptyField($client, 'User.id', null),
					'register' => $this->controller->User->Kpr->_callCheckExistingKPR($stock_id, $client_email, null, 'developer'),
					'document_type' => $document_type,

					'client_email' => $client_email,
					'client_name' => $client_name,
					'client_hp' => $client_no_hp,
					'address' => $client_address,
					'ktp' => $client_identity_number,

					'mls_id' => Common::hashEmptyField($value, 'items.mls_id'),
					'property_id' => $stock_id,
					'property_photo' => $property_photo,
					'currency_id' => Common::hashEmptyField($value, 'items.unit_currency_id', null),
					'keyword' => Common::hashEmptyField($value, 'items.keyword'),
					'property_price' => $property_price,
				),
				'KprApplication' => array(
					array(
						'first_name' => Common::hashEmptyField($value, 'buyer_data.first_name'),
						'last_name' => Common::hashEmptyField($value, 'buyer_data.last_name'),
						'full_name' => $client_name,
						'email' => $client_email,
						'no_hp' => $client_no_hp,
						'ktp' => $client_identity_number,
						'address' => $client_address,
					),
				),
				'KprProduct' => array(
					array(
						'property_id' => $stock_id,
						'label' => __('Project'),
						'value' => Common::hashEmptyField($value, 'project.name'),
					),
					array(
						'property_id' => $stock_id,
						'label' => __('Cluster/Tower'),
						'value' => Common::hashEmptyField($value, 'items.product'),
					),
					array(
						'property_id' => $stock_id,
						'label' => __('Unit'),
						'value' => Common::hashEmptyField($value, 'items.unit'),
					),
					array(
						'property_id' => $stock_id,
						'label' => __('Number'),
						'value' => Common::hashEmptyField($value, 'items.block_number'),
					),
				),
				'CrmProjectDocument' => Common::hashEmptyField($data, 'CrmProjectDocument'),
				'KprBank' => Common::hashEmptyField($kprBank, 'KprBank'),
			);

			$invoice_collector_id = Common::hashEmptyField($value, 'InvoiceCollector.id');
			$dataSave['Kpr']['InvoiceCollector'] = array(
				'id' => $invoice_collector_id,
				'on_progress_kpr' => true,
			);

			$dataSave = $this->_callDataDocuments($dataSave);
        }

        return $dataSave;
	}

    function dataNotice($value){
        $document_status = Common::hashEmptyField($value, 'KprBank.document_status');
        $is_hold = Common::hashEmptyField($value, 'KprBank.is_hold');
        $result = false;

        if( !empty($is_hold) ) {
            $result = __('Aplikasi di HOLD hingga proses Akad Kredit selesai');
        } else if($document_status){
            switch ($document_status) {
                case 'process':
            		$result = __('Pengajuan KPR dalam Proses Bank');
                    break;
                case 'approved_proposal':
            		$result = __('Aplikasi telah diterima Bank, harap menunggu hingga proses KPR selesai');
                    break;
                case 'rejected_proposal':
            		$result = __('Aplikasi ditolak oleh Bank');
                    break;
                case 'cancel':
            		$result = __('Anda telah membatalkan Aplikasi KPR');
                    break;
                case 'approved_bank':
            		$result = __('Aplikasi telah disetujui Bank');
                    break;

                case 'rejected_bank':
            		$result = __('Aplikasi telah ditolak Bank');
                    break;

                case 'rejected_bi_checking':
            		$result = __('Aplikasi tidak lulus BI checking');
                    break;

                case 'rejected_verification':
            		$result = __('Aplikasi tidak Lulus Verifikasi Dokumen');
                    break;

                case 'credit_process':
            		$result = __('Aplikasi dalam Proses Akad');
                    break;

                case 'rejected_credit':
            		$result = __('Aplikasi telah ditolak untuk Proses Akad');
                    break;

                case 'approved_bi_checking':
            		$result = __('Aplikasi lulus BI Checking');
                    break;

                case 'approved_verification':
            		$result = __('Aplikasi Lulus Verifikasi Dokumen');

                case 'process_appraisal':
            		$result = __('Aplikasi dalam tahap appraisal');
                    break;
                
                default :
            		$result = __('Selamat, Akad Kredit telah selesai');
                    break;
            }
        }

        return $result;
    }

    function _callBooking ( $booking_code = null, $principle_id = false ) {
    	if( !empty($booking_code) ) {
			$records = $this->RmCommon->getAPI('transactions/detail_order/'.$booking_code, array(
	            'header' => array(
	                'slug' => 'primedev-api',
	                'principle' => $principle_id,
	            ),
	        ));

			$value = Common::hashEmptyField($records, 'data', array());
			$invoice_number = Common::hashEmptyField($value, 'order.invoice_number');
			$value = $this->controller->User->Kpr->InvoiceCollector->getMerge($value, $invoice_number);
		} else {
			$value = array();
		}

		$this->controller->set(array(
			'value' => $value,
			'booking_code' => $booking_code,
		));

		return $value;
    }

    function _callDescription ( $value ) {
		$bank_setting_id = Common::hashEmptyField($value, 'KprBank.setting_id');
		$bankSetting = $this->User->Kpr->KprBank->BankSetting->getMerge(array(), $bank_setting_id, array(), 'BankSetting.id', array(
			'type' => 'all',
		));
		$bankSetting = $this->User->Kpr->KprBank->BankSetting->getMergeList($bankSetting, array(
			'contain' => array(
				'Bank',
				'BankProduct',
			),
		));

        $promo = Common::hashEmptyField($bankSetting, 'Bank.promo_text');
        $promo = Common::hashEmptyField($bankSetting, 'BankProduct.text_promo', $promo);

        $kprBankInstallments = Common::hashEmptyField($value, 'KprBankInstallment');
        $kprBankInstallment = end($kprBankInstallments);

        $note = Common::hashEmptyField($kprBankInstallment, 'KprBankCommission.note');
        $commission = Common::hashEmptyField($kprBankInstallment, 'KprBankCommission.value');
        $commission_percent = Common::hashEmptyField($kprBankInstallment, 'KprBankCommission.percent');
        $city_name = Common::hashEmptyField($kprBankInstallment, 'KprBankCommission.city_name');

        $promo_terms = KprCommon::_callTermsConditions(array(
			'commission' => $commission,
			'commission_percent' => $commission_percent,
			'city_name' => $city_name,
			'note' => $note,
		));

		$value['KprBank']['Description'] = array(
			'promo_name' => Common::hashEmptyField($bankSetting, 'BankProduct.name'),
			'promo_info' => $promo,
			'promo_terms' => $promo_terms,
		);

		return $value;
    }

    function _buildDataFiling ( $value ) {
		$kpr = Common::hashEmptyField($value, 'Kpr');
		$genderOptions = Configure::read('Global.Data.gender_options');;

		$kpr_application_id = Common::hashEmptyField($kpr, 'KprApplication.id');
		$name = Common::hashEmptyField($kpr, 'KprApplication.full_name');
		$email = Common::hashEmptyField($kpr, 'KprApplication.email');
		$no_hp = Common::hashEmptyField($kpr, 'KprApplication.no_hp');
		$gender_id = Common::hashEmptyField($kpr, 'KprApplication.gender_id');
		$status_marital = Common::hashEmptyField($kpr, 'KprApplication.status_marital');
		$birthplace = Common::hashEmptyField($kpr, 'KprApplication.birthplace');
		$birthday = Common::hashEmptyField($kpr, 'KprApplication.birthday');
		$address = Common::hashEmptyField($kpr, 'KprApplication.address');
		
		$customBirthday = Common::formatDate($birthday, 'd M Y', false);
		$gender = Common::hashEmptyField($genderOptions, $gender_id, '-');
		$birth = array();

		if( !empty($birthplace) ) {
			$birth[] = $birthplace;
		}
		if( !empty($birthday) ) {
			$birth[] = $birthday;
		}

		$property_id = Common::hashEmptyField($value, 'Property.id');
		$title = Common::hashEmptyField($value, 'Property.title');
		$mls_id = Common::hashEmptyField($value, 'Property.mls_id');
		$created = Common::hashEmptyField($value, 'Property.created');
		$change_date = Common::hashEmptyField($value, 'Property.change_date');
		$photo = Common::hashEmptyField($value, 'Property.photo');
		$property_type_id = Common::hashEmptyField($value, 'Property.property_type_id');
		$user_name = Common::hashEmptyField($value, 'User.full_name');

		$label = $this->RmProperty->getNameCustom($value);
		$price = $this->RmProperty->getPrice($value);
		$spec = $this->RmProperty->getSpesification($value);
		$created = Common::formatDate($created, 'd/m/Y H:i:s', false);
		$change_date = Common::formatDate($change_date, 'd/m/Y H:i:s');
		$status = $this->RmProperty->getStatus($value);
		$photo = Common::_callGenerateFullUrl($photo, Configure::read('__Site.property_photo_folder'), 'l');

		$value = array(
			'Kpr' => Common::_callSet($kpr, array(
				'id',
				'created',
			)),
			'Client' => array(
				'name' => $name,
				'email' => $email,
				'no_hp' => $no_hp,
				'gender' => $gender,
				'birthday_place' => implode(', ', $birth),
				'address' => $address,
			),
			'Property' => array(
				'id' => $property_id,
				'title' => $title,
				'label' => $label,
				'mls_id' => $mls_id,
				'photo' => $photo,
				'price' => $price,
				'spec' => $spec,
				'property_type_id' => $property_type_id,
				'created' => $created,
				'change_date' => $change_date,
				'publish_by' => $user_name,
				'status' => $status,
			),
			'PropertyAddress' => Common::hashEmptyField($value, 'PropertyAddress'),
		);

		return $value;
    }

    function formatMailblastBeforeSave($data){
    	$value        = array();
    	$get_all_user = isset($elements['get_all_user'])?$elements['get_all_user']:true;

    	$bank_id      = Common::hashEmptyField($data, 'BankProductCampaign.bank_id');
    	$list_product = Common::hashEmptyField($data, 'BankProductCampaign.list_product');

    	if (!empty($data) && !empty($list_product)) {
    		// serialize list product
    		$content_product['content_product'] = unserialize($list_product);

    		unset($data['BankProductCampaign']['list_product']);
    		$value['BankProductCampaign'] = array_merge($data['BankProductCampaign'], $content_product);
    	}

    	if ($get_all_user) {
    		$data_email_users = $this->controller->User->getData('list', array(
				'fields' => array(
					'User.id', 'User.email'
				)
			), array(
				'status' => 'active',
				'role'   => array('principle', 'admin', 'agent')
			));
			// debug($data_email_users);die();

    		$all_user['data_email_users'] = $data_email_users;
    		$value['BankProductCampaign'] = array_merge($value['BankProductCampaign'], $all_user);
    	}

    	// contain data bank
    	$value = $this->User->Kpr->Bank->getMerge($value, $bank_id);

    	return $value;
    }

	function save_queue_mailblast($data){
		$result = array();

		// S: init testing data
		$param_testing = $this->controller->params->params;
		$test     = Common::hashEmptyField($param_testing, 'named.test', 0);
		$to_email = Common::hashEmptyField($param_testing, 'named.to_email');
		$before_send = Common::hashEmptyField($param_testing, 'named.before_send', 0);
		// E: init testing data

		$data_to_blast = $this->formatMailblastBeforeSave($data);

		$email_from    = Common::hashEmptyField($data_to_blast, 'BankProductCampaign.email_from');
		$subject       = Common::hashEmptyField($data_to_blast, 'BankProductCampaign.subject_campaign');
		$email_bcc     = Common::hashEmptyField($data_to_blast, 'BankProductCampaign.data_email_users');
		$name          = __('Primers');
		$email         = Configure::read('__Site.email_from_prime');
		$template      = 'mailblast_product';

		$params            = $data_to_blast;
		$params['from']    = $email_from;
		$params['bcc']     = $email_bcc;

   		if ($test) {
   			// debug('you are here');
			$params['bcc'] = array(
				'printesting1@yopmail.com',
				'admintesting1@yopmail.com',
				'agentesting1@yopmail.com',
				$to_email,
			);

			if ($before_send) {
				$params['debug'] = 'view';
			}

   		}

   		// $emailSent = true;
   		$emailSent = $this->RmCommon->sendEmail($name, $email, $template, $subject, $params);

   		if ($emailSent) {
   			$result = array(
   				'status' => 'success',
   				'msg'    => __('Berhasil mengirim email. Mailblast dengan nama subjek %s', $subject),
   			);
   		} else {
   			$result = array(
   				'status' => 'error',
   				'msg'    => __('Gagal mengirim email. Mailblast dengan nama subjek %s', $subject),
   			);
   		}

		return $result;
   	}

   	// update queue mailblast
   	public function update_queue_mailblast($data){
		$result	= array(
			'status'	=> 'error', 
			'msg'		=> __('Tidak ada data untuk diupdate'), 
			'data'		=> $data, 
		);

		$DataToUpdate = Common::hashEmptyField($data, 'DataToUpdate');

		if($DataToUpdate){
			$url_primekpr = Common::hashEmptyField($data, 'Setting.link');
			$token        = Common::hashEmptyField($data, 'Setting.token');

			// target url
			$requestURL	  = sprintf('%s/Api/update_mailblast.json', $url_primekpr);

			$DataSetting['DataSetting'] = array(
				'url'   => $url_primekpr,
				'token' => $token,
			);
			$DataToPost['DataToUpdate'] = $DataToUpdate;

			// post data
			$postData = array_merge($DataSetting, $DataToPost);

			// option httprequest
			$options = array(
				// 'debug'	    => true,
				'method'    => 'POST',
			);

			$apiRequest	= Common::httpRequest($requestURL, $postData, $options);
			$apiStatus	= Common::hashEmptyField($apiRequest, 'status', 'error');

			if($apiStatus == 'success'){
				$result = Common::hashEmptyField($apiRequest, 'response', array());
			}
			else{
				$result = $apiRequest;
			}

		}

		return $result;
	}

}
?>