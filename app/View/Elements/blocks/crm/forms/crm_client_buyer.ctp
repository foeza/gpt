<?php 
		$data = $this->request->data;
        $modelName = !empty($modelName)?$modelName:'CrmProject';
        $data_target = !empty($data_target) ? $data_target : false;
        $error = !empty($error) ? $error : false;

        // search Client
        $autoUrl = $this->Html->url(array(
            'controller' => 'ajax',
            'action' => 'list_users',
            10,
            true,
            'admin' => false,
        ));

        if( empty($id) ) {
	        $client_type = $this->Rumahku->filterEmptyField($data, $modelName, 'client_type', 'registered');
	        $this->request->data[$modelName]['client_type'] = $client_type;

	        if( $client_type == 'registered' && !isset($_disabled) ) {
	        	$_disabled = true;
	        } else {
	        	$_disabled = !empty($_disabled)?$_disabled:false;
	        }
	    } else {
        	$_disabled = true;
	    }

	    if( empty($id) ) {
			echo $this->Rumahku->buildInputRadio($modelName.'.client_type', array(
				'registered' => __('Klien terdaftar'),
				'new' => __('Klien Baru'),
			), array(
	            'label' => sprintf(__('Jenis Klien %s'), $mandatory),
	            'error' => false,
				'frameClass' => 'col-sm-12',
				'labelClass' => 'col-sm-12',
				'class' => 'col-sm-12',
				'divClass' => 'input-group client-type trigger-disabled',
				'attributes' => array(
					'data-target' => '#information-client',
					'data-value' => 'registered',
					'data-autocomplete' => '#autocomplete2',
		            'autocomplete' => 'off',
				),
	        ));
		}
?>
<div class="row">
	<?php
			echo $this->Rumahku->buildInputGroup($modelName.'.client_email', sprintf('Email Klien %s', $mandatory), array(
	            'type' => 'text',
	            'id' => 'autocomplete2',
				'placeholder' => __('Email Klien'),
				'divClass' => 'col-sm-12',
    			'error' => $error,
				'errorFieldName' => $modelName.'.client_email',
	            'attributes' => array(
    				'autocomplete' => 'off',
	                'data-use-current-value' => 'true',
	                'data-change' => 'trigger',
    				'data-ajax-url' => $autoUrl,
	                'href' => $this->Html->url(array(
	                    'controller' => 'ajax',
	                    'action' => 'get_data_client',
	                    'model_name' => $modelName,
	                    'action_type' => 'crm',
	                    'data_target' => $data_target,
	                    'admin' => false,
	                )),
	                'data-wrapper-write-page' => '#information-client,#wrapper-kpr-document',
	            ),
	            'divClass' => 'col-sm-4 pr0',
			));
	?>
	<div id="information-client">
	<?php

			echo $this->Rumahku->buildInputGroup($modelName.'.client_name', sprintf(__('Nama Klien %s'), $mandatory), array(
				'placeholder' => __('Nama Klien'),
				'divClass' => 'col-sm-4 pr0 pl0',
				'readonly' => $_disabled,
				'error' => $error,
			));
			echo $this->Rumahku->buildInputGroup($modelName.'.client_hp', sprintf(__('No. Handphone %s'), $mandatory), array(
				'placeholder' => __('No. Handphone Klien'),
				'divClass' => 'col-sm-4 pl0',
				'readonly' => $_disabled,
				'error' => $error,
			));
	?>
	</div>
</div>