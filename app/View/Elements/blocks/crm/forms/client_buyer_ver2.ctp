<?php 
        // search Client
        $autoUrl = $this->Html->url(array(
            'controller' => 'ajax',
            'action' => 'list_users',
            10,
            true,
            'admin' => false,
        ));

        $allow_ext = __('* Hanya File berekstensi jpg, gif, png dan pdf');

        $data = $this->request->data;
        $modelName = !empty($modelName)?$modelName:'CrmProject';
        $error = isset($error)?$error:true;
        $data_target = !empty($data_target) ? $data_target : false;
?>
<div class="row" id="client_info">
	<?php 
	        echo $this->Rumahku->buildInputGroup($modelName.'.client_email', sprintf('Email Klien %s', $mandatory), array(
	            'type' => 'text',
	            'id' => 'autocomplete2',
				'placeholder' => __('Email Klien'),
				'divClass' => 'col-sm-4 pr0',
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
	                    'action_type' => 'kpr',
	                    'data_target' => $data_target,
	                    'template' => 'client_buyer_ver2',
	                    'admin' => false,
	                )),
	                'data-wrapper-write' => '#client_info',
	            ),
			));

			echo $this->Rumahku->buildInputGroup($modelName.'.client_name', sprintf(__('Nama Klien %s'), $mandatory), array(
				'placeholder' => __('Nama Klien'),
				'divClass' => 'col-sm-4 pr0 pl0 information-client',
				// 'readonly' => $_disabled,
				'error' => $error,
			));

			echo $this->Rumahku->buildInputGroup($modelName.'.client_hp', sprintf(__('No. Handphone %s'), $mandatory), array(
				'placeholder' => __('No. Handphone Klien'),
				'divClass' => 'col-sm-4 pl0 information-client',
				// 'readonly' => $_disabled,
				'error' => $error,
			));
	?>
</div>