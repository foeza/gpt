<?php

	$id			= empty($id) ? NULL : $id;
	$dataMedias	= empty($dataMedias) ? NULL : $dataMedias;
	$session_id	= empty($session_id) ? NULL : $session_id;
	$dataVideos	= empty($dataVideos) ? NULL : $dataVideos;

	$data		= $this->request->data;
	$draft_id	= Configure::read('__Site.PropertyDraft.id');
	$videos		= $this->Rumahku->filterEmptyField($data, 'PropertyVideos', 'name');

?>
<div class="step-medias">
	<div class="wrapper-upload-medias upload-video">
		<?php

			echo($this->element('blocks/properties/media_action', array(
				'active' => 'video',
			)));

		?>
		<div class="content-upload-photo">
			<?php

			//	NOTICE
				$notice = sprintf(
					'%s Rekam sekitar ruangan pada properti yang akan diiklankan, '.
					'untuk memberikan informasi lebih detil kepada calon pembeli/penyewa properti Anda.', 
					$this->Html->tag('strong', __('Tahukah Anda?'))
				);

				echo($this->Html->div('info-full alert photo-info-top',
					$this->Html->tag('p', __($notice))
				));

			//	TABS
				$activeTab = $this->Rumahku->filterEmptyField($this->params->named, 'subtab', NULL, 'upload-content');
				if($activeTab == 'url-content' || $this->request->data){
					$uploadActive = FALSE;
				}
				else{
					$uploadActive = TRUE;
				}

				$urlActive = !$uploadActive;

				$tabHead = $this->Html->tag('div', 
					$this->Html->tag('div', 
						$this->Html->tag('ul', 
							$this->Html->tag('li', 
								$this->Html->link(__('Unggah ke YouTube'), '#upload-content', array('class' => empty($this->request->data) ? 'active' : '')), 
								array(
									'class' => ($uploadActive ? 'active' : '')
								)
							).
							$this->Html->tag('li', 
								$this->Html->link(__('URL YouTube'), '#url-content', array('class' => $this->request->data ? 'active' : '')),
								array(
									'class' => ($urlActive ? 'active' : '')
								)
							)
						), 
						array(
							'class'		=> 'rku-tabs clear', 
							'redirect'	=> 'false', 
						)
					), 
					array(
						'id' => 'rku-tabs-wrapper',
					)
				);

				$tabBody = $this->Html->tag('div', 
					$this->Html->tag('div', 
						$this->element('blocks/properties/forms/youtube_upload_form', array('propertyID' => $id)), 
						array(
							'id'	=> 'upload-content', 
							'class'	=> 'tab-handle '.($uploadActive ? '' : 'hide'),
						)
					).
					$this->Html->tag('div', 
						$this->element('blocks/properties/forms/youtube_url_form', array('propertyID' => $id)), 
						array(
							'id'	=> 'url-content', 
							'class'	=> 'tab-handle '.($urlActive ? '' : 'hide'), 
						)
					), 
					array(
						'class' => 'tabs-box', 
					)
				);

				echo($tabHead.$tabBody);

				echo($this->element('blocks/properties/forms/youtube_videos', array(
					'id'			=> $id, 
					'draft_id'		=> $draft_id, 
					'session_id'	=> $session_id, 
				)));

			//	FOOTER DESC
				$youtubeText		= $this->Html->tag('strong', 'YouTube');
				$URLForMoron		= $this->Html->link('Disini', 'https://support.google.com/youtube/troubleshooter/2888402', array('target' => '_blank'));
				$youtubeChannelURL	= $this->Html->link('Disini', 'https://www.youtube.com/create_channel', array('id' => 'create-channel-link', 'target' => '_blank'));
				$youtubeChannelDesc	= sprintf(
					'Pastikan Anda sudah memiliki saluran %s sebelum mengunggah video properti. '.
					'Jika Anda belum memiliki saluran %s, Anda bisa membuat saluran %s %s', $youtubeText, $youtubeText, $youtubeText, $youtubeChannelURL
				);

				$youtubeTermsURL = $this->Html->link(sprintf('Persyaratan Layanan %s', $youtubeText), 'http://www.youtube.com/t/terms', array(
					'target' => '_blank', 
					'escape' => FALSE, 
				));

				$youtubeTerms = sprintf(
					'Dengan mengunggah video, Anda menyatakan bahwa Anda memiliki semua hak untuk konten tersebut '.
					'atau Anda diizinkan oleh Vendor untuk membuat konten yang tersedia secara publik di %s '.
					'dan hal tersebut sesuai dengan %s', $youtubeText, $youtubeTermsURL
				);

				$allowedFormat = array(
					'.mov', '.mpeg4', '.avi', 
					'.wmv', '.mpegps', '.flv', 
					'3GPP', 'WebM', 
				);

				$contentLi = '';
				$contentLi = $this->Html->tag('li', __(sprintf('URL video yang Anda tambahkan harus berasal dari %s', $youtubeText)));
				$contentLi.= $this->Html->tag('li', __($youtubeChannelDesc));
				$contentLi.= $this->Html->tag('li', __('Berikan judul untuk setiap video properti yang diunggah'));
				$contentLi.= $this->Html->tag('li', __(sprintf(
					'Video yang diunggah <strong>harus</strong> memiliki format <strong>%s</strong>. '.
					'Untuk informasi lebih detil mengenai format video yang didukung, bisa dilihat %s', 
					implode('</strong>, <strong>', $allowedFormat), 
					$URLForMoron
				)));
				$contentLi.= $this->Html->tag('li', __(sprintf('Setiap video yang diunggah akan masuk ke saluran %s Anda', $youtubeText)));
				$contentLi.= $this->Html->tag('li', __(sprintf(
					'Setiap video (baik yang diungah atau ditambahkan melalui URL %s) akan ditampilkan pada detail properti Anda', 
					$youtubeText
				)));
				$contentLi.= $this->Html->tag('li', __($youtubeTerms));

				echo($this->Html->div('photo-info-bottom', 
					$this->Html->tag('label', $this->Html->tag('b', __('Keterangan : '))).
					$this->Html->tag('ul', $contentLi, array(
						'escape' => FALSE
					))
				));

			?>
		</div>
	</div>
	<?php

		$property	= empty($property) ? NULL : $property;
		$propertyID	= $this->Rumahku->filterEmptyField($property, 'Property', 'id');
		$mlsID		= $this->Rumahku->filterEmptyField($property, 'Property', 'mls_id');
		$title		= $this->Rumahku->filterEmptyField($property, 'Property', 'title');
		$keyword	= $this->Rumahku->filterEmptyField($property, 'Property', 'keyword');
		$desc		= $this->Rumahku->filterEmptyField($property, 'Property', 'description');
		$slug		= $this->Rumahku->filterEmptyField($property, 'Property', 'slug');
		$backlink	= $this->Html->url(array(
			'admin'			=> FALSE, 
			'plugin'		=> FALSE, 
			'controller'	=> 'properties', 
			'action'		=> 'detail', 
			'mlsid'			=> $mlsID, 
			'slug'			=> $slug, 
		), TRUE);

		$backlink = sprintf(__('Untuk detail lebih lanjut mengenai properti ini : %s'), $backlink);
		$desc = sprintf('%s%s%s', $desc, '<break>', $backlink);
		$tags = explode(',', $keyword);
		$tags = Inflector::slug($tags, '-');
		$tags = strtolower(implode(',', $tags));

		echo($this->Form->hidden('property_id', array(
			'id'		=> 'property-id', 
			'value'		=> $propertyID, 
			'data-role'	=> 'youtube-property_id-input', 
		)));

		echo($this->Form->hidden('description', array(
			'value'		=> $desc, 
			'class'		=> 'form-control', 
			'data-role'	=> 'youtube-description-input', 
		)));

		echo($this->Form->hidden('tag', array(
			'value'		=> $tags, 
			'class'		=> 'form-control', 
			'data-role'	=> 'youtube-tag-input', 
		)));

		echo($this->Form->create('PropertyVideos', array(
			'id'	=> 'sell-property',
			'class'	=> 'form-horizontal',
		)));

		echo($this->element('blocks/properties/sell_action', array(
			'labelBack'		=> __('Kembali'),
			'action_type'	=> 'bottom',
		)));

	//	hidden inputs
		echo($this->Form->hidden('Property.session_id', array(
			'value'		=> $session_id, 
			'data-role'	=> 'youtube-session-input', 
		)));

		echo($this->Form->end());

	?>
</div>