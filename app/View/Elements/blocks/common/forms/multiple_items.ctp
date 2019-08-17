<?php 
        $data = $this->request->data;
        $value = !empty($value)?$value:false;
        $modelName = !empty($modelName)?$modelName:false;
        $fieldName = !empty($fieldName)?$fieldName:'name';
        $placeholder = !empty($placeholder)?$placeholder:false;

        if( empty($idx) ) {
            $addClass = 'field-copy';
        } else {
            $addClass = '';
        }
?>
<li class="<?php echo $addClass; ?>">
    <?php 
            echo $this->Form->input($modelName.'.'.$fieldName.'.', array(
                'label' => false,
                'div' => array(
                    'class' => 'form-group',
                ),
                'required' => false,
                'placeholder' => $placeholder,
                'value' => $value,
            ));
            echo $this->Html->tag('span', $this->Html->link($this->Rumahku->icon('rv4-cross'), '#', array(
                'escape' => false,
            )), array(
                'class' => 'removed',
            ));
    ?>
</li>