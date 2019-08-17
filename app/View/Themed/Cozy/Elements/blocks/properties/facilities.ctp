<?php
        $values = $this->Rumahku->filterEmptyField($value, 'PropertyFacility');

        if( !empty($values) ){
?>
<div class="print-side-left">
    <?php
            echo $this->Html->tag('h1', __('Fasilitas Properti'), array(
                'class' => 'section-title print-align-left',
            ));

            $fasilitasContent = '';

            foreach ($values as $key => $fasilitas) {
                $name = $this->Rumahku->filterEmptyField($fasilitas, 'Facility', 'name');
                $name = $this->Rumahku->filterEmptyField($fasilitas, 'PropertyFacility', 'other_text', $name);

                $fasilitasContent .= $this->Html->tag('li', $name, array(
                    'class' => 'enabled col-md-4',
                ));
            }

            echo $this->Html->tag('ul', $fasilitasContent, array(
                'class' => 'property-amenities-list row print-property-amenities-list',
            ));
    ?>
</div>
<?php
        }
?>