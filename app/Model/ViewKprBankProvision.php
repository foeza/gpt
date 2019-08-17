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
class ViewKprBankProvision extends AppModel {
	public $useTable = 'view_kpr_bank_provision';

	public $belongsTo = array(
		'Kpr' => array(
			'className' => 'Kpr',
			'foreignKey' => 'kpr_id', 
		),
	); 
	
}