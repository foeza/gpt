<?php
		$urlListSosmed = $this->Html->url(array(
			'controller' => 'users',
			'action' => 'get_content',
			'UserClientSosmedReference',
			'backprocess' => true,
			'ext' => 'json', 
		), true);

		// view if from added sosmed
		if (!empty($value_added)) {

			// content label for sosmed reference
			$contentLabel = $value_added['UserClient']['content_sosmed'];
			$sosmed_id = $value_added['UserClient']['client_ref_sosmed_id'];

			$contentLabel = empty($contentLabel) ? null : $contentLabel;
			$hiddenInput = $this->Form->hidden('UserClient.client_ref_sosmed_id', array(
				'value' => $sosmed_id,
			));

		} else {

			// content label for sosmed reference
			$contentLabel = empty($contentLabel) ? null : $contentLabel;
			$hiddenInput = $this->Form->hidden('UserClient.client_ref_sosmed_id');

		}

		echo $this->Html->tag('div', $this->Rumahku->buildInputForm('UserClient.content_sosmed', array(
			'type' => 'text', 
			'label' => __('Nama Social Media'),
			'fieldError' => 'UserClient.content_sosmed',
			'attributes' => array(
				'data-source' => $urlListSosmed,
				'data-must-match' => 'true',
				'data-role' => 'catcomplete',
				'data-post' => 'content_type:UserClientSosmedReference,design_type:multiple,search_content:other', 
				'value' => $contentLabel,
				'after' => $hiddenInput.$this->Html->link(__('Tambah social media'), array(
	                'controller' => 'users',
	                'action' => 'popup_sosmed_reference',
	                'backprocess' => true,
	            ), array(
	            	'data-source' => 'got-alert',
	            	'data-form' => 'false',
	            	'class' => 'ajaxModal add-sosmed-reference mt10',
	            	'title' => __('Tambah Social Media'),
	            )),
			),
		)), array(
			'class' => 'sosmed-id-placeholder', 
		));
?>