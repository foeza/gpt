<?php 
        $_class = !empty($_class)?$_class:false;

        $price = $this->Property->getPrice($value, false, false, false);
        $rentPrice = $this->Property->_callRentPrice($value, false, false, false);
        $spec = $this->Property->_callGetSpecification($value);

        $_action = $this->Rumahku->filterEmptyField($value, 'Property', 'property_action_id');
        $mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');
        $change_date = $this->Rumahku->filterEmptyField($value, 'Property', 'change_date');

        $customDate = $this->Rumahku->formatDate($change_date, 'j M Y');
?>
<div class="specs-sidebar <?php echo $_class; ?>">
    <?php 
            echo $this->Html->tag('h2', __('Spesifikasi'), array(
                'class' => 'section-title',
            ));
    ?>
    <div class="property-list">
        <?php 
                if( !empty($rentPrice) && $_action == 2 ) {
        ?>
        <div class="price">
            <?php 
                    echo $this->Html->tag('label', __('Harga'));
                    echo $this->Html->tag('div', $rentPrice, array(
                        'class' => 'pricing',
                    ));
            ?>
        </div>
        <?php 
                }
        ?>
        <dl>
            <?php 
                    if( !empty($price) && $_action == 1 ) {
                        echo $this->Html->tag('dt', __('Harga:'));
                        echo $this->Html->tag('dd', $price);
                    }

                    echo $this->Html->tag('dt', __('Properti ID:'));
                    echo $this->Html->tag('dd', $mls_id);

            ?>
        </dl>
        <?php 
                echo $spec;
        ?>
        <dl>
            <?php
                    echo $this->Html->tag('dt', __('Update Terakhir:'));
                    echo $this->Html->tag('dd', $customDate);
            ?>
        </dl>
    </div><!-- /.property-list -->
</div>