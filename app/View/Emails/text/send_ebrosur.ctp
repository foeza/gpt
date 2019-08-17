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

		printf(__('Anda mendapatkan email berisi eBrosur dari %s'), $nama_agent);

        printf(__('Link eBrosur : %s'), FULL_BASE_URL.$image_url);
?>