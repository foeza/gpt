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
?>
<div style="font-size: 14px;color:#000;">
	<div style="margin-top: 15px;">
		<div style="float:left;">
			<span style="width: 70px;display: inline-block;">Nomor</span>:  282/Prime/04/16<br>
			<span style="width: 70px;display: inline-block;">Hal</span>:  Penjelasan Gangguan Launcher
		</div>
		<div style="float:right;">
			Jakarta,   6 April 2016
		</div>
		<div style="clear:both;"></div>
	</div>
	<br>
	<?php
			echo $this->Html->tag('p', __('Kepada Yth'));

			printf('%s %s', $gen, $full_name);
	
			echo $this->Html->tag('p', 'Kantor '.$company_name);
	?>
	<br>
	<?php
			echo $this->Html->tag('p', __('Dengan hormat,'));
	
			$p_style = 'text-align: justify;line-height: 20px;';

			echo $this->html->tag('p', __('Bersama ini saya sampaikan bahwa layanan PRIME SYSTEM.com sedang mengalami gangguan, terutama pada penggunaan launcher. Saat ini launcher yang Bapak/Ibu gunakan dalam tahap peninjauan (suspend) oleh Google.'), array(
				'style' => $p_style
			));

			echo $this->html->tag('p', __('Hal ini disebabkan %s mengelola banyak sekali admin website Kantor Agen dengan menggunakan PRIME SYSTEM dan hal tersebut dianggap Spam dan melanggar hak cipta. Masalah tersebut sedang kami atasi dengan mengirimkan surat kepada Google, untuk menjelaskan bahwa %s memang memiliki layanan penyediaan Website Company dan Launcher dengan menggunakan PRIME SYSTEM, serta kami memiliki izin dari masing-masing kantor untuk mengelola website dan launcher serta penggunaan materi beserta logo perusahaan.', Configure::read('__Site.site_name'), Configure::read('__Site.site_name')), array(
				'style' => $p_style
			));

			echo $this->html->tag('p', __('Terkait hal ini Kami memohon maaf atas ketidaknyamanan yang terjadi. Kami berharap hal ini dapat segera teratasi sehingga launcher yang digunakan dapat segera dibuka dan digunakan kembali.'), array(
				'style' => $p_style
			));

			echo $this->html->tag('p', __('Demikian surat permohonan maaf ini saya sampaikan. Atas perhatian dan kerjasama Bapak/Ibu saya ucapkan terima kasih.'), array(
				'style' => $p_style
			));
	?>
	<br>
	<p style="margin: 0px;line-height: 15px;">
		<?php
				echo __('Hormat Saya');
		?> <br><br>
		<span style="text-decoration: underline;">Robert Adrian</span>
	<br>
	Direktur</p>
	
</div>
<div>
	<p style="text-align: center;margin-top: 40px;">
		<?php
				$url_download = $this->Html->url(array(
					'controller' => 'newsletters',
					'action' => 'download_file',
					'Surat-Penjelasan-Gangguan-Launcher.jpg',
					'admin' => false
				), true);

				echo $this->Html->link(__('Download Surat Penjelasan'), $url_download, array(
					'style' => 'padding: 10px 20px;border: 1px solid #333;border-radius: 4px;color: #069D54;text-decoration: none;font-size: 15px;',
					'target' => 'blank'
				));
		?>
	</p>
</div>