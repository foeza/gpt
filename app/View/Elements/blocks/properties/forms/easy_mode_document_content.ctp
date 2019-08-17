<?php

	$record				= empty($record) ? array() : $record;
	$propertyDocuments	= empty($propertyDocuments) ? array() : $propertyDocuments;

	if($record){
		$draftID	= Configure::read('__Site.PropertyDraft.id');
		$savePath	= Configure::read('__Site.property_photo_folder');

		$recordID	= Common::hashEmptyField($record, 'Property.id');
		$sessionID	= Common::hashEmptyField($record, 'Property.session_id');

		?>
		<div class="content-upload-photo">
			<div class="info-full alert photo-info-top">
				<?php 

					$note = $this->Html->tag('strong', __('Tahukah Anda?'));
					$note = __('%s Dengan mengunggah dokumen-dokumen properti akan membantu Anda dalam menjual dan memproses transaksi properti.', $note);

					echo($this->Html->tag('p', $note));

				?>
			</div>
			<?php 

				$model = 'CrmProjectDocument';

				echo($this->Form->create($model, array(
					'class'					=> 'form-target',
					'data-type'				=> 'content', 
					'data-wrapper-write'	=> '#property_media_wrapper', 
				)));

				$_wrapper_ajax = empty($_wrapper_ajax) ? false : $_wrapper_ajax;

				if($_wrapper_ajax){
					echo($this->Form->hidden(false, array(
						'name'	=> 'is_easy_mode', 
						'value'	=> true, 
					)));

					echo($this->Form->hidden(false, array(
						'name'	=> '_wrapper_ajax', 
						'value'	=> $_wrapper_ajax, 
					)));
				}

				echo($this->Html->tag('div', $this->element('blocks/properties/forms/document_table', array(
					'model'		=> $model, 
					'record'	=> $record, 
					'documents'	=> $propertyDocuments, 
					'urlAdd'	=> array(
						'admin'			=> true,
						'controller'	=> 'properties',
						'action'		=> 'document_add',
						'draft'			=> $draftID,
						$recordID,
					),
					'urlEdit' => array(
						'admin'			=> true,
						'controller'	=> 'properties',
						'action'		=> 'document_edit',
					),
					'urlDelete' => array(
						'controller'	=> 'properties',
						'action'		=> 'document_delete',
						'admin'			=> true,
					),
				)), array(
					'class' => 'mt20',
				)));

				echo($this->Form->end());

			?>
		</div>
		<?php

	}

?>