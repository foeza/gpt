<?php

	$isAjax			= isset($isAjax) ? $isAjax : false;
	$record			= empty($record) ? array() : $record;
	$propertyVideos	= empty($propertyVideos) ? array() : $propertyVideos;

	if($record){
		$draftID	= Configure::read('__Site.PropertyDraft.id');
		$savePath	= Configure::read('__Site.property_photo_folder');

		$recordID	= Common::hashEmptyField($record, 'Property.id');
		$sessionID	= Common::hashEmptyField($record, 'Property.session_id');

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
				$subtab		= Common::hashEmptyField($this->params->named, 'subtab', 'upload-content');
				$tabHeads	= array(
					'upload-content'	=> __('Unggah ke YouTube'), 
					'url-content'		=> __('URL YouTube'), 
				);

				$ajaxURL = array(
					'admin'			=> true, 
					'controller'	=> 'properties', 
					'action'		=> 'easy_media', 
					$recordID, 
					'video', 
				);

				foreach($tabHeads as $tabKey => $tabText){
					$class = $tabKey == $subtab ? 'active' : '';

					$tabHeads[$tabKey] = $this->Html->tag('li', $this->Html->link($tabText, array_merge($ajaxURL, array('subtab' => $tabKey)), array(
						'class'					=> sprintf('ajax-link %s', $class), 
						'data-wrapper-write'	=> '#property_media_wrapper', 
						'escape'				=> false, 
					)), array(
						'class' => $class, 
					));
				}

				$tabHead = $this->Html->tag('div', 
					$this->Html->tag('div', $this->Html->tag('ul', implode('', $tabHeads)), array(
						'class'		=> 'rku-tabs clear', 
						'redirect'	=> 'false', 
					)), 
					array(
						'id' => 'rku-tabs-wrapper',
					)
				);

				echo($tabHead);

			?>
			<div class="tabs-box">
				<div id="upload-content" class="tab-handle <?php echo($subtab == 'upload-content' ? '' : 'hide'); ?>">
					<?php

						echo($this->element('blocks/properties/forms/youtube_upload_form', array(
							'propertyID'	=> $recordID, 
							'isAjax'		=> $isAjax, 
						)));

					?>
				</div>
				<div id="url-content" class="tab-handle <?php echo($subtab == 'url-content' ? '' : 'hide'); ?>">
					<?php

						echo($this->element('blocks/properties/forms/youtube_url_form', array(
							'propertyID'	=> $recordID, 
							'isAjax'		=> $isAjax, 
						)));

					?>
				</div>
			</div>
			<?php

				echo($this->element('blocks/properties/forms/youtube_videos', array(
					'id'			=> $recordID, 
					'draft_id'		=> $draftID, 
					'session_id'	=> $sessionID, 
					'dataVideos'	=> $propertyVideos, 
				)));

			//	FOOTER DESC
				$youtubeText		= $this->Html->tag('strong', 'YouTube');
				$URLForMoron		= $this->Html->link('Disini', 'https://support.google.com/youtube/troubleshooter/2888402', array('target' => '_blank'));
				$youtubeChannelURL	= $this->Html->link('Disini', 'https://www.youtube.com/create_channel', array('id' => 'create-channel-link', 'target' => '_blank'));
				$youtubeChannelDesc	= __('Pastikan Anda sudah memiliki saluran %s sebelum mengunggah video properti. Jika Anda belum memiliki saluran %s, Anda bisa membuat saluran %s %s', $youtubeText, $youtubeText, $youtubeText, $youtubeChannelURL);

				$youtubeTermsURL = $this->Html->link(__('Persyaratan Layanan %s', $youtubeText), 'http://www.youtube.com/t/terms', array(
					'target' => '_blank', 
					'escape' => FALSE, 
				));

				$youtubeTerms	= __('Dengan mengunggah video, Anda menyatakan bahwa Anda memiliki semua hak untuk konten tersebut atau Anda diizinkan oleh Vendor untuk membuat konten yang tersedia secara publik di %s dan hal tersebut sesuai dengan %s', $youtubeText, $youtubeTermsURL);
				$allowedFormat	= implode('</strong>, <strong>', array(
					'.mov', '.mpeg4', '.avi', 
					'.wmv', '.mpegps', '.flv', 
					'3GPP', 'WebM', 
				));

				$contentLi = '';
				$contentLi = $this->Html->tag('li', __('URL video yang Anda tambahkan harus berasal dari %s', $youtubeText));
				$contentLi.= $this->Html->tag('li', __($youtubeChannelDesc));
				$contentLi.= $this->Html->tag('li', __('Berikan judul untuk setiap video properti yang diunggah'));
				$contentLi.= $this->Html->tag('li', __('Video yang diunggah <strong>harus</strong> memiliki format <strong>%s</strong>. Untuk informasi lebih detil mengenai format video yang didukung, bisa dilihat %s', $allowedFormat, $URLForMoron));
				$contentLi.= $this->Html->tag('li', __('Setiap video yang diunggah akan masuk ke saluran %s Anda', $youtubeText));
				$contentLi.= $this->Html->tag('li', __('Setiap video (baik yang diungah atau ditambahkan melalui URL %s) akan ditampilkan pada detail properti Anda', $youtubeText));
				$contentLi.= $this->Html->tag('li', __($youtubeTerms));

			?>
			<div class="row">
				<div class="photo-info-bottom">
					<?php

						echo($this->Html->tag('label', $this->Html->tag('strong', __('Keterangan:'))));
						echo($this->Html->tag('ul', $contentLi));

					?>
				</div>
			</div>
		</div>
		<?php
	}

?>