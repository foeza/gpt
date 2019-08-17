<?php

		$data_ebrosur = $params['data_ebrosur'];
		$data_support = $params['data_support'];
		$data_count = $this->Rumahku->filterEmptyField($params, 'data_count', false, 0);

		$link_title = $this->Rumahku->getNameEbrosur($data_support);
		$url = $this->Html->url($this->Rumahku->getSearchUrl($data_support), true);
		$spec_ebrosur = $this->Rumahku->getSpesificationRequestEbrosur($data_support);

		$link_selengkapnya = $this->Html->link(__('Lihat Selengkapnya'), $url, array(
        	'escape' => false,
        	'style' => 'color:#fff;font-weight: bold;'
        ));

		if(!empty($data_ebrosur)){
			$content = $this->Html->tag('p', sprintf(__('Kami menemukan %s eBrosur dengan kriteria yang Anda pilih. %s'), $data_count, $link_selengkapnya), array(
	        	'style' => 'margin:0px;padding:0px;'
	        ));

	        $content .= $this->Html->link($link_title, $url, array(
	        	'escape' => false,
	        	'full_base' => true,
	        	'style' => 'color:#fff;font-weight: bold;margin: 10px 0px 0px;display: block;'
	        ));

	        $content .= $this->Html->tag('p', $spec_ebrosur, array(
	        	'style' => 'text-decoration: underline;margin: 0px;'
	        ));

	        echo $this->Html->tag('div', $content, array(
	        	'style' => 'background:#1b1e20;color:#fff;padding: 15px;margin: 15px 0px;border-radius: 3px;'
	        ));

			$i = 1;
			foreach ($data_ebrosur as $key => $value) {
				$ebrosur_photo = $this->Rumahku->filterEmptyField($value, 'UserCompanyEbrochure', 'ebrosur_photo');

				$image_url = $this->Rumahku->photo_thumbnail(array(
		            'save_path' => Configure::read('__Site.ebrosurs_photo'), 
		            'src'=> $ebrosur_photo, 
		            'size' => 'm',
		            'url' => true
		        ));

		        echo $this->Html->image(FULL_BASE_URL.$image_url, array(
		        	'style' => 'margin:0px 0px 5px;padding:0px 0px 5px;border-bottom:1px solid #ccc;'
		        ));

		        if($i++ >= 5){
		        	break;
		        }
			}

			echo $this->Html->tag('div', $this->Html->link(__('Lihat Selengkapnya'), $url, array(
				'style' => 'color:#3AB54A;font-weight:bold;',
				'full_base' => true,
			)), array(
				'style' => 'text-align:center;margin:15px 0px;padding:0;'
			));
		}else{
			echo $this->Html->tag('p', __('Data tidak ditemukan.'));
		}
?>