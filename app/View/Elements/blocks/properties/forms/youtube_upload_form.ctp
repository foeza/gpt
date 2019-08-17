<?php

	$clientID		= Configure::read('__Site.youtube_client_id');
	$apiKey			= Configure::read('__Site.youtube_api_key');
	$propertyID		= empty($propertyID) ? NULL : $propertyID;
	$youtubeText	= $this->Html->tag('strong', 'YouTube');
	$defaultOptions	= array(
		'div'	=> FALSE, 
		'label'	=> FALSE, 
		'class'	=> 'form-control', 
	);
	$videoPrivacies = array(
		'public'	=> __('Publik'), 
		'unlisted'	=> __('Tidak Terdaftar'), 
		'private'	=> __('Pribadi'), 
	);

//	button
	$signInMessage = $this->Html->div('info-upload-photo text-center', 
		$this->Html->div('row desc', 
			$this->Html->tag('p', __('Klik tombol berikut untuk login menggunakan akun Google Anda.'))
		)
	);

//	$youtubeScopes = 'https://www.googleapis.com/auth/plus.me ';
	$youtubeScopes = 'https://www.googleapis.com/auth/youtube ';
	$youtubeScopes.= 'https://www.googleapis.com/auth/youtube.upload ';

	$signInButton = $this->Html->tag('span', '', array(
		'class'				=> 'g-signin2', 
		'data-callback'		=> 'signinCallback', 
		'data-clientid'		=> $clientID, 
		'data-cookiepolicy'	=> 'single_host_origin', 
		'data-scope'		=> $youtubeScopes, 
	));

	?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12">
				<?php

					echo($this->Html->tag('div', $signInMessage . $this->Form->button(__('Login'), array(
						'id'				=> 'signInButton', 
						'type'				=> 'button', 
						'class'				=> 'btn blue mt15 mb15 centered', 
						'data-key'			=> $apiKey, 
						'data-clientid'		=> $clientID, 
						'data-scope'		=> $youtubeScopes, 
						'data-cookiepolicy'	=> 'single_host_origin', 
					)), array(
						'class' => 'pre-sign-in text-center', 
					)));

				?>
				<div class="post-sign-in text-center">
					<div class="user-information">
						<div class="user-photo">
							<div class="user-thumb relative">
								<img src="" id="channel-thumbnail" alt="user-channel-picture" class="img-responsive" width="100%">
							</div>
						</div>
					</div>
					<span id="channel-name"></span>
					<button id="signOutButton" class="btn default mt15 mb15 centered" type="button">Logout</button>
				</div>
			</div>
		</div>
	</div>
	<?php

//	youtube channel user info
//	$template.= $this->Html->div('post-sign-in', 
//		$this->Html->div('user-information', 
//			$this->Html->div('user-photo', 
//				$this->Html->div('user-thumb relative', 
//					$this->Html->image('#', array(
//						'id'	=> 'channel-thumbnail', 
//						'alt'	=> 'user-channel-picture', 
//						'class'	=> 'img-responsive', 
//						'width'	=> '100%', 
//					))
//				)
//			)
//		).
//		$this->Html->tag('span', '', array(
//			'id' => 'channel-name'
//		)).
//		$this->Form->button(__('Sign Out'), array('id' => 'youtube-logout-button', 'class' => 'btn default')), 
//		array(
//		'align'	=> 'center', 
//		'class' => 'tacenter',
//	));

//	merge all template
//	$template = $this->Html->div('container-fluid', 
//		$this->Html->div('row', 
//			$this->Html->div('col-xs-12', 
//				$template
//			)
//		)
//	);
//	echo($template);

	echo($this->Html->div('info-upload-photo text-center', 
		$this->Html->div('row desc', 
			$this->Html->tag('h4', __(sprintf('Unggah ke %s', $youtubeText))).
			$this->Html->tag('p', __(sprintf('Anda dapat menambahkan video mengenai properti yang diiklankan dengan mengunggah video tersebut ke %s', $youtubeText)))
		)
	));

//	VIDEO INPUT PLACEHOLDER ======================================================================

	$allowedFormat = array(
		'.mov', '.mp4', '.m4a', 
		'.m4p', '.m4b', '.m4r', 
		'.m4v', '.avi', '.wmv', 
		'.mpg', '.mpeg', '.m2p', 
		'.ps', '.flv', '.3gp', '.webm'
	);

	$input = $this->Html->div('col-sm-5', 
		$this->Form->label('filename', __('Video'), array(
			'class' => 'control-label', 
		)).
		$this->Form->input('filename', array_replace($defaultOptions, array(
			'type'			=> 'file', 
			'data-role'		=> 'youtube-file-input', 
			'accept'		=> implode(', ', $allowedFormat), 
		)))
	);

	$input.= $this->Html->div('col-sm-5', 
		$this->Form->label('title', __('Judul'), array(
			'class' => 'control-label', 
		)).
		$this->Form->input('title', array_replace($defaultOptions, array(
			'placeholder'	=> __('Judul'), 
			'data-role'		=> 'youtube-title-input', 
		)))
	);

	$input.= $this->Html->div('col-sm-2', 
		$this->Form->label(NULL, '&nbsp;', array(
			'class' => 'control-label', 
		)).
		$this->Form->button(__('Unggah'), array(
			'type'		=> 'button', 
			'class'		=> 'btn background dark btn-lg btn-block', 
			'data-role'	=> 'youtube-upload-button', 
		))
	);

	$contents = $this->Form->create();
	$contents.= $this->Html->div('form-group', 
		$this->Html->div('row', 
			$input, 
			array(
				'data-role' => 'clone-row'
			)
		)
	);
	$contents.= $this->Form->end();

	echo($this->Html->div('container-fluid', $contents));
//	echo($this->Html->tag('hr'));

//	AJAX PLACEHOLDER =============================================================================

	$percentSpan	= $this->Html->tag('span', '', array('id' => 'percent-transferred'));
	$bytesSpan		= $this->Html->tag('span', '', array('id' => 'bytes-transferred'));
	$totalBytesSpan	= $this->Html->tag('span', '', array('id' => 'total-bytes'));

	$template = $this->Html->div('during-upload', 
		$this->Html->tag('p', $percentSpan.'% ('.$bytesSpan.' / '.$totalBytesSpan.' KB)', array('align' => 'center')).
		$this->Html->tag('progress', '', array('id' => 'upload-progress', 'max' => 1, 'value' => 0))
	);
	$template.= $this->Html->div('post-upload', $this->Html->tag('ul', '', array('id' => 'post-upload-status')));

	$template = $this->Html->div('container-fluid', 
		$this->Html->div('row', 
			$this->Html->div('col-xs-12', 
				$template, 
				array(
					'id' => 'progress-info-placeholder', 
				)
			)
		)
	);

	echo($template);

	echo($this->Form->hidden('property_id', array(
		'value'		=> $propertyID, 
		'data-role'	=> 'youtube-property_id-input',
	)));

?>