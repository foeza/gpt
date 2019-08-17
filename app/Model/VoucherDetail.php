<?php
class VoucherDetail extends AppModel {
	public $validate = array(
		'membership_package_id' => array(
			'notempty' => array(
				'rule'		=> array('notempty'),
				'message'	=> 'Mohon pilih paket membership'
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Paket Membership tidak valid'
			),
			'uniquePackage' => array(
				'rule' => array('uniquePackage'),
				'message' => 'Paket Membership tidak boleh sama', 
			), 
		),
		'discount_type'	=> array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukan Jenis Potongan'
			),
		),
		'discount_value' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Potongan harus berupa angka'
			),
			'checkValue' => array(
				'rule' => array('checkValue'),
				'message' => 'Potongan harus berupa angka dan lebih besar dari 0 (Untuk Potongan dengan persentase tidak boleh lebih dari 100%).'
			)
		),
	);

	public function uniquePackage($data){
		$packageID		= $this->filterEmptyField($this->data, 'VoucherDetail', 'membership_package_id');
		$arrPackages	= $this->filterEmptyField($this->data, 'VoucherDetail', 'selected_package_id');

		unset($this->data['VoucherDetail']['selected_package_id']);

		if($arrPackages){
		//	count masing-masing duplikat
			$arrCount	= array_count_values($arrPackages);
			$counter	= $this->filterEmptyField($arrCount, $packageID);

		//	ada duplikat, cuma bukan salah yang ini
			if($counter <= 1){
				return true;
			}
			else{
				if($arrPackages && count($arrPackages) == count(array_unique($arrPackages))){
					return true;
				}
				else{
					return false;
				}
			}
		}
		else{
			return true;
		}
	}

	public $belongsTo = array(
		'Voucher' => array(
			'foreignKey' => 'voucher_id',
		),
		'MembershipPackage' => array(
			'foreignKey' => 'membership_package_id',
		)
	);

	function getData($find = 'all', $options = array()){
		$defaultOptions = array(
			'condition' => array(
				'VoucherDetail.status'		=> 1,
				'VoucherDetail.is_deleted'	=> 0,
			),
		);

		if(!empty($options)){
			$defaultOptions = array_merge($defaultOptions, $options);
		}

		if($find == 'paginate'){
			$result = $defaultOptions;
		}
		else{
			$result = $this->find($find, $defaultOptions);
		}

        return $result;
	}

	function getMerge($data, $voucherID, $membershipPackageID = NULL){
		if( $data && empty($data['VoucherDetail']) && !empty($voucherID) ){
			$conditions	= array(
				'VoucherDetail.voucher_id'	=> $voucherID,
				'VoucherDetail.status'		=> 1,
				'VoucherDetail.is_deleted'	=> 0,
			);

			if($membershipPackageID){
				$conditions	= array_merge($conditions, array('VoucherDetail.membership_package_id' => $membershipPackageID));
			}

			$record	= $this->getData('all', array('conditions' => $conditions));
			$temp	= array('VoucherDetail' => array());

			if($record){
				foreach($record as $key => $value){
					$temp['VoucherDetail'][] = $value['VoucherDetail'];
				}

				$record	= $temp;
				$data	= array_merge($data, $record);
			}
		}

		return $data;
	}

	public function checkValue(){
		$applyTo		= empty($this->data['VoucherDetail']['apply_to']) ? 'all' : $this->data['VoucherDetail']['apply_to'];
		$discountType	= empty($this->data['VoucherDetail']['discount_type']) ? 'nominal' : $this->data['VoucherDetail']['discount_type'];
		$discountValue	= empty($this->data['VoucherDetail']['discount_value']) ? 0 : $this->data['VoucherDetail']['discount_value'];

		if($discountValue <= 0){
			return FALSE;
		}
		else{
			if($discountType == 'percentage' && $discountValue > 100){
				return FALSE;
			}
		}

		return TRUE;
	}
}
?>