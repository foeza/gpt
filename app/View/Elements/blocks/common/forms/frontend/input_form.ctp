<?php 
        $label = !empty($label)?$label:false;
        $labelClass = !empty($labelClass)?$labelClass:false;

        $class = !empty($class)?$class:false;
        $fieldName = !empty($fieldName)?$fieldName:false;
        $textGroup = !empty($textGroup)?$textGroup:false;
        $positionGroup = !empty($positionGroup)?$positionGroup:'right';

        $inputClass = !empty($inputClass)?$inputClass:false;
        $inputText = !empty($inputText)?$inputText:false;
        $frameClass = !empty($frameClass)?$frameClass:false;
        $infoText = !empty($infoText)?$infoText:false;
        $labelControl = isset($labelControl)?$labelControl:'control-label';

        $custom = !empty($custom)?$custom:false;
        $preview = !empty($preview)?$preview:false;
        $photoName = !empty($preview['photo'])?$preview['photo']:false;

        if( !empty($textGroup) ) {
            $inputClass .= sprintf(' has-side-control at-%s', $positionGroup);
            $class .= ' input-group';
        }

        if( !empty($preview) && !empty($photoName) ) {
            $photoName = !empty($preview['photo'])?$preview['photo']:false;
            $save_path = !empty($preview['save_path'])?$preview['save_path']:false;
            $size = isset($preview['size'])?$preview['size']:false;
            $thumb = isset($preview['thumb'])?$preview['thumb']:true;
            $urlDeleted = !empty($preview['urlDeleted'])?$preview['urlDeleted']:false;

            $photo = $this->Common->photo_thumbnail(array(
                'save_path' => $save_path, 
                'src'=> $photoName, 
                'size' => $size,
                'thumb' => $thumb,
            ));

            if( !empty($urlDeleted) ) {
                $customDelete = $this->Html->link($this->Common->icon('times'), $urlDeleted, array(
                    'escape' => false,
                    'class' => 'photo-preview-deleted btn btn-danger btn-xs'
                ), __('Are you sure want to delete this photo?'));
            } else {
                $customDelete = false;
            }
?>
<div class="form-group preview-img">
    <?php 
            if( !empty($label) ) {
                echo $this->Html->tag('div', $this->Form->label($fieldName, '&nbsp;', array(
                    'class' => $labelControl,
                )), array(
                    'class' => $labelClass.' lbl-preview',
                ));
            }

            echo $this->Html->tag('div', $photo.$customDelete, array(
                'class' => $class.' wrapper-img',
            ));
            echo $this->Form->hidden(sprintf('%s_hide', $fieldName));
    ?>
</div>
<?php 
        }
?>
<div class="<?php echo $frameClass; ?>">
    <?php 
            if( !empty($label) ) {
                echo $this->Html->tag('div', $this->Form->label($fieldName, $label, array(
                    'class' => $labelControl,
                )), array(
                    'class' => $labelClass,
                ));
            }
    ?>
    <div class="<?php echo $class; ?>">
        <?php 
                $optionsInput = array(
                    'class' => $inputClass,
                    'label' => false,
                    'required' => false,
                    'error' => false,
                    'div' => false,
                    'placeholder' => false,
                );

                if( !empty($empty) ) {
                    $optionsInput['empty'] = $empty;
                }
                if( !empty($id) ) {
                    $optionsInput['id'] = $id;
                }
                if( !empty($placeholder) ) {
                    $optionsInput['placeholder'] = $placeholder;
                }
                if( !empty($options) ) {
                    $optionsInput['options'] = $options;
                }
                if( !empty($attributes) ) {
                    $optionsInput = array_merge($optionsInput, $attributes);
                }

                if( !empty($inputText) ) {
                    $optionsInput['class'] .= ' text-control';

                    echo $this->Html->tag('div', $inputText, $optionsInput);
                } else {
                    if( !empty($type) ) {
                        $optionsInput['type'] = $type;
                    }

                    echo $this->Form->input($fieldName, $optionsInput);
                }

                if( !empty($textGroup) ) {
                    echo $this->Html->tag('div', $textGroup, array(
                        'class' => sprintf('input-group-addon at-%s', $positionGroup),
                    ));
                }

                if( !empty($infoText) ) {
                    echo $this->Html->tag('small', $this->Html->tag('span', $infoText), array(
                        'class' => 'overflow-extra-text tajustify',
                    ));
                }

                if( !empty($custom) ) {
                    $customType = !empty($custom['type'])?$custom['type']:false;
                    $customFieldName = !empty($custom['fieldName'])?$custom['fieldName']:false;
                    $customLabel = !empty($custom['label'])?$custom['label']:false;

                    switch ($customType) {
                        case 'whatsapp':
                            echo $this->Html->tag('div', $this->Form->input($customFieldName, array(
                                'type' => 'checkbox',
                                'label' => $customLabel,
                                'div' => false,
                            )), array(
                                'class' => 'extra-checkbox relative',
                            ));
                            break;
                    }
                }

                $errorMsg = $this->Form->error($fieldName, null, array(
                    'class' => 'error-message'
                ));

                if( !empty($errorMsg) ) {
                    echo $this->Html->tag('div', '', array(
                        'class' => 'clear',
                    ));
                    echo $errorMsg;
                }
        ?>
    </div>
</div>