<?php

	$isAjax		= empty($isAjax) ? false : $isAjax;
	$model		= empty($model) ? 'CrmProjectDocument' : $model;
	$documents	= empty($documents) ? array() : $documents;
	$closing	= empty($closing) ? false : $closing;
	$record		= empty($record) ? array() : $record;

	$recordID		= Common::hashEmptyField($record, 'Property.id');
	$tableColumns	= array();

	if(empty($closing)){
		$tableColumns = array_merge($tableColumns, array(
			'check' => array(
				'name'	=> $this->Rumahku->buildCheckOption($model),
				'class'	=> 'tacenter',
			),
		));
	}

	$tableColumns	= array_merge($tableColumns, array(
		'title'		=> array('name' => __('Nama Dokumen')),
		'owner'		=> array('name' => __('Pemilik Dokumen')),
		'nama'		=> array('name' => __('Nama File')),
		'created'	=> array('name' => __('Diunggah')),
		'action'	=> array('name' => __('Action'), 'class' => 'tacenter'),
	));

	$tableColumns = $this->Rumahku->_generateShowHideColumn($tableColumns, 'field-table');

	$urlAdd = !empty($urlAdd)?$urlAdd:array(
		'admin' => true,
		'controller' => 'crm',
		'action' => 'project_document_add',
		$recordID,
	);

	$urlEdit = !empty($urlEdit)?$urlEdit:array(
		'admin' => true,
		'controller' => 'crm',
		'action' => 'project_document_edit',
		$recordID,
	);

	$urlDelete = !empty($urlDelete)?$urlDelete:array(
		'admin' => true,
		'controller' => 'crm',
		'action' => 'project_document_delete',
	);

	$urlDeleteDoc = array_merge($urlDelete, array($recordID));

	if(empty($closing)){

		?>
		<div class="detail-project-action mt10 mb10">
			<div class="row">
				<div class="col-md-6">
					<?php

						echo($this->Html->link(__('Tambah Dokumen'), $urlAdd, array(
							'class'	=> 'btn blue ajaxModal inline',
							'title'	=> __('Tambah Dokumen'),
							'data-size' => 'modal-md', 
						)));

						$buttonText = __('Hapus %s', $this->Html->tag('span', false, array(
							'class' => 'check-count-target',
						)));

						echo($this->Rumahku->buildButton(array(
							'text'		=> $buttonText,
							'url'		=> $urlDeleteDoc,
							'options'	=> array(
							//	'class'					=> 'check-multiple-delete',
								'id'					=> 'property-delete-document', 
								'data-alert'			=> __('Anda yakin ingin menghapus dokumen ini?'),
								'data-form'				=> '#CrmProjectDocumentAdminEasyMediaForm', 
								'data-wrapper-write'	=> '#property_media_wrapper', 
								'use_frame'				=> false, 
							),
						), 'button-type btn inline', 'btn red hide inline ml10 ajax-link'));

					?>
				</div>
			</div>
		</div>
		<?php

	}

?>
<div class="detail-project-table">
	<?php

		if($documents){

			?>
			<div class="table-responsive">
				<table class="table">
					<?php

						if($tableColumns){
							echo($this->Html->tag('thead', $this->Html->tag('tr', $tableColumns)));
						}

					?>
					<tbody>
						<?php

							$ownerName = empty($ownerName) ? false : $ownerName;

							foreach($documents as $key => $document){
								$documentID	= Common::hashEmptyField($document, 'CrmProjectDocument.id');
								$title		= Common::hashEmptyField($document, 'CrmProjectDocument.title', '-');
								$name		= Common::hashEmptyField($document, 'CrmProjectDocument.name');
								$ownerName	= Common::hashEmptyField($document, 'CrmProjectDocument.owner_name', $ownerName);
								$isShare	= Common::hashEmptyField($document, 'CrmProjectDocument.is_share');
								$created	= Common::hashEmptyField($document, 'CrmProjectDocument.created');
								$created	= $this->Time->niceShort($created);
								$action		= '';

								if(empty($closing)){
									$urlEditDoc = array_merge($urlEdit, array($documentID));

									$action.= $this->Html->link(__('Edit'), $urlEditDoc, array(
										'class' => 'ajaxModal',
										'title' => sprintf(__('Edit - %s'), $name),
									));
								}

								$action.= $this->Html->link(__('Unduh'), array(
									'admin'			=> true,
									'controller'	=> 'settings',
									'action'		=> 'download',
									'crm_document',
									$documentID,
								), array(
									'title' => __('Unduh Dokumen'),
								));

								$tableRows = array();

								if(empty($closing)){
										$tableRows = array_merge($tableRows, array(
										array(
											$this->Rumahku->buildCheckOption($model, $documentID, 'default', false, 'check-option', false, false, array(), array(
												'data-show' => '#property-delete-document', 
											)),
											array(
												'class' => 'actions tacenter',
											),
										),
									));
								}

								$tableRows = array_merge($tableRows, array(
									$title,
									$ownerName,
									$name,
								 	$created,
									array(
								 		$action,
										array(
											'class' => 'tacenter actions',
										),
									),
								));

								echo($this->Html->tableCells(array($tableRows)));
							}

						?>
					</tbody>
				</table>
			</div>
			<?php 

		}
		else{
			echo($this->Html->tag('p', __('Data belum tersedia'), array(
				'class' => 'alert alert-warning tacenter'
			)));
		}

	?>
</div>
<?php

	echo($this->element('blocks/common/pagination', array(
		'_ajax'		=> $isAjax, 
		'options'	=> array(
			'data-wrapper-write' => '#property_media_wrapper',
		), 
	)));

?>