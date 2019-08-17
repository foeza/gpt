<?php
		$company_name = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'company_name');
		$project_name = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'project_name');
		$commision = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'commision');
		$start_date = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'start_date');
		$end_date = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'end_date');

		$customStart = $this->Rumahku->getIndoDateCutom($start_date);
		$customEnd = $this->Rumahku->getIndoDateCutom($end_date);
		
		$contacts = $this->Rumahku->filterEmptyField($params, 'ApiAdvanceDeveloper', 'Contact');

		$i= 1;
		foreach ($contacts as $key => $value) {
			$result = array();
			$phone = $this->Rumahku->filterEmptyField($value, 'ApiDeveloperContactInfo', 'value');
			$tmp_val['phone'.$i] = $phone;
			$i++;
		}
		$result['info_contact'] = $tmp_val;
		$phone1 = $this->Rumahku->filterEmptyField($result, 'info_contact', 'phone1');

		$commision =  $this->Number->format($commision, array(
		    'places' => 1,
		    'before' => '',
		    'escape' => false,
		    'decimals' => '.'
		));

		echo __('Hai! ');
		echo $company_name;
		echo "\n";
		echo __('Selamat! Permintaan Anda untuk menampilkan project ');
		echo $project_name;
		echo __(' telah kami terima dan akan tayang dari tanggal ');
		echo $customStart;
		echo __(' sampai dengan ');
		echo $customEnd;
		echo __('Info menarik untuk Anda! Dapatkan komisi sebesar ').$commision;
		echo __('untuk setiap unit project yang berhasil Anda jual.Terimakasih');
		echo "\n";
		echo __('Untuk informasi lebih lanjut silakan hubungi');
		echo $phone1;

?>

