<?php 
        $property_path = Configure::read('__Site.property_photo_folder');
        $property = $this->Rumahku->filterEmptyField($params, 'Property');
        $expired_day = $this->Rumahku->filterEmptyField($params, 'expired_day');

        $otherUrl = $this->Html->url(array(
            'controller'=> 'properties', 
            'action' => 'index',
            'status' => 'inactive',
            'admin'=> true,
        ), true);

		printf(__('Properti Anda telah Kami non-aktifkan dikarenakan selama %s hari tidak ada update/refresh terhadap properti tersebut.'), $expired_day);
		echo "\n";

		echo __('Mohon aktifkan kembali apabila iklan tersebut masih available: ');
		echo "\n\n";

		if( !empty($property) ) {
			$idx = 0;

			foreach ($property as $key => $value) {
				if( $idx < 3 ) {
			        $title = $this->Rumahku->filterEmptyField($value, 'Property', 'title');
			        $mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');

			        $label = $this->Property->getNameCustom($value);
			        $slug = $this->Rumahku->toSlug($label);
			        $price = $this->Property->getPrice($value);

					echo $label;
					echo "\n";
					echo $title;
					echo "\n";
					echo $price;
					echo "\n\n";
				}

				$idx++;
			}
		}

		if( count($property) > 3 ) {
			printf(__('Lihat Lainnya: %s'), $otherUrl);
		}
?>