<?php 
        $placeholderDescription = __('Berikan deskripsi mengenai detil dari properti ini. Anda juga bisa menyertakan detil mengenai kondisi bangunan properti saat ini, fasilitas sekitar seperti sekolah, pusat perbelanjaan, dan lainnya.');
        $data = $this->request->data;
        $property_type_id = $this->Rumahku->filterEmptyField($data, 'Property', 'property_type_id');
        $property_action_id = $this->Rumahku->filterEmptyField($data, 'Property', 'property_action_id');

        $is_connect_r123 = $this->Rumahku->filterEmptyField($data, 'UserIntegratedConfig', 'is_connect_r123');
        $is_connect_olx  = $this->Rumahku->filterEmptyField($data, 'UserIntegratedConfig', 'is_connect_olx');

        echo $this->Form->create('Property', array(
            'class' => 'form-horizontal',
            'id' => 'sell-property',
        ));
?>
<div class="step-1 toggle-page active user-fill">
    <?php 
            echo $this->element('blocks/properties/forms/input_email');

            echo $this->Rumahku->buildForm('property_action_id', __('Status Properti *'), array(
                'type' => 'radio',
                'options' => $propertyActions,
                'frame-size' => 'large',
                'value' => $property_action_id,
            ), 'horizontal');

            echo $this->Rumahku->buildForm('property_type_id', __('Jenis Properti *'), array(
                'type' => 'radio',
                'options' => $propertyTypes,
                'frame-size' => 'large',
                'value' => $property_type_id,
            ), 'horizontal');

            echo $this->Rumahku->buildInputForm('Property.title', array(
                'frameClass' => 'col-sm-12',
                'label' => __('Kalimat Promosi *'),
                'type' => 'text',
                'labelClass' => 'col-xl-2 taright col-sm-3',
                'class' => 'relative col-sm-7 col-xl-4',
                'id' => 'desc-info',
                'data_max_lenght' => 60,
                'infoText' => __('Contoh: Rumah Baru Minimalis 2 Lt 3 KT 2 KM Siap Huni Full Furnished'),
                'overflowTextContent' => array(
                    'text' => sprintf(__('%s karakter tersisa'), $this->Html->tag('span', 60, array(
                        'class' => 'limit-character'
                    ))),
                    'options' => array(
                        'class' => 'overflow-text'
                    )
                ),
                'infoClass' => 'extra-text',
                'inputClass' => 'title-property-filter',
                'attributes' => array(
                    'data-role' => 'word-filter'
                )
            ));

            echo $this->Rumahku->buildInputForm('Property.description', array(
                'frameClass' => 'col-sm-12',
                'label' => __('Deskripsi Properti *'),
                'placeholder' => $placeholderDescription,
                'type' => 'textarea',
                'labelClass' => 'col-xl-2 taright col-sm-3',
                'class' => 'relative col-sm-7 col-xl-4',
                'infoText' => __('Minimum 30 karakter'),
                'infoClass' => 'extra-text',
                'rows' => 10,
            ));
    ?>
    <div class="form-group plus">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1 label">
                <?php 
                        echo $this->Html->tag('h4', __('Informasi Vendor'));
                        echo $this->Html->tag('p', __('Untuk dapat menyimpan informasi Vendor/Client, Email, Nama dan No. HP harus diisi'));
                ?>
            </div>
        </div>
    </div>
    <?php
            echo $this->element('blocks/properties/forms/input_client', array(
                'ajax_blur' => false,
            ));

            echo $this->element('blocks/properties/forms/input_meta_tag', array(
                'ajax_blur' => false,
            ));

            // if( !empty($is_connect_olx)) {
                echo $this->Rumahku->buildInputToggle('UserIntegratedSyncProperty.do_sync', array(
                    'label' => __('Sync OLX'),
                    'frameClass' => 'col-sm-12',
                    'class' => 'relative col-xl-4 col-sm-7', 
                    'labelClass' => 'col-xl-2 col-sm-3 taright',
                    'infoText' => __('Info: pastikan bahwa data properti sudah lengkap'),
                    'infoClass' => 'info-toggle-sync',
                ));
            // }

            echo $this->element('blocks/properties/sell_action', array(
                'action_type' => 'bottom',
                'urlBack' => array(
                    'controller' => 'properties',
                    'action' => 'index',
                    'admin' => true,
                ),
                'labelBack' => __('Kembali'),
            ));
    ?>
</div>
<?php 
        echo $this->Form->hidden('Property.session_id', array(
            'value' => !empty($session_id)?$session_id:false, 
        ));

        echo $this->Form->end();
?>