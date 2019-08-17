<?php 
        $urlBack = array(
            'controller' => 'kpr',
            'action' => 'index',
            'admin' => true,
        );

        $mandatory = $this->Html->tag('span',sprintf('(%s)',__('*')),array(
            'class' => 'color-red'
        ));

		echo $this->Form->create('Kpr', array(
            'type' => 'file',
			'class' => 'form-group',
            'id' => 'fast-kpr-form'
		));
?>
<div class="crm form-border-error" id="kpr-form">
    <div class="info-wrapper">
        <?php 
                echo $this->Html->tag('h1', __('Informasi Klien'), array(
                    'class' => 'info-title',
                ));
                echo $this->element('blocks/crm/forms/crm_client_buyer', array(
                    'modelName' => 'Kpr',
                    'mandatory' => $mandatory,
                    'error' => false,
                ));
        ?>
    </div>
	<?php 
            echo $this->Html->tag('p', sprintf(__('Properti belum terdaftar? Silakan tambah properti %s'), $this->Html->link($this->Html->tag('strong', __('disini.')), array(
                'controller' => 'properties',
            //  'action' => 'sell',
                'action' => 'add',
                'admin' => true,
            ), array(
                'escape' => false,
                'target' => '_blank',
            ))), array(
                'class' => 'mb10',
            ));

            echo $this->element('blocks/kpr/property_ver2', array(
                'mandatory' => $mandatory,
                'kpr' => true,
                'error' => false,
                'autorun' => true
            ));
    ?>
    <!-- <div class="form-group kpr-checkbox mb50 mt20">
        <?php
                // echo $this->Rumahku->checkbox('is_draft', array(
                //     'label' => __('Simpan sebagai draft %s', $this->Html->tag('div', $this->Html->tag('strong', __('Perhatian : ')).$this->Html->tag('span', __('Pengajuan KPR yang disimpan sebagai draft tidak dikirim kepada bank, berguna untuk informasi penyimpanan data-data KPR Anda.'), array(
                //         'style' => 'font-style:italic;',
                //     )))),
                // ));
        ?>        
    </div> -->
    <?php
            echo $this->element('blocks/common/forms/action_custom', array(
                '_with_submit' => true,
                '_float_class' => 'floleft',
                '_button_text' => __('Lanjut'),
                '_textBack' => __('Batal'),
                '_classBack' => 'btn default floleft',
                '_button_class' => 'floright',
                '_urlBack' => $urlBack,
            ));
	?>
</div>
<?php 
		echo $this->Form->end();

        echo $this->Html->tag('div',
            $this->Html->tag('div', $this->element('blocks/common/templates/renders/dashboard'), array(
                'class' => 'dashboard-chart-loading',
            )), array(
            'class' => 'hide wrapper-chart-loading',
        ));
?>