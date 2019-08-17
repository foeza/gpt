<?php 
        // Set Build Input Form
        $options = array(
            'wrapperClass' => false,
            'frameClass' => false,
            'labelClass' => false,
            'rowFormClass' => false,
            'class' => false,
        );
        
		echo $this->Rumahku->buildInputForm('document_category_id', array_merge($options, array(
            'label' => __('Jenis Dokumen *'),
            'empty' => __('- Pilih Jenis Dokumen -'),
        )));

		echo $this->Rumahku->buildInputForm('title', array_merge($options, array(
			'type' => 'text',
            'label' => __('Judul Dokumen'),
        )));
?>