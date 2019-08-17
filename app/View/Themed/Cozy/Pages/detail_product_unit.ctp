<?php
        $_config    = !empty($_config)?$_config:false;
        $theme_name = Common::hashEmptyField($_config, 'Theme.alias', '');

        if (!empty($values)) {
            $project_id   = Common::hashEmptyField($values, 'Product.project_id');
            $product_name = Common::hashEmptyField($values, 'Product.name');
        }
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

        $this->Html->addCrumb($product_name);
        
        $save_path = Configure::read('__Site.general_folder');
?>
<div class="content gray">
    <div class="container">
        <div class="row">
            <div class="main col-sm-8">
                <?php
                        if(!empty($values)){
                ?>
                            <div id="blog-listing" class="grid-style1 developer-list clearfix">
                                <?php
                                        echo $this->element('blocks/products/detail_product_info', array(
                                            'with_container' => 'wrap-gallery',
                                            'custom_wrapper_gallery' => 'gallery-product'
                                        ));
                                        echo $this->element('blocks/products/list_unit', array(
                                            'classThemeName' => $theme_name,
                                        ));
                                ?>
                            </div>
                <?php
                        } else {
                            echo $this->Html->tag('div', __('Data tidak ditemukan.'), array(
                                'class' => 'alert alert-danger'
                            ));
                        }
                ?>
            </div>
            <?php 
                    echo $this->Html->tag('div', $this->element('blocks/common/sidebars/right_content'), array(
                        'class' => 'sidebar gray col-sm-4',
                    ));
            ?>
        </div>
    </div>
</div>