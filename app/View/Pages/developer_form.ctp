<?php
        $data = $this->request->data;
        $save_path = Configure::read('__Site.general_folder');

        $logo = $this->Rumahku->filterEmptyField($data, 'BannerDeveloper', 'photo_hide');
        $logoSize = $this->Rumahku->_rulesDimensionImage($save_path, 'large', 'size');
        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-2 col-sm-3 control-label taright',
            'class' => 'relative col-sm-6 col-xl-6',
        );

        echo $this->Form->create('BannerDeveloper', array(
            'type' => 'file',
        ));
?>
<div class="user-fill">
    <?php
            echo $this->Rumahku->buildInputForm('photo', array_merge($options, array(
                'type' => 'file',
                'label' => sprintf(__('Banner Project ( %s ) *'), $logoSize),
                'preview' => array(
                    'photo' => $logo,
                    'save_path' => $save_path,
                    'size' => 'm',
                ),
            )));
            echo $this->Rumahku->buildInputForm('title', array_merge($options, array(
                'label' => __('Nama Project *'),
            )));
            echo $this->Rumahku->buildInputForm('short_description', array_merge($options, array(
                'label' => __('Keterangan Singkat *'),
            )));
            echo $this->Rumahku->buildInputMultiple('start_date', 'end_date', array(
                'label' => __('Masa Tayang'),
                'divider' => 'rv4-bold-min small',
                'inputClass' => 'datepicker',
                'inputClass2' => 'to-datepicker',
                'frameClass' => 'col-sm-12',
                'labelDivClass' => 'col-sm-3 col-xl-2 taright',
                'class' => 'col-xs-5 col-sm-2 col-xl-2',
                'attributes' => array(
                    'type' => 'text',
                ),
            ));
            echo $this->Rumahku->buildInputToggle('is_article', array(
                'label' => __('Is Article'),
                'frameClass' => 'col-sm-12',
                'labelClass' => 'col-xl-2 taright col-sm-3',
                'attributes' => array(
                    'triggered-selector-class' => 'article-toggle-input',
                    'triggered-selector-hide-class' => 'url-toggle-input',
                ),
            ));
            echo $this->Rumahku->buildInputForm('url', array_merge($options, array(
                'type' => 'text',
                'label' => __('URL'),
                'inputClass' => 'url-toggle-input',
                'infoText' => __('Masukkan URL lengkap menggunakan HTTP://'),
            )));
            echo $this->Rumahku->buildInputForm('description', array_merge($options, array(
                'label' => __('Deskripsi *'),
                'inputClass' => 'ckeditor article-toggle-input',
                'class' => 'relative col-sm-9 col-xl-6 large',
            )));
            echo $this->Rumahku->buildInputForm('order', array_merge($options, array(
                'label' => __('Order'),
                'type' => 'text',
            )));
    ?>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
                <?php
                        echo $this->Html->link(__('Kembali'), array(
                            'action' => 'developers',
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