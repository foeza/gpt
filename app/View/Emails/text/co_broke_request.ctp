<?php 
		$senderName = $this->Rumahku->filterEmptyField($params, 'CoBrokeUser', 'name');
		$senderAddress = $this->Rumahku->filterEmptyField($params, 'CoBrokeUser', 'address');
		$senderPhone = $this->Rumahku->filterEmptyField($params, 'CoBrokeUser', 'phone');

		$mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');
		$title = $this->Rumahku->filterEmptyField($params, 'Property', 'title');

		$code = $this->Rumahku->filterEmptyField($params, 'CoBrokeProperty', 'code');

		$even = 'padding: 5px 8px;line-height: 20px;vertical-align: top;text-align: left;background-color: #f4f4f4;';
		$odd = $even.'background-color: transparent;';

		$title_properti = sprintf('%s - #%s', $title, $mls_id);

		printf(__('Anda mendapatkan permintaan kerjasama co-broking dari %s dengan kode Co-Broke %s untuk properti "%s".'), $senderName, $code, $title_properti);
		echo "\n\n";

		printf(__('Nama Agen : %s'), $senderName);echo "\n";
		printf(__('Alamat : %s'), $senderAddress);echo "\n";
		printf(__('No. Telp : %s'), $senderPhone);echo "\n";
		printf(__('Properti : %s'), $title_properti);echo "\n";
?>