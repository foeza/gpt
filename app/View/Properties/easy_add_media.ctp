<?php

	$isAjax				= isset($isAjax) ? $isAjax : false;
	$record				= empty($record) ? array() : $record;
	$propertyMedias		= empty($propertyMedias) ? array() : $propertyMedias;
	$propertyVideos		= empty($propertyVideos) ? array() : $propertyVideos;
	$propertyDocuments	= empty($propertyDocuments) ? array() : $propertyDocuments;

	$activeTab = empty($activeTab) ? 'photo' : $activeTab;
	$activeTab = in_array($activeTab, array('photo', 'video', 'document')) ? $activeTab : 'photo';

	$allowEdit = empty($allowEdit) ? false : $allowEdit;

	if($allowEdit){
		if($record){
			$draftID	= Configure::read('__Site.PropertyDraft.id');
			$savePath	= Configure::read('__Site.property_photo_folder');

			$recordID	= Common::hashEmptyField($record, 'Property.id');
			$sessionID	= Common::hashEmptyField($record, 'Property.session_id');

			echo($this->Form->hidden('Property.session_id', array(
				'value' => $sessionID, 
			)));

			?>
			<div class="step-medias">
				<div id="file-drop-zone">
					<div class="wrapper-upload-medias upload-photo">
						<div class="centered-tabs text-center">
							<ul class="rku-tabs tabs clear" redirect="false">
								<?php

									$ajaxURL = array(
										'admin'			=> true, 
										'controller'	=> 'properties', 
										'action'		=> 'easy_media', 
										$recordID, 
									);

								//	$class = $activeTab == 'photo' ? 'active' : '';

								//	echo($this->Html->tag('li', $this->Html->link(__('Foto'), '#photo-content', array(
								//		'class'		=> $class, 
								//		'escape'	=> false, 
								//	)), array('class' => $class)));

									$class = sprintf('ajax-link %s', ($activeTab == 'photo' ? 'active' : ''));

									echo($this->Html->tag('li', $this->Html->link(__('Foto'), array_merge($ajaxURL, array('photo')), array(
										'class'					=> $class, 
										'data-wrapper-write'	=> '#property_media_wrapper', 
										'escape'				=> false, 
									)), array(
										'class' => $class, 
										'style'	=> 'width: 33.3%', 
									)));

									$class = sprintf('ajax-link %s', ($activeTab == 'video' ? 'active' : ''));

									echo($this->Html->tag('li', $this->Html->link(__('Video'), array_merge($ajaxURL, array('video')), array(
										'class'					=> $class, 
										'data-wrapper-write'	=> '#property_media_wrapper', 
										'escape'				=> false, 
									)), array(
										'class' => $class, 
										'style'	=> 'width: 33.3%', 
									)));

									$class = sprintf('ajax-link %s', ($activeTab == 'document' ? 'active' : ''));

									echo($this->Html->tag('li', $this->Html->link(__('Dokumen'), array_merge($ajaxURL, array('document')), array(
										'class'					=> $class, 
										'data-wrapper-write'	=> '#property_media_wrapper', 
										'escape'				=> false, 
									)), array(
										'class' => $class, 
										'style'	=> 'width: 33.3%', 
									)));

								?>
							</ul>
						</div>
						<div class="content-upload-photo mt30">
							<div class="<?php echo(sprintf('tab-handle %s', ($activeTab == 'photo' ? false : 'hide'))); ?>" id="photo-content">
								<?php

									echo($this->element('blocks/properties/forms/easy_mode_media_content', array(
										'record'			=> $record, 
										'propertyMedias'	=> $propertyMedias, 
									)));

								?>
							</div>
							<div class="<?php echo(sprintf('tab-handle %s', ($activeTab == 'video' ? false : 'hide'))); ?>" id="video-content">
								<?php

									echo($this->element('blocks/properties/forms/easy_mode_video_content', array(
										'record'			=> $record, 
										'propertyVideos'	=> $propertyVideos, 
										'isAjax'			=> $isAjax, 
									)));

								?>
							</div>
							<div class="<?php echo(sprintf('tab-handle %s', ($activeTab == 'document' ? false : 'hide'))); ?>" id="document-content">
								<?php

									echo($this->element('blocks/properties/forms/easy_mode_document_content', array(
										'record'			=> $record, 
										'propertyDocuments'	=> $propertyDocuments, 
										'isAjax'			=> $isAjax, 
									)));

								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php

		}
		else{
			echo($this->Html->tag('div', $this->Html->tag('p', __('Properti tidak ditemukan.')), array(
				'class' => 'error-full alert', 
			)));
		}
	}

?>