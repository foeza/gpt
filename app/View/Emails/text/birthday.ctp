<?php
		$data = $params;
		$dataClient = $this->Rumahku->filterEmptyField($params, 'dataClient');;
		$dataCompany = $this->Rumahku->filterEmptyField($params, 'dataCompany');;

		$nama_agen = $this->Rumahku->filterEmptyField($params, 'agen_name');
		$nama_klien = $this->Rumahku->filterEmptyField($dataClient, 'full_name');
		
		$nama_perusahaan = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');

		printf(__('Hai %s,'), ucwords($nama_klien)); echo "\n\n";
		printf(__('Kami dari %s mengucapkan selamat ulang tahun untuk Anda di hari yang spesial ini.'), $nama_perusahaan); echo "\n\n";
		echo __('Semoga apa yang diharapkan untuk tahun ini, dapat Anda raih, dan selalu dilimpahkan rejeki.');
?>