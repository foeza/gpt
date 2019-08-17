<?php
        $label = !empty($label)?$label:false;
        $description = !empty($description)?$description:false;
        $values = !empty($values)?$values:false;
        $modelName = !empty($modelName)?$modelName:false;
        $fieldName = !empty($fieldName)?$fieldName:false;
        $classLabel = !empty($classLabel)?$classLabel:'col-sm-10 col-sm-offset-1';
        $classForm = !empty($classForm)?$classForm:'col-sm-8 col-sm-offset-1';
        $classList = !empty($classList)?$classList:'col-sm-4';
        $customContent = !empty($customContent)?$customContent:false;

        $is_building = !empty($is_building)?$is_building:false;
        $is_residence = !empty($is_residence)?$is_residence:false;
        $is_space = !empty($is_space)?$is_space:false;
        $is_lot = !empty($is_lot)?$is_lot:false;
        $data = !empty($data)?$data:false;
        $showInput = true;

        if( !empty($is_building) ) {
            $showInput = $this->Rumahku->inputTypeAllow($data, 'is_building');
        } else if( !empty($is_lot) ) {
            $showInput = $this->Rumahku->inputTypeAllow($data, 'is_lot');
        } else if( !empty($is_residence) ) {
            $showInput = $this->Rumahku->inputTypeAllow($data, 'is_residence');
        } else if( !empty($is_space) ) {
            $showInput = $this->Rumahku->inputTypeAllow($data, 'is_space');
        }

        if( !empty($showInput) && !empty($values) ) {
?>
<div class="form-group plus">
    <div class="row">
        <div class="<?php echo $classLabel; ?> label">
            <?php 
                    echo $this->Html->tag('h4', $label);

                    if( !empty($description) ) {
                        echo $this->Html->tag('p', $description);
                    }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="<?php echo $classForm; ?>">
            <div class="cb-custom" autocomplete="off">
                <ul class="row">
                    <?php 
                            $options = array(
                                'modelName' => $modelName,
                                'fieldName' => $fieldName,
                                'classList' => $classList,
                            );

                            if( is_array($values) ) {
                                foreach ($values as $id => $value) {
                                    $options['id'] = $id;
                                    $options['value'] = $value;

                                    echo $this->element('blocks/common/forms/list_checkbox_items', $options);
                                }
                            } else {
                                $options['value'] = $values;
                                echo $this->element('blocks/common/forms/list_checkbox_items', $options);
                            }

                            if( !empty($customContent) ) {
                                echo $customContent;
                            }
                    ?>
                </ul>
                <?php 
                        echo $this->Form->error($modelName.'.'.$fieldName);
                ?>
            </div>
        </div>
    </div>
</div>
<?php 
        }
?>