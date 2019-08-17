<?php

	$id			= empty($id) ? NULL : $id;
	$draft_id	= empty($draft_id) ? NULL : $draft_id;
	$session_id	= empty($session_id) ? NULL : $session_id;
	$dataVideos	= empty($dataVideos) ? NULL : $dataVideos;
	$defaultURL	= array(
		'controller'	=> 'ajax', 
		'admin'			=> FALSE, 
		'draft'			=> $draft_id, 
	);

	$contents = '';

	if($dataVideos){
	//	LOOP SAVED VIDEOS
		foreach($dataVideos as $key => $value){
			$mediaID	= $this->Rumahku->filterEmptyField($value, 'PropertyVideos', 'id');
			$youtubeID	= $this->Rumahku->filterEmptyField($value, 'PropertyVideos', 'youtube_id');
			$title		= $this->Rumahku->filterEmptyField($value, 'PropertyVideos', 'title');
			$targetURL	= $this->Html->url(array_merge($defaultURL, array(
				'action' => 'property_video_title', 
				$session_id,
				$mediaID,
				$id,
			)));

		//	GENERATE VIDEO THUMBNAIL
			$thumbnail = $this->Html->div('preview dragging', $this->Rumahku->_callYoutubeThumbnail($youtubeID, $title));

		//	GENERATE TITLE FORM
			$ajaxForm = $this->Form->create('PropertyVideos', array(
				'url'					=> $targetURL,
				'class'					=> 'ajax-form',
				'data-type'				=> 'content',
				'data-wrapper-write'	=> '#youtube-video-placeholder',
				'autocomplete'			=> 'off',
			));

			$ajaxForm.= $this->Form->input('PropertyVideos.title', array(
				'class'		=> 'form-control tacenter ajax-change-form',
				'required'	=> FALSE,
				'label'		=> FALSE,
				'value'		=> $title,
				'div'		=> array(
					'class' => 'form-group',
				),
			));

			$ajaxForm.= $this->Html->div('bottom cb-checkmark disable-drag', 
				$this->Form->input('PropertyVideos.options_id.'.$key, array(
					'type'			=> 'checkbox',
					'class'			=> 'check-option',
					'div'			=> FALSE,
					'required'		=> FALSE,
					'hiddenField'	=> FALSE,
					'value'			=> $mediaID,
					'label'			=> array(
						'text'		=> __('Pilih Video'),
						'data-show'	=> '#video-content .fly-button-media, .sell-form .step-medias .fly-button-media',
					),
				)
			));

			$ajaxForm.= $this->Form->end();

		//	REBUILD CONTENT
			$contents.= $this->Html->tag('li', 
				$this->Html->div('item', 
					$thumbnail.
					$this->Html->div('action cb-custom', $ajaxForm)
				), 
				array(
					'class'	=> 'col-sm-3 template-download', 
					'rel'	=> $mediaID
				)
			);
		}
	}

	$targetURL = $this->Html->url(array_merge($defaultURL, array(
		'action' => 'property_video_order', 
		$session_id, 
		$id, 
	)));

//	WRAP VIDEOS
	$contents = $this->Html->div('col-xs-12', 
		$this->Html->tag('ul', 
			$contents, 
			array(
				'id'					=> 'youtube-video-placeholder', 
				'class'					=> 'row list-videos drag', 
				'data-url'				=> $targetURL, 
				'data-wrapper-write'	=> '#youtube-video-placeholder'
			)
		)
	);

	echo($contents);

//	FLOATING BUTTON
	$targetURL = $this->Html->url(array_merge($defaultURL, array(
		'action' => 'property_video_delete', 
		$session_id,
		$id,
	)));

	echo($this->Html->link($this->Rumahku->icon('rv4-cross').__(' Hapus Video'), $targetURL, array(
		'class'					=> 'btn red fly-button-media ajax-link',
		'data-form'				=> '.ajax-form',
		'data-alert'			=> __('Anda yakin ingin menghapus video ini?'),
		'data-wrapper-write'	=> '#youtube-video-placeholder', 
		'escape'				=> FALSE,
	)));

?>