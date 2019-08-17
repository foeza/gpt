<?php 
		$share_id = !empty($share_id)?$share_id:0;
		$share_type = !empty($share_type)?$share_type:false;
		$_comment = !empty($_comment)?$_comment:false;
		$_print = isset($_print)?$_print:true;
        $title = !empty($title)?$title:false;
        $url = !empty($url)?$url:false;

		$title_widget = !empty($title_widget)?$title_widget:__('Share This Post:');
        
        $facebook = sprintf('https://www.facebook.com/share.php?u=%s?title=%s', $url, $title);
        $twitter = sprintf('https://twitter.com/home?status=%s+%s', $title, $url);
        $googlePlus = sprintf('https://plus.google.com/share?url=%s', $url);
		$linkwa = Common::_callPhoneWA(array(
			'text' => $url,
		));
?>
<div class="share-wraper col-sm-12 clearfix hidden-print">
	<?php 
			echo $this->Html->tag('h5', $title_widget);
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
				echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-google'), $googlePlus, array(
					'escape' => false,
                    'class' => 'popup-window',
                    'data-url' => $this->Html->url(array(
                        'controller' => 'ajax',
                        'action' => 'share',
                        $share_id,
                        $share_type,
                        'googleplus',
                        '?' => array(
                            'url' => $googlePlus,
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

				if( !empty($pinterest) ) {
					echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-pinterest'), $pinterest, array(
						'escape' => false,
						'class' => 'ajax-link',
						'data-url' => $this->Html->url(array(
							'controller' => 'ajax',
							'action' => 'share',
	                        $share_id,
	                        $share_type,
							'pinterest',
							'?' => array(
								'url' => $pinterest,
							),
							'admin' => false,
						)),
					)));
				}
		?>
	</ul>
	<?php 
			if( !empty($_print) ) {
	?>
	<a class="print-button" href="javascript:window.print();">
		<i class="fa fa-print"></i>
	</a>
	<?php 
			}
	?>
</div>
<?php 
		if( !empty($_comment) ) {
			$content = $this->Html->tag('h1', __('Comments'), array(
				'class' => 'section-title',
			));
			$content .= $this->Html->tag('div', $this->Social->get_comment_fb('100%'), array(
				'class' => 'comments',
			));

			echo $this->Html->div('hidden-print', $content);
		}
?>