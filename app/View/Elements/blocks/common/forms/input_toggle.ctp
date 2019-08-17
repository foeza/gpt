<?php         
        $data = $this->request->data;
        $frameClass = !empty($frameClass)?$frameClass:false;
        $containerClass = !empty($containerClass)?$containerClass:false;
        
        $label = !empty($label)?$label:false;
        $labelClass = !empty($labelClass)?$labelClass:false;

        $class = !empty($class)?$class:false;
        $inputClass = !empty($inputClass)?$inputClass:false;
        $fieldName = !empty($fieldName)?$fieldName:false;
        $infoText = !empty($infoText)?$infoText:false;
        $infoClass = isset($infoClass)?$infoClass:'overflow-extra-text tajustify';

        $data_toggle = !empty($data_toggle)?$data_toggle:false;
        $data_width = !empty($data_width)?$data_width:false;
        $data_height = !empty($data_height)?$data_height:false;
        $default = isset($default)?$default:false;
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
                ?>
                <div class="<?php echo $class; ?>">
                    <div class="relative">
                        <div class="<?php echo sprintf('toggle-container %s', $containerClass)?>">
                            <?php 
                                    $option_input = array(
                                        'type' => 'checkbox',
                                        'label' => false,
                                        'required' => false,
                                        'div' => false,
                                        'data-toggle' => $data_toggle,
                                        'data-width' => $data_width,
                                        'data-height' => $data_height,
                                        'class' => 'toggle-input '.$inputClass,
                                    );

                                    if(!empty($attributes)){
                                        $option_input = array_merge($option_input, $attributes);
                                    }

                                    if( empty($data) ) {
                                        $option_input['value'] = $default;
                                    }

                                    echo $this->Form->input($fieldName, $option_input);
                            ?>
                        </div>
                        <?php
                                if( !empty($infoText) ) {
                                    $infoTextStyle = !empty($infoTextStyle) ? $infoTextStyle : false;

                                    echo $this->Html->tag('small', $this->Html->tag('span', $infoText, array(
                                        'style' => $infoTextStyle,
                                    )), array(
                                        'class' => $infoClass,
                                    ));
                                }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>