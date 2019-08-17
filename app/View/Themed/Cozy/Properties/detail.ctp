<?php
        $id = $this->Rumahku->filterEmptyField($value, 'Property', 'id');
        $title = $this->Rumahku->filterEmptyField($value, 'Property', 'title');
        $mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');
        $description = $this->Rumahku->filterEmptyField($value, 'Property', 'description', false, true, 'EOL');

        $type = $this->Rumahku->filterEmptyField($value, 'PropertyType', 'name');

        $_action = $this->Rumahku->filterEmptyField($value, 'Property', 'property_action_id');
        $_type = $this->Rumahku->filterEmptyField($value, 'PropertyType', 'is_building');

        // custom badge
        $name = $this->Rumahku->filterEmptyField($value, 'PropertyStatusListing', 'name');
        $badge_color = $this->Rumahku->filterEmptyField($value, 'PropertyStatusListing', 'badge_color', '');

        $status_listing = '';
        if (!empty($name)) {
            $status_listing = $this->Html->div('value status-listing', $name, array(
                'style' => 'background-color:'.$badge_color.';',
            ));
        }

        $price = $this->Property->getPrice($value, false, false, false);
        $spec = $this->Property->getSpec($value, false, array(
                            'class' => 'amenities',
                        ));
        $spec = str_replace(array(
            'L. Bangunan',
            'L. Tanah',
            'L. Unit',
            'K. Tidur',
            'Sertifikat',
            'Lantai',
            'Dimensi',
        ), array(
            $this->Rumahku->icon('icon-area'),
            $this->Rumahku->icon('icon-area'),
            $this->Rumahku->icon('icon-area'),
            $this->Rumahku->icon('icon-bedrooms'),
            $this->Rumahku->icon('fa fa-file-text-o'),
            $this->Rumahku->icon('fa fa-bars'),
            $this->Rumahku->icon('fa fa-arrows-alt'),
        ), $spec);

        $label = $this->Property->getNameCustom($value);
        $slug = $this->Rumahku->toSlug($label);

        $urlContact = array(
            'controller'=> 'properties', 
            'action' => 'contact',
            $id,
            true,
            'admin' => false,
        );

        $this->Html->addCrumb(__('Properti'), array(
            'controller' => 'properies',
            'action' => 'find',
            'admin' => false,
        ));
        $this->Html->addCrumb($label);
?>
<!-- BEGIN CONTENT WRAPPER -->
<div class="content gray" id="detail-property">
    <div class="container">
        <div class="row">
        
            <!-- BEGIN MAIN CONTENT -->
            <div class="main col-sm-8 print-no-pd">
                <?php 
                        echo $this->Html->tag('h1', $label, array(
                            'class' => 'property-title print-no-mg'
                        ));
                ?>
                
                <div class="property-topinfo hidden-print">
                    <?php
                            echo $spec;
                            echo $this->Html->tag('div', $this->Html->link(__('Hubungi Agen'), array(
                                'controller' => 'properties',
                                'action' => 'leads',
                                $id,
                                'admin' => false,
                            ),array(
                                'class' => 'ajax-link btn btn-darkred',
                                'data-scroll' => '#contact-agent-form',
                            )), array(
                                'id' => 'property-id'
                            ));
                    ?>
                </div>

                <!-- BEGIN PROPERTY DETAIL SLIDERS WRAPPER -->
                <div id="property-detail-wrapper" class="style1">
                    <div class="price hidden-print">
                        <?php
                                echo $this->Rumahku->icon('fa fa-home').$type;
                                echo $this->Html->tag('span', $price);
                        ?>
                    </div>

                    <?php
                            if (!empty($name)) {
                                echo $this->Html->tag('div',
                                    $this->Html->tag('span', $name), array(
                                        'class' => 'price detail-status-listing hidden-print',
                                        'style' => 'background-color:'.$badge_color.';',
                                ));
                            }

                            echo $this->element('blocks/properties/galleries');
                            echo $this->element('blocks/properties/frontend/specs', array(
                                '_class' => 'visible-xs-block',
                            ));
                    ?>
                </div>
                
                <?php
                        echo $this->Html->tag('h1', $title, array(
                            'class' => 'print-h1 print-float-left print-mg-top-1 visible-print',
                        ));
                        echo $this->Html->tag('p', $description, array(
                            'class' => 'print-float-left print-mg-top-2'  
                        ));
                        echo $this->element('blocks/properties/frontend/specs', array(
                            '_class' => 'visible-print',
                        ));
                        echo $this->element('blocks/properties/facilities');
                        echo $this->element('blocks/properties/point_plus');
                        echo $this->element('blocks/properties/address');
                        echo $this->element('blocks/properties/videos');
                        // widget KPR
                        echo $this->element('widgets/kpr/list_bank', array(
                            'headerClass' => 'property__subtitle',
                        ));
                        echo $this->element('blocks/properties/map');
                        echo $this->element('blocks/properties/share');
                        echo $this->element('blocks/common/contact', array(
                            '_url' => $urlContact,
                        ));
                        echo $this->element('blocks/properties/neighbours');
                ?>
            </div>  
            <!-- END MAIN CONTENT -->
            <div class="sidebar gray col-sm-4 hidden-print">
                <?php 
                        echo $this->element('blocks/properties/frontend/specs', array(
                            '_class' => 'hidden-xs',
                        ));
                        echo $this->element('widgets/widget_calculator_kpr');
                        echo $this->element('blocks/common/sidebars/right_content');
                ?>
            </div>
        </div>
    </div>
</div>