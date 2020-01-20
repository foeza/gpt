<?php 
        $plahol_desc = __('Berikan deskripsi mengenai detil dari produk ini. Anda juga bisa menyertakan detil mengenai kondisi produk ini, ketersediaan produk (PO), dan sebagainya.');

        $data         = $this->request->data;
        $prop_type_id = Common::hashEmptyField($data, 'Property.property_type_id');
        $prop_act_id  = Common::hashEmptyField($data, 'Property.property_action_id');

        echo $this->Form->create('Property', array(
            'class' => 'form-horizontal',
            'id' => 'sell-property',
        ));
?>
<div class="step-1 toggle-page active user-fill">
    <?php 
            // echo $this->element('blocks/properties/forms/input_email');

            echo $this->Rumahku->buildForm('property_action_id', __('Jual Produk *'), array(
                'type' => 'radio',
                'options' => $propertyActions,
                'frame-size' => 'large',
                'value' => $prop_act_id,
            ), 'horizontal');
            echo $this->element('blocks/properties/forms/input_product_category');
            
            echo $this->Rumahku->buildInputForm('Property.title', array(
                'frameClass' => 'col-sm-12',
                'label' => __('Judul/Kalimat Promosi *'),
                'type' => 'text',
                'labelClass' => 'col-xl-2 taright col-sm-3',
                'class' => 'relative col-sm-7 col-xl-4',
                'id' => 'desc-info',
                'data_max_lenght' => 60,
                'infoText' => __('Contoh: Ciput Arab Tali Kaos Polos'),
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
            echo $this->element('blocks/properties/forms/price_sell');

            // echo $this->Rumahku->buildForm('property_type_id', __('Kategori Produk *'), array(
            //     'type' => 'radio',
            //     'options' => $propertyTypes,
            //     'frame-size' => 'large',
            //     'value' => $prop_type_id,
            // ), 'horizontal');



            // echo $this->Rumahku->buildInputForm('Property.description', array(
            //     'frameClass' => 'col-sm-12',
            //     'label' => __('Deskripsi Produk *'),
            //     'placeholder' => $plahol_desc,
            //     'type' => 'textarea',
            //     'labelClass' => 'col-xl-2 taright col-sm-3',
            //     'inputClass' => 'ckeditor',
            //     'class' => 'relative col-sm-7 col-xl-4',
            //     'infoText' => __('Minimum 30 karakter'),
            //     'infoClass' => 'extra-text',
            //     'rows' => 10,
            // ));

            $options = array(
                'frameClass' => 'col-sm-12',
                'labelClass' => 'col-xl-2 taright col-sm-3',
            );

            echo $this->Rumahku->buildInputForm('Property.description', array_merge($options, array(
                'label' => __('Deskripsi *'),
                'inputClass' => 'ckeditor',
                'class' => 'relative col-sm-7 col-xl-4',
            )));

            // echo $this->element('blocks/properties/forms/input_client', array(
            //     'ajax_blur' => false,
            // ));

            echo $this->element('blocks/properties/forms/input_meta_tag', array(
                'ajax_blur' => false,
            ));

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