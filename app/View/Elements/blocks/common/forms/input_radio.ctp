<?php 
        $frameClass = !empty($frameClass)?$frameClass:false;
        $divClass = !empty($divClass)?$divClass:false;
        $label = !empty($label)?$label:false;
        $labelClass = !empty($labelClass)?$labelClass:false;

        $class = !empty($class)?$class:false;
        $fieldName = !empty($fieldName)?$fieldName:false;

        $options = !empty($options)?$options:false;
        $styling = !empty($styling)?$styling:false;

        $attributeOptions = array(
            'legend' => false,
            'separator' => '</li><li class="cb-checkmark radio">',
            'required' => false,
        );

        if( !empty($attributes) ) {
            $attributeOptions = array_merge($attributeOptions, $attributes);
        }
?>
<div class="form-group radio-custom <?php echo $divClass; ?>">
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
                ?>
                <div class="<?php echo $class; ?> cb-custom">
                    <?php 
                            switch ($styling) {
                                case 'line':
                                    echo $this->Html->tag('ul', $this->Html->tag('li', $this->Form->radio($fieldName, $options, $attributeOptions), array(
                                        'class' => 'cb-checkmark radio',
                                    )).$this->Form->error($fieldName), array(
                                        'class' => 'rd-line',
                                    ));
                                    break;
                                
                                default:
                                    echo $this->Form->radio($fieldName, $options, array(
                                        'legend' => false,
                                    ));
                                    break;
                            }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>