<?php
        // $default_solid_option = Common::hashEmptyField($this->request->data, 'UserCompanyConfig.watermark_solid', 0);
        $dataMatch = array(
            array('.watermark-solid-placeholder', array('logo'), 'slide'),
        );
        $dataMatch = json_encode($dataMatch);
?>

<div class="form-group">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-1 col-sm-4 col-md-3 control-label taright">
                    <?php
                            echo $this->Form->label('watermark_type', __('Tipe Watermark'), array(
                                'class' => 'control-label'
                            ));
                    ?>
                </div>                
                <div class="relative col-sm-5 col-xl-4">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                                    echo $this->Form->input('watermark_type', array(
                                        'label' => false,
                                        'class' => 'handle-toggle form-control',
                                        'options' => array(
                                            'logo' => __('Logo Perusahaan'),
                                            'text' => 'Text'
                                        ),
                                        'data-match' => $dataMatch,
                                    ));
                            ?> 
                        </div>

                        <div class="col-sm-6">
                            <div class="watermark-solid-placeholder">
                                <?php
                                        echo $this->Form->input('watermark_solid', array(
                                            'label' => false,
                                            'class' => ' form-control',
                                            'empty' => __('Transparan'),
                                            'options' => array(
                                                // '0' => __('Transparan'),
                                                '1' => __('Solid')
                                            ),
                                            // 'value' => $default_solid_option,
                                        ));
                                ?>
                            </div>
                        </div>

                    </div>             
                </div>
            </div>
        </div>
    </div>
</div>