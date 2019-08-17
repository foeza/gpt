<?php
        $product_name = Common::hashEmptyField($values, 'Product.name', '');
        $title        = Common::hashEmptyField($values, 'ProductUnit.name', '');
        $project_id   = Common::hashEmptyField($values, 'ProductUnit.project_id', '');
        $description  = Common::hashEmptyField($values, 'ProductUnit.description', false, array(
            'type' => 'EOL',
        ));

        // unit_id  = original_id in table api_advance_developer_product_unit
        $unit_id      = Common::hashEmptyField($values, 'ProductUnit.id');

        $data_gallery = Common::hashEmptyField($values, 'Gallery', false);

        $price = $this->Project->getPrice($values, false, false, false);
        $spec = $this->Project->_callSpecUnit($values, array(
            'class' => 'overviewList',
        ));

        $listUnitMaterial = $this->Project->_callListUnitMaterial($values, array(
            'class' => 'overviewList',
        ));

        // breadscrumb
        if (!empty($product_name)) {
            $this->Html->addCrumb(__('Developers'), array(
                'controller' => 'pages',
                'action' => 'developers',
                'admin' => false,
            ));
            $this->Html->addCrumb(__('Product'), array(
                'controller' => 'pages',
                'action' => 'list_product',
                $project_id,
                'admin' => false,
            ));

            $this->Html->addCrumb($product_name, array(
                'controller' => 'pages',
                'action' => 'detail_product_unit',
                $product_id,
                'admin' => false,
            ));
        } else {
            $this->Html->addCrumb(__('Unit'), array(
                'controller' => 'pages',
                'action' => 'unit_list',
                $project_id,
                'admin' => false,
            ));
        }

        $this->Html->addCrumb( sprintf('Unit %s',$title) );

        if (!empty($product_name)) {
            $product_name = sprintf('%s - ', $this->Html->tag('strong', $product_name));
        } else {
            $product_name = '';
        }
?>
<!-- BEGIN CONTENT WRAPPER -->
<div class="content gray" id="detail-property">
    <div class="container">
        <div class="row">
        
            <!-- BEGIN MAIN CONTENT -->
            <div class="main col-sm-8 print-no-pd">
                <?php
                        $title_unit = sprintf('%s %s', $product_name, __('Unit : ').$title);

                        echo $this->Html->tag('h1', $title_unit, array(
                            'class' => 'property-title print-no-mg'
                        ));
                ?>

                <!-- BEGIN PROPERTY DETAIL SLIDERS WRAPPER -->
                <div id="property-detail-wrapper" class="style1">
                    <div class="price unit-price hidden-print">
                        <?php
                                echo $this->Html->tag('span', __('Mulai dari ').$price);
                        ?>
                    </div>
                    <?php 
                            echo $this->element('blocks/products/widget_media_unit', array(
                                'dataMedias' => $data_gallery
                            ));
                    ?>
                </div>
                
                <?php
                        echo $this->Html->tag('p', $description, array(
                            'class' => 'print-float-left print-mg-top-2'  
                        ));

                        // widget share
                        echo $this->Html->tag('div', $this->element('blocks/common/share', array(
                            'share_id'      => $unit_id,
                            'share_type'    => 'project_detail_unit',
                            'url'           => $this->Html->url($this->here, true),
                            'title'         => $og_meta['title'],
                            '_print'        => false,
                        )), array(
                            'class' => 'mt20',
                        ));                        

                        echo $this->element('blocks/projects/bookings/table_list_stocks', array(
                            'generate_api' => true,
                            'project_id' => $project_id,
                            'url' => array(
                                'controller' => 'pages',
                                'action' => 'stocks_booking',
                                $project_id,
                                $product_unit_id,
                                $product_id,
                                'admin' => false
                            )
                        ));
                ?>
            </div>  
            <!-- END MAIN CONTENT -->
            <div class="sidebar gray col-sm-4 hidden-print">
                <?php
                        echo $this->element('blocks/products/widget_spec', array(
                            'spec' => $spec,
                            'listUnitMaterial' => $listUnitMaterial,
                        ));
                        echo $this->element('blocks/common/sidebars/right_content');
                ?>
            </div>
        </div>
    </div>
</div>