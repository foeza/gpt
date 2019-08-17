<?php 
        echo $this->Form->create('PropertyAsset', array(
            'class' => 'form-horizontal',
            'id' => 'sell-property',
        ));

        $request_data = $this->request->data;
        $ref_data =& $this->request->data;

        $property_id = Common::hashEmptyField($request_data, 'Property.id');

        $cobroke_types  = Configure::read('__Site.cobroke_type');
        $data           = !empty($dataBasic)?$dataBasic:false;

        $furnishedOptions = $this->Rumahku->filterEmptyField($_global_variable, 'furnished');
        $lotUnitName = !empty($lotUnitName)?$lotUnitName:false;
        $viewSites = !empty($viewSites)?$viewSites:false;
        $certificates = $this->Property->_callCertificates($certificates);

        $config_co_broke        = Common::hashEmptyField($_config, 'UserCompanyConfig.is_co_broke');
        $config_open_co_broke   = Common::hashEmptyField($_config, 'UserCompanyConfig.is_open_cobroke');
        $is_bt_commission       = Common::hashEmptyField($_config, 'UserCompanyConfig.is_bt_commission');

        if(!empty($config_co_broke) && !empty($config_open_co_broke)){
            $default_agent_commission               = Common::hashEmptyField($_config, 'UserCompanyConfig.default_agent_commission');
            $default_type_price_co_broke_commision  = Common::hashEmptyField($_config, 'UserCompanyConfig.default_type_price_co_broke_commision');
            $default_co_broke_commision             = Common::hashEmptyField($_config, 'UserCompanyConfig.default_co_broke_commision');
            $default_type_co_broke_commission       = Common::hashEmptyField($_config, 'UserCompanyConfig.default_type_co_broke_commission');
            $default_type_co_broke                  = Common::hashEmptyField($_config, 'UserCompanyConfig.default_type_co_broke');

            if(empty($property_id)){
                $data['Property']['is_cobroke'] = $ref_data['Property']['is_cobroke'] = true;
            }
        }else{
            $default_agent_commission               = false;
            $default_type_price_co_broke_commision  = false;
            $default_co_broke_commision             = false;
            $default_type_co_broke_commission       = false;
            $default_type_co_broke                  = false;
        }

        $ref_data['Property']['commission']                     = Common::hashEmptyField($ref_data, 'Property.commission', $default_agent_commission);
        $ref_data['Property']['co_broke_commision']             = Common::hashEmptyField($ref_data, 'Property.co_broke_commision', $default_co_broke_commision);
        $ref_data['Property']['type_price_co_broke_commision']  = Common::hashEmptyField($ref_data, 'Property.type_price_co_broke_commision', $default_type_price_co_broke_commision);
        $ref_data['Property']['type_co_broke_commission']       = Common::hashEmptyField($ref_data, 'Property.type_co_broke_commission', $default_type_co_broke_commission);
        $ref_data['Property']['co_broke_type']                  = Common::hashEmptyField($ref_data, 'Property.co_broke_type', $default_type_co_broke);

        $kolisting_koselling = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_kolisting_koselling');

        $property_action_id = $this->Rumahku->filterEmptyField($data, 'Property', 'property_action_id');
        $is_cobroke = $this->Rumahku->filterEmptyField($data, 'Property', 'is_cobroke');

        $is_building = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_building');
        $is_lot = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_lot');
        $is_space = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'is_space');
        $_type = $this->Rumahku->filterEmptyField($data, 'PropertyType', 'name');

        $bt = $this->Rumahku->filterEmptyField($User, 'UserConfig', 'bt', 0);
        $bt = $this->Rumahku->filterEmptyField($request_data, 'Property', 'bt', $bt);

        if( !empty($is_space) ) {
            $lotLabel = __('Harga Satuan');
        } else {
            $lotLabel = __('Satuan Luas');
        }

        if ($_type == 'Komersil') {
            $mandatory = ' ';
        } else {
            $mandatory = '*';
        }
