<?php 
        $inputName = sprintf('%s.%s', $modelName, $fieldName);

        if( !empty($id) ) {
            $inputName = sprintf('%s.%s', $inputName, $id);
        } else {
            $id = true;
        }

        $contentLi = $this->Form->input($inputName, array(
            'type' => 'checkbox',
            'label' => false,
            'div' => false,
            'required' => false,
            'error' => false,
            'value' => $id,
        ));
        $contentLi .= $this->Form->label($inputName, $value);

        echo $this->Html->tag('li', $contentLi, array(
            'class' => 'cb-checkmark '.$classList,
        ));
?>