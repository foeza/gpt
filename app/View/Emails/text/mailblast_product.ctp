<?php
		// debug($params);die();
		$params   = !empty($params)?$params:false;
		$bankLogo = Common::hashEmptyField($params, 'Bank.logo');

		if(!empty($bankLogo)){
			$customLogo = $bankLogo;
		}else{
			$customLogo = '/img/primesystem.png';
		}

?>