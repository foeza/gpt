<?php 
        $_class = !empty($_class)?$_class:false;

        // $rentPrice = $this->Property->_callRentPrice($value, false, false, false);
        // $spec  = $this->Property->_callGetSpecification($value);
        $price = $this->Property->getPrice($value, false, false, false);

        $_action     = Common::hashEmptyField($value, 'Property.property_action_id');
        $change_date = Common::hashEmptyField($value, 'Property.change_date');

        // custom badge
        $name        = Common::hashEmptyField($value, 'PropertyProductCategory.name');
        $badge_color = Common::hashEmptyField($value, 'PropertyProductCategory.badge_color');

        $customDate = $this->Rumahku->formatDate($change_date, 'j M Y');
?>
<div class="specs-sidebar <?php echo $_class; ?>">
    <?php 
            echo $this->Html->tag('h2', __('Info Produk'), array(
                'class' => 'section-title',
            ));
    ?>
    <div class="property-list">
        <dl>
            <?php 
                    echo $this->Html->tag('dt', __('Kategori:'));
                    echo $this->Html->tag('dd', $name);

                    if( !empty($price) && $_action == 1 ) {
                        echo $this->Html->tag('dt', __('Harga:'));
                        echo $this->Html->tag('dd', $price);
                    }

            ?>
        </dl>
        <?php 
                // echo $spec;
        ?>
        <dl>
            <?php
                    echo $this->Html->tag('dt', __('Update Terakhir:'));
                    echo $this->Html->tag('dd', $customDate);
            ?>
        </dl>
    </div><!-- /.property-list -->
</div>