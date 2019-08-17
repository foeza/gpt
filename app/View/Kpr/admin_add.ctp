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
		));
?>
<div class="crm form-border-error" id="kpr-form">
    <?php
            echo $this->element('blocks/kpr/application_tab', array(
                'sub_title' => __('Tentukan properti yang diajukan'),
                'step' => 'Basic',
            ));
    ?>
    <div class="info-wrapper">
        <?php 
                echo $this->Html->tag('h1', __('Informasi Klien'), array(
                    'class' => 'info-title',
                ));
                echo $this->element('blocks/crm/forms/client_buyer', array(
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
            ))));
            echo $this->element('blocks/crm/property', array(
                'mandatory' => $mandatory,
                'kpr' => true,
                'error' => false,
            ));

            echo $this->element('blocks/common/forms/action_custom', array(
                '_with_submit' => true,
                '_float_class' => false,
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
?>