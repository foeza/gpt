<?php
class LotpropertyMarketing extends AppModel {
	var $name = 'LotpropertyMarketing';

	function getMerge ( $data, $mktg ) {
		$agent = $this->find('first', array(
			'conditions' => array(
				'LotpropertyMarketing.marketingid' => $mktg,
			),
		));

		if( !empty($agent) ) {
			$data = array_merge($data, $agent);
		}

		return $data;
	}
}
?>