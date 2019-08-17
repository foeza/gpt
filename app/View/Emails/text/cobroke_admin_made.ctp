<?php
		$mls_id 						= Common::hashEmptyField($params, 'Property.mls_id');
		$title 							= Common::hashEmptyField($params, 'Property.title');
		$commission 					= Common::hashEmptyField($params, 'Property.commission');
		$type_co_broke_commission 		= Common::hashEmptyField($params, 'Property.type_co_broke_commission');
		$co_broke_commision 			= Common::hashEmptyField($params, 'Property.co_broke_commision');
		$type_price_co_broke_commision 	= Common::hashEmptyField($params, 'Property.type_price_co_broke_commision');

		$property_cobroke_commission = $this->CoBroke->commissionName($co_broke_commision, $type_co_broke_commission, $type_price_co_broke_commision);

		$senderName = Common::hashEmptyField($params, 'User', 'full_name');

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

		printf(__('Properti Anda dengan id "%s" telah dijadikan Co-Broke oleh Admin/Principal Anda'), $title_properti);
		echo "\n\n";

		printf(__('Nama Agen : %s'), $senderName); echo "\n";
		printf(__('Properti : %s - %s'), $title_properti, $url); echo "\n";
		printf(__('Komisi Agen : %s%% dari Penjualan Properti'), $commission); echo "\n";
		printf(__('Komisi Broker : %s'), $property_cobroke_commission); echo "\n";
?>