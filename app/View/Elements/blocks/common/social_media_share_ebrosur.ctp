<?php 
		$og_image = !empty($urlEbrosur)?$urlEbrosur:false;
		$type = !empty($type)?$type:false;

		$property_title = $this->Rumahku->safeTagPrint($property_title);
        $page_url = FULL_BASE_URL.$this->here;
        $file_id = $this->Rumahku->filterEmptyField($detail, 'UserCompanyEbrochure', 'id');
        $phone = $this->Rumahku->filterEmptyField($detail, 'UserCompanyEbrochure', 'phone');
        
        $title = '';
		if(!empty($detail['UserCompanyEbrochure']['property_type_id'])){
			$title .= $propertyTypes[$detail['UserCompanyEbrochure']['property_type_id']].' ';
		}

		if(!empty($detail['UserCompanyEbrochure']['property_title'])){
			$title .= $detail['UserCompanyEbrochure']['property_title'];
		}

        $og_url = $this->Html->url(array(
        	'controller' => 'ebrosurs', 
        	'action' => 'detail', 
			$file_id,
        	'admin'=>false
    	), true);

        $full_og_url = $og_url;

		$facebook_text = '';
		$text_share = '';

		$facebook = sprintf('https://www.facebook.com/sharer/sharer.php?u=%s', $full_og_url);
        $twitter = sprintf('https://twitter.com/home?status=%s+%s', $title, $full_og_url);
        $googlePlus = sprintf('https://plus.google.com/share?url=%s', $full_og_url);
        $linkedin = sprintf('https://www.linkedin.com/shareArticle?mini=true&url=%s&source=LinkedIn', $full_og_url);
        $pinterest = sprintf('http://pinterest.com/pin/create/link/?url=%s', $full_og_url);
		
?>
<div class="article-share-horizontal">
	<?php
			$icon_fb = $this->Html->image('/img/icons/fb.png');
			$icon_twitter = $this->Html->image('/img/icons/twitter.png');
			$icon_gplus = $this->Html->image('/img/icons/gplus.png');
			$icon_pinterest = $this->Html->image('/img/icons/pinterest.png');
			$icon_linkedin = $this->Html->image('/img/icons/linkedin.png');
			$icon_message = $this->Rumahku->icon('rv4-wa');
			$icon_print = $this->Rumahku->icon('rv4-print');
			$icon_download = $this->Rumahku->icon('rv4-download');
			$icon_telephone = $this->Rumahku->icon('rv4-telephone');
			$icon_mail = $this->Rumahku->icon('rv4-mail');

			$list = $this->Html->tag('li', __('Bagikan Ebrosur ini : '), array(
				'class' => 'list-social-first btn'
			));

			$list .= $this->Html->tag('li', $this->Html->link($icon_fb, $facebook, array(
				'escape' => false,
				'title' => __('Facebook'),
				'class' => 'popup-window',
				'data-url' => $this->Html->url(array(
					'controller' => 'ajax',
					'action' => 'share',
					$file_id,
					$type,
					'facebook',
					'?' => array(
						'url' => $facebook,
					),
					'admin' => false,
				)),
			)));

			$list .= $this->Html->tag('li', $this->Html->link($icon_twitter, $twitter, array(
				'escape' => false,
				'title' => __('Twitter'),
				'class' => 'popup-window',
				'data-url' => $this->Html->url(array(
					'controller' => 'ajax',
					'action' => 'share',
					$file_id,
					$type,
					'twitter',
					'?' => array(
						'url' => $twitter,
					),
					'admin' => false,
				)),
			)));

			$list .= $this->Html->tag('li', $this->Html->link($icon_gplus, $googlePlus, array(
				'escape' => false,
				'title' => __('Google+'),
				'class' => 'popup-window',
				'data-url' => $this->Html->url(array(
					'controller' => 'ajax',
					'action' => 'share',
					$file_id,
					$type,
					'googleplus',
					'?' => array(
						'url' => $googlePlus,
					),
					'admin' => false,
				)),
			)));

			$list .= $this->Html->tag('li', $this->Html->link($icon_pinterest, $pinterest, array(
				'escape' => false,
				'title' => __('Pinterest'),
				'class' => 'popup-window',
				'data-url' => $this->Html->url(array(
					'controller' => 'ajax',
					'action' => 'share',
					$file_id,
					$type,
					'pinterest',
					'?' => array(
						'url' => $og_image,
					),
					'admin' => false,
				)),
			)));

			$list .= $this->Html->tag('li', $this->Html->link($icon_linkedin, $linkedin, array(
				'escape' => false,
				'title' => __('Linkedin'),
				'class' => 'popup-window',
				'data-url' => $this->Html->url(array(
					'controller' => 'ajax',
					'action' => 'share',
					$file_id,
					$type,
					'linkedin',
					'?' => array(
						'url' => $full_og_url,
					),
					'admin' => false,
				)),
			)));

			$article_text = sprintf(__('%s - %s'), $property_title, $full_og_url);
			$linkwa = Common::_callPhoneWA(array(
				'text' => $article_text,
			));

			$list .= $this->Html->tag('li', $this->Html->link($icon_message, $linkwa, array(
				'escape' => false,
				'title' => __('Whatsapp'),
				'class' => 'btn default popup-window',
				'data-url' => $this->Html->url(array(
					'controller' => 'ajax',
					'action' => 'share',
					$file_id,
					$type,
					'whatsapp',
					'?' => array(
						'url' => $linkwa,
					),
					'admin' => false,
				)),
				'data-type' => 'redirect',
			)));

			$list .= $this->Html->tag('li', $this->Html->link($icon_print, $full_og_url.'/print', array(
				'escape' => false,
				'target' => 'blank',
				'title' => __('Print'),
				'class' => 'btn default',
			)));

			$list .= $this->Html->tag('li', $this->Html->link($icon_download, array(
				'controller' => 'ebrosurs',
				'action' => 'download',
				$file_id,
				Configure::read('__Site.ebrosurs_photo'),
				'admin' => false
			), array(
				'escape' => false,
				'title' => __('Download'),
				'class' => 'btn default'
			)));

			$list .= $this->Html->tag('li', $this->Html->link($icon_mail, array(
				'controller' => 'ebrosurs',
				'action' => 'mail',
				$file_id,
				'admin' => true
			), array(
				'escape' => false,
				'title' => __('Kirim eBrosur'),
				'class' => 'btn default ajaxModal'
			)));

			echo $this->Html->tag('ul', $list);
	?>
</div>