<?php
        $data = $this->request->data;
        $_class = isset($_class) ? $_class : '';

        $filter = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_home_right_filter_cozy');
        $property_action = $this->Rumahku->filterEmptyField($data, 'Search', 'property_action', 1);

        if( !empty($filter) ){
?>
<div id="find_agents" data-animation-direction="fade" data-animation-delay="250" class="<?php echo $_class; ?> locations-trigger">
    <?php 
            echo $this->Html->tag('h2', __('Pencarian Cepat'), array(
                'class' => 'section-title'
            ));

            echo $this->Form->create('Search', array(
                'url'=> array(
                    'controller' => 'properties',
                    'action' => 'search',
                    'find',
                    'admin' => false
                ), 
                'inputDefaults' => array('div' => false),
            ));
            
            echo $this->Rumahku->buildFrontEndInputForm('property_action', false, array(
                'type' => 'select',
                'frameClass' => false,
                'options' => $propertyActions,
            ));
            echo $this->Rumahku->buildFrontEndInputForm('property_status_id', false, array(
                'type' => 'select',
                'frameClass' => false,
                'empty' => __('- Pilih Kategori Properti -'),
                'options' => $categoryStatus,
            ));
            echo $this->Rumahku->buildFrontEndInputForm('keyword', false, array(
                'type' => 'text',
                'placeholder' => __('Provinsi, Kota, Area, ID Properti, dll...'),
                'frameClass' => false,
            ));
            echo $this->Rumahku->buildFrontEndInputForm('region', false, array(
                'type' => 'select',
                'frameClass' => false,
                'empty' => __('- Pilih Provinsi -'),
                'inputClass' => 'form-control regionId',
            ));
            echo $this->Rumahku->buildFrontEndInputForm('city', false, array(
                'type' => 'select',
                'frameClass' => false,
                'empty' => __('- Pilih Kota -'),
                'inputClass' => 'form-control cityId',
            ));
            echo $this->Rumahku->buildFrontEndInputForm('subarea', false, array(
                'type' => 'select',
                'frameClass' => false,
                'empty' => __('- Pilih Area -'),
                'inputClass' => 'form-control subareaId',
            ));

            echo $this->Html->tag('div', $this->Form->button(__('Cari'), array(
                'type' => 'submit', 
                'class' => 'btn btn-default',
            )), array(
                'class' => 'form-actions',
            ));

            echo $this->Rumahku->setFormAddress( 'PropertyAddress' );
            echo $this->Form->end();
    ?>
</div>
<?php
        }
?>