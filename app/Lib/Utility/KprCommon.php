<?php
App::uses('CakeText', 'Utility');

class KprCommon {
    public static function _callCalcNotary ( $data, $params = 0 ,$model = 'BankSetting') {
        $price = Common::hashEmptyField($data, $model.'.property_price', 0);
        $price = Common::hashEmptyField($params, 'price', $price);

        $loan_price = Common::hashEmptyField($data, $model.'.loan_price', 0);
        $loan_price = Common::hashEmptyField($params, 'loan_price', $loan_price);

        $sale_purchase_certificate_persen = Common::hashEmptyField($data, $model.'.sale_purchase_certificate', 0);
        $transfer_title_charge_persen = Common::hashEmptyField($data, $model.'.transfer_title_charge', 0);
        $credit_agreement = Common::hashEmptyField($data, $model.'.credit_agreement', 0);
        $skmht_persen = Common::hashEmptyField($data, $model.'.letter_mortgage', 0);
        $apht_persen = Common::hashEmptyField($data, $model.'.imposition_act_mortgage', 0);
        $ht_persen = Common::hashEmptyField($data, $model.'.mortgage', 0);
        $other_certificate_persen = Common::hashEmptyField($data, $model.'.other_certificate', 0);

        $category_sale_purchase_certificate = Common::hashEmptyField($data, $model.'.category_sale_purchase_certificate');
        $category_transfer_title_charge = Common::hashEmptyField($data, $model.'.category_transfer_title_charge');
        $category_letter_mortgage = Common::hashEmptyField($data, $model.'.category_letter_mortgage');
        $category_imposition_act_mortgage = Common::hashEmptyField($data, $model.'.category_imposition_act_mortgage');
        $category_mortgage = Common::hashEmptyField($data, $model.'.category_mortgage');
        $category_other_certificate = Common::hashEmptyField($data, $model.'.category_other_certificate');
        $category_credit_agreement = Common::hashEmptyField($data, $model.'.category_credit_agreement');

        $param_sale_purchase_certificate = Common::hashEmptyField($data, $model.'.param_sale_purchase_certificate');
        $param_transfer_title_charge = Common::hashEmptyField($data, $model.'.param_transfer_title_charge');
        $param_letter_mortgage = Common::hashEmptyField($data, $model.'.param_letter_mortgage');
        $param_imposition_act_mortgage = Common::hashEmptyField($data, $model.'.param_imposition_act_mortgage');
        $param_mortgage = Common::hashEmptyField($data, $model.'.param_mortgage');
        $param_other_certificate = Common::hashEmptyField($data, $model.'.param_other_certificate');
        $param_credit_agreement = Common::hashEmptyField($data, $model.'.param_credit_agreement');

        if($param_sale_purchase_certificate == 'price'){
            $sale_purchase_certificate = Common::_callPercentageValue($price, $sale_purchase_certificate_persen, $category_sale_purchase_certificate);
        }else{
            $sale_purchase_certificate = Common::_callPercentageValue($loan_price, $sale_purchase_certificate_persen, $category_sale_purchase_certificate);
        }

        if($param_transfer_title_charge == 'price'){
            $transfer_title_charge = Common::_callPercentageValue($price, $transfer_title_charge_persen, $category_transfer_title_charge);
        }else{
            $transfer_title_charge = Common::_callPercentageValue($loan_price, $transfer_title_charge_persen, $category_transfer_title_charge);
        }
        
        if($param_letter_mortgage == 'price'){
            $SKMHT = Common::_callPercentageValue($price, $skmht_persen, $category_letter_mortgage);
        }else{
            $SKMHT = Common::_callPercentageValue($loan_price, $skmht_persen, $category_letter_mortgage);
        }

        if($param_imposition_act_mortgage == 'price'){
            $APHT = Common::_callPercentageValue($price, $apht_persen, $category_imposition_act_mortgage);
        }else{
            $APHT = Common::_callPercentageValue($loan_price, $apht_persen, $category_imposition_act_mortgage);
        }

        if($param_mortgage == 'price'){
            $HT = Common::_callPercentageValue($price, $ht_persen, $category_mortgage);
        }else{
            $HT = Common::_callPercentageValue($loan_price, $ht_persen, $category_mortgage);
        }

        if($param_other_certificate == 'price'){
            $other_certificate = Common::_callPercentageValue($price, $other_certificate_persen, $category_other_certificate);
        }else{
            $other_certificate = Common::_callPercentageValue($loan_price, $other_certificate_persen, $category_other_certificate);
        }

        if($param_credit_agreement == 'price'){
            $credit_agreement = Common::_callPercentageValue($price, $credit_agreement, $category_credit_agreement);
        }else{
            $credit_agreement = Common::_callPercentageValue($loan_price, $credit_agreement, $category_credit_agreement);
        }
        
        $total = $sale_purchase_certificate + $transfer_title_charge + $credit_agreement + $SKMHT + $APHT + $HT + $other_certificate;
        $total = round($total, 0);

        return (in_array($total, array(0, FALSE)))?null:$total;
        
    }

