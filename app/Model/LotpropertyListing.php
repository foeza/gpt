<?php
class LotpropertyListing extends AppModel {
	var $name = 'LotpropertyListing';

	var $belongsTo = array(
		'LotpropertyTipeproperti' => array(
			'className' => 'LotpropertyTipeproperti',
			'foreignKey' => 'tipelistingid',
		),
		'LotpropertyLokasi' => array(
			'className' => 'LotpropertyLokasi',
			'foreignKey' => 'lokasiid',
		),
		'LotpropertyLokasisub' => array(
			'className' => 'LotpropertyLokasisub',
			'foreignKey' => 'lokasisubid',
		),
	);
}
?>