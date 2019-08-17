<?php
		$report_id = $this->Rumahku->filterEmptyField($params, 'ReportQueue', 'id');
		$link = $this->Html->url(array(
			'controller' => 'settings',
			'action' => 'download',
			'report',
			$report_id,
			'path',
			'admin' => true,
		), true);

		echo $this->Html->tag('p', __('Kami ingin menginformasikan bahwa laporan Anda telah siap di unduh.'), array(
			'style' => 'margin:15px 0px;padding:0px;'
		));

		echo $this->Html->tag('p', __('Anda dapat mengunduh laporan tersebut melalui company web atau'), array(
			'style' => 'margin:15px 0px;padding:0px;'
		));

		echo $this->Html->link(__('Klik untuk mengunduh'), $link, array(
			'style' => 'width: 200px; padding:10px 15px; background:#204798; color: #fff; text-decoration: none; border-radius: 3px; margin: 20px 0 20px 190px; text-align: center;'
		));
?>