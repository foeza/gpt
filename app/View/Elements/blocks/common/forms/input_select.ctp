<?php 
        $fieldName = !empty($fieldName)?$fieldName:false;
        $fieldError = !empty($fieldError)?$fieldError:false;
        $boxClass = !empty($boxClass)?$boxClass:'col-sm-5';
        $label = !empty($label)?$label:false;
        $options = !empty($options)?$options:array();
?>
<div class="<?php echo $boxClass;?>">
    <?php 
            echo $this->Form->label($fieldName, $label);
    ?>
    <div class="input-group side">
        <div class="select">
            <?php 
                    echo $this->Form->input($fieldName, array_merge(array(
                        'label' => false,
                        'required' => false,
                        'div' => false,
                        'error' => false,
                        'class' => 'form-control',
                    ), $options));
                    echo $this->Rumahku->icon('rv4-angle-down', false, 'span');
            ?>
        </div>
    </div>
    <?php 
            if( !empty($fieldError) ) {
                $fieldName = $fieldError;
            }

            echo $this->Form->error($fieldName);
    ?>
</div>