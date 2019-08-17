<?php 
        if( !empty($options) ) {
            $classSize = !empty($classSize)?$classSize:false;
            $currentValue = !empty($value)?$value:false;

            if( !empty($label) ) {
                echo $this->Form->label($fieldName, $label);
            }

            $inputOptions = empty($inputOptions) ? array() : $inputOptions;
?>
<div class="<?php echo $classSize; ?> tracker-radio-id">
    <ul class="tabs clear">
        <?php 
                $idx = 0;
                $tempArr = array();

                foreach ($options as $id => $value) {
                    if( $idx > 4 ) {
                        $tempArr[$id] = $value;
                    } else {
                        if( $currentValue == $id ) {
                            $addClass = 'active';
                        } else {
                            $addClass = '';
                        }

                        echo $this->Html->tag('li', $this->Html->link($value, 'javascript:void(0);', array(
                            'class' => 'action '.$addClass,
                            'data-value' => $id,
                        )), array(
                            'role' => 'presentation',
                        ));
                    }

                    $idx++;
                }

                if( !empty($tempArr) ) {
                    $contentLi = false;
                    $parentClass = '';

                    foreach ($tempArr as $id => $value) {
                        if( $currentValue == $id ) {
                            $addClass = 'active';
                            $parentClass = 'active';
                        } else {
                            $addClass = '';
                        }

                        $contentLi .= $this->Html->tag('li', $this->Html->link($value, 'javascript:void(0);', array(
                            'class' => 'action '.$addClass,
                            'data-value' => $id,
                        )));
                    }

                    $contentUl = $this->Html->tag('ul', $contentLi, array(
                        'class' => 'dropdown-menu',
                    ));
                    echo $this->Html->tag('li', $this->Html->link(__('Lainnya&nbsp;').$this->Rumahku->icon('caret'), '#', array(
                        'escape' => false,
                        'class' => 'dropdown-toggle '.$parentClass,
                        'data-toggle' => 'dropdown',
                        'aria-expanded' => 'true',
                        'aria-hashpopup' => 'true',
                    )).$contentUl, array(
                        'role' => 'presentation',
                        'class' => 'dropdown-group',
                    ));
                }
        ?>
    </ul>
    <?php         
                echo $this->Form->hidden($fieldName, array_replace(array(
                    'class' => 'info-radio-id',
                    'error' => false,
                ), $inputOptions));

                if( !empty($error) ) {
                    echo $this->Form->error($fieldName);
                }
            }
    ?>
</div>