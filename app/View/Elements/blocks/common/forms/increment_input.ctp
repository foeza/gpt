<?php 
        $showInput = true;
        $label = !empty($label)?$label:false;
        $labelClass = !empty($labelClass)?$labelClass:false;
        $inputClass = !empty($inputClass)?$inputClass:false;

        $class = !empty($class)?$class:false;
        $fieldName = !empty($fieldName)?$fieldName:false;
        $textGroup = !empty($textGroup)?$textGroup:false;

        $is_building = !empty($is_building)?$is_building:false;
        $is_residence = !empty($is_residence)?$is_residence:false;
        $is_space = !empty($is_space)?$is_space:false;
        $is_lot = !empty($is_lot)?$is_lot:false;
        $data = !empty($data)?$data:false;

        if( !empty($is_building) ) {
            $showInput = $this->Rumahku->inputTypeAllow($data, 'is_building');
        } else if( !empty($is_lot) ) {
            $showInput = $this->Rumahku->inputTypeAllow($data, 'is_lot');
        } else if( !empty($is_residence) ) {
            $showInput = $this->Rumahku->inputTypeAllow($data, 'is_residence');
        } else if( !empty($is_space) ) {
            $showInput = $this->Rumahku->inputTypeAllow($data, 'is_space');
        }

        if( !empty($showInput) ) {
?>
<div class="form-group">
    <div class="row">
        <div class="col-sm-8">
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
                <div class="<?php echo $class; ?> increment">
                    <?php 
                            echo $this->Html->link($this->Rumahku->icon('rv4-bold-min mr0 fs085'), '#', array(
                                'escape' => false,
                                'class' => 'input-group-addon at-left op-min',
                                'role' => 'button',
                                'data-action' => 'min',
                                'data-target' => 'tmp-increment',
                            ));
                            echo $this->Form->input($fieldName, array(
                                'type' => 'text',
                                'label' => false,
                                'required' => false,
                                'error' => false,
                                'div' => false,
                                'class' => 'form-control has-side-control at-bothway tmp-increment '.$inputClass,
                            ));
                            echo $this->Html->link($this->Rumahku->icon('rv4-bold-plus mr0 fs085'), '#', array(
                                'escape' => false,
                                'class' => 'input-group-addon at-right op-plus',
                                'role' => 'button',
                                'data-action' => 'plus',
                                'data-target' => 'tmp-increment',
                            ));
                    ?>
                </div>
                <?php 
                        if( !empty($textGroup) ) {
                            echo $this->Html->tag('span', $textGroup, array(
                                'class' => 'sign col-sm-1',
                            ));
                        }
                ?>
            </div>
            <?php 
                    echo $this->Form->error($fieldName, null, array(
                        'class' => 'error-message tacenter'
                    ));
            ?>
        </div>
    </div>
</div>
<?php 
        }
?>