<?php
        $save_path = Configure::read('__Site.general_folder');
        
        $this->Html->addCrumb(__('Developers'), array(
                'action' => 'developers',
            ));
        $this->Html->addCrumb($module_title);
?>
<div class="content gray">
    <div class="container">
        <div class="row">
            <div class="main col-sm-8">
                <?php
                        if(!empty($values)){
                ?>
                <div id="blog-listing list-product" class="grid-style1 developer-list clearfix">
                    <?php
                            echo $this->element('blocks/projects/product_list');
                    ?>
                </div>
                <?php
                        }else{
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