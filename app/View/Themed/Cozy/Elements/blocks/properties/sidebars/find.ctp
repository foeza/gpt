<?php 
        $displayShow = !empty($displayShow)?$displayShow:false;
        $propertyTypes = !empty($propertyTypes)?$propertyTypes:false;
        $propertyActions = !empty($propertyActions)?$propertyActions:false;
        $propertyDirections = !empty($propertyDirections)?$propertyDirections:false;
        $subareas = !empty($subareas)?$subareas:false;

        $roomOptions = $this->Rumahku->filterEmptyField($_global_variable, 'room_options');
        $lotOptions = $this->Rumahku->filterEmptyField($_global_variable, 'lot_options');
        $priceOptions = $this->Rumahku->filterEmptyField($_global_variable, 'price_options');
?>
<div class="advanced-search-sidebar hidden-print locations-trigger search-placeholder">
    <?php
            echo $this->Html->tag('h2', __('Pencarian Cepat'), array(
                'class' => 'section-title',
            ));
            echo $this->Rumahku->setFormAddress( 'Search' );

            echo $this->Form->create('Search', array(
                'url'=> array(
                    'controller' => 'properties',
                    'action' => 'search',
                    'find',
                    'admin' => false,
                ), 
                'inputDefaults' => array('div' => false),
            ));

            echo $this->Form->hidden('show', array(
                'value' => $displayShow,
                'class' => 'show-sort'
            ));
    ?>
    <div class="form-group">
        <?php
                if( !empty($list_companies) ) {

                    echo $this->Form->input('principle_id',array(
                        'label'=> false, 
                        'required' => false,
                        'div' => false,
                        'class' => 'chosen-select form-control',
                        'empty' => __('Perusahaan'),
                        'options' => $list_companies,
                    ));
                }
                
                echo $this->Form->input('property_action',array(
                    'label'=> false, 
                    'required' => false,
                    'div' => false,
                    'class' => 'chosen-select form-control',
                    'empty' => __('Jenis Properti'),
                    'options' => $propertyActions,
                ));

                echo $this->Form->input('keyword', array(
                    'type' => 'text',
                    'class' => 'form-control keyword-refine',
                    'placeholder' => __('Provinsi, Kota, Area, ID Properti, dll...'),
                    'label' => false,
                    'div' => false
                ));

                echo $this->Form->input('user', array(
                    'type' => 'text',
                    'class' => 'form-control',
                    'placeholder' => __('Nama/Email Agen'),
                    'label' => false,
                    'div' => false
                ));

                echo $this->Form->input('typeid',array(
                    'label'=> false, 
                    'required' => false,
                    'div' => false,
                    'class' => 'chosen-select form-control',
                    'empty' => __('Tipe Properti'),
                    'options' => $propertyTypes,
                ));

                echo $this->Form->input('property_status_id',array(
                    'label'=> false, 
                    'required' => false,
                    'div' => false,
                    'class' => 'chosen-select form-control',
                    'empty' => __('Pilih Kategori Properti'),
                    'options' => $categoryStatus,
                ));

                echo $this->Form->input('region', array(
                    'type' => 'select',
                    'label' => false, 
                    'div' => false,
                    'required' => false,
                    'empty' => __('Provinsi'),
                    'class' => 'form-control regionId',
                ));

                echo $this->Form->input('city', array(
                    'type' => 'select',
                    'label' => false, 
                    'required' => false,
                    'div' => false,
                    'empty' => __('Kota'),
                    'class' => 'form-control cityId',
                ));

                echo $this->Form->input('subarea', array(
                    'type' => 'select',
                    'label' => false, 
                    'required' => false,
                    'div' => false,
                    'empty' => __('Area'),
                    'class' => 'form-control subareaId',
                    'options' => $subareas,
                ));

                echo $this->Form->input('lot_size', array(
                    'id' => 'lotSizeId', 
                    'label' => false, 
                    'required' => false,
                    'div' => false,
                    'data-placeholder' => __('Luas Tanah'),
                    'class' => 'form-control',
                    'empty' => __('Luas Tanah'),
                    'options' => $lotOptions,
                ));

                echo $this->Form->input('building_size', array(
                    'id' => 'buildingSizeId', 
                    'label' => false, 
                    'required' => false,
                    'div' => false,
                    'data-placeholder' => __('Luas Bangunan'),
                    'class' => 'form-control',
                    'empty' => __('Luas Bangunan'),
                    'options' => $lotOptions,
                ));
            
                echo $this->Form->input('beds', array(
                    'label' => false, 
                    'required' => false,
                    'div' => false,
                    'class' => 'form-control',
                    'id' => 'search_bedrooms',
                    'empty' => __('Kamar Tidur'),
                    'options' => $roomOptions,
                ));

                echo $this->Form->input('baths', array(
                    'label' => false, 
                    'required' => false,
                    'div' => false,
                    'class' => 'form-control',
                    'id' => 'search_bathrooms',
                    'empty' => __('Kamar Mandi'),
                    'options' => $roomOptions,
                ));

                echo $this->Form->input('property_direction',array(
                    'label'=> false, 
                    'required' => false,
                    'div' => false,
                    'class' => 'chosen-select form-control',
                    'empty' => __('Arah Bangunan'),
                    'options' => $propertyDirections,
                ));

			//	echo $this->Form->input('price', array(
			//		'label' => false, 
			//		'required' => false,
			//		'div' => false,
			//		'class' => 'form-control',
			//		'empty' => __('Range Harga'),
			//		'options' => $priceOptions,
			//	));

				echo($this->element('blocks/common/forms/dynamic_price_input', array(
					'empty'		=> __('Range Harga'), 
					'model'		=> 'Search', 
					'field'		=> 'price', 
					'freetext'	=> true, 
					'options'	=> $priceOptions, 
				)));

                echo $this->Form->input('certificate', array(
                    'label' => false, 
                    'required' => false,
                    'div' => false,
                    'class' => 'form-control',
                    'empty' => __('Sertifikat'),
                    'options' => $certificates,
                ));

                echo $this->Html->tag('p', $this->Form->button(__('Cari'), array(
                    'type' => 'submit', 
                    'class' => 'btn btn-default-color',
                )), array(
                    'class' => 'center',
                ));
        ?>
    </div>
    <?php
            echo $this->Form->end();
    ?>
</div>
<!-- END ADVANCED SEARCH -->