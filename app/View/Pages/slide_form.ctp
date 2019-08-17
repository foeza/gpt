<?php
        $data = $this->request->data;
        $save_path = Configure::read('__Site.general_folder');

        $logo = $this->Rumahku->filterEmptyField($data, 'BannerSlide', 'photo_hide');
        $options = array(
            'frameClass' => 'col-sm-8',
            'labelClass' => 'col-xl-2 col-sm-4 control-label taright',
            'class' => 'relative col-sm-6 col-xl-6',
        );

        echo $this->Form->create('BannerSlide', array(
            'type' => 'file',
        ));
?>
<div class="user-fill">
    <?php
            echo $this->Rumahku->buildInputForm('photo', array(
                'type' => 'file',
                'label' => __('Upload Banner ( 1349x450 ) *'),
                'preview' => array(
                    'photo' => $logo,
                    'save_path' => $save_path,
                    'size' => 'm',
                ),
            ));
            echo $this->Rumahku->buildInputForm('title', array(
                'label' => __('Judul Banner'),
            ));
            echo $this->Rumahku->buildInputForm('url', array(
                'type' => 'text',
                'label' => __('URL'),
                'infoText' => __('Masukkan URL lengkap menggunakan HTTP://'),
            ));
            echo $this->Rumahku->buildInputMultiple('start_date', 'end_date', array(
                'label' => __('Masa Tayang'),
                'divider' => 'rv4-bold-min small',
                'inputClass' => 'datepicker',
                'inputClass2' => 'to-datepicker',
                'frameClass' => 'col-sm-8',
                'attributes' => array(
                    'type' => 'text',
                ),
            ));
            echo $this->Rumahku->buildInputForm('order', array(
                'label' => __('Order'),
                'type' => 'text',
            ));
            echo $this->Rumahku->buildInputToggle('is_video', array_merge($options, array(
                'label' => __('URL Video Youtube ?'),
            )));
            echo $this->Rumahku->buildInputToggle('is_show_icon_play', array_merge($options, array(
                'label' => __('Tampilkan icon play ?'),
            )));
    ?>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
                <?php
                        echo $this->Html->link(__('Kembali'), array(
                            'action' => 'slides',
                            'admin' => true
                        ), array(
                            'class'=> 'btn default',
                        ));
                        echo $this->Form->button(__('Simpan'), array(
                            'type' => 'submit', 
                            'class'=> 'btn blue',
                        ));
                ?>
            </div>
        </div>
    </div>
</div>

<?php 
    echo $this->Form->end(); 
?>