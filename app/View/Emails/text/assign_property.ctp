<?php
		$id = Common::hashEmptyField($params, 'id');
		$decline_name = Common::hashEmptyField($params, 'Decline.User.full_name');
		$assign_name = Common::hashEmptyField($params, 'Assign.User.full_name');

		$property_count = Common::hashEmptyField($params, 'UserActivedAgent.property_count');
		$client_count = Common::hashEmptyField($params, 'UserActivedAgent.client_count');

	  	$userAgentDetails = Common::hashEmptyField($params, 'UserActivedAgentDetail');

	  	echo __('Anda telah mendapatkan data properti dan klien dari agen %s', $decline_name);
	  	echo "\n\n";
	  	echo __('Agen %s telah dinon-aktifkan. Data properti yang berkaitan dengan agen tersebut saat ini telah dialihkan kepada Anda. Namun apabila agen %s diaktifkan kembali, maka data akan dikembalikan kepada agen %s seperti sedia kala.', $decline_name, $decline_name, $decline_name);
	  	echo "\n\n";

	  	if($property_count){
		  	echo __('Berikut %s properti yang alihkan kepada Anda', $property_count);  
		  	echo "\n";
		  	echo $this->Html->url(array(
		  		'controller' => 'properties',
		  		'action' => 'index',
		  		'status' => 'assign',
                'document_id' => $id,
		  		'admin' => true,
		  	), true);
		  	echo "\n\n";
	  	}

	  	if($client_count){
	  		echo __('Berikut %s klien yang alihkan kepada Anda', $client_count);  	
	  		echo "\n";
	  		echo $this->Html->url(array(
		  		'controller' => 'users',
		  		'action' => 'client_info',
		  		'admin' => true,
		  	), true);
		  	echo "\n";
	  	}

?>