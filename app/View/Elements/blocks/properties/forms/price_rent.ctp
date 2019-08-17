<?php 
        $currencies = !empty($currencies)?$currencies:false;
        $periods = !empty($periods)?$periods:false;
        $data = $this->request->data;
        
        $values = $this->Rumahku->filterEmptyField($data, 'PropertyPrice', 'currency_id');
?>
<div class="price-list relative form-added">
    <ul>
        <?php 
                if( !empty($values) ) {
                    $idx = 0;

                    foreach ($values as $key => $value) {
                        echo $this->element('blocks/properties/forms/price_items', array(
                            'idx' => $idx,
                        ));
                        $idx++;
                    }
                } else {
                    echo $this->element('blocks/properties/forms/price_items', array(
                        'idx' => 0,
                    ));
                }
        ?>
    </ul>
    <div class="row">
        <div class="col-sm-8">
            <div class="col-sm-offset-4 col-sm-6">
                <div class="form-group">
                    <?php 
                            $contentLink = $this->Html->tag('span', $this->Rumahku->icon('rv4-bold-plus'), array(
                                'class' => 'btn dark small-fixed',
                            ));
                            $contentLink .= $this->Html->tag('span', __('Tambah Harga per Periode'));
                            echo $this->Html->link($contentLink, '#', array(
                                'escape' => false,
                                'role' => 'button',
                                'class' => 'field-added',
                            ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>