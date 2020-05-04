<?php 
		$share_id 	= !empty($share_id)?$share_id:0;
		$share_type = !empty($share_type)?$share_type:false;
        $title 		= !empty($title)?$title:false;
        $url 		= !empty($url)?$url:false;

		$h_widget 	= !empty($title_widget)?$title_widget:__('Bagikan :');
        
        $facebook 	= sprintf('https://www.facebook.com/share.php?u=%s?title=%s', $url, $title);
        $twitter 	= sprintf('https://twitter.com/intent/tweet?text=%s&url=%s', $title, $url);

		$linkwa 	= Common::_callPhoneWA(array(
			'text' => $url,
		));
?>
<div class="share-wraper col-sm-12 clearfix hidden-print">
	<?php 
			echo $this->Html->tag('h5', $h_widget);
	?>
	<ul class="social-networks">
		<?php 
				echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-facebook'), $facebook, array(
					'escape' => false,
                    'class' => 'popup-window',
                    'data-url' => $this->Html->url(array(
                        'controller' => 'ajax',
                        'action' => 'share',
                        $share_id,
                        $share_type,
                        'facebook',
                        '?' => array(
                            'url' => $facebook,
                        ),
                        'admin' => false,
                    )),
				)));
				echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-twitter'), $twitter, array(
					'escape' => false,
                    'class' => 'popup-window',
                    'data-url' => $this->Html->url(array(
                        'controller' => 'ajax',
                        'action' => 'share',
                        $share_id,
                        $share_type,
                        'twitter',
                        '?' => array(
                            'url' => $twitter,
                        ),
                        'admin' => false,
                    )),
				)));

				if( !empty($linkwa) ) {
					echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('rv4-wa'), $linkwa, array(
						'escape' => false,
                        'class' => 'popup-window',
                        'data-url' => $this->Html->url(array(
                            'controller' => 'ajax',
                            'action' => 'share',
	                        $share_id,
	                        $share_type,
                            'whatsapp',
                            '?' => array(
                                'url' => $linkwa,
                            ),
                            'admin' => false,
                        )),
                        'data-type' => 'redirect',
					)));
				}

		?>
	</ul>

</div>