<?php
        echo $this->Form->create('User', array(
            'class' => 'form-target',
        ));

        if( !empty($values) ) {
?>
            <div class="my-properties">
                <div class="wrapper-border">
                    <div id="list-property">
                        <?php   
                                foreach ($values as $key => $value) {
                                    echo $this->element('blocks/properties/items', array(
                                        'value' => $value,
                                        'fullDisplay' => false,
                                    ));
                                } 
                        ?>
                    </div>
                </div>
            </div>
<?php
        } else {
            echo $this->Html->tag('p', __('Data belum tersedia'), array(
                'class' => 'alert alert-warning'
            ));
        }

        echo $this->Form->end();
        echo $this->element('blocks/common/pagination');
?>