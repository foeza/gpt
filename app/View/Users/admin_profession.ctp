<?php 
		// Set Build Input Form
        $urlBack = !empty($urlBack)?$urlBack:false;
        $options = array(
            'frameClass' => 'col-sm-10',
            'labelClass' => '',
            'class' => '',
        );
        $optionsCheckbox = array(
        	'classLabel' => 'col-sm-10',
        	'classForm' => 'col-sm-10',
        	'classList' => 'col-sm-3',
        );
?>
<div class="user-profession mb30">
	<?php
			echo $this->element('blocks/users/simple_info');
            echo $this->element('blocks/users/tabs/profile');
			echo $this->Form->create('UserConfig');
            echo $this->Html->tag('h2', __('Informasi Profesi'), array(
                'class' => 'sub-heading'
            ));

			echo $this->Rumahku->buildInputForm('award', array_merge($options, array(
                'type' => 'textarea',
                'label' => __('Penghargaan'),
                'rows' => 3,
	        )));
			echo $this->Rumahku->buildInputForm('experience', array_merge($options, array(
                'type' => 'textarea',
                'label' => __('Pengalaman Kerja'),
                'rows' => 3,
	        )));

            echo $this->element('blocks/common/forms/list_checkbox', array_merge($optionsCheckbox, array(
            	'label' => __('Tipe Klien'),
            	'description' => __('Pilihlah 1 atau lebih dari tipe klien yang lebih sering atau ingin Anda tangani.'),
            	'values' => $client_types,
            	'modelName' => 'UserClientType',
            	'fieldName' => 'client_type_id',
	        )));

            echo $this->element('blocks/common/forms/list_checkbox', array_merge($optionsCheckbox, array(
            	'label' => __('Tipe Properti'),
            	'description' => __('Pilihlah 1 atau lebih tipe properti yang lebih sering atau ingin Anda iklankan.'),
            	'values' => $propertyTypes,
            	'modelName' => 'UserPropertyType',
            	'fieldName' => 'property_type_id',
	        )));

            echo $this->element('blocks/common/forms/list_checkbox', array_merge($optionsCheckbox, array(
            	'label' => __('Spesialisasi'),
            	'description' => __('Pilihlah 1 atau lebih dari tipe kegiatan properti yang menjadi kehandalan Anda.'),
            	'values' => $specialists,
                'modelName' => 'UserSpecialist',
            	'fieldName' => 'specialist_id',
	        )));

            echo $this->element('blocks/common/forms/list_checkbox', array_merge($optionsCheckbox, array(
            	'label' => __('Bahasa'),
            	'description' => __('Pilihlah 1 atau lebih dari bahasa yang Anda kuasai dan dapat digunakan secara aktif.'),
            	'values' => $languages,
                'modelName' => 'UserLanguage',
            	'fieldName' => 'language_id',
            	'customContent' => $this->element('blocks/common/forms/other_checkbox', array(
                    'modelName' => 'UserLanguage',
                    'fieldName' => 'other_id',
                    'fieldNameText' => 'other_text',
            		'description' => __('Berikan tanda koma untuk lebih dari 1 bahasa'),
        		)),
	        )));

            echo $this->element('blocks/common/forms/list_checkbox', array_merge($optionsCheckbox, array(
            	'label' => __('Sertifikasi'),
            	'description' => __('Pilihlah 1 atau lebih sertifikasi berkaitan dengan bidang properti yang Anda miliki.'),
            	'values' => $agent_certificates,
                'modelName' => 'UserAgentCertificate',
            	'fieldName' => 'agent_certificate_id',
            	'customContent' => $this->element('blocks/common/forms/other_checkbox', array(
                    'modelName' => 'UserAgentCertificate',
            		'fieldName' => 'other_id',
            		'fieldNameText' => 'other_text',
            		'description' => __('Berikan tanda koma untuk lebih dari 1 sertifikasi'),
        		)),
	        )));

            echo $this->element('blocks/common/forms/action_custom', array(
                '_with_submit' => true,
                '_margin_class' => 'bottom',
                '_button_text' => __('Simpan Perubahan'),
                '_textBack' => __('Kembali'),
                '_classBack' => 'btn default floleft',
                '_button_class' => 'floright',
                '_urlBack' => $urlBack,
            ));
			echo $this->Form->end(); 
	?>
</div>