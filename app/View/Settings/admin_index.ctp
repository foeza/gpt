<?php 
        echo $this->Form->create('UserSetting', array(
            'class' => 'form-horizontal',
        ));
?>
<div class="wrapper-form user-fill">
    <?php 
            echo $this->Rumahku->buildForm('sign_date', __('Tgl Kontrak'), array(
                'type' => 'text',
                'size' => 'medium',
                'class' => 'datepicker',
            ), 'horizontal');
            echo $this->Rumahku->buildInputMultiple('from_date', 'to_date', array(
                'label' => __('Tgl Tayang'),
                'divider' => 'rv4-bold-min small',
                'inputClass' => 'datepicker',
                'inputClass2' => 'to-datepicker',
            ));
            echo $this->Rumahku->buildIncrementInput('limit_agent', array(
                'label' => __('Jumlah Agen'),
            ));
            echo $this->Rumahku->buildIncrementInput('limit_listing', array(
                'label' => __('Jumlah Listing'),
            ));
            // echo $this->Rumahku->buildIncrementInput('limit_premium', array(
            //     'label' => __('Jumlah Premium Listing'),
            // ));
            echo $this->Rumahku->buildForm('google_analytics', __('ID Google Analytics'), array(
                'size' => 'small',
            ), 'horizontal');
            echo $this->Rumahku->buildInputToggle('apps', array(
                'label' => __('Launcher'),
                'frameClass' => 'col-sm-8',
                'labelClass' => 'col-xl-2 taright col-sm-4',
            ));
            echo $this->Rumahku->buildInputToggle('agent_website', array(
                'label' => __('Agent Personal Website'),
                'frameClass' => 'col-sm-8',
                'labelClass' => 'col-xl-2 taright col-sm-4',
            ));
            echo $this->Rumahku->buildForm('note', __('Keterangan'), array(
                'type' => 'textarea',
                'size' => 'large',
            ), 'horizontal');
            echo $this->element('blocks/common/multiple_forms', array(
                'modelName' => 'UserSettingEmail',
                'labelName' => __('List Email'),
                'placeholder' => __('Masukkan Email'),
                'infoTop' => __('Daftar Email yang didapatkan oleh Perusahaan'),
            ));
    ?>
    <div class="action-group bottom">
        <div class="btn-group floright">
            <?php
                    echo $this->Form->button(__('Simpan'), array(
                        'type' => 'submit',
                        'class' => 'btn blue',
                    ));
            ?>
        </div>
        <div class="clear"></div>
    </div>
</div>
<?php
        echo $this->Form->end();
?>