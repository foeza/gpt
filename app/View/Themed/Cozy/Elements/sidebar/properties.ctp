<?php 
        $titlePage = !empty($titlePage)?$titlePage:__('Properti Pilihan');
        $linkOpt = $this->Rumahku->_callIsDirector()?array(
            'target' => '_blank',
        ):array();

        if( !empty($properties) ) {
?>
<div class="widget widget-simple">
    <div class="widget-title">
        <?php 
                echo $this->Html->tag('h2', $titlePage);
        ?>
    </div><!-- /.widget-title -->
    <div class="widget-content">
        <?php 
                foreach ($properties as $key => $property) {
                    $size = 'm';
                    $mls_id = $this->Rumahku->safeTagPrint($property['Property']['mls_id']);
                    $photo = $this->Rumahku->safeTagPrint($property['Property']['photo']);
                    $slug =  $this->Rumahku->toSlug($this->Rumahku->getPropertyNameCustom($property));
                    $property_url = $this->Rumahku->_callUrlProperty($property, $mls_id, $slug);

                    $lot_unit = $this->Rumahku->getLotUnit($property['Property']['lot_unit']);
                    $lot_unit = ($lot_unit) ? $lot_unit : ' m&sup2;';

                    $propertyPrice = $this->Rumahku->generatePriceProperty($property);
        ?>
        <div class="property-small">
            <div class="property-small-inner">
                <div class="property-small-image">
                    <?php 
                            $detail_content = $this->Rumahku->photo_thumbnail(array(
                                'save_path' => Configure::read('__Site.property_photo_folder'), 
                                'src'=> $photo, 
                                'size' => $size,
                                'zc'=> 1,
                            ));
                            echo $this->Html->link($detail_content, $property_url, array_merge($linkOpt, array(
                                'escape' => false,
                                'class' => 'property-small-image-inner',
                            )));
                    ?>
                </div><!-- /.property-small-image -->
                <div class="property-small-content">
                    <?php 
                            $title = $this->Rumahku->safeTagPrint($property['Property']['title']);
                            echo $this->Html->tag('h3', $this->Html->link($title, $property_url, array_merge($linkOpt, array(
                                'escape' => false,
                            ))), array(
                                'class' => 'property-small-title',
                            ));
                            echo $this->Html->tag('div', $this->Html->tag('small', !empty($property['PropertyAddress']['City']['name'])?$property['PropertyAddress']['City']['name']:false));
                            echo $this->Html->tag('div', $propertyPrice, array(
                                'class' => 'property-small-price',
                            ));
                    ?>
                </div><!-- /.property-small-content -->
            </div><!-- /.property-small-inner -->
        </div><!-- /.property-small -->
        <?php 
                }
        ?>
    </div><!-- /.widget-content -->
</div><!-- /.widget -->
<?php 
        }
?>