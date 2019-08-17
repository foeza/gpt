<?php
		$data = $this->request->data;

        echo $this->Rumahku->buildInputForm('MailchimpConfig.app_name', array(
            'label' => __('Nama Aplikasi'),
        ));
        echo $this->Rumahku->buildInputForm('MailchimpConfig.client_id', array(
            'label' => __('Client ID'),
            'type' => 'text'
        ));
        echo $this->Rumahku->buildInputForm('MailchimpConfig.secret_key', array(
            'label' => __('Secret Key'),
        ));
?>