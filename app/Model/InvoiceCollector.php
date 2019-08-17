<?php
App::uses('AppModel', 'Model');
/**
 * InvoiceCollector Model
 *
 * @property InvoiceCollectorProfile $InvoiceCollectorProfile
 * @property Project $Project
 * @property Company $Company
 * @property InvoiceCollectorDetail $InvoiceCollectorDetail
 */
class InvoiceCollector extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'company_id'	=> array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan company_id',
			),
		),
		'project_id'	=> array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan project_id',
			),
		),
		'type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan tipe dari invoice',
			),
		),
		'invoice_number' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan kode invoice',
			),
		),
	);

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'company_id',
		),
	);

	function doSave($dataSave = array(), $type = 'PrimeDeveloper'){
		$principle = Configure::read('Config.Company.data.User.id');
		
		$data_default = array(
			'company_id' => $principle,
		);

		if( !empty($dataSave) ) {
			$data_default = array_merge($data_default, $dataSave);
		}

		$data_default['type'] = $type;

		$data['InvoiceCollector'] = $data_default;

		$flag = $this->saveAll($data, array(
			'validate' => 'only', 
		));
		
		if($flag){
			return $this->saveAll($data);
		}
		else{
			return false;
		}
	}

	function getData( $find, $options = false, $elements = array() ){
		$principle = Configure::read('Config.Company.data.User.id');
		$kpr = Common::hashEmptyField($elements, 'kpr');

		$default_options = array(
			'conditions'=> array(
				'InvoiceCollector.company_id' => $principle
			),
			'order' => array(),
			'contain' => array(),
			'fields' => array(),
			'group' => array(),
		);

		if( !empty($kpr) ) {
			$default_options['conditions']['InvoiceCollector.is_kpr'] = true;
			$default_options['conditions']['InvoiceCollector.on_progress_kpr'] = false;
			$default_options['conditions']['InvoiceCollector.is_payment_confirm'] = true;
		}

		return $this->merge_options($default_options, $options, $find);
	}

	function getMerge( $data, $invoice_number ) {
		if( !empty($invoice_number) ) {
			$invoice_data = $this->getData('first', array(
				'conditions' => array(
					'InvoiceCollector.invoice_number' => $invoice_number
				)
			));

			if( !empty($invoice_data) ) {
				$data = array_merge($data, $invoice_data);
			}
		}

		return $data;
	}

	function _callInvoicePaid ($invoice_number) {
        return $this->updateAll(array(
            'InvoiceCollector.is_payment_confirm' => 1,
            'InvoiceCollector.modified' => "'".date('Y-m-d H:i:s')."'",
        ), array(
            'InvoiceCollector.invoice_number' => $invoice_number,
        ));
	}
}