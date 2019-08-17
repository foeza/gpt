<?php
App::uses('CommonBehavior', 'Model');

	
class KprBehavior extends CommonBehavior {

    function generateCode(model $model) {
		$prefix = 'RKU';

		$last_order = $model->getData('first', array(
			'fields'=> array(
				'RIGHT(Kpr.code, 5) as code'
			),
			'conditions'=> array(
				'LEFT(Kpr.code, 11)' => $prefix.date('Ymd'),
			),
			'order'=> array(
				'Kpr.code' => 'DESC',			
			),
		), array(
			'status' => 'all',
			'company' => false,
		));

		$new_code = $prefix.date('Ymd');

		if(!empty($last_order[0]['code'])) {
			$new_code .= str_pad((int)$last_order[0]['code']+1, 5, '0', STR_PAD_LEFT);
		} else {
			$new_code .= str_pad(1, 5, '0', STR_PAD_LEFT);
		}

		return $new_code;
	}

	function getFilterBanks(model $model, $bank_ids, $checked = array(), $options = array()){
		$property_type_id 	= Common::hashEmptyField($options, 'property_type_id');
		$price 	= Common::hashEmptyField($options, 'price');
		
		//get bank list 
		$bank_active = $model->Bank->getData('list', array(
			'fields' => array('id', 'id'),
		));

		$bank_list = $model->KprBank->Bank->BankSetting->getData('list', array(
			'conditions' => array(
				'BankSetting.id' => $checked,
				'BankSetting.bank_id' => $bank_active,
			),
			'fields' => array('bank_id', 'bank_id'),
		), array(
			'type' => 'product',
		));

		$options = array(
			'BankProduct.bank_id' => $bank_active,
			'BankProduct.bank_id NOT' => $bank_ids
		);

		$values = $model->KprBank->Bank->BankProduct->getData('all', array(
			'conditions' => $options,
		), array(
			'status' => 'publish',
			'price' => $price,
			'property_type_id' => $property_type_id,
			'company_id' => Configure::read('Principle.id'),
		));

		$values = $this->getMergeList($model->KprBank->Bank->BankProduct, $values, array(
			'contain' => array(
				'BankSetting' => array(
					'elements' => array(
						'type' => 'all'
					),
				),
				'Bank',
			),
		));

		// checked disabled
		if(!empty($values) && !empty($bank_list)){
			foreach($values AS $key => $value){
				$bank_id = $this->filterEmptyField($model, $value, 'Bank', 'id');
				$bank_setting_id = $this->filterEmptyField($model, $value, 'BankSetting', 'id');

				if(!in_array($bank_setting_id, $checked) && in_array($bank_id, $bank_list)){
					$values[$key]['BankSetting']['disabled'] = 'disabled';
				}
			}
		}
		// 
		return $values;
	}

	function _callDataKpr ( model $model, $data ) {
		$dataKpr = $model->filterEmptyField($data, 'Kpr', false, array());
		$dataKpr = array_filter($dataKpr);
		$credit_total = $model->filterEmptyField($dataKpr, 'credit_total');
		$dp = $model->filterEmptyField($dataKpr, 'dp');
		$down_payment = $model->filterEmptyField($dataKpr, 'down_payment');
		$property_price = $model->filterEmptyField($dataKpr, 'property_price');

		return array(
			'credit_total' => $credit_total,
			'dp' => $dp,
			'down_payment' => $down_payment,
			'property_price' => $property_price,
		);
	}

	function callGetBank ( model $model, $data , $checked = false) {
		$data = $model->dataConverter($data, array(
			'price' => array(
				'Kpr' => array(
					'property_price',
				),
			),
		));
		
		$dataKpr = $model->filterEmptyField($data,'Kpr');
		$property_type_id = $model->filterEmptyField($data,'Property','property_type_id');
		$region_id = $model->filterEmptyField($data,'PropertyAddress','region_id');
		$city_id = $model->filterEmptyField($data,'PropertyAddress','city_id');
		$price = $model->filterEmptyField($data,'Kpr','property_price');

        $bankKprs = $model->filterEmptyField($data, 'KprBank');
        $bank_ids = Set::combine($bankKprs,'{n}.KprBank.bank_id', '{n}.KprBank.bank_id');

		$banks = $this->getFilterBanks($model, $bank_ids, $checked, array(
			'property_type_id' => $property_type_id,
			'price' => $price,
		));
		$banks = $model->KprBank->Bank->BankCommissionSetting->getKomisi($banks, array(
			'property_type_id' => $property_type_id,
			'region_id' => $region_id,
			'city_id' => $city_id,
			'price' => $price,
			'data' => $data,
		));
		
		$api = Configure::read('__Site.is_rest');

		if( !empty($api) ) {
			$banks = Common::hashEmptyField($banks, 'qualify');
		}

		return $banks;
	}

