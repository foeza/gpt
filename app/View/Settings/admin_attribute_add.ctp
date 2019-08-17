<?php 
		$urlBack = array(
			'controller' => 'setting',
			'action' => 'attributes',
			'admin' => true,
		);

		echo $this->element('blocks/settings/attributes/tab_action');

        echo $this->Form->create('Attribute', array(
            'class' => 'form-horizontal',
        ));
?>
<div class="user-fill">
	<?php 
            echo $this->Rumahku->buildInputForm('name', array(
                'label' => __('Attribute Name *'),
            ));
            echo $this->Rumahku->buildInputForm('type', array(
                'label' => __('Tipe Attribute'),
                'empty' => __('Pilih Tipe'),
                'options' => Configure::read('__Site.Attribute.Type'),
            ));
            echo $this->Rumahku->buildInputToggle('is_required', array(
                'label' => __('is Required ?'),
                'frameClass' => 'col-sm-8',
                'labelClass' => 'col-xl-2 col-sm-4 control-label taright',
                'class' => 'relative col-sm-8 col-xl-4',
            ));
    ?>
</div>
<div class="action-group bottom">
    <div class="btn-group floright">
        <?php
            echo $this->Form->button(__('Save'), array(
                'type' => 'submit', 
                'class'=> 'btn blue',
            ));

            echo $this->Html->link(__('Back'), $urlBack, array(
                'class'=> 'btn default',
            ));
        ?>
    </div>
</div>
<?php 
        echo $this->Form->end();
?>