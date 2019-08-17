<?php
		$data = $this->Rumahku->filterEmptyField($params, 'data');

		$full_name = $this->Rumahku->filterEmptyField($data, 'User', 'full_name');
		$gender_id = $this->Rumahku->filterEmptyField($data, 'User', 'gender_id');
		$company_name = $this->Rumahku->filterEmptyField($data, 'UserCompany', 'name');

		if(!empty($gender_id) && $gender_id == 2){
			$gen = 'Ibu';
		}else{
			$gen = 'Bapak';
		}

		echo __('Jakarta,   6 April 2016')."\n\n";
		echo __('Nomor : 282/Prime/04/16')."\n";
		echo __('Hal : Penjelasan Gangguan Launcher')."\n\n";

		echo __('Kepada Yth')."\n\n";

		echo sprintf('%s %s', $gen, $full_name)."\n\n";
		
		echo 'Kantor '.$company_name."\n\n";
		echo __('Dengan hormat,')."\n\n";

		echo __('Bersama ini saya sampaikan bahwa layanan PRIME SYSTEM sedang mengalami gangguan, terutama pada penggunaan launcher. Saat ini launcher yang Bapak/Ibu gunakan dalam tahap peninjauan (suspend) oleh Google.')."\n\n";

		echo __('Hal ini disebabkan %s mengelola banyak sekali admin website Kantor Agen dengan menggunakan PRIME SYSTEM dan hal tersebut dianggap Spam dan melanggar hak cipta. Masalah tersebut sedang kami atasi dengan mengirimkan surat kepada Google, untuk menjelaskan bahwa %s memang memiliki layanan penyediaan Website Company dan Launcher dengan menggunakan PRIME SYSTEM, serta kami memiliki izin dari masing-masing kantor untuk mengelola website dan launcher serta penggunaan materi beserta logo perusahaan.', Configure::read('__Site.site_name'), Configure::read('__Site.site_name'))."\n\n";
		echo __('Terkait hal ini Kami memohon maaf atas ketidaknyamanan yang terjadi. Kami berharap hal ini dapat segera teratasi sehingga launcher yang digunakan dapat segera dibuka dan digunakan kembali.')."\n\n";
		echo __('Demikian surat permohonan maaf ini saya sampaikan. Atas perhatian dan kerjasama Bapak/Ibu saya ucapkan terima kasih.')."\n\n";

		echo __('Hormat Saya')."\n\n";
		echo __('Robert Adrian')."\n";
		echo __('Direktur')."\n";

		$url_download = $this->Html->url(array(
			'controller' => 'newsletters',
			'action' => 'download_file',
			'Surat-Penjelasan-Gangguan-Launcher.jpg',
			'admin' => false
		), true);

		echo __('Download Surat Penjelasan : ').$url_download;
?>