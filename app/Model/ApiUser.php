<?php
class ApiUser extends AppModel {
	var $name = 'ApiUser';

	function get_access($apikey, $api_secret){
		$result = false;

		$cek = $this->find('count', array(
			'conditions' => array(
				'ApiUser.user_key' => $apikey,
				'ApiUser.secret_key' => $api_secret
			)
		));

		if(!empty($cek) && $cek > 0){
			$result = true;
		}

		return $result;
	}
}
?>