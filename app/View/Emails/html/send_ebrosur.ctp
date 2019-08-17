<?php
		$data_ebrosur = $this->Rumahku->filterEmptyField($params, 'data_ebrosur');
		$data_agent = $this->Rumahku->filterEmptyField($params, 'data_agent');

		$ebrosur_photo = $this->Rumahku->filterEmptyField($data_ebrosur, 'UserCompanyEbrochure', 'ebrosur_photo');
		$nama_agent = $this->Rumahku->filterEmptyField($data_agent, 'User', 'full_name');

		$image_url = $this->Rumahku->photo_thumbnail(array(
            'save_path' => Configure::read('__Site.ebrosurs_photo'), 
            'src'=> $ebrosur_photo, 
            'size' => 'fullsize',
            'url' => true
        ));

		echo $this->Html->tag('p', sprintf(__('Anda mendapatkan email berisi eBrosur dari %s'), $nama_agent), array(
			'style' => 'margin:15px 0px;padding:0px;'
		));

        echo $this->Html->image(FULL_BASE_URL.$image_url, array(
        	'style' => 'margin:0px 0px 5px;padding:0px 0px 5px;border-bottom:1px solid #ccc;width:100%;'
        ));
?>