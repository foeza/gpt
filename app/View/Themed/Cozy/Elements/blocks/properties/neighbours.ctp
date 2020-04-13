<?php
        if(!empty($neighbours)) {
            echo $this->Html->tag('h1', __('Produk Terkait'), array(
                'class' => 'section-title',
            ));

?>
            <div id="similar-properties" class="grid-style1 clearfix hidden-print">
                <?php
                        echo $this->element('blocks/properties/frontend/items', array(
                            'properties' => $neighbours,
                            '_class' => 'col-md-4',
                        ));
                ?>
            </div>
<?php
        }

        // if ($this->action == 'detail') {
        //     echo $this->element('blocks/properties/frontend/link_other_property');
        // }
?>