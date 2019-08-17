<?php
		$decline_name = Common::hashEmptyField($params, 'Decline.full_name');
		$assign_name = Common::hashEmptyField($params, 'Assign.full_name');

		$is_property = Common::hashEmptyField($params, 'UserActivedAgent.is_property');
		$userAgentDetails = Common::hashEmptyField($params, 'UserActivedAgentDetail');

		$property_count = Common::hashEmptyField($params, 'UserActivedAgent.property_count');
		$client_count = Common::hashEmptyField($params, 'UserActivedAgent.client_count');

		echo __('%s telah diaktifkan kembali', $decline_name);
	  	echo "\n\n";
	  	echo __('Properti dan klien milik %s yang sebelumnya dialihkan kepada anda, saat ini telah dikembalikan', $decline_name);
	  	echo "\n\n\n";

	  	if($property_count){
		  	echo __('Berikut %s properti yang dikembalikan kepada agen %s :', $property_count, $decline_name);	
		  	echo "\n";
		  	echo $this->Html->url(array(
		  		'controller' => 'properties',
		  		'action' => 'index',
		  		'admin' => true,
		  	), true);
		  	echo "\n\n";
	  	}

	  	if($client_count){
	  		echo __('Berikut %s klien yang dikembalikan kepada agen %s :', $client_count, $decline_name);	
		  	echo "\n";
		  	echo $this->Html->url(array(
		  		'controller' => 'users',
		  		'action' => 'client_info',
		  		'admin' => true,
		  	), true);
		  	echo "\n";
	  	}

	  	echo __('Terhitung sejak email ini dikirimkan, anda tidak mempunyai hak terhadap data tersebut');
	  	echo "\n";
?>