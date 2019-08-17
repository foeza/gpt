<?php 
        $mandatory = $this->Rumahku->_callLblConfigValue('is_mandatory_no_address', '*');

        echo $this->Form->create('PropertyAddress', array(
            'class' => 'form-horizontal',
            'id' => 'sell-property',
        ));
?>
<div class="step-2 locations-trigger">
    <?php 
            echo $this->Html->tag('div', $this->Html->tag('p', __('Lengkapi alamat properti Anda dengan lengkap, untuk memastikan lokasi pada peta sesuai dengan lokasi properti saat ini. Kelengkapan dan kebenaran data pada alamat, juga dapat membantu properti Anda cepat terjual/tersewa.')), array(
                'class' => 'welcome-word',
            ));
    ?>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <?php 
                            echo $this->Html->tag('div', $this->Form->label('address', __('Alamat *'), array(
                                'class' => 'control-label',
                            )), array(
                                'class' => 'col-xl-2 taright col-sm-2',
                            ));
                    ?>
                    <div class="relative col-sm-7 col-xl-4">
                        <?php 
                                echo $this->Form->input('address', array(
                                    'id' => 'rku-address',
                                    'class' => 'form-control',
                                    'label' => false,
                                    'div' => false,
                                    'required' => false,
                                    'placeholder' => __('Nama jalan'),
                                ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-md-8 col-sm-10 col-xl-4 col-sm-offset-2 col-xl-offset-2 no-pleft">
                        <div class="row">
                            <?php 
                                    echo $this->Html->tag('div', $this->Form->label('no', sprintf(__('No %s'), $mandatory), array(
                                        'class' => 'control-label',
                                    )), array(
                                        'class' => 'col-xl-2 taright col-sm-1 no-pright',
                                    ));
                            ?>
                            <div class="relative col-sm-2 col-xl-2 col-md-2">
                                <?php 
                                        echo $this->Form->input('no', array(
                                            'label' => false,
                                            'div' => false,
                                            'class' => 'form-control',
                                            'id' => 'rku-no-address',
                                            'required' => false,
                                        ));
                                ?>
                            </div>
                            <?php 
                                    echo $this->Html->tag('div', $this->Form->label('rw', __('RW'), array(
                                        'class' => 'control-label',
                                    )), array(
                                        'class' => 'col-xl-2 taright col-sm-1 no-pright',
                                    ));
                            ?>
                            <div class="relative col-sm-2 col-xl-2 col-md-2">
                                <?php 
                                        echo $this->Form->input('rw', array(
                                            'label' => false,
                                            'div' => false,
                                            'class' => 'form-control',
                                        ));
                                ?>
                            </div>
                            <?php 
                                    echo $this->Html->tag('div', $this->Form->label('rt', __('RT'), array(
                                        'class' => 'control-label',
                                    )), array(
                                        'class' => 'col-xl-2 taright col-sm-1 no-pright',
                                    ));
                            ?>
                            <div class="relative col-sm-2 col-xl-2 col-md-2">
                                <?php 
                                        echo $this->Form->input('rt', array(
                                            'label' => false,
                                            'div' => false,
                                            'class' => 'form-control',
                                        ));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
            
            if(empty($_config['UserCompanyConfig']['is_hidden_address_property'])){
                echo $this->Rumahku->buildInputToggle('hide_address', array(
                    'label' => __('Sembunyikan Alamat'),
                ));
            }

        //  https://basecamp.com/1789306/projects/10415456/todos/359349920 - [EN] - Area dibuat autocomplete
            echo($this->Html->tag('div', $this->element('blocks/properties/forms/location_picker'), array(
                'class' => 'mb15'
            )));

        /*
            echo $this->Rumahku->setFormAddress( 'PropertyAddress' );
            echo $this->Rumahku->buildForm('region_id', __('Provinsi *'), array(
                'empty' => __('Pilih Provinsi'),
                'class' => 'regionId',
                'size' => 'medium',
                'frame-class' => 'col-sm-12',
                'frame-label-class' => 'col-xl-2 taright col-sm-2',
            ), 'horizontal');
            echo $this->Rumahku->buildForm('city_id', __('Kota *'), array(
                'empty' => __('Pilih Kota'),
                'class' => 'cityId',
                'size' => 'medium',
                'frame-class' => 'col-sm-12',
                'frame-label-class' => 'col-xl-2 taright col-sm-2',
            ), 'horizontal');
            echo $this->Rumahku->buildForm('subarea_id', __('Area *'), array(
                'empty' => __('Pilih Area'),
                'class' => 'subareaId',
                'size' => 'medium',
                'frame-class' => 'col-sm-12',
                'frame-label-class' => 'col-xl-2 taright col-sm-2',
            ), 'horizontal');
        */

            echo $this->Rumahku->buildForm('zip', __('Kode Pos *'), array(
                'size' => 'small',
                'class' => 'rku-zip',
                'frame-class' => 'col-sm-12',
                'frame-label-class' => 'col-xl-2 taright col-sm-2',
            ), 'horizontal');

            /*
            Sementara
            if(empty($_config['UserCompanyConfig']['is_hidden_map'])){
                echo $this->Rumahku->buildInputToggle('hide_map', array(
                    'label' => __('Sembunyikan Lokasi Peta'),
                ));
            }
    ?>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <?php 
                            echo $this->Html->tag('div', $this->Form->label('location', __('Lokasi')), array(
                                'class' => 'col-xl-2 taright col-sm-2',
                            ));
                    ?>
                    <div class="relative col-sm-7 col-xl-4">
                        <div id="map_container">
                            <div id="gmap-rku"></div>
                        </div>
                        <?php
                                echo $this->Html->tag('small', __('Edit Lokasi'));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
            */
    ?>
    <?php 
            echo $this->element('blocks/properties/sell_action', array(
                'action_type' => 'bottom',
                'labelBack' => __('Kembali'),
            ));
    ?>
</div>
<?php 
        echo $this->Form->end();
?>