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

		printf(__('Kami ingin menginformasikan bahwa laporan Anda telah siap di unduh.'));
		printf(__('Anda dapat mengunduh laporan tersebut melalui company web atau'));

		echo $this->Html->link(__('Klik untuk mengunduh'), $link, array(
			'style' => 'text-decoration: none; cursor: pointer;'
		));
?>