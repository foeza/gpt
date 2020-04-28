<?php
        $id     = Common::hashEmptyField($value, 'Property.id');
        $title  = Common::hashEmptyField($value, 'Property.title');
        $mls_id = Common::hashEmptyField($value, 'Property.mls_id');
        $desc   = Common::hashEmptyField($value, 'Property.description');

        $price = $this->Property->getPrice($value, false, false, false);

        $label = $this->Property->getNameCustom($value);
        $slug = $this->Rumahku->toSlug($label);

        // $urlContact = array(
        //     'controller'=> 'properties', 
        //     'action' => 'contact',
        //     $id,
        //     true,
        //     'admin' => false,
        // );

        $this->Html->addCrumb(__('Produk'), array(
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
                <div id="property-detail-wrapper" class="style1">
                    <?php
                            echo $this->element('blocks/properties/galleries');
                            echo $this->element('blocks/properties/frontend/specs', array(
                                '_class' => 'visible-xs-block',
                            ));
                    ?>
                </div>
                
                <div class="wrapper-section-desc">
                    <?php
                            echo $this->Html->tag('h3', __('Deskripsi'), array('class' => 'section-title'));
                            echo $this->Html->tag('div', $desc, array(
                                'id'    => 'section-desc',
                                'class' => 'print-float-left print-mg-top-2'  
                            ));
                            // echo $this->element('blocks/properties/facilities');
                            // echo $this->element('blocks/properties/point_plus');
                            // echo $this->element('blocks/properties/address');
                            // echo $this->element('blocks/properties/videos');
                            

                            echo $this->element('blocks/properties/share');
                            // echo $this->element('blocks/properties/neighbours');
                            // echo $this->element('blocks/common/contact', array(
                            //     '_url' => $urlContact,
                            // ));
                    ?>
                </div>
            </div>  
            <!-- END MAIN CONTENT -->
            <div class="sidebar gray col-sm-4 hidden-print">
                <?php 
                        echo $this->element('blocks/properties/frontend/specs', array(
                            '_class' => 'hidden-xs',
                        ));
                        // echo $this->element('blocks/common/sidebars/right_content');
                ?>
            </div>
        </div>
    </div>
</div>