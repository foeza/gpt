<?php 
        $dataRequest = $this->request->data;

        $showInput = true;
        $label = !empty($label)?$label:false;
        $labelClass = !empty($labelClass)?$labelClass:false;
        $labelInClass = isset($labelInClass)?$labelInClass:'control-label';
        $disabled = !empty($disabled)?$disabled:false;

        $class = !empty($class)?$class:false;
        $fieldName = !empty($fieldName)?$fieldName:false;
        $otherContent = !empty($otherContent)?$otherContent:false;

        $textGroup = !empty($textGroup)?$textGroup:false;
        $positionGroup = !empty($positionGroup)?$positionGroup:'right';
        $classGroup = !empty($classGroup)?$classGroup:false;
        $classGroupPosition = !empty($classGroupPosition)?$classGroupPosition:false;

        $textGroupSecond = !empty($textGroupSecond)?$textGroupSecond:false;
        $positionGroupSecond = !empty($positionGroupSecond)?$positionGroupSecond:'left';
        $classGroupSecond = !empty($classGroupSecond)?$classGroupSecond:false;

        $inputClass = !empty($inputClass)?$inputClass:false;
        $inputText = !empty($inputText)?$inputText:false;
        $wrapperClass = isset($wrapperClass)?$wrapperClass:'row';
        $frameClass = isset($frameClass)?$frameClass:false;
        $formGroupClass = isset($formGroupClass)?$formGroupClass:'form-group';

        $default = !empty($default)?$default:false;
        $infoText = !empty($infoText)?$infoText:false;
        $infoClass = isset($infoClass)?$infoClass:'overflow-extra-text tajustify';
        $rows = !empty($rows)?$rows:false;
        $readonly = !empty($readonly)?$readonly:false;
        $url = !empty($url)?$url:false;
        $placeholder = !empty($placeholder)?$placeholder:false;
        $multiple = !empty($multiple)?$multiple:false;
        $data_max_lenght = !empty($data_max_lenght)?$data_max_lenght:false;

        $error = isset($error)?$error:true;
        $fieldError = isset($fieldError)?$fieldError:$fieldName;
        $custom = !empty($custom)?$custom:false;
        $preview = !empty($preview)?$preview:false;
        $photoName = !empty($preview['photo'])?$preview['photo']:false;
        $download = !empty($preview['download'])?$preview['download']:false;

        $detailView = !empty($preview['detailView'])?$preview['detailView']:false;

        $outer_group = isset($outer_group) ? $outer_group : false;

        $is_building = !empty($is_building)?$is_building:false;
        $is_residence = !empty($is_residence)?$is_residence:false;
        $is_space = !empty($is_space)?$is_space:false;
        $is_lot = !empty($is_lot)?$is_lot:false;
        $data = !empty($data)?$data:false;
        $errorMsg = false;
        $delete_photo = !empty($delete_photo) ? $delete_photo : false;

        $overflowTextContent = isset($overflowTextContent) ? $overflowTextContent : array();

        $overflow_text       = $this->Rumahku->filterEmptyField($overflowTextContent, 'text', false, '', false);
        $overflow_tag        = $this->Rumahku->filterEmptyField($overflowTextContent, 'tag', false, 'div');
        $overflow_tag_options  = $this->Rumahku->filterEmptyField($overflowTextContent, 'options', false, array());

        $infopopover = isset($infopopover) ? $infopopover : '';
        if(!empty($infopopover)){

            $desc_modal = $this->Rumahku->filterEmptyField($infopopover, 'content');
            $title_modal = $this->Rumahku->filterEmptyField($infopopover, 'title');
            $options_modal = $this->Rumahku->filterEmptyField($infopopover, 'options', false, array());
            $icon_modal = $this->Rumahku->filterEmptyField($infopopover, 'icon', false, 'rv4-shortip');

            $infopopover = $this->Rumahku->noticeInfo($desc_modal, $title_modal, $options_modal, $icon_modal);
        }

        if( !empty($is_building) ) {
            $showInput = $this->Rumahku->inputTypeAllow($data, 'is_building');
        } else if( !empty($is_lot) ) {
            $showInput = $this->Rumahku->inputTypeAllow($data, 'is_lot');
        } else if( !empty($is_residence) ) {
            $showInput = $this->Rumahku->inputTypeAllow($data, 'is_residence');
        } else if( !empty($is_space) ) {
            $showInput = $this->Rumahku->inputTypeAllow($data, 'is_space');
        }

        if( !isset($rowFormClass) ) {
            if( empty($class) && empty($labelClass) ) {
                $rowFormClass = '';
            } else {
                $rowFormClass = 'row';
            }
        }

        if( !empty($textGroup) && !empty($textGroupSecond) && !$outer_group ) {
            $inputClass .= ' has-side-control at-bothway';

            if( $classGroupPosition != 'inside' ) {
                $class .= ' input-group';
            }
        } else if( !empty($textGroup) && !$outer_group ) {
            $inputClass .= sprintf(' has-side-control at-%s', $positionGroup);
            
            if( $classGroupPosition != 'inside' ) {
                $class .= ' input-group';
            }
        }

        if( !empty($error) && !empty($fieldError) ) {
            if( !is_array($fieldError) ) {
                $fieldError = array(
                    $fieldError,
                );
            }

            if( !empty($errorMsg) ) {
                $errorMsg = $this->Html->tag('div', $errorMsg, array(
                    'class' => 'error-message'
                ));
            } else {
                foreach ($fieldError as $key => $error) {
                    if( $this->Form->isFieldError($error) ) {
                        $errorMsg = $this->Form->error($error, null, array(
                            'class' => 'error-message'
                        ));
                        break;
                    }
                }
            }

            if( !empty($errorMsg) ) {
                $errorMsg = $this->Html->tag('div', '', array(
                    'class' => 'clear',
                )).$errorMsg;
            }
        }

        if( !empty($showInput) ) {
            if( !empty($preview) && !empty($photoName) ) {
                $photoName = !empty($preview['photo'])?$preview['photo']:false;
                $save_path = !empty($preview['save_path'])?$preview['save_path']:false;
                $size = !empty($preview['size'])?$preview['size']:false;
                $photo = $this->Rumahku->photo_thumbnail(array(
                    'save_path' => $save_path, 
                    'src'=> $photoName, 
                    'size' => $size,
                ), array(
                    'class' => 'img-thumbnail'
                ));
?>
<div class="form-group preview-img">
    <div class="<?php echo $rowFormClass; ?>">
        <div class="<?php echo $frameClass; ?>">
            <?php 
                    if( !empty($label) ) {
                        echo $this->Html->tag('div', $this->Form->label($fieldName, '&nbsp;', array(
                            'class' => $labelInClass,
                        )), array(
                            'class' => $labelClass.' lbl-preview',
                        ));
                    }

                    $delete_image = '';
                    if(!empty($delete_photo['url'])){
                        $delete_url         = $this->Rumahku->filterEmptyField($delete_photo, 'url');
                        $option_link        = $this->Rumahku->filterEmptyField($delete_photo, 'option_link');
                        $option_wrapper     = $this->Rumahku->filterEmptyField($delete_photo, 'option_wrapper');
                        $confirm            = $this->Rumahku->filterEmptyField($delete_photo, 'confirm');

                        $default_option_link = array(
                            'escape' => false
                        );

                        $default_option_wrapper = array(
                            'class' => 'cross-image'
                        );

                        if(!empty($option_link)){
                            $default_option_link = array_merge($default_option_link, $option_link);
                        }

                        if(!empty($option_wrapper)){
                            $default_option_wrapper = array_merge($default_option_wrapper, $option_wrapper);
                        }

                        $delete_image = $this->Html->tag(
                            'span', 
                            $this->Html->link(
                                $this->Rumahku->icon('rv4-bold-cross'), 
                                $delete_url, 
                                $default_option_link,
                                $confirm
                            ),
                            $default_option_wrapper
                        );
                    }

                    echo $this->Html->tag('div', $this->Html->div('box-image-preview', $delete_image.$photo), array(
                        'class' => $class.' wrapper-img',
                    ));

                    if( !empty($download) ) {
                        echo $this->Html->link($this->Rumahku->icon('rv4-download').__('Lihat'), $download, array(
                            'escape' => false,
                            'class' => 'download-file',
                        ));
                    }

                    if(!empty($detailView)){
                        echo $this->Html->link($this->Rumahku->icon('rv4-image-2').__('lihat file'), $detailView, array(
                            'escape' => false,
                            'class' => 'ajaxModal',
                            'data-wrapper-write' => '#wrapper-modal-write',
                        ));
                    }
                    echo $this->Form->hidden(sprintf('%s_hide', $fieldName));
                    echo $this->Form->hidden(sprintf('%s_save_path', $fieldName));
                    echo $this->Form->hidden(sprintf('%s_name', $fieldName));
            ?>
        </div>
    </div>
</div>
<?php 
        }
?>
<div class="<?php echo $formGroupClass; ?>">
    <div class="<?php echo $wrapperClass; ?>">
        <div class="<?php echo $frameClass; ?>">
            <div class="<?php echo $rowFormClass; ?>">
                <?php 
                        if( !empty($label) ) {
                            echo $this->Html->tag('div', $this->Form->label($fieldName, $label, array(
                                'class' => $labelInClass,
                            )), array(
                                'class' => $labelClass,
                            ));
                        }

                        if($outer_group){
                            echo '<div class="'.$class.' outer-group-custom">';
                            $class = 'input-group';
                        }
                ?>
                <div class="<?php echo $class; ?>">
                    <?php 
                            $inputResult = '';
                            $optionsInput = array(
                                'class' => $inputClass,
                                'label' => false,
                                'required' => false,
                                'error' => false,
                                'div' => false,
                                'disabled' => $disabled,
                                'readonly' => $readonly,
                                'url' => $url,
                                'placeholder' => $placeholder,
                            );

                            if( !empty($empty) ) {
                                $optionsInput['empty'] = $empty;
                            }
                            if( !empty($id) ) {
                                $optionsInput['id'] = $id;
                            }
                            if( !empty($options) ) {
                                $optionsInput['options'] = $options;
                            }
                            if( !empty($rows) ) {
                                $optionsInput['rows'] = $rows;
                            }
                            if( !empty($attributes) ) {
                                $optionsInput = array_merge($optionsInput, $attributes);
                            }
                            if( !empty($autocomplete) ) {
                                $optionsInput['autocomplete'] = $autocomplete;
                            }
                            if( !empty($data_url) ) {
                                $optionsInput['data-ajax-url'] = $data_url;
                            }
                            if(!empty($data_max_lenght)){
                                $optionsInput['maxlength'] = $data_max_lenght;
                            }
                            if(!empty($default)){
                                $optionsInput['value'] = $default;
                            }

                            if( !empty($textGroup) && $positionGroup == 'left') {
                                $inputResult .= $this->Html->tag('div', $textGroup, array(
                                    'class' => sprintf('input-group-addon at-%s %s', $positionGroup, $classGroup),
                                ));
                            }

                            if( !empty($inputText) ) {
                                $optionsInput['class'] .= ' text-control';

                                $inputResult .= $this->Html->tag('div', $inputText, $optionsInput);
                            } else {
                                $optionsInput['class'] .= ' form-control';

                                if( !empty($type) ) {
                                    $optionsInput['type'] = $type;
                                }

                                if(!empty($multiple)){
                                    $optionsInput['multiple'] = $multiple;
                                }

                                $inputResult .= $this->Form->input($fieldName, $optionsInput);

                                if( !empty($otherContent) ) {
                                    $modelOtherName = $this->Rumahku->filterEmptyField($otherContent, 'modelName');
                                    $fieldOtherName = $this->Rumahku->filterEmptyField($otherContent, 'fieldName');
                                    $descriptionOther = $this->Rumahku->filterEmptyField($otherContent, 'description');
                                    $fieldNameTrigger = $this->Rumahku->filterEmptyField($otherContent, 'fieldNameTrigger');
                                    $fieldValueTrigger = $this->Rumahku->filterEmptyField($otherContent, 'fieldValueTrigger');

                                    $valueOther = $this->Rumahku->filterEmptyField($dataRequest, $modelOtherName, $fieldNameTrigger);

                                    if( $valueOther == $fieldValueTrigger ) {
                                        $addClassOther = 'show';
                                    } else {
                                        $addClassOther = '';
                                    }

                                    $inputResult .= $this->Form->input($modelOtherName.'.'.$fieldOtherName, array(
                                        'type' => 'text',
                                        'id' => 'other-text',
                                        'class' => $addClassOther,
                                        'label' => false,
                                        'div' => false,
                                        'required' => false,
                                        'placeholder' => $descriptionOther,
                                    ));
                                }
                            }

                            if( !empty($textGroup) && $positionGroup == 'right') {
                                $inputResult .= $this->Html->tag('div', $textGroup, array(
                                    'class' => sprintf('input-group-addon at-%s %s', $positionGroup, $classGroup),
                                ));
                            }

                            if( !empty($textGroupSecond) ) {
                                $inputResult .= $this->Html->tag('div', $textGroupSecond, array(
                                    'class' => sprintf('input-group-addon at-%s %s', $positionGroupSecond, $classGroupSecond),
                                ));
                            }

                            if( !empty($infoText) ) {
                                $inputResult .= $this->Html->tag('small', $this->Html->tag('span', $infoText), array(
                                    'class' => $infoClass,
                                ));
                            }

                            if( !empty($overflowTextContent) ) {
                                $inputResult .= $this->Html->tag($overflow_tag, $overflow_text, $overflow_tag_options);
                            }

                            if( !empty($custom) ) {
                                $customType = !empty($custom['type'])?$custom['type']:false;
                                $customFieldName = !empty($custom['fieldName'])?$custom['fieldName']:false;
                                $customLabel = !empty($custom['label'])?$custom['label']:false;

                                switch ($customType) {
                                    case 'whatsapp':
                                        $inputResult .= $this->Html->tag('div', $this->Form->input($customFieldName, array(
                                            'type' => 'checkbox',
                                            'label' => $customLabel,
                                            'div' => false,
                                        )), array(
                                            'class' => 'extra-checkbox relative',
                                        ));
                                        break;
                                }
                            }

                            if( empty($textGroup) || $classGroupPosition == 'inside' ) {
                                $inputErrorMsg = $errorMsg;
                            } else {
                                $inputErrorMsg = false;
                            }

                            if( $classGroupPosition == 'inside' ) {
                                echo $this->Html->tag('div', 
                                    $this->Html->tag('div', $inputResult, array(
                                        'class' => 'input-group mb0',
                                    )).
                                    $inputErrorMsg
                                );
                            } else {
                                echo $inputResult;
                                echo $inputErrorMsg;
                            }
                    ?>
                </div>
                <?php 
                        if($outer_group){
                            echo '</div>';
                        }

                        if(!empty($infopopover)){
                            echo $infopopover;
                        }

                        if( !empty($textGroup) && $classGroupPosition != 'inside' ) {
                            echo $errorMsg;
                        }
                ?>
            </div>
        </div>
    </div>
</div>
<?php 
        }
?>