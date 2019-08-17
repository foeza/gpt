<?php
		$title 			= $this->Rumahku->filterEmptyField($params, 'Property', 'title');
		$change_date 	= $this->Rumahku->filterEmptyField($params, 'Property', 'change_date');
		
		$code = $this->Rumahku->filterEmptyField($params, 'CoBrokeProperty', 'code');
		$id = $this->Rumahku->filterEmptyField($params, 'CoBrokeProperty', 'id');

		$user_name = $this->Rumahku->filterEmptyField($params, 'User', 'full_name');
		$company_name = $this->Rumahku->filterEmptyField($params, 'UserCompany', 'name');

		$label = $this->Property->getNameCustom($params);
		$price = $this->Property->getPrice($params, __('(Harap hubungi Agen terkait)'));

		$specs = $this->Property->getSpec($params, array(), false, false);

		echo __('Berikut kami sampaikan bahwa property co-broke di bawah ini sudah terjual')."\n\n";

		echo __('Data Properti:')."\n";

		echo $label."\n";
		echo $title."\n";
		echo sprintf(__('Harga : %s'), $price)."\n";
		echo sprintf(__('Kode Co-Broke: #%s'), $code)."\n";
		
		echo __('Spesifikasi:')."\n";
		
		if(!empty($specs)){
			foreach ($specs as $key => $value) {
				$name = $this->Rumahku->filterEmptyField($value, 'name');
				$val = $this->Rumahku->filterEmptyField($value, 'value');

				echo sprintf(__('%s : %s'), $name, $val)."\n";
			}

			echo "\n";
		}

		echo "\n\n".__('Harap hubungi pihak agen atau principle yang bersangkutan untuk mengetahui informasi lebih lanjut, terima kasih.');
?>