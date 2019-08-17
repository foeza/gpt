<?php 
        $saveFolder = Configure::read('__Site.document_folder');
        $dataMedias = !empty($dataMedias)?$dataMedias:false;
        $session_id = !empty($session_id)?$session_id:false;
        $id = !empty($id)?$id:0;

	//	tambahan saat direquest lewat easy mode
		$_wrapper_ajax	= isset($_wrapper_ajax) ? $_wrapper_ajax : false;
?>
<div class="crm-document-form">
	<div class="content" id="wrapper-modal-write-2form">
		<?php 
	            echo $this->UploadForm->loadCrm($this->Html->url(array(
	                'controller' => 'kpr',
		            'action' => 'process_appraisal_upload',
	                $id,
	                $session_id,
	                'admin' => true,
	            )), $dataMedias, $saveFolder, array(
	            	'label' => __('Tambah Dokumen'),
	            	'label_class' => 'file-uploads darkblue pull-left',
	            ));

				echo $this->Form->create('CrmProjectDocument', array(
					'title' => __('Proses Appraisal'), 
					'class' => 'ajax-form',
					'data-type' => 'content',
					'data-wrapper-write' => $_wrapper_ajax ? sprintf('#%s', $_wrapper_ajax) : '#wrapper-modal-write',
					'data-reload' => 'true',
					'id' => 'wrapper-modal-write',
				));

            	echo $this->element('blocks/common/flash');

		        // Set Build Input Form
		        $options = array(
		            'wrapperClass' => false,
		            'frameClass' => false,
		            'labelClass' => false,
		            'rowFormClass' => false,
		            'class' => false,
		        );

				echo $this->Rumahku->buildInputForm('note', array_merge($options, array(
					'type' => 'textarea',
		            'label' => __('Keterangan / Catatan'),
		        )));
       	?>
		<div class="modal-footer">
			<?php 
		            echo $this->Html->link(__('Upload Bukti Bayar'), 'javascript:void(0);', array(
		                'class' => 'file-uploads btn uploads btn darkblue pull-left'
		            ));

					echo $this->Form->button(__('Proses'), array(
	    	            'class' => 'btn blue',
	    	        ));
			?>
		</div>
		<?php 
				echo $this->Form->hidden('session_id', array(
					'value' => $session_id,
	            ));
				echo $this->Form->end(); 
		?>
	</div>
</div>