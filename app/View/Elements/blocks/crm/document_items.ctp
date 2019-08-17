<?php 
		$id = !empty($id)?$id:false;
		$owner_name = !empty($owner_name)?$owner_name:false;
		$dataColumns = array();
		$urlAdd = !empty($urlAdd)?$urlAdd:array(
            'controller' => 'crm',
            'action' => 'project_document_add',
            $id,
            'admin' => true,
        );
		$urlEdit = !empty($urlEdit)?$urlEdit:array(
            'controller' => 'crm',
			'action' => 'project_document_edit',
			$id,
            'admin' => true,
        );
		$urlDelete = !empty($urlDelete)?$urlDelete:array(
            'controller' => 'crm',
			'action' => 'project_document_delete',
            'admin' => true,
        );

		if( empty($closing) ) {
			$dataColumns = array_merge($dataColumns, array(
	            'check' => array(
	                'name' => $this->Rumahku->buildCheckOption('CrmProjectDocument'),
	                'class' => 'tacenter',
	            ),
	        ));
		}

		$dataColumns = array_merge($dataColumns, array(
            'title' => array(
                'name' => __('Nama Dokumen'),
            ),
            'owner' => array(
                'name' => __('Pemilik Dokumen'),
            ),
            'nama' => array(
                'name' => __('NAMA FILE'),
            ),
            'created' => array(
                'name' => __('Diunggah'),
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'tacenter',
            ),
        ));

        $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table' );
		$urlDeleteDoc = $urlDelete;
		$urlDeleteDoc[] = $id;
        
		if( empty($closing) ) {
?>
<div class="detail-project-action">
	<div class="row">
		<div class="col-sm-6">
			<?php 
					echo $this->Html->link(__('Tambah Dokumen'), $urlAdd, array(
	                    'class' => 'btn blue ajaxModal btn-add',
	                    'title' => __('Unggah Dokumen'),
	                    'data-close' => 'reload',
	                ));

					echo $this->Rumahku->buildButton(array(
			            'text' => __('Hapus').$this->Html->tag('span', '', array(
			            	'class' => 'check-count-target',
		            	)),
			            'url' => $urlDeleteDoc,
		            	'options' => array(
		            		'class' => 'check-multiple-delete',
		            		'data-alert' => __('Anda yakin ingin menghapus dokumen ini?'),
		        		),
			        ), 'button-type button-style-1', 'btn red hide');
			?>
			<!-- <div class="col-sm-3 floright">
				<div class="total">
					<p><strong>1 - 3</strong> dari <strong>3 dokumen</strong></p>
				</div>
			</div> -->
		</div>
	</div>
</div>
<?php 
		}
?>
<div class="detail-project-table">
	<?php
			if( !empty($documents) ) {
	?>
	<div class="table-responsive">
		<table class="table">
	    	<?php
	                if( !empty($fieldColumn) ) {
	                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
	                }
	        ?>
			<tbody>
	      		<?php
		      			foreach( $documents as $key => $value ) {
		      				$doc_id = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'id');
			                $title = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'title', '-');
			                $name = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'name');
			                $is_share = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'is_share');
			                $created = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'created');

			                $owner = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'owner_name', $owner_name);

			                $customCreated = $this->Time->niceShort($created);
			                $action = '';

							if( empty($closing) ) {
								$urlEditDoc = $urlEdit;
								$urlEditDoc[] = $doc_id;
				                $action .= $this->Html->link(__('Edit'), $urlEditDoc, array(
			  						'class' => 'ajaxModal',
			  						'title' => sprintf(__('Edit - %s'), $name),
			  					));
				            }

			                $action .= $this->Html->link(__('Unduh'), array(
		      					'controller' => 'settings',
		      					'action' => 'download',
		      					'crm_document',
		      					$doc_id,
		      					'admin' => true,
		  					), array(
		  						'title' => __('Download Dokumen'),
		  					));
		  					$contentTable = array();

							if( empty($closing) ) {
			  					$contentTable = array_merge($contentTable, array(
						        	array(
						         		$this->Rumahku->buildCheckOption('CrmProjectDocument', $doc_id, 'default'),
							            array(
							            	'class' => 'actions tacenter',
						            	),
							        ),
						        ));
							}

		  					$contentTable = array_merge($contentTable, array(
					            $title,
					            $owner,
					        	$name,
					         	$customCreated,
					            array(
					         		$action,
						            array(
						            	'class' => 'tacenter actions',
					            	),
						        ),
					        ));

							echo $this->Html->tableCells(array($contentTable));
						}
	      		?>
			</tbody>
		</table>
	</div>
    <?php 
    		} else {
    			echo $this->Html->tag('p', __('Data belum tersedia'), array(
    				'class' => 'alert alert-warning'
				));
    		}
	?>
</div>
<?php 
		echo $this->element('blocks/common/pagination');
?>