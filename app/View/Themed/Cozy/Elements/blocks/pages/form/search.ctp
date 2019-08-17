<?php
        echo $this->Form->create('Search', array(
            'url'=> array(
                'controller' => 'properties',
                'action' => 'search_engine',
                'admin' => false
            ), 
            'inputDefaults' => array('div' => false),
            'type' => 'file',
            'class' => 'form-main-search'
        ));
?>
    <div class="row locations-trigger">
        <div class="col-lg-3 col-md-3 col-sm-5">
            <div class="formBlock select">
                <?php
                    echo $this->Form->input('typeid',array(
                        'label'=> __('Tipe Properti').'<br>', 
                        'required' => false,
                        'empty' => __('Tampilkan Semua'),
                        'div' => false,
                        'id' => 'propertyType',
                        'class' => 'formDropdown'
                    ));
                ?>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4">
            <div class="formBlock select">
                <?php
                    echo $this->Form->input('beds', array(
                        'label' => __('Kamar Tidur').'<br>', 
                        'required' => false,
                        'div' => false,
                        'empty' => __('Tampilkan Semua'),
                        'options' => $room_options,
                        'class' => 'formDropdown',
                        'id' => 'baths'
                    ));
                ?>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4">
            <div class="formBlock select">
                <?php
                    echo $this->Form->input('baths', array(
                        'label' => __('Kamar Mandi').'<br>', 
                        'required' => false,
                        'div' => false,
                        'empty' => __('Tampilkan Semua'),
                        'options' => $room_options,
                        'class' => 'formDropdown',
                        'id' => 'baths'
                    ));
                ?>
            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-7">
            <div class="formBlock">
                <label for="price-min"><?php echo __('Rentang Harga')?></label><br/>
                <div style="float:right; margin-top:-25px;">
                    <div class="priceInput">
                        <?php
                            echo $this->Form->input('min_price', array(
                                'label' => false,
                                'div' => false,
                                'required' => false,
                                'type' => 'text',
                                // 'id' => 'price-min',
                                'class' => 'priceInput price-min'
                            ));
                        ?>
                    </div>
                    <span style="float:left; margin-right:10px; margin-left:10px;">-</span>
                    <div class="priceInput">
                        <?php
                            echo $this->Form->input('max_price', array(
                                'label' => false,
                                'div' => false,
                                'required' => false,
                                'type' => 'text',
                                // 'id' => 'price-max',
                                'class' => 'priceInput price-max'
                            ));
                        ?>
                    </div>
                </div><br/>
                <div class="priceSlider"></div>
                <div class="priceSliderLabel"><span>0</span><span style="float:right;">25,000,000,000</span></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6">
            <div class="formBlock select">
                <?php
                    echo $this->Form->input('region', array(
                        'label' => __('Provinsi'), 
                        'div' => false,
                        'required' => false,
                        'empty' => __('Tampilkan Semua'),
                        'class' => 'formDropdown regionId'
                    ));
                ?>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6">
            <?php
                echo $this->Form->input('city', array(
                    'label' => __('Kota'), 
                    'required' => false,
                    'div' => false,
                    'empty' => __('Tampilkan Semua'),
                    'class' => 'form-control add-advance-search cityId'
                ));
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6">
            <?php
                echo $this->Form->input('subarea', array(
                    'label' => __('Area'), 
                    'required' => false,
                    'div' => false,
                    'empty' => __('Tampilkan Semua'),
                    'class' => 'form-control add-advance-search subareaId'
                ));
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6">
            <div class="formBlock">
                <?php
                    echo $this->form->submit('CARI PROPERTI', array(
                        'class' => 'buttonColor',
                        'style' => 'margin-top:24px;',
                    ));
                ?>
            </div>
        </div>
        <div style="clear:both;"></div>
    </div>
<?php
        if($property_action_id != 'semua'){
            echo $this->Form->hidden('property_action', array(
                'value' => $property_action_id,
            ));
        }

        echo $this->Form->end();
?>