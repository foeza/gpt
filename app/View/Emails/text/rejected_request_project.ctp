<?php
	$reason_rejected = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'reason_rejected');
	$project_name = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'project_name');

	echo __('Mohon maaf! Permintaan untuk menampilkan project ');
	echo $project_name;
	echo __(', pada web Anda kami tolak dengan alasan ');
	echo $reason_rejected;
	echo __('. Harap hubungi admin support kami untuk informasi lebih lanjut. Terimakasih.');
	
?>

