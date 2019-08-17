<?php
		$data = $params;
		$dataClient = $this->Rumahku->filterEmptyField($params, 'dataClient');;
		$dataCompany = $this->Rumahku->filterEmptyField($params, 'dataCompany');;

		$nama_agen = $this->Rumahku->filterEmptyField($params, 'agen_name');
		$nama_klien = $this->Rumahku->filterEmptyField($dataClient, 'full_name');
		
		$nama_perusahaan = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');
		$domain = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompanyConfig', 'domain');
?>
<div style="background:#18334b;color: #fff;padding-bottom: 15px;">
	<div style="background:url(/images/hbd-greet-bg.jpg);width: 600px;height: 460px;">
		<?php
				echo $this->Html->tag('h1', sprintf(__('Hai %s,'), ucwords($nama_klien)), array(
					'style' => 'font-size:21px;text-align: center;padding: 366px 0px 0px;color: #fff;'
				));
		?>
	</div>
	<?php
			echo $this->Html->tag('p', sprintf(__('Kami dari %s mengucapkan selamat ulang tahun untuk Anda di hari yang spesial ini.'), $this->Html->link($nama_perusahaan, $domain, array(
				'style' => 'color: #fff;text-decoration:underline;',
			))), array(
					'style' => 'font-size:18px;text-align: center;padding: 0 100px;margin-top: 0px;'
				));

			echo $this->Html->tag('p', __('Semoga apa yang diharapkan untuk tahun ini, dapat Anda raih, dan selalu dilimpahkan rejeki.'), array(
				'style' => 'font-size:18px;text-align: center;padding: 0 100px;margin-top: 0px;'
			));
	?>
</div>