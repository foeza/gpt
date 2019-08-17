<?php
App::uses('AppModel', 'Model');
/**
 * ViewKprBankProvision Model
 *
 * @property ViewKprBankProvision $ViewKprBankProvision
 * @property Project $Project
 * @property Company $Company
 * @property ViewBookingReportDetail $ViewBookingReportDetail
 */
class ViewPropertyPayment extends AppModel {
	public $useTable = 'view_property_payment';

	public $belongsTo = array(
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'id', 
		),
	); 

	function getData( $find = 'all', $options = array(), $elements = array() ){
		$default_options = array(
			'contain'		=> array(), 
			'conditions'	=> array(),
			'order'			=> array(),
			'field' => array()
		);

        return $this->merge_options($default_options, $options, $find);
	}
	
}