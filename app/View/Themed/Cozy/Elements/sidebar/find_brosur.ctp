<!-- BEGIN ADVANCED SEARCH -->
<?php
        $propertyActions = !empty($propertyActions)?$propertyActions:false;
        $propertyDirections = !empty($propertyDirections)?$propertyDirections:false;
        $certificates = !empty($certificates)?$certificates:false;

        $furnishedOptions = $this->Rumahku->filterEmptyField($_global_variable, 'furnished');
        $roomOptions = $this->Rumahku->filterEmptyField($_global_variable, 'room_options');
        $lotOptions = $this->Rumahku->filterEmptyField($_global_variable, 'lot_options');
        $priceOptions = $this->Rumahku->filterEmptyField($_global_variable, 'price_options');

        echo $this->Html->tag('h2', __('Pencarian E-Brosur'), array(
            'class' => 'section-title'
        ));
?>
    <div class="form-group locations-trigger">
        
        <div class="col-sm-12">
            <?php
                    echo $this->Form->input('typeid',  array(
                        'label' => false, 
                        'div' => false,
                        'required' => false,
                        'empty' => __('Semua Tipe Properti'),
                        'class' => 'form-control',
                        'options' => $propertyTypes,
                    ));

                    echo $this->Form->input('property_action', array(
                        'label' => false, 
                        'div' => false,
                        'required' => false,
                        'empty' => __('Jenis Properti'),
                        'class' => 'form-control',
                        'options' => $propertyActions,
                    ));

                    echo $this->Form->input('date',array(
                        'label'=> false, 
                        'placeholder'=> __('Tgl Dibuat'), 
                        'class'=>'form-control date-range',
                        'required' => false,
                        'type' => 'text',
                        'autocomplete'=> false,
                    ));

                    echo $this->Form->input('code',array(
                        'label'=> false, 
                        'placeholder' => __('ID Brosur'),
                        'required' => false,
                        'div' => false,
                        'class' => 'form-control'
                    ));

                    echo $this->Form->input('name',array(
                        'label'=> false, 
                        'required' => false,
                        'placeholder' => __('Nama / Email Agen'),
                        'div' => false,
                        'class' => 'form-control',
                    ));

                    echo $this->Rumahku->setFormAddress( 'Search' );
                    echo $this->Form->input('region', array(
                        'label' => false, 
                        'div' => false,
                        'required' => false,
                        'empty' => __('Provinsi'),
                        'data-placeholder' => __('Provinsi'),
                        'class' => 'form-control regionId',
                        'options' => array()
                    ));

                    echo $this->Form->input('city', array(
                        'label' => false, 
                        'required' => false,
                        'div' => false,
                        'options' => (!empty($cities)) ? $cities : array(),
                        'empty' => __('Kota'),
                        'class' => 'form-control cityId',
                        'data-placeholder' => __('Kota'),
                    ));

                    echo $this->Form->input('subarea', array(
                        'label' => false, 
                        'required' => false,
                        'div' => false,
                        'options' => (!empty($subareas)) ? $subareas : array(),
                        'empty' => __('Area'),
                        'data-placeholder' => __('Area'),
                        'class' => 'form-control subareaId'
                    ));

                    echo $this->Form->input('price', array(
                        'label' => false, 
                        'required' => false,
                        'div' => false,
                        'options' => $priceOptions,
                        'empty' => __('Harga (Rp)'),
                        'data-placeholder' => __('Harga (Rp)'),
                        'class' => 'form-control'
                    ));

                    echo $this->Form->input('beds', array(
                        'label' => false, 
                        'required' => false,
                        'div' => false,
                        'options' => $roomOptions,
                        'empty' => __('Kamar Tidur'),
                        'data-placeholder' => __('Kamar Tidur'),
                        'class' => 'form-control'
                    ));

                    echo $this->Form->input('baths', array(
                        'label' => false, 
                        'required' => false,
                        'div' => false,
                        'options' => $roomOptions,
                        'empty' => __('Kamar Mandi'),
                        'data-placeholder' => __('Kamar Mandi'),
                        'class' => 'form-control'
                    ));

                    echo $this->Form->input('lot_size', array(
                        'label' => false, 
                        'required' => false,
                        'div' => false,
                        'options' => $lotOptions,
                        'empty' => __('Luas Tanah'),
                        'data-placeholder' => __('Luas Tanah'),
                        'class' => 'form-control'
                    ));

                    echo $this->Form->input('building_size', array(
                        'label' => false, 
                        'required' => false,
                        'div' => false,
                        'options' => $lotOptions,
                        'empty' => __('Luas Bangunan'),
                        'data-placeholder' => __('Luas Bangunan'),
                        'class' => 'form-control'
                    ));

                    echo $this->Form->input('certificate_id', array(
                        'label' => false, 
                        'required' => false,
                        'div' => false,
                        'options' => $certificates,
                        'empty' => __('Sertifikat'),
                        'data-placeholder' => __('Sertifikat'),
                        'class' => 'form-control'
                    ));

                    echo $this->Form->input('furnished', array(
                        'label' => false, 
                        'required' => false,
                        'div' => false,
                        'options' => $furnishedOptions,
                        'empty' => __('Interior'),
                        'data-placeholder' => __('Interior'),
                        'class' => 'form-control'
                    ));

                    echo $this->Form->input('property_direction_id', array(
                        'label' => false, 
                        'required' => false,
                        'div' => false,
                        'options' => $propertyDirections,
                        'empty' => __('Hadap'),
                        'data-placeholder' => __('Hadap'),
                        'class' => 'form-control'
                    ));

                    echo $this->Form->hidden('user');
            ?>
        </div>
            
        <p>&nbsp;</p>
        <p class="center">
            <button type="submit" class="btn btn-default-color">Cari</button>
        </p>
    </div>
<!-- END ADVANCED SEARCH -->