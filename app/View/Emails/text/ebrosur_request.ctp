<?php

		$data_ebrosur = $params['data_ebrosur'];
		$data_support = $params['data_support'];
		$data_count = $this->Rumahku->filterEmptyField($params, 'data_count', false, 0);

		$link_title = $this->Rumahku->getNameEbrosur($data_support);
		$url = $this->Html->url($this->Rumahku->getSearchUrl($data_support), true);

		$spec_ebrosur = $this->Rumahku->getSpesificationRequestEbrosur($data_support);

		if(!empty($data_ebrosur)){
			printf(__('Kami menemukan %s eBrosur dengan kriteria yang Anda pilih'), $data_count);
			echo "\n\n";
			printf('Link eBrosur : %s', $url);
			echo "\n";
			printf($spec_ebrosur);
			echo "\n";

			$i = 1;
			foreach ($data_ebrosur as $key => $value) {
				$ebrosur_photo = $this->Rumahku->filterEmptyField($value, 'UserCompanyEbrochure', 'ebrosur_photo');

				$image_url = $this->Rumahku->photo_thumbnail(array(
		            'save_path' => Configure::read('__Site.ebrosurs_photo'), 
		            'src'=> $ebrosur_photo, 
		            'size' => 'm',
		            'url' => true
		        ));

		        printf('%s. %s', $i, FULL_BASE_URL.$image_url);
		        echo "\n";
		        
		        if($i++ >= 5){
		        	break;
		        }
			}
		}else{
			echo __('Data tidak ditemukan.');
		}
?>