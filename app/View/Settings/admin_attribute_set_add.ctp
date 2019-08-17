<?php 
		$urlBack = array(
			'controller' => 'settings',
			'action' => 'attribute_sets',
			'admin' => true,
		);
        $web_colors = $this->Rumahku->filterEmptyField($_global_variable, 'web_colors');

		echo $this->element('blocks/settings/attributes/tab_action', array(
            'urlBack' => $urlBack,
        ));
        echo $this->Form->create('AttributeSet', array(
            'class' => 'form-horizontal',
        ));
?>
<div class="user-fill">
	<?php 
            echo $this->Rumahku->buildInputForm('name', array(
                'label' => __('Set Name *'),
            ));
            echo $this->Rumahku->buildInputForm('scope', array(
                'label' => __('Scope'),
                'empty' => __('Pilih Scope'),
                'options' => array(
                    'crm' => __('CRM'),
                ),
            ));
            echo $this->Rumahku->fieldColorPicker('bg_color', __('Warna Background'), array(
                'frameClass' => 'col-sm-8',
                'labelClass' => 'col-xl-2 col-sm-4 control-label taright',
                'class' => 'relative col-sm-6 col-xl-4',
                'defaultClass' => 'col-sm-2 col-xl-2',
                'dataField' => 'bg_color',
                'dataDefault' => $web_colors,
            ));
            echo $this->Rumahku->buildInputForm('description', array(
                'label' => __('Description'),
                'optional' => true,
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