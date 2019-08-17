<?php 
        // search Client
        $autoUrl = $this->Html->url(array(
            'controller' => 'ajax',
            'action' => 'list_users',
            10,
            true,
            'admin' => false,
        ));

        $data = $this->request->data;
        $clientJobTypes = !empty($clientJobTypes)?$clientJobTypes:false;
        $modelName = !empty($modelName)?$modelName:'CrmProject';
        $error = isset($error)?$error:true;
        $data_target = !empty($data_target) ? $data_target : false;
        $genderOptions = $this->Rumahku->filterEmptyField($_global_variable, 'gender_options');
        $status_marital = $this->Rumahku->filterEmptyField($_global_variable, 'status_marital');

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
	                    'action_type' => 'kpr',
	                    'data_target' => $data_target,
	                    'admin' => false,
	                )),
	                'data-wrapper-write' => '#information-client',
	            ),
			));
			
	?>
</div>
<div id="information-client">
	<div class="row">
		<?php
				echo $this->Rumahku->buildInputGroup($modelName.'.client_name', sprintf(__('Nama Klien %s'), $mandatory), array(
					'placeholder' => __('Nama Klien'),
					'divClass' => 'col-sm-4 pr0',
					// 'readonly' => $_disabled,
    				'error' => $error,
				));
				echo $this->Rumahku->buildInputGroup($modelName.'.client_hp', sprintf(__('No. Handphone %s'), $mandatory), array(
					'placeholder' => __('No. Handphone Klien'),
					'divClass' => 'col-sm-4 pr0 pl0',
					// 'readonly' => $_disabled,
    				'error' => $error,
				));

				echo $this->Rumahku->buildInputGroup($modelName.'.client_job_type_id', __('Jenis Pekerjaan ').$mandatory, array(
					'type' => 'select',
					'divClass' => 'col-sm-4 pl0',					
					'options' => array(
						$clientJobTypes,
					),
					'error' => $error,
					// 'disabled' => $_disabled,
					'attributes' => array(
	                    'empty' => __('Pilih Jenis pekerjaan'),
	                ),
				));
		?>
	</div>
	<div class = "row">
		<?php
				echo $this->Rumahku->buildInputGroup($modelName.'.birthplace', sprintf(__('Tempat Lahir %s'), $mandatory), array(
					'placeholder' => __('Tempat Lahir'),
					'divClass' => 'col-sm-3 pr0',
					// 'readonly' => $_disabled,
					'error' => $error,
				));
				echo $this->Rumahku->buildInputGroup($modelName.'.birthday', sprintf(__('Tanggal Lahir %s'), $mandatory), array(
					'placeholder' => __('Tanggal Lahir'),
					'divClass' => 'col-sm-3 pr0 pl0',
					'inputClass' => 'birthdaypicker',
					// 'readonly' => $_disabled,
					'error' => $error,
				));

				echo $this->Rumahku->buildInputGroup($modelName.'.gender_id', __('Jenis Kelamin ').$mandatory, array(
	                'type' => 'select',
	                'placeholder' => __('Pilih Jenis Kelamin'),
	                'divClass' => 'col-sm-3 pr0 pl0',
	                'options' => array(
	                    $genderOptions,
	                ),
	                'error' => $error,
	                // 'disabled' => $_disabled,
	                'attributes' => array(
	                    'empty' => __('Pilih Jenis Kelamin'),
	                ),
	            ));

	            echo $this->Rumahku->buildInputGroup($modelName.'.status_marital', __('Status Menikah ').$mandatory, array(
                    'type' => 'select',
                    'divClass' => 'col-sm-3 pl0',
                    'options' => array(
                        null => __('Pilih status menikah'),
                        $status_marital,
                    ),
                    'error' => $error,
                ));
		?>
	</div>
	<div class = "row">
		<?php
				echo $this->Rumahku->buildInputGroup($modelName.'.address', sprintf(__('Alamat Klien %s'), $mandatory), array(
					'placeholder' => __('Alamat Klien'),
					'divClass' => 'col-sm-12',
					// 'readonly' => $_disabled,
					'error' => $error,
				));
		?>
	</div>
	<div class="row locations-trigger">
            <?php
	    			echo $this->Rumahku->setFormAddress( 'Kpr' );
                    echo $this->Rumahku->buildInputGroup($modelName.'.region_id', __('Provinsi ').$mandatory, array(
                        'type' => 'select',
                        'empty' => __('Pilih Provinsi'),
                        'inputClass'=>'regionId',
                        'placeholder' => __('Masukkan Provinsi sesuai KTP'),
                        'divClass' => 'col-sm-4 pr0',
                        'error' => $error,
                        'attributes' => array(
                            'empty' => __('Pilih Provinsi'),
                        ),
                    ));
                    echo $this->Rumahku->buildInputGroup($modelName.'.city_id', __('Kota ').$mandatory, array(
                        'type' => 'select',
                        'inputClass'=>'cityId',
                        'placeholder' => __('Masukkan Kota sesuai KTP'),
                        'divClass' => 'col-sm-4 pr0 pl0',
                        'error' => $error,
                        'attributes' => array(
                            'empty' => __('Pilih Kota'),
                        ),
                    ));
                    echo $this->Rumahku->buildInputGroup($modelName.'.subarea_id', __('Area ').$mandatory, array(
                        'type' => 'select',
                        'inputClass'=>'subareaId',
                        'placeholder' => __('Masukkan Area sesuai KTP'),
                        'divClass' => 'col-sm-4 pl0',
                        'error' => $error,
                        // 'options' => $subareas_0,
                        'attributes' => array(
                            'empty' => __('Pilih Area'),
                        ),
                    ));

                    echo $this->Form->hidden($modelName.'.zip', array(
						'inputClass' => 'rku-zip',
                    ));
            ?>
        </div>
</div>