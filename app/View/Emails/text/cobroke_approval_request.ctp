<?php 
		$input_data = Common::hashEmptyField($params, 'input_data');
		$approval 	= Common::hashEmptyField($params, 'approval');

		$senderName = Common::hashEmptyField($params, 'User.full_name');

		$mls_id 	= Common::hashEmptyField($params, 'Property.mls_id');
		$title 		= Common::hashEmptyField($params, 'Property.title');

		$code 		= Common::hashEmptyField($params, 'CoBrokeProperty.code');
		$decline 	= Common::hashEmptyField($input_data, 'CoBrokeProperty.decline');
		$decline_reason = Common::hashEmptyField($input_data, 'CoBrokeProperty.decline_reason');

		$greet 		= Common::hashEmptyField($params, 'subject');
		$request 	= Common::hashEmptyField($params, 'request');
		
		if(!empty($request)){
			$address = $this->Rumahku->filterEmptyField($params, 'PropertyAddress', 'address');

			$greet = sprintf(__('Agen %s telah mengajukan properti "%s, %s" menjadi properti Co-Broke.<br>Harap lakukan peninjauan pada properti yang diajukan'), $senderName, $title, $address);
		}else if(!empty($approval) && !empty($decline)){
			$greet .= sprintf(__(' dengan alasan : %s'), $decline_reason);
		}

		$title_properti = sprintf('%s - #%s', $title, $mls_id);

		$label = $this->Property->getNameCustom($params);
		$slug = $this->Rumahku->toSlug($label);

		$url = $this->Html->url(array(
			'controller' => 'properties', 
			'action'	 => 'detail',
			'mlsid'		 => $mls_id,
			'slug'		 => $slug, 
			'admin'		 => FALSE,
		), true);

		echo $greet."\n\n";
		printf(__('Nama Agen : %s'), $senderName);
		echo "\n";
		printf(__('Properti : %s'), $title_properti);
		echo "\n";
?>