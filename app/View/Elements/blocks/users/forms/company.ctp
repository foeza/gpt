<div class="locations-trigger">
    <?php 
            // Set Build Input Form
            $options = array(
                'frameClass' => 'col-sm-12',
                'labelClass' => 'col-xl-1 col-sm-4 col-md-3 control-label taright',
                'class' => 'relative col-sm-8 col-xl-4',
            );

            $data = $this->request->data;
            $save_path = Configure::read('__Site.logo_photo_folder');
            $photoSize = $this->Rumahku->_rulesDimensionImage($save_path, 'large', 'size');
            $logo = $this->Rumahku->filterEmptyField($data, 'UserCompany', 'logo_hide');

            echo $this->Html->tag('h2', __('Informasi Perusahaan'), array(
                'class' => 'sub-heading'
            ));
            echo $this->Rumahku->buildInputForm('logo', array_merge($options, array(
                'type' => 'file',
                'label' => sprintf(__('Logo ( %s ) *'), $photoSize),
                'preview' => array(
                    'photo' => $logo,
                    'save_path' => $save_path,
                    'size' => 'xxsm',
                ),
            )));
            echo $this->Rumahku->buildInputForm('name', array_merge($options, array(
                'label' => __('Nama Perusahaan *'),
            )));
            echo $this->Rumahku->buildInputForm('description', array_merge($options, array(
                'label' => __('Tentang Perusahaan'),
                'inputClass' => 'ckeditor',
            )));
            
            echo $this->Html->tag('h2', __('Informasi Alamat'), array(
                'class' => 'sub-heading'
            ));

            $mandatory      = '*';
            $modelName      = 'UserCompany';
            $inputOptions   = array(
                'frameClass'    => 'col-sm-12',
                'labelClass'    => 'col-xl-1 col-sm-4 col-md-3 control-label taright',
                'class'         => 'relative col-sm-8 col-xl-4',
            );

            echo($this->Rumahku->buildInputForm($modelName.'.address', array_replace($inputOptions, array(
                'label'         => __('Alamat %s', $mandatory),
                'attributes'    => array(
                    'id' => 'rku-address', 
                ), 
            ))));

			echo($this->Rumahku->buildInputForm('additional_address', array_replace($inputOptions, array(
				'label'		=> __('Alamat Lain (optional)'),
			//	'infoText'	=> __('Harap masukkan alamat dengan jelas dan lengkap.'),
			//	'class'		=> 'relative col-sm-5 col-xl-7',
			))));

            echo($this->element('blocks/properties/forms/location_picker', array(
                'options' => array_replace($inputOptions, array(
                    'mandatory' => $mandatory, 
                    'model'     => $modelName, 
                )), 
            )));

            echo($this->Rumahku->buildInputForm($modelName.'.zip', array_replace($inputOptions, array(
                'label'         => __('Kode Pos %s', $mandatory),
                'inputClass'    => 'rku-zip', 
                'class'         => 'relative col-sm-2 col-xl-7',
            ))));

            /*
            echo $this->Rumahku->buildInputForm('address', array_merge($options, array(
                'id' => 'rku-address',
                'label' => __('Alamat *'),
            )));
            echo $this->Rumahku->buildInputForm('region_id', array_merge($options, array(
                'label' => __('Provinsi *'),
                'inputClass' => 'regionId',
                'empty' => __('Pilih Provinsi'),
            )));
            echo $this->Rumahku->buildInputForm('city_id', array_merge($options, array(
                'label' => __('Kotamadya *'),
                'inputClass' => 'cityId',
                'empty' => __('Pilih Kota'),
            )));
            echo $this->Rumahku->buildInputForm('subarea_id', array_merge($options, array(
                'label' => __('Area *'),
                'inputClass' => 'subareaId',
                'empty' => __('Pilih Area'),
            )));
            echo $this->Rumahku->buildInputForm('zip', array_merge($options, array(
                'label' => __('Kode Pos *'),
                'inputClass' => 'rku-zip',
            )));
            echo $this->Rumahku->setFormAddress( 'UserCompany' );
            */

            /*
            Sementara
    ?>
    <div class="row">
    	<div class="col-sm-12">
    	  	<div class="form-group">
    	       	<div class="row">
    	            <div id="map_container">
    	                <div id="gmap-rku"></div>
    	            </div>
    	        </div>
    	    </div>
        </div>
    </div>
    <?php
            */

		//	echo $this->Rumahku->buildInputForm('additional_address', array_merge($options, array(
		//		'label' => __('Alamat Lain (optional)'),
		//		'class' => 'relative col-sm-5 col-xl-7',
		//		'infoText' => __('Harap masukkan alamat dengan jelas dan lengkap.'),
		//	)));
        	echo $this->Html->tag('h2', __('Informasi Kontak'), array(
            	'class' => 'sub-heading'
            ));
            echo $this->Rumahku->buildInputForm('contact_name', array_merge($options, array(
                'label' => __('Nama Kontak *'),
            )));
            echo $this->Rumahku->buildInputForm('contact_email', array_merge($options, array(
                'label' => __('Email Kontak *'),
            )));
            echo $this->Rumahku->buildInputForm('phone', array_merge($options, array(
                'label' => __('No. Telepon *'),
                'class' => 'relative col-sm-5 col-xl-7',
                'infoText' => __('Harap gunakan kode area untuk nomor telepon. Contoh: 0215332555'),
            )));
            echo $this->Rumahku->buildInputForm('phone_2', array_merge($options, array(
                'label' => __('No. Telepon 2'),
                'class' => 'relative col-sm-5 col-xl-7',
                'infoText' => __('Harap gunakan kode area untuk nomor telepon. Contoh: 0215332555'),
            )));
            echo $this->Rumahku->buildInputForm('fax', array_merge($options, array(
                'label' => __('Fax'),
                'class' => 'relative col-sm-5 col-xl-7',
                'infoText' => __('Harap gunakan kode area untuk fax. Contoh: 0215332555'),
            )));
    ?>
</div>