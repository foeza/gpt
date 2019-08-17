<?php 
        $frameClass = !empty($frameClass)?$frameClass:false;
        $label = !empty($label)?$label:false;
        $labelClass = !empty($labelClass)?$labelClass:false;
        $defaultClass = isset($defaultClass)?$defaultClass:false;

        $class = !empty($class)?$class:false;
        $fieldName = !empty($fieldName)?$fieldName:false;

        $dataField = !empty($dataField)?$dataField:false;
        $dataDefault = !empty($dataDefault)?$dataDefault:false;
?>
<div class="form-group">
    <div class="row">
        <div class="<?php echo $frameClass; ?>">
            <div class="row">
                <?php 
                        if( !empty($label) ) {
                            echo $this->Html->tag('div', $this->Form->label($fieldName, $label, array(
                                'class' => 'control-label',
                            )), array(
                                'class' => $labelClass,
                            ));
                        }
                        echo $this->Form->input($fieldName, array(
                            'label'=> false,
                            'required' => false,
                            'class' => 'form-control colorPicker',
                            'data-field' => $dataField,
                            'id' => $dataField,
                            'required' => false,
                            'placeholder' => __('Pilih Warna'),
                            'div' => array(
                                'class' => $class,
                            ),
                            'autocomplete' => 'off',
                        ));

					//	if( !empty($defaultClass) ) {
                            if(array_key_exists($dataField, $dataDefault)){
                                echo $this->Html->tag('div', $this->Html->tag('button', __('Default'), array(
                                    'class' => 'form-control btn default btn-theme-default',
                                    'data-field' => $dataField,
                                    'data-default' => $dataDefault[$dataField],
                                )), array(
                                    'class' => $defaultClass,
                                ));
                            }
					//	}
                ?>
            </div>
        </div>
    </div>
</div>