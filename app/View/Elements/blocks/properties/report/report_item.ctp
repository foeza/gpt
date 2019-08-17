<?php
        $customPhoto = isset($customPhoto) ? $customPhoto : false;
        $name = isset($name) ? $name : false;
        $itemUrl = isset($itemUrl) ? $itemUrl : false;
?>

<div class="template-download col-sm-4">
    <div class="item">
        <?php 
                echo $this->Html->tag('div', $customPhoto, array(
                    'class' => 'preview relative',
                ));
                echo $this->Html->tag('label', $name);
        ?>
        <div class="action">
            <div class="form-group">
                <?php 
                        echo $this->Html->link(__('Pilih'), $itemUrl, array(
                            'class' => 'btn green',
                        ));
                ?>
            </div>
        </div>
    </div>
</div>