?>
<div class="step-3 user-fill">
    <?php 
            if( $property_action_id == 2 ){
                echo $this->element('blocks/properties/forms/price_rent');
            } else {
                echo $this->element('blocks/properties/forms/price_sell');
            }

            echo $this->Rumahku->buildInputForm('lot_unit_id', array(
                'label' => sprintf(__('%s *'), $lotLabel),
                'empty' => sprintf(__('Pilih %s'), $lotLabel),
                'id' => 'lot-unit-id',
                'data' => $data,
                'class' => 'col-sm-5 col-xl-4 input-group',
            ));
            echo $this->Rumahku->buildInputForm('Property.certificate_id', array(
                'id' => 'other-field',
                'label' => __('Sertifikat *'),
                'empty' => __('Pilih Sertifikat'),
                'class' => 'col-sm-5 col-xl-4 input-group',
                'options' => $certificates,
                'otherContent' => array(
                    'modelName' => 'Property',
                    'fieldName' => 'others_certificate',
                    'fieldNameTrigger' => 'certificate_id',
                    'fieldValueTrigger' => -1,
                    'description' => __('Lainnya'),
                ),
                'attributes' => array(
                    'data-show' => '#other-text',
                ),
            ));
            echo $this->Rumahku->buildInputForm('lot_size', array(
                'type' => 'text',
                'label' => __('Luas Tanah *'),
                'textGroup' => $lotUnitName,
                'formGroupClass' => 'form-group input-text-center',
                'classGroup' => 'lot-unit',
                'class' => 'col-sm-3 col-xl-2',
                'inputClass' => 'input_number',
                'is_lot' => true,
                'data' => $data,
            ));
            echo $this->Rumahku->buildInputForm('building_size', array(
                'type' => 'text',
                'label' => sprintf(__('Luas Bangunan %s'), $mandatory),
                'textGroup' => $lotUnitName,
                'formGroupClass' => 'form-group input-text-center',
                'classGroup' => 'lot-unit',
                'class' => 'col-sm-3 col-xl-2',
                'inputClass' => 'input_number',
                'is_building' => true,
                'data' => $data,
            ));
            echo $this->Rumahku->buildInputMultiple('lot_width', 'lot_length', array(
                'label' => __('Dimensi Tanah'),
                'classGroup' => 'lot-unit',
                'inputClass' => 'input_number',
                'inputClass2' => 'input_number',
                'textGroup' => $lotUnitName,
                'placeholder1' => __('Lebar'),
                'placeholder2' => __('Panjang'),
                'attributes' => array(
                    'type' => 'text',
                ),
            ));
            echo $this->Rumahku->buildIncrementInput('beds', array(
                'label' => sprintf(__('Kamar Tidur %s'), $mandatory),
                'inputClass' => 'input_number',
                'class' => 'input-group col-xs-5 col-sm-5 col-md-3 col-xl-2',
                'is_residence' => true,
                'data' => $data,
            ));
            echo $this->Rumahku->buildIncrementInput('baths', array(
                'label' => sprintf(__('Kamar Mandi %s'), $mandatory),
                'inputClass' => 'input_number',
                'class' => 'input-group col-xs-5 col-sm-5 col-md-3 col-xl-2',
                'is_residence' => true,
                'data' => $data,
            ));
            echo $this->Rumahku->buildIncrementInput('beds_maid', array(
                'label' => __('Kamar Tidur Ekstra'),
                'inputClass' => 'input_number',
                'class' => 'input-group col-xs-5 col-sm-5 col-md-3 col-xl-2',
                'is_residence' => true,
                'data' => $data,
            ));
            echo $this->Rumahku->buildIncrementInput('baths_maid', array(
                'label' => __('Kamar Mandi Ekstra'),
                'inputClass' => 'input_number',
                'class' => 'input-group col-xs-5 col-sm-5 col-md-3 col-xl-2',
                'is_residence' => true,
                'data' => $data,
            ));
            echo $this->Rumahku->buildIncrementInput('level', array(
                'label' => __('Jumlah Lantai'),
                'inputClass' => 'input_number',
                'class' => 'input-group col-xs-5 col-sm-5 col-md-3 col-xl-2',
                'is_building' => true,
                'data' => $data,
            ));
            echo $this->Rumahku->buildIncrementInput('cars', array(
                'label' => __('Garasi'),
                'inputClass' => 'input_number',
                'class' => 'input-group col-xs-5 col-sm-5 col-md-3 col-xl-2',
                'textGroup' => __('Mobil'),
                'is_building' => true,
                'data' => $data,
            ));
            echo $this->Rumahku->buildIncrementInput('carports', array(
                'label' => __('Carport'),
                'inputClass' => 'input_number',
                'class' => 'input-group col-xs-5 col-sm-5 col-md-3 col-xl-2',
                'textGroup' => __('Mobil'),
                'is_building' => true,
                'data' => $data,
            ));
            echo $this->Rumahku->buildIncrementInput('phoneline', array(
                'label' => __('Jumlah Line Telepon'),
                'class' => 'input-group col-xs-5 col-sm-5 col-md-3 col-xl-2',
                'inputClass' => 'input_number',
                'is_building' => true,
                'data' => $data,
            ));
            echo $this->Rumahku->buildInputForm('electricity', array(
                'type' => 'text',
                'label' => __('Daya Listrik'),
                'textGroup' => __('Watt'),
                'class' => 'col-sm-3 col-xl-2',
                'inputClass' => 'input_number',
                'is_building' => true,
                'data' => $data,
            ));
            echo $this->Rumahku->buildInputForm('furnished', array(
                'label' => __('Interior'),
                'options' => $furnishedOptions,
                'empty' => __('Pilih Interior'),
                'is_building' => true,
                'data' => $data,
                'class' => 'col-sm-5 col-xl-4 input-group',
            ));
            echo $this->Rumahku->buildInputForm('property_direction_id', array(
                'label' => __('Arah Bangunan'),
                'empty' => __('Pilih Arah Bangunan'),
                'is_building' => true,
                'data' => $data,
                'class' => 'col-sm-5 col-xl-4 input-group',
            ));

            if( !empty($is_building) ) {
    ?>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-8">
                <div class="row">
                    <?php 
                            echo $this->Html->tag('div', $this->Form->label('year_built', __('Tahun dibangun')), array(
                                'class' => 'col-sm-4 col-xl-2 control-label taright',
                            ));
                    ?>
                    <div class="col-sm-5 col-xl-4 input-group">
                        <?php
                                echo $this->Rumahku->year('year_built', date('Y')-50, date('Y'), null, array(
                                    'class' => false, 
                                    'empty' => __('Pilih Tahun'),
                                    'required' => false,
                                ), 'year_built');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
            }
            
            echo $this->Rumahku->buildInputForm('property_condition_id', array(
                'label' => __('Kondisi Bangunan'),
                'empty' => __('Pilih Kondisi Bangunan'),
                'is_building' => true,
                'data' => $data,
                'class' => 'col-sm-5 col-xl-4 input-group',
            ));

            if( !empty($viewSites) ) {
                echo $this->Rumahku->buildInputForm('view_site_id', array(
                    'label' => sprintf(__('View %s'), $_type),
                    'empty' => __('Pilih View'),
                    'options' => $viewSites,
                    'class' => 'col-sm-5 col-xl-4 input-group',
                ));
            }
    ?>
    <div class="form-group plus">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1 label">
                <?php 
                        echo $this->Html->tag('h4', __('Informasi Agen'));
                ?>
            </div>
        </div>
    </div>
    <?php 
            echo $this->Rumahku->buildInputForm('Property.contract_date', array(
                'type' => 'text',
                'label' => __('Tgl Kontrak'),
                'class' => 'col-sm-5 col-xl-4 input-group datepicker',
                'inputClass' => 'datepicker',
                'attributes' => array(
                    'title' => __('Tanggal kontrak/kesepakatan dengan Vendor'),
                ),
            ));

            $commission_options = array(
                'type' => 'text',
                'label' => __('Komisi Pemilik Listing'),
                'formGroupClass' => 'form-group input-text-center',
                'class' => 'col-sm-3 col-xl-2',
                'inputClass' => 'input_number',
                'textGroup' => '%',
            );

            if(!empty($config_co_broke)){
                $commission_options['infopopover'] = array(
                    'title' => __('Info Komisi'),
                    'content' => __('Jika Anda ingin menjadikan properti menjadi listing Co-Broke, maka Anda harus memasukkan komisi agen terlebih dahulu'),
                    'icon' => '',
                    'options' => array(
                        'data-modal-size' => 'modal-md col-md-3'
                    ),
                );
            }

            echo $this->Rumahku->buildInputForm('Property.commission', $commission_options);

            if(!empty($is_bt_commission)){
                $text = 'BT';
                $description_bt = __('%s atau Broker Tradisional dipakai untuk menyebut orang-orang yang kerjanya sebagai perantara / jasa penjual properti yang tidak terdaftar di kantor broker properti resmi', $text);
                if(empty($config_co_broke)){
                    $text = 'Komisi Perantara';
                    $description_bt = __('%s dipakai untuk menyebut orang-orang yang kerjanya sebagai perantara / jasa penjual properti yang tidak terdaftar di kantor broker properti resmi', $text);
                }

                echo $this->Rumahku->buildInputForm('Property.bt', array(
                    'type' => 'text',
                    'label' => __('%s *', $text),
                    'formGroupClass' => 'form-group input-text-center',
                    'class' => 'col-sm-3 col-xl-2',
                    'inputClass' => 'input_number',
                    'textGroup' => '%',
                    'infopopover' => array(
                        'title' => __('Apa itu %s ?', $text),
                        'content' => $description_bt,
                        'options' => array(
                            'data-modal-size' => 'modal-md col-md-3'
                        )
                    ),
                    'attributes' => array(
                        'value' => $bt
                    )
                ));
            }

            // start is $config_co_broke
            if( !empty($config_co_broke) ) {
    ?>
    <div class="form-group plus">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1 label">
                <?php 
                        echo $this->Html->tag('h4', __('Informasi Co-Broke'));

                        echo $this->Html->tag('p', __('Anda bisa dengan mudah menampilkan listing properti Anda di channel Co-Broke dengan hanya klik "Jadikan listing Co-Broke?". <br><i>Catatan : jadikan listing Co-Broke hanya bisa dilakukan jika Komisi Co-Broke lebih dari 0</i>'));
                ?>
            </div>
        </div>
    </div>
    <?php
        $commission_display = (!empty($is_cobroke) ? 'block' : 'none');
    ?>
    
    <div class="form-group <?php echo($config_open_co_broke ? 'hide' : ''); ?>">
        <div class="row">
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-xl-2 col-sm-4 control-label taright">
                        <?php
                                echo $this->Form->label('Property.is_cobroke', __('Jadikan listing <br> Co-Broke?'), array(
                                    'class' => 'control-label'
                                ));
                        ?>
                    </div>
                    <div class="col-sm-3 col-xl-2 no-pleft">
                        <?php
                                $checked = Common::hashEmptyField($this->data, 'Property.is_cobroke', $config_open_co_broke, array(
                                    'isset' => true, 
                                ));

                                echo $this->Rumahku->checkbox('Property.is_cobroke', array(
                                    'mt' => 'mt10',
                                    'class' => 'handle-toggle-content',
                                    'data-target' => '.commision-cobroke-box', 
                                    'checked'   => $checked, 
                                ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="commision-cobroke-box" style="display:<?php echo $commission_display;?>">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-8">
                    <div class="row">
                        <?php
                                $error = '';
                                if($this->Form->error('Property.co_broke_commision')){
                                    $error = $this->Form->error('Property.co_broke_commision');
                                }

                                echo $this->Html->div('col-xl-2 col-sm-4 control-label taright', $this->Form->label('Property.co_broke_commision', sprintf(__('Komisi Broker *')), array(
                                    'class' => 'control-label',
                                )));
                        ?>
                        <div class="col-sm-5 col-xl-2 input-group">
                            <div>
                                <?php
                                        echo $this->Form->input('Property.type_price_co_broke_commision', array(
                                            'id' => 'currency',
                                            'class' => 'input-group-addon change-type-price-commission',
                                            'label' => false,
                                            'div' => false,
                                            'required' => false,
                                            'options' => array(
                                                'percentage' => 'Persentase',
                                                'nominal' => 'Nominal'
                                            ),
                                        ));

                                        echo $this->Form->input('Property.co_broke_commision', array(
                                            'type' => 'text',
                                            'id' => 'price',
                                            'class' => 'form-control has-side-control at-left input_price padding-custom-input-group',
                                            'label' => false,
                                            'div' => false,
                                            'required' => false,
                                            'error' => false,
                                        ));
                                ?>
                            </div>
                            <?php
                                    if(!empty($error)){
                                        echo $error;
                                    }
                            ?>
                        </div>
                        <span class="notice-static-modal">
                            <?php
                                    $shortip = $this->Rumahku->icon('rv4-shortip');

                                    echo $this->Html->link($shortip, 'javascript:void(0)', array(
                                        'escape' => false,
                                        'class' => 'static-modal',
                                        'data-toggle' => 'popover',
                                        'data-content' => __('Komisi broker akan di hitung setelah perhitungan komisi dari agen terhadap perusahaan. Rumus : total komisi agen x persentase komisi broker'),
                                        'data-modal-size' => 'modal-md col-md-3',
                                        'data-placement' => 'right',
                                        'data-original-title' => __('Bagaimana cara perhitungan komisi Co-Broke ?')
                                    ));
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php 
                echo $this->Rumahku->buildInputForm('Property.type_co_broke_commission', array(
                    'label' => __('Asal Komisi Broker *'),
                    'class' => 'col-sm-5 col-xl-4 input-group',
                    'options' => $type_commission,
                ));
        ?>
        <div class="form-group">
            <div class="row">
                <div class="col-sm-8">
                    <div class="row">
                        <?php
                                echo $this->Html->div('col-xl-2 col-sm-4 control-label taright', $this->Form->label('Property.co_broke_type', __('Tipe Co-Broke *'), array(
                                    'class' => 'control-label',
                                )));
                        ?>
                        <div class="col-sm-5 col-xl-2 input-group">
                            <div>
                                <?php
                                        echo $this->Form->input('Property.co_broke_type', array(
                                            'class' => 'form-control',
                                            'label' => false,
                                            'div' => false,
                                            'required' => false,
                                            'options' => $cobroke_types
                                        ));
                                ?>
                            </div>
                        </div>
                        <span class="notice-static-modal">
                            <?php
                                    $shortip = $this->Rumahku->icon('rv4-shortip');

                                    echo $this->Html->link($shortip, 'javascript:void(0)', array(
                                        'escape' => false,
                                        'class' => 'static-modal',
                                        'data-toggle' => 'popover',
                                        'data-content' => __('Internal hanya akan muncul di internal perusahaan saja, sedangkan eksternal hanya akan muncul diluar dari perusahaan'),
                                        'data-modal-size' => 'modal-md col-md-3',
                                        'data-placement' => 'right',
                                        'data-original-title' => __('Apa itu Co-Broke Internal dan Eksternal ?')
                                    ));
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <?php  
                // co_broke note
                $placeholder_cobroke_note = __('Berikan catatan apabila Anda menjadikan listing ini sebagai listing co-broke, untuk mempercepat penjualan. Contoh : Dapatkan bonus berupa trip perjalanan ke Perancis selama 3 Hari. S&K berlaku.');

                echo $this->Rumahku->buildInputForm('Property.cobroke_note', array(
                    'label' => __('Co-Broke Note'),
                    'placeholder' => $placeholder_cobroke_note,
                    'type' => 'textarea',
                    'class' => 'col-xl-4 col-sm-8 cobroke-note',
                    'rows' => 8,
                ));
        ?>

    </div>
    <?php

            } // end is $config_co_broke

            if( !empty($kolisting_koselling) ) {
                echo $this->Rumahku->buildInputForm('Property.kolisting_koselling', array(
                    'type' => 'text',
                    'label' => __('Kolisting Koseling'),
                    'class' => 'col-sm-5 col-xl-4 input-group',
                ));
            }
            echo $this->element('blocks/common/forms/list_checkbox', array(
                'label' => __('Fasilitas Properti'),
                'values' => $facilities,
                'modelName' => 'PropertyFacility',
                'fieldName' => 'facility_id',
                'is_building' => true,
                'data' => $data,
                'customContent' => $this->element('blocks/common/forms/other_checkbox', array(
                    'class' => 'col-sm-10',
                    'modelName' => 'PropertyFacility',
                    'fieldName' => 'other_id',
                    'fieldNameText' => 'other_text',
                    'description' => __('Berikan tanda koma untuk lebih dari 1 fasilitas'),
                )),
            ));
            
            echo $this->element('blocks/common/multiple_forms', array(
                'modelName' => 'PropertyPointPlus',
                'labelName' => __('Nilai Lebih Properti'),
                'placeholder' => __('Masukkan nilai lebih dari properti yang Anda tawarkan, disini'),
                'infoTop' => __('Berikan informasi mengenai nilai lebih dari properti yang Anda tawarkan. Hal ini juga dapat membantu properti Anda lebih cepat terjual/tersewa.'),
            ));

            echo $this->element('blocks/properties/sell_action', array(
                'action_type' => 'bottom',
                'labelBack' => __('Kembali'),
            ));
    ?>
</div>
<?php 
        echo $this->Form->end();
?>