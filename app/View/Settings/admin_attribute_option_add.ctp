<?php 
        $id = !empty($id)?$id:false;
		$urlBack = array(
			'controller' => 'settings',
			'action' => 'attribute_options',
            $id,
			'admin' => true,
		);

		$this->Html->addCrumb(__('Manage Options'), $urlBack);
		$this->Html->addCrumb($module_title);

		echo $this->element('blocks/settings/attributes/tab_action');

        echo $this->Form->create('AttributeOption', array(
            'class' => 'form-horizontal',
        ));
?>
<div class="user-fill">
	<?php 
            echo $this->Rumahku->buildInputForm('name', array(
                'label' => __('Attribute Name *'),
            ));
            echo $this->Rumahku->buildInputForm('attribute_set_id', array(
                'label' => __('Tampil di Attribute Set'),
                'empty' => __('Semua Attribute Set'),
            ));
            echo $this->Rumahku->buildInputForm('type', array(
                'label' => __('Tipe Attribute'),
                'empty' => __('Pili Tipe'),
                'options' => Configure::read('__Site.Attribute.Type'),
            ));
            echo $this->Rumahku->buildInputForm('order', array(
                'label' => __('Order'),
                'class' => 'relative col-sm-3 col-xl-2',
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
        ?>
    </div>
</div>
<?php 
        echo $this->Form->end();
?>