	public static function _callTotalPaymentKPR($value, $model = 'BankSetting') {
		$down_payment = Common::hashEmptyField($value, $model.'.down_payment');
		$appraisal = Common::hashEmptyField($value, $model.'.appraisal');
		$insurance = Common::hashEmptyField($value, $model.'.insurance');
		$commission = Common::hashEmptyField($value, $model.'.commission');
		$administration = Common::hashEmptyField($value, $model.'.administration');
		$total_first_credit = Common::hashEmptyField($value, $model.'.total_first_credit');

		$total_notary = KprCommon::_callCalcNotary($value, false, $model);
        $grandtotal = round($down_payment + $appraisal + $administration + $insurance + $commission + $total_notary + $total_first_credit,0);

        $value[$model]['total_first_credit'] = round($total_first_credit, 0);
        $value[$model]['notary'] = !empty($total_notary)?$total_notary:0;
        $value[$model]['grandtotal'] = $grandtotal;

        return $value;
	}

	public static function _callPermissionEditDocument( $kpr_document_status, $kpr_bank_document_status ) {
		$deny_edit_document = Configure::read('__Site.Global.Variable.KPR.deny_edit_document');
		$flag_no_edit = !in_array($kpr_document_status, $deny_edit_document);

		if( !is_array($kpr_bank_document_status) ) {
			$kpr_bank_document_status = array(
				$kpr_bank_document_status,
			);
		}
		
		if($flag_no_edit && in_array('process', $kpr_bank_document_status)){
        	return 1;
		} else {
        	return 0;
		}
	}

    public static function getFilterCommissionAgent($val){
        $kpr_bank = Common::hashEmptyField($val, 'KprBank');

        if(!empty($kpr_bank)){
            $agentCommission = !empty($kpr_bank['agentCommission'])?$kpr_bank['agentCommission']:array();
            $commission = Common::hashEmptyField($agentCommission, 'value');
            $percent = Common::hashEmptyField($agentCommission, 'percent', 0);
            $termsAddOn = Common::hashEmptyField($agentCommission, 'note');
            $region_name = Common::hashEmptyField($agentCommission, 'region_name');
            $city_name = Common::hashEmptyField($agentCommission, 'city_name');
        }else{
            $bankCommissionSetting = Common::hashEmptyField($val, 'BankCommissionSetting');
            $commission = Common::hashEmptyField($bankCommissionSetting, 'commission');
            $percent = Common::hashEmptyField($bankCommissionSetting, 'percent');
            $termsAddOn = Common::hashEmptyField($bankCommissionSetting, 'description');
            $region_name = Common::hashEmptyField($bankCommissionSetting, 'Region', 'name');
            $city_name = Common::hashEmptyField($bankCommissionSetting, 'City', 'name');
        }

        return array(
            'commission' => $commission,
            'percent' => $percent,
            'termsAddOn' => $termsAddOn,
            'region_name' => $region_name,
            'city_name' => $city_name,
        );
        
    }

    public static function _callTermsConditions ( $params = null ) {
        $terms_note = Configure::read('__Site.Global.Variable.KPR.terms_and_conditions.note');
        $terms_notice = Configure::read('__Site.Global.Variable.KPR.terms_and_conditions.notice');
        $terms_without_provision = Configure::read('__Site.Global.Variable.KPR.terms_and_conditions.without_provision');

        $promo_terms = array();
        $note = Common::hashEmptyField($params, 'note');
        $commission = Common::hashEmptyField($params, 'commission');
        $commission_percent = Common::hashEmptyField($params, 'commission_percent');
        $city_name = Common::hashEmptyField($params, 'city_name');
        $provision = Common::hashEmptyField($params, 'provision', true, array(
            'isset' => true,
        ));

        if( !empty($note) ) {
            $promo_terms[] = $note;
        }

        if( !empty($provision) ) {
            if(!empty($commission)){
                $commission = Common::getCurrencyPrice($commission);
                $view_commission = __('%s%s (%s)', $commission_percent,__('%'), $commission);

                if( !empty($city_name) ) {
                    $city_name = __('di kota %s', $city_name);
                }
                    
                $commission_text = __('Provisi pengajuan KPR %s sebesar %s', $city_name, $view_commission);                
                $commission_text .= PHP_EOL.__('Catatan: Besar Provisi dapat berubah sewaktu-waktu, sesuai plafon yang disetujui oleh Bank');
                $promo_terms[] = $commission_text;
            }else {
                $promo_terms[] = $terms_without_provision;
            }
        }

        $promo_terms = array_merge($promo_terms, $terms_note);
        $promo_terms[] = $terms_notice;

        return $promo_terms;
    }
}