<?php 
        $data = $this->request->data;
        $modelName = !empty($modelName)?$modelName:false;
        $fieldName = !empty($fieldName)?$fieldName:'name';
        $labelName = !empty($labelName)?$labelName:false;
        $placeholder = !empty($placeholder)?$placeholder:false;
        $infoTop = !empty($infoTop)?$infoTop:false;
        $infoBottom = !empty($infoBottom)?$infoBottom:false;
        $divClassTop = isset($divClassTop)?$divClassTop:'col-sm-10 col-sm-offset-1';
        $limit = !empty($limit)?$limit:2;

        $values = $this->Rumahku->filterEmptyField($data, $modelName, 'name');
?>
<div class="form-group plus" id="point-plus">
    <div class="row desc">
        <div class="<?php echo $divClassTop; ?>">
            <?php 
                    echo $this->Html->tag('h4', $labelName);

                    if( !empty($infoTop) ) {
                        echo $this->Html->tag('p', $infoTop);
                    }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="<?php echo $divClassTop; ?>">
            <div class="extra-plus-list form-added">
                <ul>
                    <?php 
                            if( !empty($values) ) {
                                $idx = 0;

                                foreach ($values as $key => $value) {
                                    echo $this->element('blocks/common/forms/multiple_items', array(
                                        'idx' => $idx,
                                        'value' => $value,
                                        'modelName' => $modelName,
                                        'fieldName' => $fieldName,
                                        'placeholder' => $placeholder,
                                    ));
                                    $idx++;
                                }
                            } else {
                                for ($i=0; $i < $limit; $i++) { 
                                    echo $this->element('blocks/common/forms/multiple_items', array(
                                        'idx' => $i,
                                        'modelName' => $modelName,
                                        'placeholder' => $placeholder,
                                        'fieldName' => $fieldName,
                                    ));
                                }
                            }
                    ?>
                </ul>
                <?php 
                        $contentLink = $this->Html->tag('span', $this->Rumahku->icon('rv4-bold-plus'), array(
                            'class' => 'btn dark small-fixed',
                        ));
                        $contentLink .= $this->Html->tag('span', sprintf(__('Tambah %s'), $labelName));
                        echo $this->Html->link($contentLink, '#', array(
                            'escape' => false,
                            'role' => 'button',
                            'class' => 'field-added',
                        ));
                ?>
            </div>
        </div>
    </div>
    <?php 
            if( !empty($infoBottom) ) {
    ?>
    <div class="row">
        <div class="<?php echo $divClassTop; ?>">
            <?php 
                    echo $this->Html->tag('p', $infoBottom);
            ?>
        </div>
    </div>
    <?php 
            }
    ?>
</div>