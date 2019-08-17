<?php
        $description = !empty($description)?$description:false;
        $frameClass = !empty($frameClass)?$frameClass:'col-sm-10';
        $class = !empty($class)?$class:'col-sm-6';

        $modelName = !empty($modelName)?$modelName:false;
        $fieldName = !empty($fieldName)?$fieldName:false;
        $fieldNameText = !empty($fieldNameText)?$fieldNameText:false;

        $data = $this->request->data;
        $disabled = !$this->Rumahku->filterEmptyField($data, $modelName, $fieldName);
        $otherText = $this->Rumahku->filterEmptyField($data, $modelName, $fieldNameText);

        if( !empty($disabled) && !empty($otherText) ) {
            $this->request->data[$modelName][$fieldNameText] = '';
        }

        $fieldName = $modelName.'.'.$fieldName;
        $fieldNameText = $modelName.'.'.$fieldNameText;

        $contentLi = $this->Form->input($fieldName, array(
            'type' => 'checkbox',
            'label' => false,
            'div' => false,
            'required' => false,
            'error' => false,
            'class' => 'chk-other-item',
            'value' => 1,
        ));
        $contentLi .= $this->Form->label($fieldName, __('Lainnya'));
        $contentLi .= $this->Html->tag('div', $this->Rumahku->buildInputForm($fieldNameText, array(
            'frameClass' => $frameClass,
            'labelClass' => '',
            'inputClass' => 'other-checkbox',
            'infoText' => $description,
            'disabled' => $disabled,
        )), array(
            'class' => 'text-other-checkbox'
        ));

        echo $this->Html->tag('li', $contentLi, array(
            'class' => 'cb-checkmark other-checkboxes '.$class,
        ));
?>