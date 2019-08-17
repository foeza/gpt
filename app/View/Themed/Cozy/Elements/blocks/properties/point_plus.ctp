<?php 
        $values = $this->Rumahku->filterEmptyField($value, 'PropertyPointPlus');
        
        if( !empty($values) ){
?>
<div class="print-side-right">
    <?php
            echo $this->Html->tag('h1', __('Nilai Tambah Properti'), array(
                'class' => 'section-title print-align-left',
            ));

            $point_content = '';

            foreach ($values as $key => $pointPlus) {
                $name = $this->Rumahku->filterEmptyField($pointPlus, 'PropertyPointPlus', 'name');
                $point_content .= $this->Html->tag('li', $name, array(
                    'class' => 'enabled col-md-4'
                ));
            }

            echo $this->Html->tag('ul', $point_content, array(
                'class' => 'property-amenities-list row print-property-amenities-list point-plus',
            ));
    ?>
</div>
<?php
        }
?>