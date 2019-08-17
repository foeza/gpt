<?php 
        $property_path = Configure::read('__Site.property_photo_folder');
        $property = $this->Rumahku->filterEmptyField($params, 'Property');
        $expired_day = $this->Rumahku->filterEmptyField($params, 'expired_day');

        $otherUrl = $this->Html->url(array(
            'controller'=> 'properties', 
            'action' => 'index',
            'status' => 'incoming-inactive',
            'admin'=> true,
        ), true);

		printf(__('Properti Anda tidak terupdate/refresh selama %s hari, dalam 7 hari kedepan properti Anda akan Kami non-aktifkan.'), $expired_day);
		echo "\n";
		echo __('Mohon update/refresh kembali apabila iklan tersebut masih available: ');
		echo "\n\n";

		if( !empty($property) ) {
			foreach ($property as $key => $value) {
		        $title = $this->Rumahku->filterEmptyField($value, 'Property', 'title');
		        $mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');

		        $label = $this->Property->getNameCustom($value);
		        $slug = $this->Rumahku->toSlug($label);
		        $price = $this->Property->getPrice($value);

		        $url = $this->Html->url(array(
		            'controller'=> 'properties', 
		            'action' => 'index',
		            'mlsid' => $mls_id,
		            'admin'=> true,
		        ), true);

		        echo $label;
				echo "\n";
				echo $title;
				echo "\n";
				echo $price;
				echo "\n\n";
			}
		}

		printf(__('Lihat Lainnya: %s'), $otherUrl);
?>