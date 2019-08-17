<?php 
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');

		if( !empty($mls_id) ) {
       	 	$title = $this->Rumahku->filterEmptyField($params, 'Property', 'title');
	        $label = $this->Property->getNameCustom($params);
       	 	$price = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'property_price');

       	 	if( !empty($price) ) {
	        	$price = $this->Rumahku->getCurrencyPrice($price);
	        } else {
	        	$price = $this->Property->getPrice($params);
	        }

	        echo $label;
	        echo "\n";

			echo $title;
	        echo "\n";

			echo $price;
	        echo "\n\n";
		}
?>