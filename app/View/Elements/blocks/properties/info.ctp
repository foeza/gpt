<?php
        if( !empty($value['Property']) ) {
            $mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');
            $commission = $this->Rumahku->filterEmptyField($value, 'Property', 'commission');
            $client = $this->Rumahku->filterEmptyField($value, 'Owner', 'full_name');

            $price = $this->Property->getPrice($value);
?>
<div class="detail-project-content">
    <div class="info-wrapper contract m0">
        <div id="contract-info">
            <div id="prop-info">
                <?php 
                
                        $slide = $this->Html->link($this->Rumahku->icon('rv4-angle-up'), '#', array(
                            'escape' => false,
                            'class' => 'toggle-display floright',
                            'data-display' => "#detail-project-property",
                            'data-type' => 'slide',
                            'data-arrow' => 'true',
                        ));

                        echo $this->Html->tag('h1', sprintf(__('Informasi Properti %s'), $slide), array(
                            'class' => 'info-title',
                        ));

                        $items =  $this->Html->tag('div', $this->element('blocks/properties/items', array(
                            'fullDisplay' => false,
                            'value' => $value,
                        )), array(
                            'id' => 'list-property',
                            'class' => 'tab-content'
                        ));
                        ## VIEW INFORMATION PROPERTY
                        echo $this->Html->tag('div', $items, array(
                            'id' => 'detail-project-property'
                        ));
                ?>
            </div>
        </div>
    </div>
</div>
<?php
        }
?>