	function mergeOptionApplication(model $model, $data, $modelName){
		$value = array();
		$value_model = $this->filterEmptyField($model, $data, $modelName);

		if(!empty($value_model) && !empty($modelName)){
			$region_id = $this->filterEmptyField($model, $data, $modelName, 'region_id');
			$city_id = $this->filterEmptyField($model, $data, $modelName, 'city_id');
			$subarea_id = $this->filterEmptyField($model, $data, $modelName, 'subarea_id');
			$job_type_id = $this->filterEmptyField($model, $data, $modelName, 'job_type_id');

			$value = $model->JobType->getMerge($value, $job_type_id);
			$value = $model->Region->getMerge($value, $region_id, 'Region', array(
				'cache' => array(
					'name' => __('Region.%s', $region_id),
				),
			));
			$value = $model->City->getMerge($value, $city_id, 'City', 'City.id', array(
				'cache' => __('City.%s', $city_id),
			));
			$value = $model->Subarea->getMerge($value, $subarea_id, 'Subarea', 'Subarea.id', array(
				'cache' => __('Subarea.%s', $subarea_id),
				'cacheConfig' => 'subareas',
			));
			if(!empty($value)){
				$data[$modelName] = array_merge($data[$modelName],$value);
			}
		}
		
		return $data;
	}

	function mergeApplication(model $model, $value, $id, $index = false){
		if(isset($value)){
			if(empty($index)){
				$value = $model->getMerge( $value, $id, array(
					'optionConditions' => array(
						'KprApplication.parent_id' => NULL,
					),
					'fieldName' => 'KprApplication.kpr_id'
				));
				$value = $this->mergeOptionApplication($model, $value, 'KprApplication');
				$kpr_application_id  = $model->filterEmptyField($value, 'KprApplication', 'id');
				## GET SPOUSE PARTICULAR
				$value = $model->getMerge( $value, $kpr_application_id, array(
					'virtualModel' => 'KprApplicationParticular',
					'fieldName' => 'KprApplication.parent_id', 
				));	
				$value = $this->mergeOptionApplication($model, $value, 'KprApplicationParticular');
			}else{
				$vals = $model->getData('all', array(
					'conditions' => array(
						'KprApplication.kpr_id' => $id,
					),
					'order' => array(
						'KprApplication.parent_id' => 'ASC',
					),
				));
				if(!empty($vals)){
					foreach($vals AS $key => $val){
						$dataApp['KprApplication'][] = $val['KprApplication'];
					}
					$value = array_merge($value, $dataApp);
				}
			}
			
		}
		return $value;
	}

	function bank_sync( Model $model, $datas){
		$flag = true;
		if(!empty($datas)){
			foreach($datas AS $key => $data){
				 if(!$model->save($data, FALSE)){
				 	$flag = false;
				 }
			}
		}
		return $flag;
	}

	function calcLoan ( Model $model, $price, $bunga_kpr = false ) {
		$bunga_kpr_persen = $this->_getBungaKPRPersen( $model, $bunga_kpr );
		return $price * $bunga_kpr_persen;
	}

	function _getBungaKPRPersen ( Model $model, $bunga_kpr = false ) {
        $bunga_kpr = !empty($bunga_kpr)?$bunga_kpr:Configure::read('__Site.bunga_kpr');
        return ( 100 - $bunga_kpr ) / 100;
    }

	function calByPercent(Model $model, $price, $percent){
		return round(($percent/100)*$price, 0);
	}

	function MergeEmailKPR(model $model, $kpr_bank_id){
		$value = array();
		if(!empty($kpr_bank_id)){
			$value = $model->getData('first', array(
				'conditions' => array(
					'KprBank.id' => $kpr_bank_id,
				),
			));
			$kpr_id = $this->filterEmptyField($model, $value, 'KprBank', 'kpr_id');
			$bank_id = $this->filterEmptyField($model, $value, 'KprBank', 'bank_id');

			$value = $model->Kpr->getMerge($value, $kpr_id, 'Kpr.id', array(
				'elements' => array(
					'company' => false,
				),
			));
			$value = $model->Bank->getMerge($value, $bank_id);
			$value = $model->Bank->BankContact->getMerge($value, $bank_id);

			$user_id = $this->filterEmptyField($model, $value, 'Kpr', 'user_id');
			$agent_id = $this->filterEmptyField($model, $value, 'Kpr', 'agent_id');
			$property_id = $this->filterEmptyField($model, $value, 'Kpr', 'property_id');
			
			$value = $model->Kpr->Property->getMerge($value, $property_id);
			$value = $model->Kpr->Property->PropertyAddress->getMerge($value, $property_id, true);
			$value = $model->Kpr->User->getMerge($value, $user_id, true, 'UserClient');
			$value = $model->Kpr->User->getMerge($value, $agent_id, true, 'Agent');
			$parent_id = $this->filterEmptyField($model, $value, 'Agent', 'parent_id');
			$value = $model->Kpr->User->UserCompanyConfig->getMerge($value, $parent_id);
		}
		return $value;
	}

	function creditFix(model $model, $amount, $rate, $year=20){
		
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
			return $mortgage;
		}
	}

	function callGetBankDeveloper ( model $model, $data , $checked = false) {
		$price = Common::hashEmptyField($data, 'Kpr.property_price');

        $bankKprs = $model->filterEmptyField($data, 'KprBank');
        $bank_ids = Set::combine($bankKprs,'{n}.KprBank.bank_id', '{n}.KprBank.bank_id');

		$banks = $this->getFilterBanks($model, $bank_ids, $checked);
		$banks = $model->KprBank->Bank->BankCommissionSetting->getKomisiDeveloper($banks, array(
			'price' => $price,
		));
		
		return $banks;
	}
}
?>